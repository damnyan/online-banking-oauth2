<?php

namespace Dmn\OnlineBankingOAuth2\Bpi\Exceptions;

use Exception;
use Throwable;

class ServerException extends Exception
{
    public $code;

    /**
     * Construct
     *
     * @param string $message
     * @param string $code
     * @param Throwable $previous
     */
    public function __construct(
        string $message,
        string $code,
        Throwable $previous = null
    ) {
        $this->code = $code;
        parent::__construct($message, 0, $previous);
    }

    /**
     * Get Code
     *
     * @return string
     */
    public function code(): string
    {
        return $this->code;
    }
}
