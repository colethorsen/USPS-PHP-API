<?php

namespace ColeThorsen\USPS\Tests\Services;

use ColeThorsen\USPS\Enums\DestinationEntryFacilityType;
use ColeThorsen\USPS\Enums\ExtraService;
use ColeThorsen\USPS\Enums\MailClass;
use ColeThorsen\USPS\Enums\PriceType;
use ColeThorsen\USPS\Enums\ProcessingCategory;
use ColeThorsen\USPS\Enums\RateIndicator;
use ColeThorsen\USPS\Tests\TestCase;

class InternationalPricesTest extends TestCase
{
    /**
     * Test getting base rates for international shipping
     */
    public function test_get_base_rates(): void
    {
        $testAddress = $this->getValidTestAddress();
        $params      = [
            'originZIPCode'                => $testAddress['ZIPCode'],
            'foreignPostalCode'            => 'SW1A 1AA', // UK postal code
            'destinationCountryCode'       => 'GB',
            'destinationEntryFacilityType' => DestinationEntryFacilityType::INTERNATIONAL_SERVICE_CENTER->value,
            'weight'                       => 2.0,
            'length'                       => 12.0,
            'width'                        => 8.0,
            'height'                       => 3.0,
            'mailClass'                    => MailClass::PRIORITY_MAIL_INTERNATIONAL->value,
            'processingCategory'           => ProcessingCategory::MACHINABLE->value,
            'rateIndicator'                => RateIndicator::SINGLE_PIECE->value,
            'priceType'                    => PriceType::RETAIL->value,
            'mailingDate'                  => date('Y-m-d', strtotime('+1 day')),
        ];

        $response = $this->usps->internationalPrices->baseRates($params);

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
     * Test getting extra service rates for international
     */
    public function test_get_extra_service_rates(): void
    {
        $params = [
            'destinationCountryCode' => 'CA', // Canada
            'mailClass'              => MailClass::PRIORITY_MAIL_INTERNATIONAL->value,
            'rateIndicator'          => RateIndicator::SINGLE_PIECE->value,
            'priceType'              => PriceType::RETAIL->value,
            'weight'                 => 1.5,
            'mailingDate'            => date('Y-m-d', strtotime('+1 day')),
            'extraService'           => ExtraService::HAZMAT_CLASS_7_RADIOACTIVE_MATERIALS->value,
        ];

        $response = $this->usps->internationalPrices->extraServiceRates($params);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('extraServiceRates', $response);
        $this->assertIsArray($response->extraServiceRates);
    }

    /**
     * Test getting list of available international rates
     */
    public function test_get_base_rates_list(): void
    {
        $testAddress = $this->getValidTestAddress();
        $params      = [
            'originZIPCode'          => $testAddress['ZIPCode'],
            'destinationCountryCode' => 'MX', // Mexico
            'weight'                 => 3.0,
            'length'                 => 14.0,
            'width'                  => 10.0,
            'height'                 => 5.0,
            'mailingDate'            => date('Y-m-d', strtotime('+1 day')),
        ];

        $response = $this->usps->internationalPrices->baseRatesList($params);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('rateOptions', $response);
        $this->assertIsArray($response->rateOptions);
    }

    /**
     * Test getting total rates with extra services
     */
    public function test_get_total_rates(): void
    {
        $testAddress   = $this->getValidTestAddress();
        $canadaAddress = $this->getTestCanadaAddress();

        $params = [
            'originZIPCode'          => $testAddress['ZIPCode'],
            'foreignPostalCode'      => $canadaAddress['postalCode'],
            'destinationCountryCode' => $canadaAddress['countryCode'],
            'weight'                 => 1.5,
            'length'                 => 12.0,
            'width'                  => 8.0,
            'height'                 => 3.0,
            'mailClass'              => MailClass::PRIORITY_MAIL_EXPRESS_INTERNATIONAL->value,
            'processingCategory'     => ProcessingCategory::MACHINABLE->value,
            'rateIndicator'          => RateIndicator::SINGLE_PIECE->value,
            'priceType'              => PriceType::RETAIL->value,
            'mailingDate'            => date('Y-m-d', strtotime('+1 day')),
            'itemValue'              => 100.00,
            'extraServices'          => [
                ExtraService::HAZMAT_CLASS_7_RADIOACTIVE_MATERIALS->value,
            ],
        ];

        $response = $this->usps->internationalPrices->totalRates($params);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('rateOptions', $response);
        $this->assertIsArray($response->rateOptions);
    }

    /**
     * Test getting international letter rates
     */
    public function test_get_letter_rates(): void
    {
        $params = [
            'destinationCountryCode' => 'GB',
            'weight'                 => 0.25,
            'processingCategory'     => ProcessingCategory::LETTERS->value,
            'height'                 => 4.25,
            'length'                 => 8.25,
            'thickness'              => 0.25,
            'mailingDate'            => date('Y-m-d', strtotime('+1 day')),
        ];

        $response = $this->usps->internationalPrices->letterRates($params);

        $this->assertIsObject($response);
        $this->assertObjectHasProperty('rates', $response);
        $this->assertIsArray($response->rates);
    }
}
