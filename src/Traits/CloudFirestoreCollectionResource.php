<?php

namespace Wead\Firestore\Traits;

trait CloudFirestoreCollectionResource
{
    abstract public function getCollection();

    private function collection($name, $createDefaultDoc = false)
    {
        $name = self::clearName($name);

        $uri = "/documents/{$name}/";

        if (!$createDefaultDoc) {
            $response = new \stdClass;
            $response->name = $this->getBaseUri($uri);
            $response->fullName = $uri;
        } else {
            $response = $this->makeRequestApi('POST', $uri);
            $response->fullName = $response->name;
        }

        $response->objectType = "collection";
        $response->name = str_replace($this->getBaseUri(), '', $uri);

        return $response;
    }
}
