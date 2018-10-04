<?php

namespace App\Tests\Integration\Controller;

use App\Tests\Integration\IntegrationWebTestCase;

/**
 * Class ControllerWebTestCase
 * @package App\Tests\Integration\Controller
 */
class ControllerWebTestCase extends IntegrationWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->client->setServerParameter('HTTP_X_API_KEY', '1234567890');
        $this->resetFixtures($this->client->getKernel());
    }

    /**
     * @param string $fixturesPath
     * @param string $filename
     *
     * @return string
     */
    protected function loadRequestData(string $fixturesPath, string $filename): string
    {
        $path = $fixturesPath.'/requestBodies/'.$filename.'.json';

        return file_get_contents($path);
    }

    /**
     * @param string $fixturesPath
     * @param string $filename
     *
     * @return string
     */
    protected function loadExpectedResponseData(string $fixturesPath, string $filename): string
    {
        $path = $fixturesPath.'/expectedResponseBodies/'.$filename.'.json';
        $readableJson = file_get_contents($path);

        return json_encode(json_decode($readableJson), JSON_HEX_APOS, JSON_HEX_QUOT);
    }

    /**
     * @param string      $method
     * @param string      $uri
     * @param null|string $requestData
     */
    protected function sendRequest(string $method, string $uri, ?string $requestData = null)
    {
        $this->client->request($method, $uri, [], [], [], $requestData);
    }
}