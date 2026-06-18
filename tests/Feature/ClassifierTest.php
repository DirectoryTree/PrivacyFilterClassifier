<?php

use DirectoryTree\PrivacyFilterClassifier\Classifier;
use DirectoryTree\PrivacyFilterClassifier\Entity;
use DirectoryTree\PrivacyFilterClassifier\Exceptions\BinaryNotFoundException;
use DirectoryTree\PrivacyFilterClassifier\Exceptions\ModelNotFoundException;

it('returns entity instances from the privacy filter output', function () {
    $entities = (new Classifier(
        binaryPath: $this->binaryPath,
        modelPath: $this->modelPath,
        timeout: 5,
    ))->entities('Contact John Doe at jdoe@example.com from 555-0100.');

    expect($entities)->toHaveCount(1)
        ->and($entities[0])->toBeInstanceOf(Entity::class)
        ->and($entities[0]->type)->toBe('private_email')
        ->and($entities[0]->text)->toBe('jdoe@example.com')
        ->and($entities[0]->start)->toBe(20)
        ->and($entities[0]->end)->toBe(36)
        ->and($entities[0]->score)->toBe(0.9876)
        ->and($entities[0]->length())->toBe(16);
});

it('uses byte offsets to hydrate text when the cli text field is not valid json', function () {
    $this->setFakePrivacyFilterEnvironment([
        'PRIVACY_FILTER_FAKE_MODE' => 'raw-text',
        'PRIVACY_FILTER_FAKE_NEEDLE' => 'John "JD" Doe',
        'PRIVACY_FILTER_FAKE_TYPE' => 'private_person',
    ]);

    $entities = (new Classifier(
        binaryPath: $this->binaryPath,
        modelPath: $this->modelPath,
        timeout: 5,
    ))->entities('Contact John "JD" Doe today.');

    expect($entities)->toHaveCount(1)
        ->and($entities[0]->type)->toBe('private_person')
        ->and($entities[0]->text)->toBe('John "JD" Doe');
});

it('throws an exception when the binary does not exist', function () {
    (new Classifier(
        binaryPath: __DIR__.'/missing-privacy-filter',
        modelPath: $this->modelPath,
        timeout: 5,
    ))->entities('Contact John Doe at jdoe@example.com.');
})->throws(BinaryNotFoundException::class);

it('throws an exception when the model does not exist', function () {
    (new Classifier(
        binaryPath: $this->binaryPath,
        modelPath: __DIR__.'/missing-model.gguf',
        timeout: 5,
    ))->entities('Contact John Doe at jdoe@example.com.');
})->throws(ModelNotFoundException::class);
