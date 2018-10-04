<?php

namespace App\Tests\Integration\Controller\Agencies;

use App\Tests\Integration\Controller\ControllerWebTestCase;

/**
 * Class AgencyApiControllerReadTest
 * @package App\Tests\Integration\Controller\Agencies
 */
class AgencyApiControllerReadTest extends ControllerWebTestCase
{
    /**
     * Ensure the correct JSON response is received for an Agency index.
     */
    public function testCorrectResponseIsReceivedForAgencyIndex()
    {
        $this->sendRequest('GET', '/api/agencies');

        $expected = $this->loadExpectedResponseData(__DIR__, 'index');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct JSON response is received for a single Agency item.
     */
    public function testCorrectResponseIsReceivedForASingleAgency()
    {
        $this->sendRequest('GET', '/api/agencies/1');

        $expected = $this->loadExpectedResponseData(__DIR__, 'singleAgency');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct JSON response is received for all a single Agency item's "relationships" objects.
     */
    public function testCorrectResponseIsReceivedForAgencyRelationships()
    {
        $this->sendRequest('GET', '/api/agencies/1/relationships');

        $expected = $this->loadExpectedResponseData(__DIR__, 'agencyRelationships');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct JSON response is received for an Agency's Service "relationships" object.
     */
    public function testCorrectResponseIsReceivedForAgencyServiceRelationships()
    {
        $this->sendRequest('GET', '/api/agencies/1/relationships/services');

        $expected = $this->loadExpectedResponseData(__DIR__, 'agencyServiceRelationships');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct JSON response is received for an Agency's related Service index.
     */
    public function testCorrectResponseIsReceivedWhenRetrievingServicesIndexForAnIndividualAgency()
    {
        $this->sendRequest('GET', '/api/agencies/1/services');

        $expected = $this->loadExpectedResponseData(__DIR__, 'firstAgencyServices');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the controller redirects the request when details of a specific service are
     * requested through the agencies URL.
     */
    public function testRedirectToServicesControllerWhenServicesDetailsRequestedThroughAgenciesUrl()
    {
        $this->sendRequest('GET', '/api/agencies/1/services/web-development');
        $this->client->followRedirect();

        $expected = $this->loadExpectedResponseData(__DIR__.'/../Services', 'singleService');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }
}