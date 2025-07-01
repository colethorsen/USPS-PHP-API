<?php

namespace ColeThorsen\USPS\Services;

use ColeThorsen\USPS\Exceptions\USPSException;

class Addresses extends Service
{
    protected const BASE_PATH      = '/addresses/v3/';
    protected const API_DEFINITION = 'addresses.yaml';

    /**
     * Verify/standardize a single address
     * GET /addresses/v3/address
     *
     * @param array{
     *     firm?: string,
     *     streetAddress: string,
     *     secondaryAddress?: string,
     *     city?: string,
     *     urbanization?: string,
     * 	   state: string,
     *     ZIPCode?: string,
     *     ZIPPlus4?: string
     * } $address
     *
     * @throws USPSException
     */
    public function address(array $address): object
    {
        $address = $this->processZipCodes($address);

        return $this->request('GET', 'address', [
            'query' => $address,
        ]);
    }

    /**
     * City and state lookup by ZIP Code
     * GET /addresses/v3/city-state
     *
     * @throws USPSException
     */
    public function cityState(string $zipCode): object
    {
        return $this->request('GET', 'city-state', [
            'query' => ['ZIPCode' => $zipCode],
        ]);
    }

    /**
     * ZIP Code lookup by address
     * GET /addresses/v3/zipcode
     *
     * @param array{
     *     streetAddress: string,
     *     city: string,
     *     state: string,
     *     firm?: string
     * } $address
     *
     * @throws USPSException
     */
    public function zipcode(array $address): object
    {
        return $this->request('GET', 'zipcode', [
            'query' => $address,
        ]);
    }
}
