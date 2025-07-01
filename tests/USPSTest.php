<?php

namespace ColeThorsen\USPS\Tests;

class USPSTest extends TestCase
{
    /**
     * Test creating USPS client instance
     */
    public function test_create_client(): void
    {
        $this->assertInstanceOf(\ColeThorsen\USPS\USPS::class, $this->usps);
    }

    /**
     * Test accessing services through magic getter
     */
    public function test_access_services(): void
    {
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\Addresses::class, $this->usps->addresses);
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\CarrierPickup::class, $this->usps->carrierPickup);
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\Locations::class, $this->usps->locations);
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\DomesticPrices::class, $this->usps->domesticPrices);
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\InternationalPrices::class, $this->usps->internationalPrices);
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\DomesticLabels::class, $this->usps->domesticLabels);
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\InternationalLabels::class, $this->usps->internationalLabels);
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\Containers::class, $this->usps->containers);
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\Payments::class, $this->usps->payments);
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\PMOD::class, $this->usps->pmod);
        $this->assertInstanceOf(\ColeThorsen\USPS\Services\Adjustments::class, $this->usps->adjustments);
    }

    /**
     * Test service caching (should return same instance)
     */
    public function test_service_caching(): void
    {
        $addresses1 = $this->usps->addresses;
        $addresses2 = $this->usps->addresses;

        $this->assertSame($addresses1, $addresses2);
    }

    /**
     * Test accessing invalid service throws exception
     */
    public function test_access_invalid_service_throws_exception(): void
    {
        $this->expectException(\ColeThorsen\USPS\Exceptions\TechnicalException::class);
        $this->expectExceptionMessage("Service 'invalidService' not found");

        $this->usps->invalidService;
    }

    /**
     * Test configuration
     */
    public function test_configuration(): void
    {
        $config = $this->usps->getConfig();

        $this->assertIsArray($config);
        $this->assertArrayHasKey('testMode', $config);
        $this->assertArrayHasKey('validateRequests', $config);
        $this->assertTrue($config['testMode']);
        $this->assertTrue($config['validateRequests']);
    }

    /**
     * Test static client factory method
     */
    public function test_static_client_factory(): void
    {
        $consumerKey    = getenv('USPS_CONSUMER_KEY');
        $consumerSecret = getenv('USPS_CONSUMER_SECRET');

        if (! $consumerKey || ! $consumerSecret) {
            $this->markTestSkipped('USPS API credentials not configured');
        }

        $client = \ColeThorsen\USPS\USPS::client($consumerKey, $consumerSecret, [
            'testMode' => true,
        ]);

        $this->assertInstanceOf(\ColeThorsen\USPS\USPS::class, $client);
    }
}
