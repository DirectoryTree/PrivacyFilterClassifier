<div align="center">
<h1>Privacy Filter Classifier</h1>
<p>Framework agnostic PHP classifier for <code>privacy-filter.cpp</code> binaries.</p>
</div>

## Installation

You may install the package via Composer:

```bash
composer require directorytree/privacy-filter-classifier
```

## Usage

Create a classifier using the local binary and model paths:

```php
use DirectoryTree\PrivacyFilterClassifier\Classifier;

$classifier = new Classifier(
    binaryPath: '/path/to/privacy-filter',
    modelPath: '/path/to/privacy-filter-f16.gguf',
    timeout: 60,
);

$entities = $classifier->entities('Contact John Doe at jdoe@example.com.');
```

You may provide a classification threshold at runtime:

```php
$entities = $classifier->entities(
    text: 'Contact John Doe at jdoe@example.com.',
    threshold: 0.75,
);
```

## Entities

The `entities` method returns an array of `DirectoryTree\PrivacyFilterClassifier\Entity` instances:

```php
/** @var \DirectoryTree\PrivacyFilterClassifier\Entity $entity */
foreach ($entities as $entity) {
    $entity->type;  // private_email
    $entity->text;  // jdoe@example.com
    $entity->start; // 20
    $entity->end;   // 36
    $entity->score; // 0.98
}
```

## Testing

You may run the package test suite using Pest:

```bash
./vendor/bin/pest
```
