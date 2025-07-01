<?php

namespace ColeThorsen\USPS\Services;

use ColeThorsen\USPS\Exceptions\TechnicalException;
use ColeThorsen\USPS\USPS;
use ColeThorsen\USPS\Validation\Validator;

abstract class Service
{
    protected USPS $usps;
    protected Validator $validator;

    /**
     * Base API path for this service
     */
    protected const BASE_PATH = '';

    /**
     * API Definition for this service.
     */
    protected const API_DEFINITION = null;

    public function __construct(USPS $usps)
    {
        $this->usps = $usps;

        if (! static::API_DEFINITION) {
            throw new TechnicalException('API Definition not found for ' . static::class);
        }

        $this->validator = new Validator(
            static::API_DEFINITION,
            $this->usps->getConfig()['cache'] ?? null
        );
    }

    /**
     * Send a request through the USPS client
     */
    protected function request(string $method, string $uri, array $options = []): object|array
    {
        // Prepend base path if uri doesn't start with /
        if (static::BASE_PATH && ! str_starts_with($uri, '/')) {
            $uri = static::BASE_PATH . $uri;
        }

        // Pass the validator instance for this service
        return $this->usps->sendRequest($method, $uri, $options, $this->validator);
    }

    /**
     * Process ZIP codes in data recursively
     * Splits ZIP codes with +4 into separate fields
     */
    protected function processZipCodes(array $data): array
    {
        foreach ($data as $key => $value) {
            // Process nested arrays recursively
            if (is_array($value)) {
                $data[$key] = $this->processZipCodes($value);
            }
            // Process ZIP codes
            elseif ($key === 'ZIPCode' && is_string($value) && strpos($value, '-') !== false) {
                [$zip, $plus4]   = explode('-', $value, 2);
                $data['ZIPCode'] = $zip;

                if (! isset($data['ZIPPlus4'])) {
                    $data['ZIPPlus4'] = $plus4;
                }
            }
        }

        return $data;
    }
}
