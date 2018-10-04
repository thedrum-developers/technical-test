<?php

namespace App\Tests\Integration\Controller;

/**
 * Class ApiControllerReadTest
 * @package App\Tests\Integration\Controller
 */
class ApiControllerWriterTestCase extends ControllerWebTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->client->setServerParameter('CONTENT_TYPE', 'application/json');
    }
}