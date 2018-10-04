<?php

namespace App\Tests\Integration\Controller\Services;

use App\Tests\Integration\Controller\ApiControllerWriterTestCase;

/**
 * Class ServiceApiControllerWriteTest
 * @package App\Tests\Integration\Controller\Services
 */
class ServiceApiControllerWriterTestCase extends ApiControllerWriterTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
    }

    /**
     * Ensure the correct status code is sent on service creation.
     */
    public function testCorrectStatusCodeIsSentOnServiceCreation()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createSingleService');
        $this->sendRequest('POST', '/api/services', $requestData);

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure the response headers contain a Location field after service creation.
     */
    public function testResponseHeadersOnServiceCreationContainLocationField()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createSingleService');
        $this->sendRequest('POST', '/api/services', $requestData);

        $headers = $this->client->getResponse()->headers;
        $this->assertEquals(true, $headers->contains('location', self::BASE_URL.'api/services'));
    }

    /**
     * Ensure the correct response body is received on creation of a single service.
     */
    public function testCorrectResponseBodyIsReceivedOnSingleServiceCreation()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createSingleService');
        $this->sendRequest('POST', '/api/services', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'createdSingleService');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure a single service is persisted to the database on creation.
     */
    public function testCreatedSingleServiceIsSavedToDatabase()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createSingleService');
        $this->sendRequest('POST', '/api/services', $requestData);
        $this->sendRequest('GET', '/api/services/first-created-service', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'firstPersistedService');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct response body is received on creation of multiple services.
     */
    public function testCorrectResponseBodyIsReceivedOnMultipleServiceCreation()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createMultipleServices');
        $this->sendRequest('POST', '/api/services', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'createdMultipleServices');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure multiple services are persisted to the database on creation.
     */
    public function testCreatedMultipleServicesAreSavedToDatabase()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createMultipleServices');
        $this->sendRequest('POST', '/api/services', $requestData);

        $responses = [];
        $expected = [];

        $this->sendRequest('GET', '/api/services/first-created-service');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(__DIR__, 'firstPersistedService');

        $this->sendRequest('GET', '/api/services/second-created-service');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(
            __DIR__,
            'secondPersistedService'
        );

        $this->assertEquals($expected, $responses);
    }

    /**
     * Ensure the correct response body is received when updating a single service.
     */
    public function testCorrectResponseIsReceivedForUpdatingASingleService()
    {
        $requestData = $this->loadRequestData(__DIR__, 'updateSingleService');
        $this->sendRequest('PUT', '/api/services/web-development', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'firstUpdatedService');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure a single updated service is persisted to the database.
     */
    public function testCreatedUpdatedSingleServiceIsSavedToDatabase()
    {
        $requestData = $this->loadRequestData(__DIR__, 'updateSingleService');
        $this->sendRequest('PUT', '/api/services/web-development', $requestData);
        $this->sendRequest('GET', '/api/services/first-updated-service', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'firstUpdatedService');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct response body is received when updating a single service and
     * its relationships at the same time.
     */
    public function testCorrectResponseIsReceivedForUpdatingASingleServiceAndItsRelationships()
    {
        $requestData = $this->loadRequestData(
            __DIR__,
            'updateSingleServiceWithRelationships'
        );
        $this->sendRequest('PUT', '/api/services/web-development', $requestData);

        $expected = $this->loadExpectedResponseData(
            __DIR__,
            'firstUpdatedServiceWithRelationships'
        );
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure a single updated service and its updated relationships have been persisted to the database.
     */
    public function testUpdatedSingleServiceAndItsUpdatedRelationshipsAreSavedToTheDatabase()
    {
        $requestData = $this->loadRequestData(
            __DIR__,
            'updateSingleServiceWithRelationships'
        );
        $this->sendRequest('PUT', '/api/services/web-development', $requestData);
        $this->sendRequest('GET', '/api/services/first-updated-service', $requestData);

        $expected = $this->loadExpectedResponseData(
            __DIR__,
            'firstUpdatedServiceWithRelationships'
        );
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct response body is received when updating a single service.
     */
    public function testCorrectResponseIsReceivedForUpdatingMultipleServices()
    {
        $requestData = $this->loadRequestData(__DIR__, 'updateMultipleServices');
        $this->sendRequest('PUT', '/api/services', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'updatedMultipleServices');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure multiple updated services are saved to the database.
     */
    public function testUpdatedMultipleServicesAreSavedToTheDatabase()
    {
        $requestData = $this->loadRequestData(__DIR__, 'updateMultipleServices');
        $this->sendRequest('PUT', '/api/services', $requestData);

        $responses = [];
        $expected = [];

        $this->sendRequest('GET', '/api/services/first-updated-service');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(__DIR__, 'firstUpdatedService');

        $this->sendRequest('GET', '/api/services/second-updated-service');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(
            __DIR__,
            'secondUpdatedService'
        );

        $this->assertEquals($expected, $responses);
    }

    /**
     * Ensure the correct response body is received when updating multiple services and
     * their relationships at the same time.
     */
    public function testCorrectResponseIsReceivedForUpdatingMultipleServicesAndTheirRelationships()
    {
        $requestData = $this->loadRequestData(
            __DIR__,
            'updateMultipleServicesWithRelationships'
        );
        $this->sendRequest('PUT', '/api/services', $requestData);

        $expected = $this->loadExpectedResponseData(
            __DIR__,
            'updatedMultipleServicesWithRelationships'
        );
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure multiple updated services and their updated agency relationships are saved to the database.
     */
    public function testUpdatedMultipleAgenciesAndTheirUpdatedRelationshipsAreSavedToTheDatabase()
    {
        $requestData = $this->loadRequestData(__DIR__, 'updateMultipleServicesWithRelationships');
        $this->sendRequest('PUT', '/api/services', $requestData);

        $responses = [];
        $expected = [];

        $this->sendRequest('GET', '/api/services/first-updated-service');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(__DIR__, 'firstUpdatedServiceWithRelationships');

        $this->sendRequest('GET', '/api/services/second-updated-service');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(
            __DIR__,
            'secondUpdatedServiceWithRelationships'
        );

        $this->assertEquals($expected, $responses);
    }

    /**
     * Ensure the correct response body is received when updating only service relationships.
     */
    public function testCorrectResponseIsReceivedForUpdatingOnlyAgencyRelationships()
    {
        $requestData = $this->loadRequestData(
            __DIR__,
            'updateServiceOnlyRelationships'
        );
        $this->sendRequest('PUT', '/api/services/web-development', $requestData);

        $expected = $this->loadExpectedResponseData(
            __DIR__,
            'updatedServiceOnlyRelationships'
        );
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure service relationships are persisted to the database when only they are sent.
     */
    public function testUpdatedAgencyOnlyRelationshipsAreSavedToTheDatabase()
    {
        $requestData = $this->loadRequestData(
            __DIR__,
            'updateServiceOnlyRelationships'
        );
        $this->sendRequest('PUT', '/api/services/web-development', $requestData);
        $this->sendRequest('GET', '/api/services/web-development', $requestData);

        $expected = $this->loadExpectedResponseData(
            __DIR__,
            'updatedServiceOnlyRelationships'
        );
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }
}
