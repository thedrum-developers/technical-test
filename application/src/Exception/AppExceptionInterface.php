<?php

namespace App\Exception;

/**
 * Interface AppExceptionInterface
 * @package App\Exception
 */
interface AppExceptionInterface
{
    /**
     * @return mixed
     */
    public function getStatusCode();
}