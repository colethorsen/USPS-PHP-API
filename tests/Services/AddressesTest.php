<?php

namespace ColeThorsen\USPS\Tests\Services;

use ColeThorsen\USPS\Exceptions\ValidationException;
use ColeThorsen\USPS\Tests\TestCase;

class AddressesTest extends TestCase
{
    /**
     * Test address validation with a valid address
     */
    public function test_address_validation(): void
    {
        $address = $this->getValidTestAddress();

        $response = $this->usps->addresses->address($address);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('address', $response);
        $this->assertObjectHasProperty('streetAddress', $response->address);
        $this->assertObjectHasProperty('city', $response->address);
        $this->assertObjectHasProperty('state', $response->address);
        $this->assertObjectHasProperty('ZIPCode', $response->address);
    }

    /**
     * Test address validation with ZIP+4 format
     */
    public function test_address_validation_with_zip_plus4(): void
    {
        $address = $this->getValidTestAddressWithPlus4();

        $zipCode = explode('-', $address['ZIPCode']);

        $response = $this->usps->addresses->address($address);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('address', $response);
        // Verify ZIP was split correctly
        $this->assertEquals($zipCode[0], $response->address->ZIPCode);
        $this->assertEquals($zipCode[1], $response->address->ZIPPlus4);
    }

    /**
     * Test address validation with optional fields
     */
    public function test_address_validation_with_optional_fields(): void
    {
        $address                     = $this->getValidTestAddress();
        $address['firm']             = 'Test Company';
        $address['secondaryAddress'] = 'Suite 100';

        $response = $this->usps->addresses->address($address);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('address', $response);
    }

    /**
     * Test city/state lookup by ZIP code
     */
    public function test_city_state_lookup(): void
    {
        $testAddress = $this->getValidTestAddress();
        $response    = $this->usps->addresses->cityState($testAddress['ZIPCode']);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('city', $response);
        $this->assertObjectHasProperty('state', $response);
        $this->assertObjectHasProperty('ZIPCode', $response);
        $this->assertEquals(strtoupper($testAddress['city']), $response->city);
        $this->assertEquals($testAddress['state'], $response->state);
        $this->assertEquals($testAddress['ZIPCode'], $response->ZIPCode);
    }

    /**
     * Test ZIP code lookup
     */
    public function test_zip_code_lookup(): void
    {
        // Remove ZIPCode from the valid test address for ZIP lookup
        $address = $this->getValidTestAddress();
        unset($address['ZIPCode']);

        $response = $this->usps->addresses->zipcode($address);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('address', $response);
        $this->assertObjectHasProperty('ZIPCode', $response->address);
        $this->assertEquals($this->getValidTestAddress()['ZIPCode'], $response->address->ZIPCode);
    }

    /**
     * Test validation error with invalid state
     */
    public function test_address_validation_with_invalid_state(): void
    {
        $this->expectException(ValidationException::class);

        $address          = $this->getInvalidTestAddress();
        $address['state'] = 'ILA'; // Invalid state code

        $this->usps->addresses->address($address);
    }

    /**
     * Test validation with missing required fields
     */
    public function test_address_validation_with_missing_fields(): void
    {
        $this->expectException(ValidationException::class);

        $testAddress = $this->getValidTestAddress();
        $address     = [
            'city' => $testAddress['city'],
            // Missing streetAddress and state
        ];

        $this->usps->addresses->address($address);
    }
}
