<?php

namespace ColeThorsen\USPS\Exceptions;

class USPSException extends \Exception
{
    protected $errorData;

    public function __construct(string $message, int $code = 0, $errorData = null)
    {
        parent::__construct($message, $code);
        $this->errorData = $errorData;
    }

    /**
     * Get the full error data from the API response
     *
     * @return mixed
     */
    public function getErrorData()
    {
        return $this->errorData;
    }

    /**
     * Get a specific error field if available
     *
     * @return mixed|null
     */
    public function getErrorField(string $field)
    {
        if (is_object($this->errorData)) {
            return $this->errorData->$field ?? null;
        }

        return null;
    }
}
