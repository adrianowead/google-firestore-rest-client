<?php

namespace Wead\Firestore\Traits;

trait CloudFirestoreDocumentResource
{
    use CloudFirestoreDocumentFieldType;

    abstract public function getDocument($collection, $name);

    private function document($collection, $name, $createDefaultDoc = false)
    {
        $collection->name = substr($collection->name, -1) == '/' ? substr($collection->name, 0, -1) : $collection->name;

        $name = self::clearName($name);

        $uri = "{$collection->name}/{$name}";

        if (!$createDefaultDoc) {
            $response = new \stdClass;
            $response->name = $name;
            $response->fullName = $uri;
        } else {
            $response = $this->makeRequestApi('POST', $uri);
        }

        $response->objectType = "document";

        return $response;
    }

    private function updateDocument($doc, $fields = [])
    {
        $doc->name = substr($doc->name, -1) == '/' ? substr($doc->name, 0, -1) : $doc->name;
        $doc->fullName = substr($doc->fullName, -1) == '/' ? substr($doc->fullName, 0, -1) : $doc->fullName;

        $doc->name = self::clearName($doc->name);

        $uri = $this->getBaseUri($doc->fullName);

        $fieldsMapped = self::mapFieldValues(array_reverse(explode("/", $doc->name))[0], $fields);

        $response = $this->makeRequestApi('PATCH', $uri, $fieldsMapped);
        $response->fullName = $response->name;

        $response->objectType = "document";
        $response->name = str_replace($this->getBaseUri(), '', $uri);

        $response->name = self::clearName($response->name);

        return $response;
    }

    private function readDocumentFields($doc)
    {
        $doc->name = substr($doc->name, -1) == '/' ? substr($doc->name, 0, -1) : $doc->name;
        $doc->fullName = substr($doc->fullName, -1) == '/' ? substr($doc->fullName, 0, -1) : $doc->fullName;

        $doc->name = self::clearName($doc->name);

        $uri = $this->getBaseUri($doc->fullName);

        $response = $this->makeRequestApi('GET', $uri);
        $response->fullName = $response->name;

        $response->objectType = "document";
        $response->name = str_replace($this->getBaseUri(), '', $uri);

        return $response;
    }
}
