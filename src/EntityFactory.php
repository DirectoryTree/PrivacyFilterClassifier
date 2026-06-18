<?php

namespace DirectoryTree\PrivacyFilterClassifier;

use DirectoryTree\PrivacyFilterClassifier\Exceptions\UnexpectedOutputException;

/**
 * Creates entity instances from privacy-filter output.
 */
class EntityFactory
{
    /**
     * Create entities from privacy-filter CLI output.
     *
     * @return array<int, Entity>
     */
    public function fromOutput(string $output, string $sourceText): array
    {
        $decoded = json_decode($output, true);

        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $this->fromDecodedOutput($decoded, $sourceText);
        }

        return $this->fromOutputPrefixes($output, $sourceText);
    }

    /**
     * Create entities from decoded JSON output.
     *
     * @param  array<int, array<string, mixed>>  $entities
     * @return array<int, Entity>
     */
    public function fromDecodedOutput(array $entities, string $sourceText): array
    {
        return array_map(fn (array $entity) => (
            Entity::from($entity, $sourceText)
        ), $entities);
    }

    /**
     * Create entities from invalid JSON output prefixes.
     *
     * @return array<int, Entity>
     */
    public function fromOutputPrefixes(string $output, string $sourceText): array
    {
        // The upstream CLI prints entity text directly into a JSON-like response
        // without escaping it first. Read only the fields before the text value,
        // then rehydrate entity text from the original byte offsets.
        preg_match_all(
            '/\{\s*"entity_group"\s*:\s*"(?P<type>[^"]+)"\s*,\s*"start"\s*:\s*(?P<start>-?\d+)\s*,\s*"end"\s*:\s*(?P<end>-?\d+)\s*,\s*"score"\s*:\s*(?P<score>-?(?:\d+\.?\d*|\.\d+)(?:[eE][+-]?\d+)?)\s*,\s*"text"\s*:\s*"/',
            $output,
            $matches,
            PREG_SET_ORDER,
        );

        if (empty($matches)) {
            throw UnexpectedOutputException::fromOutput($output);
        }

        return static::fromDecodedOutput($matches, $sourceText);
    }
}
