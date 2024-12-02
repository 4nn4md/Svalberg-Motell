<?php

/**
 * This code was generated by
 * ___ _ _ _ _ _    _ ____    ____ ____ _    ____ ____ _  _ ____ ____ ____ ___ __   __
 *  |  | | | | |    | |  | __ |  | |__| | __ | __ |___ |\ | |___ |__/ |__|  | |  | |__/
 *  |  |_|_| | |___ | |__|    |__| |  | |    |__] |___ | \| |___ |  \ |  |  | |__| |  \
 *
 * Twilio - Trusthub
 * This is the public Twilio REST API.
 *
 * NOTE: This class is auto generated by OpenAPI Generator.
 * https://openapi-generator.tech
 * Do not edit the class manually.
 */

namespace Twilio\Rest\Trusthub\V1;

use Twilio\Exceptions\TwilioException;
use Twilio\ListResource;
use Twilio\Options;
use Twilio\Values;
use Twilio\Version;
use Twilio\Serialize;


class ComplianceTollfreeInquiriesList extends ListResource
    {
    /**
     * Construct the ComplianceTollfreeInquiriesList
     *
     * @param Version $version Version that contains the resource
     */
    public function __construct(
        Version $version
    ) {
        parent::__construct($version);

        // Path Solution
        $this->solution = [
        ];

        $this->uri = '/ComplianceInquiries/Tollfree/Initialize';
    }

    /**
     * Create the ComplianceTollfreeInquiriesInstance
     *
     * @param string $tollfreePhoneNumber The Tollfree phone number to be verified
     * @param string $notificationEmail The email address to receive the notification about the verification result.
     * @param array|Options $options Optional Arguments
     * @return ComplianceTollfreeInquiriesInstance Created ComplianceTollfreeInquiriesInstance
     * @throws TwilioException When an HTTP error occurs.
     */
    public function create(string $tollfreePhoneNumber, string $notificationEmail, array $options = []): ComplianceTollfreeInquiriesInstance
    {

        $options = new Values($options);

        $data = Values::of([
            'TollfreePhoneNumber' =>
                $tollfreePhoneNumber,
            'NotificationEmail' =>
                $notificationEmail,
            'BusinessName' =>
                $options['businessName'],
            'BusinessWebsite' =>
                $options['businessWebsite'],
            'UseCaseCategories' =>
                Serialize::map($options['useCaseCategories'], function ($e) { return $e; }),
            'UseCaseSummary' =>
                $options['useCaseSummary'],
            'ProductionMessageSample' =>
                $options['productionMessageSample'],
            'OptInImageUrls' =>
                Serialize::map($options['optInImageUrls'], function ($e) { return $e; }),
            'OptInType' =>
                $options['optInType'],
            'MessageVolume' =>
                $options['messageVolume'],
            'BusinessStreetAddress' =>
                $options['businessStreetAddress'],
            'BusinessStreetAddress2' =>
                $options['businessStreetAddress2'],
            'BusinessCity' =>
                $options['businessCity'],
            'BusinessStateProvinceRegion' =>
                $options['businessStateProvinceRegion'],
            'BusinessPostalCode' =>
                $options['businessPostalCode'],
            'BusinessCountry' =>
                $options['businessCountry'],
            'AdditionalInformation' =>
                $options['additionalInformation'],
            'BusinessContactFirstName' =>
                $options['businessContactFirstName'],
            'BusinessContactLastName' =>
                $options['businessContactLastName'],
            'BusinessContactEmail' =>
                $options['businessContactEmail'],
            'BusinessContactPhone' =>
                $options['businessContactPhone'],
            'ThemeSetId' =>
                $options['themeSetId'],
            'SkipMessagingUseCase' =>
                Serialize::booleanToString($options['skipMessagingUseCase']),
        ]);

        $headers = Values::of(['Content-Type' => 'application/x-www-form-urlencoded' ]);
        $payload = $this->version->create('POST', $this->uri, [], $data, $headers);

        return new ComplianceTollfreeInquiriesInstance(
            $this->version,
            $payload
        );
    }


    /**
     * Provide a friendly representation
     *
     * @return string Machine friendly representation
     */
    public function __toString(): string
    {
        return '[Twilio.Trusthub.V1.ComplianceTollfreeInquiriesList]';
    }
}
