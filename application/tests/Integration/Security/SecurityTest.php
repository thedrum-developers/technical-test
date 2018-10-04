<?php

namespace App\Tests\Integration\Security;

use App\Tests\Integration\IntegrationWebTestCase;

/**
 * Class SecurityTest
 * @package App\Tests\Integration\Security
 */
class SecurityTest extends IntegrationWebTestCase
{
    /**
     * Ensure a 401 status code is received when no API key is in the headers.
     */
    public function test401StatusCodeSentWhenNoApiKeyInHeaders()
    {
        $this->client->request('GET', '/api/services');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }

    /**
     * Ensure a 401 status code is received when an unknown API key is sent.
     */
    public function test401StatusCodeWhenUnknownApiKeyIsSent()
    {
        $this->client->setServerParameter('HTTP_X_API_KEY', 'foo');
        $this->client->request('GET', '/api/services');
        $this->assertEquals(401, $this->client->getResponse()->getStatusCode());
    }
}