<?php

namespace App\Tests\Integration\Controller;

/**
 * Class ValidationTest
 * @package App\Tests\Integration\Controller
 */
class ValidationTest extends ApiControllerWriterTestCase
{
    /**
     * Ensure a 400 status code is received if the request data contains invalid JSON.
     */
    public function test400StatusCodeReceivedWhenRequestDataContainsInvalidJson()
    {
        $this->sendRequest('POST', '/api/services', '{}{');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 400 status code is received if the request method is not GET and
     * "content-type" is not set to "application/json" in the headers.
     */
    public function test400StatusCodeReceivedWhenRequestMethodIsNotGetAndContentTypeIsNotJson()
    {
        $this->client->setServerParameter('CONTENT_TYPE', 'text/html');
        $this->sendRequest('POST', '/api/services', '{"Foo": "Bar"}{');
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 400 status code is received if the request body's "data" key does
     * not contain an array when using the POST method.
     */
    public function test400StatusCodeReceivedWhenRequestDataDoesNotContainANestedArrayUnderTheDataKeyWithPOSTMethod()
    {
        $invalidData = [
            'data' => [
                'shouldBeInArray' => 'invalid',
            ],
        ];
        $this->sendRequest('POST', '/api/services', json_encode($invalidData));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 400 status code is received if the request body's "data" key does
     * not contain an array, but also contains multiple items and might therefore
     * pass the check done to ensure updates for multiple items can not happen at
     * endpoints using a slug.
     */
    public function test400StatusCodeReceivedWhenRequestDataDoesNotContainANestedArrayUnderTheDataKeyWithPUTMethod()
    {
        $invalidData = [
            'data' => [
                'shouldBe' => 'inArray',
                'butStill' => 'needsExplicitly',
                'checkedWhenBeing' => 'PUT'
            ],
        ];
        $this->sendRequest('PUT', '/api/services/web-development', json_encode($invalidData));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 400 status code is received if the request body does not contain
     * a "type" member.
     */
    public function test400StatusCodeReceivedWhenRequestDataDoesNotContainATypeMember()
    {
        $invalidData = [
            'data' => [
                [
                    'invalid' => 'failed',
                ],
            ],
        ];
        $this->sendRequest('POST', '/api/services', json_encode($invalidData));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 400 status code is received when the "type" member specified in the
     * request data does not match the endpoint.
     */
    public function test400StatusCodeReceivedWhenRequestDataTypeMemberIsInvalidForEndpoint()
    {
        $invalidData = [
            'data' => [
                [
                    'type' => 'invalid',
                ],
            ],
        ];
        $this->sendRequest('POST', '/api/services', json_encode($invalidData));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 400 status code is received when the request data does not contain
     * an "attributes" object.
     */
    public function test400StatusCodeReceivedWhenRequestDataDoesNotContainAttributesObject()
    {
        $invalidData = [
            'data' => [
                [
                    'type' => 'services',
                ],
            ],
        ];
        $this->sendRequest('POST', '/api/services', json_encode($invalidData));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 400 status code is received when the request method is 'POST'
     * and an "id" member is set in the request data.
     */
    public function test400StatusCodeReceivedWhenRequestMethodIsPOSTAndIdMemberIsSet()
    {
        $invalidData = [
            'data' => [
                [
                    'id' => '1',
                    'type' => 'services',
                ],
            ],
        ];
        $this->sendRequest('POST', '/api/services', json_encode($invalidData));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }


    /**
     * Ensure a 400 status code is received if the request body contains update data for
     * multiple entities and the endpoints uses a slug.
     */
    public function testEnsureUpdatingWithSlugOnlyUpdatesOneEntity()
    {
        $requestData = [
            'data' => [
                [
                    'id' => "1",
                    'type' => 'services',
                    'attributes' => [
                        'name' => 'Foo Development',
                        'slug' => 'foo-development'
                    ],
                ],
                [
                    'id' => "2",
                    'type' => 'services',
                    'attributes' => [
                        'name' => 'Bar Development',
                        'slug' => 'bar-development'
                    ],
                ],
            ],
        ];
        $this->sendRequest('PUT', '/api/services/web-development', json_encode($requestData));
        $this->assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 404 status code is received when an entity cannot be found
     * by criteria, eg. a URL slug.
     */
    public function test404StatusCodeReceivedWhenEntityIsNotFoundByCriteria()
    {
        $this->sendRequest('GET', '/api/services/invalid-slug');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 404 status code is received when an entity cannot be found by id.
     */
    public function test404StatusCodeReceivedWhenEntityIsNotFoundById()
    {
        $requestData = [
            'data' => [
                [
                    'type' => 'agencies',
                    'id' => '255',
                    'attributes' => 'foo'
                ],
            ],
        ];
        $this->sendRequest('PUT', '/api/agencies/255', json_encode($requestData));
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 422 status code is received if the request data fails Doctrine's
     * Entity validation.
     */
    public function test422StatusCodeReceivedWhenEntityFailsDoctrineValidation()
    {
        $requestData = [
            'data' => [
                [
                    'type' => 'services',
                    'attributes' => [
                        'name' => 'Web Development',
                        'slug' => 'web-development'
                    ]
                ],
            ],
        ];
        $this->sendRequest('POST', '/api/services', json_encode($requestData));
        $this->assertEquals(422, $this->client->getResponse()->getStatusCode());
    }
}