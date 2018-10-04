<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class RequestedDataNotFoundException
 * @package App\Exception
 */
class RequestedDataNotFoundException extends HttpException implements AppExceptionInterface
{
    /**
     * RequestedDataNotFoundException constructor.
     *
     * @param string|null     $message
     * @param \Exception|null $previous
     * @param int             $code
     * @param array           $headers
     */
    public function __construct(string $message = null, \Exception $previous = null, array $headers = [], int $code = 0)
    {
        parent::__construct(404, $message, $previous, $headers, $code);
    }
}