<?php

namespace DirectoryTree\PrivacyFilterClassifier;

/**
 * Known privacy-filter entity types.
 */
enum EntityType: string
{
    case AccountNumber = 'account_number';
    case PrivateAddress = 'private_address';
    case PrivateDate = 'private_date';
    case PrivateEmail = 'private_email';
    case PrivatePerson = 'private_person';
    case PrivatePhone = 'private_phone';
    case PrivateUrl = 'private_url';
    case Secret = 'secret';
}
