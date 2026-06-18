<?php

use DirectoryTree\PrivacyFilterClassifier\Entity;
use DirectoryTree\PrivacyFilterClassifier\EntityType;
use DirectoryTree\PrivacyFilterClassifier\Exceptions\UnexpectedOutputException;

it('creates an entity from a decoded payload', function () {
    $entity = Entity::from([
        'entity_group' => 'email',
        'start' => 20,
        'end' => 36,
        'score' => 0.9876,
        'text' => 'ignored@example.com',
    ], 'Contact John Doe at jdoe@example.com.');

    expect($entity->type)->toBe('email')
        ->and($entity->text)->toBe('jdoe@example.com')
        ->and($entity->start)->toBe(20)
        ->and($entity->end)->toBe(36)
        ->and($entity->score)->toBe(0.9876);
});

it('creates an entity from alternate type keys', function () {
    $entity = Entity::from([
        'label' => 'person',
        'start' => 8,
        'end' => 16,
        'score' => 0.9876,
    ], 'Contact John Doe today.');

    expect($entity->type)->toBe('person')
        ->and($entity->text)->toBe('John Doe');
});

it('exposes entity details as arrays and json', function () {
    $entity = new Entity(
        type: 'email',
        start: 20,
        end: 36,
        score: 0.9876,
        text: 'jdoe@example.com',
    );

    expect($entity->length())->toBe(16)
        ->and($entity->toArray())->toBe([
            'type' => 'email',
            'start' => 20,
            'end' => 36,
            'score' => 0.9876,
            'text' => 'jdoe@example.com',
        ])
        ->and($entity->jsonSerialize())->toBe($entity->toArray());
});

it('returns the known entity type enum', function () {
    $entity = new Entity(
        type: 'private_email',
        start: 20,
        end: 36,
        score: 0.9876,
        text: 'jdoe@example.com',
    );

    expect($entity->type())->toBe(EntityType::PrivateEmail);
});

it('returns null when the entity type is unknown', function () {
    $entity = new Entity(
        type: 'private_custom_identifier',
        start: 20,
        end: 36,
        score: 0.9876,
        text: 'ABC-123',
    );

    expect($entity->type())->toBeNull();
});

it('throws an exception when the decoded payload is invalid', function () {
    Entity::from([
        'entity_group' => 'email',
        'start' => 20,
        'end' => 200,
        'score' => 0.9876,
    ], 'Contact John Doe at jdoe@example.com.');
})->throws(UnexpectedOutputException::class);
