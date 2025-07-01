<?php

namespace ColeThorsen\USPS\Tests;

use ColeThorsen\USPS\USPS;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected USPS $usps;

    protected function setUp(): void
    {
        parent::setUp();

        // Get credentials from environment variables
        $consumerKey    = $_ENV['USPS_CONSUMER_KEY'];
        $consumerSecret = $_ENV['USPS_CONSUMER_SECRET'];

        if (! $consumerKey || ! $consumerSecret) {
            $this->markTestSkipped(
                'USPS API credentials not configured. Set USPS_CONSUMER_KEY and USPS_CONSUMER_SECRET environment variables.'
            );
        }

        // Create USPS client with test mode enabled
        $this->usps = new USPS($consumerKey, $consumerSecret, [
            'testMode'         => true,
            'validateRequests' => true,
        ]);
    }

    /**
     * Get a valid test address for USPS API testing
     */
    protected function getValidTestAddress(): array
    {
        return [
            'streetAddress' => '600 Fourth Ave',
            'city'          => 'Seattle',
            'state'         => 'WA',
            'ZIPCode'       => '98104',
        ];
    }

    /**
     * Get a valid test address with ZIP+4
     */
    protected function getValidTestAddressWithPlus4(): array
    {
        return [
            'streetAddress' => '600 Fourth Ave',
            'city'          => 'Seattle',
            'state'         => 'WA',
            'ZIPCode'       => '98104-1822',
        ];
    }

    /**
     * Get an invalid test address
     */
    protected function getInvalidTestAddress(): array
    {
        return [
            'streetAddress' => '123 Fake Street',
            'city'          => 'InvalidCity',
            'state'         => 'XX',
            'ZIPCode'       => '00000',
        ];
    }

    /**
     * Get a secondary test address (e.g., for "from" addresses in label tests)
     */
    protected function getSecondaryTestAddress(): array
    {
        return [
            'streetAddress' => '456 Oak St',
            'city'          => 'Los Angeles',
            'state'         => 'CA',
            'ZIPCode'       => '90210',
        ];
    }

    /**
     * Get a secondary test address with ZIP+4
     */
    protected function getSecondaryTestAddressWithPlus4(): array
    {
        return [
            'streetAddress' => '456 Oak St',
            'city'          => 'Los Angeles',
            'state'         => 'CA',
            'ZIPCode'       => '90210-3701',
        ];
    }

    /**
     * Get a test UK address for international shipping tests
     */
    protected function getTestUKAddress(): array
    {
        return [
            'streetAddress' => '10 Downing Street',
            'city'          => 'London',
            'province'      => 'England',
            'postalCode'    => 'SW1A 2AA',
            'countryCode'   => 'GB',
        ];
    }

    /**
     * Get a test Canada address for international shipping tests
     */
    protected function getTestCanadaAddress(): array
    {
        return [
            'streetAddress' => '123 Main St',
            'city'          => 'Toronto',
            'province'      => 'ON',
            'postalCode'    => 'M5V 3A8',
            'countryCode'   => 'CA',
        ];
    }
}
