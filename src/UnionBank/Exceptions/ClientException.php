<?php

namespace Dmn\OnlineBankingOAuth2\UnionBank\Exceptions;

use Exception;
use Throwable;

class ClientException extends Exception
{
    public $errors;

    /**
     * Construct
     *
     * @param string $message
     * @param array $errors
     * @param Throwable $previous
     */
    public function __construct(
        string $message,
        array $errors,
        Throwable $previous = null
    ) {
        $this->errors = $errors;
        parent::__construct($message, 0, $previous);
    }

    /**
     * Get Errors
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }
}
