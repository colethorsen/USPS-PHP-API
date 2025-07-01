<?php

namespace ColeThorsen\USPS;

use ColeThorsen\USPS\Exceptions\TechnicalException;
use ColeThorsen\USPS\Exceptions\ValidationException;
use ColeThorsen\USPS\Validation\Validator;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use League\OpenAPIValidation\PSR7\Exception\ValidationFailed;
use Psr\Http\Message\RequestInterface;

class USPS
{
    protected Client $client;
    protected array $services = [];
    protected string $consumerKey;
    protected string $consumerSecret;
    protected ?string $accessToken = null;
    protected ?int $tokenExpiry    = null;

    // Default configuration
    protected array $config = [
        'baseUrl'          => 'https://apis.usps.com',
        'testUrl'          => 'https://apis-tem.usps.com',
        'testMode'         => false,
        'timeout'          => 30,
        'validateRequests' => true,
        'cache'            => null, // PSR-6 CacheItemPoolInterface instance
    ];

    public function __construct(string $consumerKey, string $consumerSecret, array $config = [])
    {
        $this->consumerKey    = $consumerKey;
        $this->consumerSecret = $consumerSecret;

        // Merge provided config with defaults
        $this->config = array_merge($this->config, $config);

        $baseUri = $this->config['testMode'] ? $this->config['testUrl'] : $this->config['baseUrl'];

        // Create a handler stack with our validation middleware
        $stack = HandlerStack::create();
        $stack->push($this->createValidationMiddleware());

        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => $this->config['timeout'],
            'headers'  => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'handler' => $stack,
        ]);
    }

    /**
     * Creates a new USPS Client with the given credentials.
     */
    public static function client(string $consumerKey, string $consumerSecret, array $config = []): self
    {
        return new self($consumerKey, $consumerSecret, $config);
    }

    /**
     * Magic getter to access services
     */
    public function __get(string $name)
    {
        // Check if we already have an instance of this service
        if (array_key_exists($name, $this->services)) {
            return $this->services[$name];
        }

        // If not, look up the class and instantiate it
        $classMap = [
            'addresses'           => Services\Addresses::class,
            'domesticPrices'      => Services\DomesticPrices::class,
            'internationalPrices' => Services\InternationalPrices::class,
            'locations'           => Services\Locations::class,
        ];

        $serviceClass = $classMap[$name] ?? null;

        if (! $serviceClass) {
            throw new TechnicalException("Service '{$name}' not found");
        }

        // Create and cache the service instance
        $this->services[$name] = new $serviceClass($this);

        return $this->services[$name];
    }

    /**
     * Get the current configuration
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Create middleware for request validation
     */
    protected function createValidationMiddleware(): callable
    {
        return function (callable $handler) {
            return function (RequestInterface $request, array $options) use ($handler) {
                // If validator is provided in options, use it
                if (isset($options['validator']) && $options['validator'] instanceof Validator) {
                    $options['validator']->validateRequest($request);
                }

                // Continue with the request
                return $handler($request, $options);
            };
        };
    }

    /**
     * Send a request to the USPS API
     */
    public function sendRequest(string $method, string $uri, array $options = [], ?Validator $validator = null): object|array
    {
        try {
            // Ensure we have a valid token
            $this->ensureValidToken();

            // Add Bearer token to headers
            $options['headers'] = array_merge(
                $options['headers'] ?? [],
                ['Authorization' => 'Bearer ' . $this->accessToken]
            );

            // Add validator to options if validation is enabled and validator provided
            if ($this->config['validateRequests'] && $validator) {
                $options['validator'] = $validator;
            }

            // Send the request (middleware will handle validation)
            $response = $this->client->request($method, $uri, $options);
            $body     = $response->getBody()->getContents();

            return json_decode($body) ?: new \stdClass;

        } catch (ValidationFailed $e) {

            throw new ValidationException('Request validation failed: ' . $e->getMessage(), 400);
        } catch (\GuzzleHttp\Exception\ClientException $e) {

            $response   = $e->getResponse();
            $statusCode = $response ? $response->getStatusCode() : 0;
            $body       = $response ? $response->getBody()->getContents() : '';
            $errorData  = json_decode($body) ?: new \stdClass;

            // Build detailed error message from USPS error structure
            $message = $errorData->error->message ?? $errorData->message ?? 'Request failed';

            // If we have detailed errors array, append them
            if (isset($errorData->error->errors) && is_array($errorData->error->errors)) {
                $errorDetails = [];
                foreach ($errorData->error->errors as $error) {
                    $detail = sprintf(
                        '%s: %s (Code: %s)',
                        $error->title  ?? 'Error',
                        $error->detail ?? 'No details',
                        $error->code   ?? 'Unknown'
                    );
                    if (isset($error->source->parameter)) {
                        $detail .= " [Parameter: {$error->source->parameter}]";
                    }
                    $errorDetails[] = $detail;
                }
                $message .= "\n" . implode("\n", $errorDetails);
            }

            switch ($statusCode) {
                case 400:
                    throw new ValidationException($message, 400, $errorData);
                case 401:
                    // Try to refresh token once and retry
                    if (! isset($options['_retry_auth'])) {
                        $this->accessToken      = null;
                        $options['_retry_auth'] = true;

                        return $this->sendRequest($method, $uri, $options, $validator);
                    }
                    throw new TechnicalException($message, 401, $errorData);
                case 429:
                    // Retry rate limits up to 3 times using Retry-After header
                    $retryCount = $options['_retry_count'] ?? 0;
                    if ($retryCount < 3) {
                        // Get retry delay from header, default to exponential backoff
                        $retryAfter = $response->getHeader('Retry-After')[0] ?? null;
                        $delay      = $retryAfter ? (int) $retryAfter : pow(2, $retryCount);

                        // Cap delay at 60 seconds
                        $delay = min($delay, 60);

                        sleep($delay);
                        $options['_retry_count'] = $retryCount + 1;

                        return $this->sendRequest($method, $uri, $options, $validator);
                    }
                    throw new TechnicalException($message, 429, $errorData);
                default:
                    throw new TechnicalException($message, $statusCode, $errorData);
            }
        }
    }

    /**
     * Ensure we have a valid access token
     */
    protected function ensureValidToken(): void
    {
        if (! $this->accessToken || time() >= ($this->tokenExpiry - 60)) {
            $this->refreshToken();
        }
    }

    /**
     * Refresh the OAuth2 access token using client credentials flow
     */
    protected function refreshToken(): void
    {
        try {
            $response = $this->client->post('/oauth2/v3/token', [
                'json' => [
                    'client_id'     => $this->consumerKey,
                    'client_secret' => $this->consumerSecret,
                    'grant_type'    => 'client_credentials',
                ],
            ]);

            $data = json_decode($response->getBody()->getContents());

            $this->accessToken = $data->access_token;
            $this->tokenExpiry = time() + ($data->expires_in ?? 3600);
        } catch (\Exception $e) {
            throw new TechnicalException('Failed to obtain access token: ' . $e->getMessage());
        }
    }
}
