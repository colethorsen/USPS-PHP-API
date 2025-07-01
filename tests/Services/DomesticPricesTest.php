<?php

namespace ColeThorsen\USPS\Tests\Services;

use ColeThorsen\USPS\Enums\ExtraService;
use ColeThorsen\USPS\Enums\FacilityType;
use ColeThorsen\USPS\Enums\MailClass;
use ColeThorsen\USPS\Enums\PriceType;
use ColeThorsen\USPS\Enums\ProcessingCategory;
use ColeThorsen\USPS\Enums\RateIndicator;
use ColeThorsen\USPS\Tests\TestCase;

class DomesticPricesTest extends TestCase
{
    /**
     * Test getting base rates for domestic shipping
     */
    public function test_get_base_rates(): void
    {
        $testAddress      = $this->getValidTestAddress();
        $alternateAddress = $this->getSecondaryTestAddress();

        $params = [
            'originZIPCode'                => $testAddress['ZIPCode'],
            'destinationZIPCode'           => $alternateAddress['ZIPCode'],
            'weight'                       => 1.5,
            'length'                       => 12.0,
            'width'                        => 8.0,
            'height'                       => 3.0,
            'mailClass'                    => MailClass::PRIORITY_MAIL->value,
            'processingCategory'           => ProcessingCategory::MACHINABLE->value,
            'rateIndicator'                => RateIndicator::SINGLE_PIECE->value,
            'destinationEntryFacilityType' => FacilityType::NONE->value,
            'priceType'                    => PriceType::RETAIL->value,
            'mailingDate'                  => date('Y-m-d', strtotime('+1 day')),
        ];

        $response = $this->usps->domesticPrices->baseRates($params);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('rates', $response);
        $this->assertIsArray($response->rates);

        if (! empty($response->rates)) {
            $rate = $response->rates[0];
            $this->assertObjectHasProperty('price', $rate);
            $this->assertObjectHasProperty('mailClass', $rate);
        }
    }

    /**
     * Test getting extra service rates
     */
    public function test_get_extra_service_rates(): void
    {
        $testAddress      = $this->getValidTestAddress();
        $alternateAddress = $this->getSecondaryTestAddress();

        $params = [
            'originZIPCode'      => $testAddress['ZIPCode'],
            'destinationZIPCode' => $alternateAddress['ZIPCode'],
            'weight'             => 1.5,
            //  'length'                       => 12.0,
            //  'width'                        => 8.0,
            //  'height'                       => 3.0,
            'mailClass' => MailClass::PRIORITY_MAIL->value,
            //  'processingCategory'           => ProcessingCategory::MACHINABLE->value,
            //  'rateIndicator'                => RateIndicator::SINGLE_PIECE->value,
            // 'destinationEntryFacilityType' => FacilityType::NONE->value,
            'priceType'   => PriceType::RETAIL->value,
            'itemValue'   => 400,
            'mailingDate' => date('Y-m-d', strtotime('+1 day')),

            'extraServices' => [
                ExtraService::TRACKING_PLUS_6_MONTHS->value,
                ExtraService::USPS_TRACKING_ELECTRONIC->value,
            ],
        ];

        $response = $this->usps->domesticPrices->extraServiceRates($params);

        $this->assertIsArray($response);

        if (! empty($response)) {
            $rate = $response[0];
            $this->assertObjectHasProperty('price', $rate);
            $this->assertObjectHasProperty('extraService', $rate);
            $this->assertObjectHasProperty('priceType', $rate);
        }

    }

    /**
     * Test getting list of available base rates
     */
    public function test_get_base_rates_list(): void
    {
        $testAddress = $this->getValidTestAddress();
        $params      = [
            'originZIPCode'      => $testAddress['ZIPCode'],
            'destinationZIPCode' => '90210',
            'weight'             => 2.0,
            'length'             => 10.0,
            'width'              => 8.0,
            'height'             => 4.0,
            'mailingDate'        => date('Y-m-d', strtotime('+1 day')),
        ];

        $response = $this->usps->domesticPrices->baseRatesList($params);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('rateOptions', $response);
        $this->assertIsArray($response->rateOptions);
    }

    /**
     * Test getting total rates including extra services
     */
    public function test_get_total_rates(): void
    {
        $testAddress      = $this->getValidTestAddress();
        $alternateAddress = $this->getSecondaryTestAddress();

        $params = [
            'originZIPCode'      => $testAddress['ZIPCode'],
            'destinationZIPCode' => $alternateAddress['ZIPCode'],
            'weight'             => 1.0,
            'length'             => 12.0,
            'width'              => 8.0,
            'height'             => 3.0,

        ];

        $response = $this->usps->domesticPrices->totalRates($params);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('rateOptions', $response);
        $this->assertIsArray($response->rateOptions);
    }

    /**
     * Test getting letter rates
     */
    public function test_get_letter_rates(): void
    {
        $params = [
            'weight'             => 0.5,
            'processingCategory' => ProcessingCategory::LETTERS->value,
            'height'             => 6.125,
            'length'             => 11.5,
            'thickness'          => 0.25,
            'mailingDate'        => date('Y-m-d', strtotime('+1 day')),
        ];

        $response = $this->usps->domesticPrices->letterRates($params);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('rates', $response);
        $this->assertIsArray($response->rates);
    }
}
