<?php

namespace DirectoryTree\PrivacyFilterClassifier;

use DirectoryTree\PrivacyFilterClassifier\Exceptions\BinaryNotFoundException;
use DirectoryTree\PrivacyFilterClassifier\Exceptions\ModelNotFoundException;
use DirectoryTree\PrivacyFilterClassifier\Exceptions\PrivacyFilterFailedException;
use Symfony\Component\Process\Process;

/**
 * Runtime API for classifying text with the local privacy-filter binary.
 */
class Classifier
{
    /**
     * The default classification threshold.
     */
    protected const DEFAULT_THRESHOLD = 0.5;

    /**
     * Create a new privacy-filter API instance.
     */
    public function __construct(
        protected string $binaryPath,
        protected string $modelPath,
        protected float $timeout,
        protected ?EntityFactory $entityFactory = null,
    ) {}

    /**
     * Get the entities detected in the given text.
     *
     * @return array<int, Entity>
     */
    public function entities(string $text, ?float $threshold = null): array
    {
        $this->assertBinaryExists();
        $this->assertModelExists();

        $process = new Process(
            command: $this->command($threshold),
            input: $text,
            timeout: $this->timeout
        );

        $process->run();

        if (! $process->isSuccessful()) {
            throw PrivacyFilterFailedException::because(
                error: $process->getErrorOutput(),
                exitCode: $process->getExitCode() ?? 1,
            );
        }

        return $this->newEntityFactory()->fromOutput($process->getOutput(), $text);
    }

    /**
     * Get the command to execute the privacy-filter binary.
     *
     * @return array<int, string>
     */
    protected function command(?float $threshold = null): array
    {
        return [
            $this->binaryPath,
            '--classify',
            $this->modelPath,
            (string) ($threshold ?? self::DEFAULT_THRESHOLD),
        ];
    }

    /**
     * Ensure the configured binary exists.
     */
    protected function assertBinaryExists(): void
    {
        if (! is_file($this->binaryPath)) {
            throw BinaryNotFoundException::at($this->binaryPath);
        }
    }

    /**
     * Ensure the configured GGUF model exists.
     */
    protected function assertModelExists(): void
    {
        if (! is_file($this->modelPath)) {
            throw ModelNotFoundException::at($this->modelPath);
        }
    }

    /**
     * Create a new entity factory instance.
     */
    protected function newEntityFactory(): EntityFactory
    {
        return $this->entityFactory ??= new EntityFactory;
    }
}
