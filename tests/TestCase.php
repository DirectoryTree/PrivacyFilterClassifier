<?php

namespace DirectoryTree\PrivacyFilterClassifier\Tests;

use PHPUnit\Framework\TestCase as PHPUnit;

/**
 * Base test case for package tests.
 */
abstract class TestCase extends PHPUnit
{
    /**
     * The fake privacy-filter binary path.
     */
    protected string $binaryPath;

    /**
     * The fake privacy-filter model path.
     */
    protected string $modelPath;

    /**
     * Prepare the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->binaryPath = __DIR__.'/Fixtures/privacy-filter';
        $this->modelPath = $this->makeModel();
    }

    /**
     * Reset fake privacy-filter environment variables.
     */
    protected function tearDown(): void
    {
        foreach ([
            'PRIVACY_FILTER_FAKE_MODE',
            'PRIVACY_FILTER_FAKE_NEEDLE',
            'PRIVACY_FILTER_FAKE_TYPE',
        ] as $key) {
            putenv($key);

            unset($_ENV[$key], $_SERVER[$key]);
        }

        if (is_file($this->modelPath)) {
            unlink($this->modelPath);
        }

        parent::tearDown();
    }

    /**
     * Set environment variables for the fake privacy-filter binary.
     *
     * @param  array<string, string>  $environment
     */
    protected function setFakePrivacyFilterEnvironment(array $environment): void
    {
        foreach ($environment as $key => $value) {
            putenv("{$key}={$value}");

            $_ENV[$key] = $value;
            $_SERVER[$key] = $value;
        }
    }

    /**
     * Create a temporary model fixture.
     */
    protected function makeModel(): string
    {
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'privacy-filter-classifier-model-'.bin2hex(random_bytes(8)).'.gguf';

        file_put_contents($path, 'privacy-filter test model');

        return $path;
    }
}
