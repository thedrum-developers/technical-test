<?php

namespace App\Tests\Integration\Controller\Agencies;

use App\Tests\Integration\Controller\ApiControllerWriterTestCase;

/**
 * Class AgencyApiControllerWriteTest
 * @package App\Tests\Integration\Controller\Agencies
 */
class AgencyApiControllerWriterTestCase extends ApiControllerWriterTestCase
{
    /**
     * Ensure the correct status code is sent on agency creation.
     */
    public function testCorrectStatusCodeIsSentOnAgencyCreation()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createSingleAgency');
        $this->sendRequest('POST', '/api/agencies', $requestData);

        $this->assertEquals(201, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure the response headers contain a Location field after agency creation.
     */
    public function testResponseHeadersOnAgencyCreationContainLocationField()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createSingleAgency');
        $this->sendRequest('POST', '/api/agencies', $requestData);

        $headers = $this->client->getResponse()->headers;
        $this->assertEquals(true, $headers->contains('location', self::BASE_URL.'api/agencies'));
    }

    /**
     * Ensure the correct response body is received on creation of a single agency.
     */
    public function testCorrectResponseBodyIsReceivedOnSingleAgencyCreation()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createSingleAgency');
        $this->sendRequest('POST', '/api/agencies', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'createdSingleAgency');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure a single agency is persisted to the database on creation.
     */
    public function testCreatedSingleAgencyIsSavedToDatabase()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createSingleAgency');
        $this->sendRequest('POST', '/api/agencies', $requestData);
        $this->sendRequest('GET', '/api/agencies/4', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'firstPersistedAgency');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct response body is received on creation of multiple agencies.
     */
    public function testCorrectResponseBodyIsReceivedOnMultipleAgencyCreation()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createMultipleAgencies');
        $this->sendRequest('POST', '/api/agencies', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'createdMultipleAgencies');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure multiple agencies are persisted to the database on creation.
     */
    public function testCreatedMultipleAgenciesAreSavedToDatabase()
    {
        $requestData = $this->loadRequestData(__DIR__, 'createMultipleAgencies');
        $this->sendRequest('POST', '/api/agencies', $requestData);

        $responses = [];
        $expected = [];

        $this->sendRequest('GET', '/api/agencies/4');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(__DIR__, 'firstPersistedAgency');

        $this->sendRequest('GET', '/api/agencies/5');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(
            __DIR__,
            'secondPersistedAgency'
        );

        $this->assertEquals($expected, $responses);
    }

