<?php

namespace App\Tests\Integration\Controller\Services;

use App\Tests\Integration\Controller\ControllerWebTestCase;

/**
 * Class ServiceApiControllerReadTest
 * @package App\Tests\Integration\Controller\Services
 */
class ServiceApiControllerReadTest extends ControllerWebTestCase
{
    /**
     * Ensure the correct JSON response is received for a Service index.
     */
    public function testCorrectResponseIsReceivedForServiceIndex()
    {
        $this->sendRequest('GET', '/api/services');

        $expected = $this->loadExpectedResponseData(__DIR__, 'index');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct JSON response is received for a single Service item.
     */
    public function testCorrectResponseIsReceivedForASingleService()
    {
        $this->sendRequest('GET', '/api/services/web-development');

        $expected = $this->loadExpectedResponseData(__DIR__, 'singleService');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct JSON response is received for all a single Service item's "relationships" objects.
     */
    public function testCorrectResponseIsReceivedForServiceRelationships()
    {
        $this->sendRequest('GET', '/api/services/web-development/relationships');

        $expected = $this->loadExpectedResponseData(__DIR__, 'serviceRelationships');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct JSON response is received for an Service's Agency "relationships" object.
     */
    public function testCorrectResponseIsReceivedForServiceAgencyRelationships()
    {
        $this->sendRequest('GET', '/api/services/web-development/relationships/agencies');

        $expected = $this->loadExpectedResponseData(__DIR__, 'serviceAgencyRelationships');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }


    /**
     * Ensure the correct JSON response is received for a Service's related Agencies index.
     */
    public function testCorrectResponseIsReceivedWhenRetrievingAgenciesIndexForAnIndividualService()
    {
        $this->sendRequest('GET', '/api/services/web-development/agencies');

        $expected = $this->loadExpectedResponseData(__DIR__, 'firstServiceAgencies');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the controller redirects the request when details of a specific agency are
     * requested through the services URL.
     */
    public function testRedirectToAgenciesControllerWhenAgencyDetailsRequestedThroughServicesUrl()
    {
        $this->sendRequest('GET', '/api/services/web-development/agencies/1');
        $this->client->followRedirect();

        $expected = $this->loadExpectedResponseData(__DIR__.'/../Agencies', 'singleAgency');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }
}
