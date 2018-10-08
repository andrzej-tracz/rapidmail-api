<?php

namespace App\Domain\Template\Exceptions;

class InvalidArchiveException extends \Exception
{
    protected $message = 'The archive ZIP files is invalid';
}
