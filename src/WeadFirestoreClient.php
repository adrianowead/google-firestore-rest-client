<?php

namespace Wead\Firestore;

use Wead\Firestore\Traits\CloudFirestoreRestClient;

class WeadFirestoreClient
{
    use CloudFirestoreRestClient;

    protected $serviceAccount;

    public function __construct($googleFilePath)
    {
        if (!file_exists($googleFilePath)) {
            throw new \Exception("Google File Account json does not exists");
        }

        $this->serviceAccount = self::accountFromJsonFile($googleFilePath);
    }

    public function getCollection($name)
    {
        return $this->collection($name);
    }

    public function getDocument($collection, $name)
    {
        return $this->document($collection, $name);
    }

    public function removeDocument($doc)
    {
        return $this->delete($doc);
    }

    public function setDocument($doc, $fields = [])
    {
        return $this->updateDocument($doc, $fields);
    }

    public function readDocument($doc, $fields = [])
    {
        return $this->readDocumentFields($doc, $fields);
    }
}
