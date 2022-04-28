<?php

require "vendor/autoload.php";

use \Wead\Firestore\WeadFirestoreClient;

$firestorage = new WeadFirestoreClient("./google-account-services.json");

$collection = $firestorage->getCollection('users');

$doc = $firestorage->getDocument($collection, "adriano");
$doc2 = $firestorage->getDocument($collection, "para-apagar");

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
    ]
]);

$read = $firestorage->readDocument($doc);

print_r($read);

$outDoc = $firestorage->setDocument($doc2, [
    "conteudo" => "temporário"
]);

$read = $firestorage->readDocument($doc2);
print_r($read);

// excluindo um documento
$firestorage->removeDocument($doc2);