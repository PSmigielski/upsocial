<?php

namespace App\Exception;

use ErrorException;

class ApiException extends ErrorException{
    public function __construct(array $message, string $code){
        $this->message = $message;
        $this->code = $code;
    }
    public function getApiMessage(): array{
        return $this->message;
    }
}
