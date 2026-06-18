<?php

namespace DirectoryTree\PrivacyFilterClassifier\Exceptions;

use RuntimeException;

/**
 * Exception thrown when the configured privacy-filter binary cannot be found.
 */
class BinaryNotFoundException extends RuntimeException
{
    /**
     * Create a new exception for the missing binary path.
     */
    public static function at(string $path): self
    {
        return new self("The privacy-filter binary does not exist at [{$path}].");
    }
}