    /**
     * Ensure the correct response body is received when updating a single agency.
     */
    public function testCorrectResponseIsReceivedForUpdatingASingleAgency()
    {
        $requestData = $this->loadRequestData(__DIR__, 'updateSingleAgency');
        $this->sendRequest('PUT', '/api/agencies/1', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'firstUpdatedAgency');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure a single updated agency is persisted to the database.
     */
    public function testUpdatedSingleAgencyIsSavedToDatabase()
    {
        $requestData = $this->loadRequestData(__DIR__, 'updateSingleAgency');
        $this->sendRequest('PUT', '/api/agencies/1', $requestData);
        $this->sendRequest('GET', '/api/agencies/1', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'firstUpdatedAgency');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct response body is received when updating a single agency and
     * its relationships at the same time.
     */
    public function testCorrectResponseIsReceivedForUpdatingASingleAgencyAndItsRelationships()
    {
        $requestData = $this->loadRequestData(
            __DIR__,
            'updateSingleAgencyWithRelationships'
        );
        $this->sendRequest('PUT', '/api/agencies/1', $requestData);

        $expected = $this->loadExpectedResponseData(
            __DIR__,
            'firstUpdatedAgencyWithRelationships'
        );
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure a single updated agency and its updated relationships have been persisted to the database.
     */
    public function testUpdatedSingleAgencyAndItsUpdatedRelationshipsAreSavedToTheDatabase()
    {
        $requestData = $this->loadRequestData(
            __DIR__,
            'updateSingleAgencyWithRelationships'
        );
        $this->sendRequest('PUT', '/api/agencies/1', $requestData);
        $this->sendRequest('GET', '/api/agencies/1', $requestData);

        $expected = $this->loadExpectedResponseData(
            __DIR__,
            'firstUpdatedAgencyWithRelationships'
        );
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure the correct response body is received when updating multiple agencies.
     */
    public function testCorrectResponseIsReceivedForUpdatingMultipleAgencies()
    {
        $requestData = $this->loadRequestData(__DIR__, 'updateMultipleAgencies');
        $this->sendRequest('PUT', '/api/agencies', $requestData);

        $expected = $this->loadExpectedResponseData(__DIR__, 'updatedMultipleAgencies');
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure multiple updated agencies are saved to the database.
     */
    public function testUpdatedMultipleAgenciesAreSavedToTheDatabase()
    {
        $requestData = $this->loadRequestData(__DIR__, 'updateMultipleAgencies');
        $this->sendRequest('PUT', '/api/agencies', $requestData);

        $responses = [];
        $expected = [];

        $this->sendRequest('GET', '/api/agencies/1');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(__DIR__, 'firstUpdatedAgency');

        $this->sendRequest('GET', '/api/agencies/2');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(
            __DIR__,
            'secondUpdatedAgency'
        );

        $this->assertEquals($expected, $responses);
    }

    /**
     * Ensure the correct response body is received when updating multiple agencies and
     * their relationships at the same time.
     */
    public function testCorrectResponseIsReceivedForUpdatingMultipleAgenciesAndTheirRelationships()
    {
        $requestData = $this->loadRequestData(
            __DIR__,
            'updateMultipleAgenciesWithRelationships'
        );
        $this->sendRequest('PUT', '/api/agencies', $requestData);

        $expected = $this->loadExpectedResponseData(
            __DIR__,
            'updatedMultipleAgenciesWithRelationships'
        );
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure multiple updated agencies and their updated services relationships are saved to the database.
     */
    public function testUpdatedMultipleAgenciesAndTheirUpdatedRelationshipsAreSavedToTheDatabase()
    {
        $requestData = $this->loadRequestData(__DIR__, 'updateMultipleAgenciesWithRelationships');
        $this->sendRequest('PUT', '/api/agencies', $requestData);

        $responses = [];
        $expected = [];

        $this->sendRequest('GET', '/api/agencies/1');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(__DIR__, 'firstUpdatedAgencyWithRelationships');

        $this->sendRequest('GET', '/api/agencies/2');
        $responses[] = $this->client->getResponse()->getContent();
        $expected[] = $this->loadExpectedResponseData(
            __DIR__,
            'secondUpdatedAgencyWithRelationships'
        );

        $this->assertEquals($expected, $responses);
    }

    /**
     * Ensure the correct response body is received when updating only agency relationships.
     */
    public function testCorrectResponseIsReceivedForUpdatingOnlyAgencyRelationships()
    {
        $requestData = $this->loadRequestData(
            __DIR__,
            'updateAgencyOnlyRelationships'
        );
        $this->sendRequest('PUT', '/api/agencies/1', $requestData);

        $expected = $this->loadExpectedResponseData(
            __DIR__,
            'updatedAgencyOnlyRelationships'
        );
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }

    /**
     * Ensure agency relationships are persisted to the database when only they are sent.
     */
    public function testUpdatedAgencyOnlyRelationshipsAreSavedToTheDatabase()
    {
        $requestData = $this->loadRequestData(
            __DIR__,
            'updateAgencyOnlyRelationships'
        );
        $this->sendRequest('PUT', '/api/agencies/1', $requestData);
        $this->sendRequest('GET', '/api/agencies/1', $requestData);

        $expected = $this->loadExpectedResponseData(
            __DIR__,
            'updatedAgencyOnlyRelationships'
        );
        $this->assertEquals($expected, $this->client->getResponse()->getContent());
    }
}