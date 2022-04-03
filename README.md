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
// https://cloud.google.com/iam/docs/creating-managing-service-account-keys
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

![BuyMeACoffee](https://img.shields.io/badge/Buy%20Me%20a%20Coffee-ffdd00?style=for-the-badge&logo=buy-me-a-coffee&logoColor=black)

![PayPal](https://raw.githubusercontent.com/adrianowead/adrianowead/main/img/qr-code-donate.png) [![PayPal](https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif)](https://www.paypal.com/donate/?hosted_button_id=WW7N7R4Z5RA6A)