<?php

namespace App\Domain\Template\Exceptions;

use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ArchiveNotReadableException extends FileException
{
    public function __construct($path, $message = null, $code = 0, \Throwable $previous = null)
    {
        $message = $message ?: "Cannot open archive at path {$path}";

        parent::__construct($message, $code, $previous);
    }
}
