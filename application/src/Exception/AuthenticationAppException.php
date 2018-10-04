<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AuthenticationAppException
 * @package App\Exception
 */
class AuthenticationAppException extends HttpException implements AppExceptionInterface
{
    /**
     * AuthenticationAppException constructor.
     *
     * @param string|null     $message
     * @param \Exception|null $previous
     * @param int             $code
     * @param array           $headers
     */
    public function __construct(string $message = null, \Exception $previous = null, array $headers = [], int $code = 0)
    {
        parent::__construct(401, $message, $previous, $headers, $code);
    }
}