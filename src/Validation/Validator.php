<?php

namespace ColeThorsen\USPS\Validation;

use GuzzleHttp\Psr7\Request;
use League\OpenAPIValidation\PSR7\RequestValidator;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use Psr\Cache\CacheItemPoolInterface;

class Validator
{
    private RequestValidator $validator;

    public function __construct(string $yaml, ?CacheItemPoolInterface $cache = null)
    {
        $builder = (new ValidatorBuilder)
            ->fromYamlFile(__DIR__ . '/../../definitions/' . $yaml);

        // Use provided PSR-6 cache if available
        if ($cache !== null) {
            $builder->setCache($cache);
        }

        $this->validator = $builder->getRequestValidator();
    }

    public function validateRequest(Request $request): void
    {
        $this->validator->validate($request);
    }
}
