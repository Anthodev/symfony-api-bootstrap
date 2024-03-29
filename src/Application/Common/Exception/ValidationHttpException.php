<?php

declare(strict_types=1);

namespace App\Application\Common\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ValidationHttpException extends HttpException
{
    /**
     * @param array<string, mixed> $headers
     */
    public function __construct(string $message = '', ?\Throwable $previous = null, array $headers = [], int $code = 0)
    {
        parent::__construct(Response::HTTP_BAD_REQUEST, $message, $previous, $headers, $code);
    }
}
