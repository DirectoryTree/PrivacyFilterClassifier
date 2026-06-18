<?php

namespace DirectoryTree\PrivacyFilterClassifier;

interface ClassifierInterface
{
    /**
     * Classify the given text and return the classification results.
     */
    public function entities(string $text, ?float $threshold = null): array;
}
