<?php

namespace ColeThorsen\USPS\Services;

use ColeThorsen\USPS\Enums\ExtraService;
use ColeThorsen\USPS\Enums\MailClass;
use ColeThorsen\USPS\Enums\PaymentAccountType;
use ColeThorsen\USPS\Enums\PriceType;
use ColeThorsen\USPS\Enums\ProcessingCategory;
use ColeThorsen\USPS\Enums\RateIndicator;
use ColeThorsen\USPS\Exceptions\USPSException;

class InternationalPrices extends Service
{
    protected const BASE_PATH      = '/international-prices/v3/';
    protected const API_DEFINITION = 'international-prices.yaml';

    /**
     * Get international base postage rates
     * POST /international-prices/v3/base-rates/search
     *
     * @param array{
     *     originZIPCode: string,
     *     foreignPostalCode?: string,
     *     destinationCountryCode: string,
     *     weight: float,
     *     length?: float,
     *     width?: float,
     *     height?: float,
     *     mailClass: value-of<MailClass>,
     *     processingCategory?: value-of<ProcessingCategory>,
     *     rateIndicator: value-of<RateIndicator>,
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
        $params = $this->processZipCodes($params);

        return $this->request('POST', 'base-rates/search', [
            'json' => $params,
        ]);
    }

    /**
     * Get international extra service rates
     * POST /international-prices/v3/extra-service-rates/search
     *
     * @param array{
     *     mailClass: value-of<MailClass>,
     *     priceType: value-of<PriceType>,
     *     originZIPCode?: string,
     *     destinationCountryCode?: string,
     *     weight?: float,
     *     extraServices?: array<value-of<ExtraService>>,
     *     itemValue?: float,
     *     mailingDate?: string,
     *     accountType?: value-of<PaymentAccountType>,
     *     accountNumber?: string
     * } $params
     *
     * @throws USPSException
     */
    public function extraServiceRates(array $params): object
    {
        $params = $this->processZipCodes($params);

        return $this->request('POST', 'extra-service-rates/search', [
            'json' => $params,
        ]);
    }

    /**
     * Get list of potential international rates
     * POST /international-prices/v3/base-rates-list/search
     *
     * @param array{
     *     originZIPCode: string,
     *     foreignPostalCode?: string,
     *     destinationCountryCode: string,
     *     weight: float,
     *     length?: float,
     *     width?: float,
     *     height?: float,
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
        $params = $this->processZipCodes($params);

        return $this->request('POST', 'base-rates-list/search', [
            'json' => $params,
        ]);
    }

    /**
     * Get total international rates including extra services
     * POST /international-prices/v3/total-rates/search
     *
     * @param array{
     *     originZIPCode: string,
     *     foreignPostalCode?: string,
     *     destinationCountryCode: string,
     *     weight: float,
     *     length?: float,
     *     width?: float,
     *     height?: float,
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
        $params = $this->processZipCodes($params);

        return $this->request('POST', 'total-rates/search', [
            'json' => $params,
        ]);
    }

    /**
     * Get international rates for letters/flats
     * POST /international-prices/v3/letter-rates/search
     *
     * @param array{
     *     destinationCountryCode: string,
     *     weight: float,
     *     length: float,
     *     height: float,
     *     thickness: float,
     *     processingCategory: value-of<ProcessingCategory>,
     *     rateIndicator: value-of<RateIndicator>,
     *     priceType: value-of<PriceType>,
     *     mailingDate?: string,
     *     accountType?: value-of<PaymentAccountType>,
     *     accountNumber?: string
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
