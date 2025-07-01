<?php

namespace ColeThorsen\USPS\Services;

use ColeThorsen\USPS\Enums\FacilityType;
use ColeThorsen\USPS\Enums\MailClass;
use ColeThorsen\USPS\Enums\ProcessingCategory;
use ColeThorsen\USPS\Exceptions\USPSException;

class Locations extends Service
{
    protected const BASE_PATH      = '/locations/v3/';
    protected const API_DEFINITION = 'locations.yaml';

    /**
     * Find dropoff locations for destination entry parcels
     * GET /locations/v3/dropoff-locations
     *
     * @param array{
     *     destinationZIPCode: string,
     *     destinationZIPPlus4?: string,
     *     mailClass?: value-of<MailClass>,
     *     processingCategory?: value-of<ProcessingCategory>,
     *     destinationEntryFacilityType?: value-of<FacilityType>,
     *     palletized?: bool,
     *     mailingDate?: string
     * } $params
     *
     * @throws USPSException
     */
    public function dropoffLocations(array $params): array
    {
        $params = $this->processZipCodes($params);

        return $this->request('GET', 'dropoff-locations', [
            'query' => $params,
        ]);
    }

    /**
     * Find post office locations
     * GET /locations/v3/post-office-locations
     *
     * @param array{
     *     streetAddress?: string,
     *     secondaryAddress?: string,
     *     city?: string,
     *     state?: string,
     *     ZIPCode?: string,
     *     ZIPPlus4?: string,
     *     LAT?: string,
     *     LONG?: string,
     *     radius?: int,
     *     postOfficeType?: array,
     *     PO-LOBBY_AFTER_HOURS?: bool,
     *     mailServiceHours?: array,
     *     carrierPickupAvailable?: bool,
     *     accountableMailAvailable?: bool,
     *     businessMailAcceptanceAvailable?: bool,
     *     businessMailAcceptanceHours?: array,
     *     bulkMailAcceptanceAvailable?: bool,
     *     bulkMailAcceptanceHours?: array,
     *     POBoxAvailable?: bool,
     *     packageDepositAvailable?: bool,
     *     packageDepositHours?: array,
     *     passportAppointmentsAvailable?: bool,
     *     passportPhotoServiceAvailable?: bool,
     *     notaryServiceAvailable?: bool,
     *     carrierRouteAvailable?: bool,
     *     generalDeliveryAvailable?: bool,
     *     moneyOrdersAvailable?: bool,
     *     stampedEnvelopeAvailable?: bool,
     *     stampedCardAvailable?: bool,
     *     parcelLockerAvailable?: bool,
     *     parcelLockerPackageMaxLength?: int,
     *     parcelLockerPackageMaxWidth?: int,
     *     parcelLockerPackageMaxHeight?: int,
     *     offset?: int,
     *     limit?: int
     * } $params
     *
     * @throws USPSException
     */
    public function postOfficeLocations(array $params): object
    {
        $params = $this->processZipCodes($params);

        return $this->request('GET', 'post-office-locations', [
            'query' => $params,
        ]);
    }

    /**
     * Find USPS Parcel Locker locations
     * GET /locations/v3/parcel-locker-locations
     *
     * @param array{
     *     city?: string,
     *     state?: string,
     *     ZIPCode?: string,
     *     offset: int,
     *     limit?: int
     * } $params
     *
     * @throws USPSException
     */
    public function parcelLockerLocations(array $params): array
    {
        $params = $this->processZipCodes($params);

        return $this->request('GET', 'parcel-locker-locations', [
            'query' => $params,
        ]);
    }
}
