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

## Installation

It's reommended to install with composer:

    composer require wead/google-firestore-rest-client

## Usage

Sample usage

```php
<?php

require "vendor/autoload.php";

use \Wead\Firestore\WeadFirestoreClient;

// download this json file from Google Console API
$firestorage = new WeadFirestoreClient("./google-account-services.json");

// it's only get collection object, but not create it
$collection = $firestorage->getCollection('users');

// set new or existing document inside this collection
$doc = $firestorage->getDocument($collection, "adriano");

// insert or update document with content
// a fild will be removed if already exists online but not informed here
$outDoc = $firestorage->setDocument($doc, [
    "name" => "Adriano Maciel",
    "email" => "adriano_mail@hotmail.com"
]);

// get a object with all information and fields inside this document
$read = $firestorage->readDocument($doc);

print_r($read);
```