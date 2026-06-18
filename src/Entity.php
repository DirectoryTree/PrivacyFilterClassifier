<?php

namespace DirectoryTree\PrivacyFilterClassifier;

use DirectoryTree\PrivacyFilterClassifier\Exceptions\UnexpectedOutputException;
use JsonSerializable;

/**
 * A classified entity detected by privacy-filter.
 */
readonly class Entity implements JsonSerializable
{
    /**
     * Create a new entity instance.
     */
    public function __construct(
        public string $type,
        public int $start,
        public int $end,
        public float $score,
        public string $text,
    ) {}

    /**
     * Create an entity from a decoded entity payload.
     *
     * @param  array<string, mixed>  $entity
     */
    public static function from(array $entity, string $sourceText): self
    {
        $start = (int) ($entity['start'] ?? -1);
        $end = (int) ($entity['end'] ?? -1);
        $score = (float) ($entity['score'] ?? 0);
        $type = (string) ($entity['entity_group'] ?? $entity['type'] ?? $entity['label'] ?? '');

        if (empty($type) || $start < 0 || $end < $start || $end > strlen($sourceText)) {
            throw UnexpectedOutputException::fromOutput(json_encode($entity, JSON_THROW_ON_ERROR));
        }

        return new self(
            type: $type,
            start: $start,
            end: $end,
            score: $score,
            text: substr($sourceText, $start, $end - $start),
        );
    }

    /**
     * Get the entity length in bytes.
     */
    public function length(): int
    {
        return $this->end - $this->start;
    }

    /**
     * Get the known entity type.
     */
    public function type(): ?EntityType
    {
        return EntityType::tryFrom($this->type);
    }

    /**
     * Convert the entity to an array.
     *
     * @return array{type: string, start: int, end: int, score: float, text: string}
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'start' => $this->start,
            'end' => $this->end,
            'score' => $this->score,
            'text' => $this->text,
        ];
    }

    /**
     * Convert the entity to a JSON-serializable value.
     *
     * @return array{type: string, start: int, end: int, score: float, text: string}
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
