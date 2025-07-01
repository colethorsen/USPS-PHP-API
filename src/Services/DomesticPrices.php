<?php

namespace ColeThorsen\USPS\Services;

use ColeThorsen\USPS\Enums\ExtraService;
use ColeThorsen\USPS\Enums\FacilityType;
use ColeThorsen\USPS\Enums\MailClass;
use ColeThorsen\USPS\Enums\PaymentAccountType;
use ColeThorsen\USPS\Enums\PriceType;
use ColeThorsen\USPS\Enums\ProcessingCategory;
use ColeThorsen\USPS\Enums\RateIndicator;
use ColeThorsen\USPS\Exceptions\USPSException;

class DomesticPrices extends Service
{
    protected const BASE_PATH      = '/prices/v3/';
    protected const API_DEFINITION = 'domestic-prices.yaml';

    /**
     * Get base price for a package
     * POST /prices/v3/base-rates/search
     *
     * @param array{
     *     originZIPCode: string,
     *     destinationZIPCode: string,
     *     weight: float,
     *     length: float,
     *     width: float,
     *     height: float,
     *     mailClass: value-of<MailClass>,
     *     processingCategory: value-of<ProcessingCategory>,
     *     rateIndicator: value-of<RateIndicator>,
     *     destinationEntryFacilityType: value-of<FacilityType>,
     *     priceType: value-of<PriceType>,
     *     mailingDate?: string,
     *     accountType?: value-of<PaymentAccountType>,
     *     accountNumber?: string
     * } $params
     *
     * @throws USPSException
     */
    public function baseRates(array $params): object
    {
        return $this->request('POST', 'base-rates/search', [
            'json' => $params,
        ]);
    }

    /**
     * Get prices for extra services
     * POST /prices/v3/extra-service-rates/search
     *
     * @param array{
     *     mailClass: value-of<MailClass>,
     *     priceType: value-of<PriceType>,
     *     extraServices?: array<value-of<ExtraService>>,
     *     itemValue?: float,
     *     weight?: float,
     *     originZIPCode?: string,
     *     destinationZIPCode?: string,
     *     mailingDate?: string,
     *     accountType?: value-of<PaymentAccountType>,
     *     accountNumber?: string
     * } $params
     *
     * @throws USPSException
     */
    public function extraServiceRates(array $params): array
    {
        return $this->request('POST', 'extra-service-rates/search', [
            'json' => $params,
        ]);
    }

    /**
     * Get list of eligible products for package dimensions
     * POST /prices/v3/base-rates-list/search
     *
     * @param array{
     *     originZIPCode: string,
     *     destinationZIPCode: string,
     *     weight: float,
     *     length: float,
     *     width: float,
     *     height: float,
     *     mailClass?: value-of<MailClass>,
     *     mailClasses?: array,
     *     priceType?: value-of<PriceType>,
     *     mailingDate?: string,
     *     accountType?: value-of<PaymentAccountType>,
     *     accountNumber?: string
     * } $params
     *
     * @throws USPSException
     */
    public function baseRatesList(array $params): object
    {
        return $this->request('POST', 'base-rates-list/search', [
            'json' => $params,
        ]);
    }

    /**
     * Get total price including base rate and extra services
     * POST /prices/v3/total-rates/search
     *
     * @param array{
     *     originZIPCode: string,
     *     destinationZIPCode: string,
     *     weight: float,
     *     length: float,
     *     width: float,
     *     height: float,
     *     mailClass?: value-of<MailClass>,
     *     mailClasses?: array,
     *     priceType?: value-of<PriceType>,
     *     mailingDate?: string,
     *     accountType?: value-of<PaymentAccountType>,
     *     accountNumber?: string,
     *     itemValue?: float,
     *     extraServices?: array
     * } $params
     *
     * @throws USPSException
     */
    public function totalRates(array $params): object
    {
        return $this->request('POST', 'total-rates/search', [
            'json' => $params,
        ]);
    }

    /**
     * Get prices for letters/flats/cards
     * POST /prices/v3/letter-rates/search
     *
     * @param array{
     *     weight: float,
     *     length: float,
     *     height: float,
     *     thickness: float,
     *     processingCategory: value-of<ProcessingCategory>,
     *     mailingDate?: string,
     *     nonMachinableIndicators?: object,
     *     extraServices?: array<value-of<ExtraService>>,
     *     itemValue?: float
     * } $params
     *
     * @throws USPSException
     */
    public function letterRates(array $params): object
    {
        return $this->request('POST', 'letter-rates/search', [
            'json' => $params,
        ]);
    }
}
