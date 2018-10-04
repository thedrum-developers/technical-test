<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class InvalidDataException
 * @package App\Exception
 */
class InvalidDataException extends HttpException implements AppExceptionInterface
{
    /**
     * InvalidDataException constructor.
     *
     * @param string|null     $message
     * @param \Exception|null $previous
     * @param int             $code
     * @param array           $headers
     */
    public function __construct(string $message = null, \Exception $previous = null, array $headers =[], int  $code = 0)
    {
        parent::__construct(422, $message, $previous, $headers, $code);
    }
}