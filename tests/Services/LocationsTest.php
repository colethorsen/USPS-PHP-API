<?php

namespace ColeThorsen\USPS\Tests\Services;

use ColeThorsen\USPS\Enums\MailClass;
use ColeThorsen\USPS\Enums\ProcessingCategory;
use ColeThorsen\USPS\Tests\TestCase;

class LocationsTest extends TestCase
{
    /**
     * Test finding dropoff locations
     */
    public function test_find_dropoff_locations(): void
    {
        $params = [
            'mailClass'          => MailClass::PARCEL_SELECT->value,
            'destinationZIPCode' => $this->getSecondaryTestAddress()['ZIPCode'],
            'processingCategory' => ProcessingCategory::MACHINABLE->value,
            'mailingDate'        => date('Y-m-d', strtotime('+1 day')),
        ];

        $response = $this->usps->locations->dropoffLocations($params);

        $this->assertIsArray($response);

        if (! empty($response)) {
            $location = $response[0];
            $this->assertIsObject($location);
            $this->assertObjectHasProperty('facilityName', $location);
            $this->assertObjectHasProperty('facilityAddress', $location);
        }
    }

    /**
     * Test finding post office locations by ZIP code
     */
    public function test_find_post_office_locations_by_zip(): void
    {
        $address = $this->getValidTestAddress();
        $params  = [
            'ZIPCode' => $address['ZIPCode'],
        ];

        $response = $this->usps->locations->postOfficeLocations($params);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('locations', $response);
        $this->assertIsArray($response->locations);

        if (! empty($response->locations)) {
            $postOffice = $response->locations[0];
            $this->assertObjectHasProperty('facilityName', $postOffice);
            $this->assertObjectHasProperty('facilityAddress', $postOffice);
            // facilityID may not always be present in the response
        }
    }

    /**
     * Test finding post office locations by city and state
     */
    public function test_find_post_office_locations_by_city_state(): void
    {
        $address = $this->getValidTestAddress();
        $params  = [
            'city'  => $address['city'],
            'state' => $address['state'],
        ];

        $response = $this->usps->locations->postOfficeLocations($params);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('locations', $response);
        $this->assertIsArray($response->locations);
    }

    /**
     * Test finding parcel locker locations
     */
    public function test_find_parcel_locker_locations(): void
    {
        $params = [
            'city'   => $this->getSecondaryTestAddress()['city'],
            'state'  => $this->getSecondaryTestAddress()['state'],
            'offset' => 0,     // Required parameter
            'limit'  => 10,    // Required parameter
        ];

        $response = $this->usps->locations->parcelLockerLocations($params);

        $this->assertIsArray($response);
        // Response structure may vary based on location availability
    }

    /**
     * Test dropoff locations with ZIP+4
     */
    public function test_dropoff_locations_with_zip_plus4(): void
    {
        $destAddress = $this->getSecondaryTestAddressWithPlus4();
        $params      = [
            'mailClass'           => MailClass::MEDIA_MAIL->value,
            'destinationZIPCode'  => substr($destAddress['ZIPCode'], 0, 5), // Just the 5-digit ZIP
            'destinationZIPPlus4' => substr($destAddress['ZIPCode'], -4),  // Just the 4-digit extension
            'processingCategory'  => ProcessingCategory::NONSTANDARD->value,
            'mailingDate'         => date('Y-m-d', strtotime('+2 days')),
        ];

        $response = $this->usps->locations->dropoffLocations($params);

        $this->assertIsArray($response);
        // Test array can be empty or contain locations
    }
}
