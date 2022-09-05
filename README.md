> **Warning**
> This package is _**abandoned**_ in favor of [Cloud Firestore client for PHP](https://packagist.org/packages/google/cloud-firestore).
> And not will be updated.


# Wead Google Cloud Firestore REST Client

Integrate client PHP with REST Api from Google Cloud Firestore.

## Requirements
[![Supported PHP version](https://img.shields.io/badge/php-%5E5.6-blue.svg)]()


## Dependecies
[![Requires Guzzle](https://img.shields.io/badge/Guzzle-~6.0-lightgrey.svg)]()
[![Firebase / PHP-JWT](https://img.shields.io/badge/Firebase%20%2F%20PHP--JWT-5.0-red.svg)]()

## Current features

- [x] Get
- [x] Set
- [x] Remove

## Installation

It's reommended to install with composer:

> composer require wead/google-firestore-rest-client

## Usage

Sample usage

```php
<?php

require "vendor/autoload.php";

use \Wead\Firestore\WeadFirestoreClient;

// download this json file from Google Console API
// https://cloud.google.com/iam/docs/creating-managing-service-account-keys
$firestorage = new WeadFirestoreClient("./google-account-services.json");

// it's only get collection object, but not create it
$collection = $firestorage->getCollection('users');

// set new or existing document inside this collection
$doc = $firestorage->getDocument($collection, "adriano");
$doc2 = $firestorage->getDocument($collection, "temp-to-remove"); // delete document example

// insert or update document with content
// a fild will be removed if already exists online but not informed here
// suport nested array and objects
$outDoc = $firestorage->setDocument($doc, [
    "name" => "Adriano Maciel",
    "email" => "adriano_mail@hotmail.com",
    "social" => [
        [
            "dev" => [
                "github" => [
                    "https://github.com/adrianowead",
                    "https://github.com/adrianowead/google-firestore-rest-client",
                ],
                "https://pt.stackoverflow.com/users/109468/adriano-maciel",
            ],
        ],
        [
            "https://www.linkedin.com/in/adrianowead",
        ],
        "type_int" => 1,
        "type_bool" => false,
        "type_null" => null,
        "string_empty" => "",
        "array_empty" => [],
        "array_null" => [null],
        "array_bool" => [false],
        "array_string_empty" => [""],
        "object_empty" => json_decode('{}'),
        "object_string_empty" => json_decode('{"0":""}'),
        "object_null" => json_decode('{"0":null}'),
        "object_bool" => json_decode('{"0":false}'),
    ]
]);
// get a object with all information and fields inside this document
$read = $firestorage->readDocument($doc);

print_r($read);

// write and remove temp document
$outDoc = $firestorage->setDocument($doc2, [
    "content" => "to be removed"
]);

$read = $firestorage->readDocument($doc2);
print_r($read);

// delete doc
$firestorage->removeDocument($doc2);
```

[![BuyMeACoffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-ffdd00?style=for-the-badge&logo=buy-me-a-coffee&logoColor=black)](https://www.paypal.com/donate/?hosted_button_id=WW7N7R4Z5RA6A)

![PayPal](https://raw.githubusercontent.com/adrianowead/adrianowead/main/img/qr-code-donate.png)
