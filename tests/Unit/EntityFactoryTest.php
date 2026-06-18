<?php

use DirectoryTree\PrivacyFilterClassifier\EntityFactory;
use DirectoryTree\PrivacyFilterClassifier\Exceptions\UnexpectedOutputException;

it('creates entities from decoded output', function () {
    $entities = (new EntityFactory)->fromDecodedOutput([
        [
            'entity_group' => 'email',
            'start' => 20,
            'end' => 36,
            'score' => 0.9876,
            'text' => 'jdoe@example.com',
        ],
    ], 'Contact John Doe at jdoe@example.com.');

    expect($entities)->toHaveCount(1)
        ->and($entities[0]->type)->toBe('email')
        ->and($entities[0]->text)->toBe('jdoe@example.com');
});

it('creates entities from output prefixes', function () {
    $entities = (new EntityFactory)->fromOutputPrefixes(
        "[\n {\"entity_group\": \"person\", \"start\": 8, \"end\": 21, \"score\": 0.9876, \"text\": \"John \"JD\" Doe\"}\n]\n",
        'Contact John "JD" Doe today.',
    );

    expect($entities)->toHaveCount(1)
        ->and($entities[0]->type)->toBe('person')
        ->and($entities[0]->text)->toBe('John "JD" Doe');
});

it('throws an exception for invalid output prefixes', function () {
    (new EntityFactory)->fromOutputPrefixes('invalid output', 'Contact John Doe.');
})->throws(UnexpectedOutputException::class);
