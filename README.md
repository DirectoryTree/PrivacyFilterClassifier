<h1 align="center">Privacy Filter Classifier</h1>

<p align="center">Framework agnostic PHP classifier for <a href="https://github.com/DirectoryTree/PrivacyFilterBinaries"><code>privacy-filter.cpp</code></a> binaries.</p>

<p align="center">
    <a href="https://github.com/DirectoryTree/PrivacyFilterClassifier/actions/workflows/run-tests.yml"><img src="https://img.shields.io/github/actions/workflow/status/DirectoryTree/PrivacyFilterClassifier/run-tests.yml?branch=master&amp;style=flat-square" alt="Tests"></a>
    <a href="https://packagist.org/packages/directorytree/privacy-filter-classifier"><img src="https://img.shields.io/packagist/dt/directorytree/privacy-filter-classifier.svg?style=flat-square" alt="Total Downloads"></a>
    <a href="https://packagist.org/packages/directorytree/privacy-filter-classifier"><img src="https://img.shields.io/packagist/v/directorytree/privacy-filter-classifier.svg?style=flat-square" alt="Latest Version"></a>
    <a href="https://github.com/DirectoryTree/PrivacyFilterClassifier/blob/master/LICENSE"><img src="https://img.shields.io/github/license/DirectoryTree/PrivacyFilterClassifier?style=flat-square" alt="License"></a>
</p>

<p align="center">
    <a href="#installation">Installation</a>
    <span> · </span>
    <a href="#usage">Usage</a>
    <span> · </span>
    <a href="#entities">Entities</a>
</p>

---

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

You may provide a classification threshold at runtime. Only entities with a confidence score equal to or greater than the threshold will be returned:

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
