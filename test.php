<?php

require "vendor/autoload.php";

use \Wead\Firestore\WeadFirestoreClient;

$firestorage = new WeadFirestoreClient("./google-account-services.json");

$collection = $firestorage->getCollection('users');

$doc = $firestorage->getDocument($collection, "adriano");

// $outDoc = $firestorage->setDocument($doc, [
//     "name" => "Adriano Maciel",
//     "email" => "adriano_mail@hotmail.com"
// ]);

$read = $firestorage->readDocument($doc);

print_r($read);