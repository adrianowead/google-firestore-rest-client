<?php

namespace Wead\Firestore\Traits;

trait CloudFirestoreDocumentResource
{
    abstract public function getDocument();

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

    public static function mapFieldValues($name, $fields)
    {
        $out = [
            "name" => $name,
            "fields" => [],
        ];

        $intField = function ($val) {
            return ["integerValue" => trim($val)];
        };

        $stringField = function ($val) {
            return ["stringValue" => (string) trim($val)];
        };

        $doubleField = function ($val) {
            return ["doubleValue" => trim($val)];
        };

        $nullField = function ($val) {
            return ["nullValue" => null];
        };

        $booleanField = function ($val) {
            return ["booleanValue" => (bool) trim($val)];
        };

        $carbonTimestampField = function ($val) {
            return ["timestampValue" => $val->toIso8601ZuluString()];
        };

        $arrayField = function ($val, $testValue) use ($stringField, $intField, $doubleField, $nullField, $booleanField) {
            $result = [
                "arrayValue" => [
                    "values" => [],
                ],
            ];

            foreach ($val as $k => $v) {
                if ($v) {
                    $fnc = $testValue($v);
                    $result["arrayValue"]["values"][] = $$fnc($v, $testValue);
                }
            }

            return sizeof($result["arrayValue"]["values"]) > 0 ? $result : false;
        };

        $testValue = function ($value) {
            $result = null;

            if (is_object($value)) {
                if (!$value instanceof \Carbon\Carbon) {
                    throw new \Exception("Unknown object type");
                }

                $result = "carbonTimestampField";
            } else if ((filter_var($value, FILTER_VALIDATE_INT, ['min_range' => 0]) != false || $value == "0" ) && substr_count($value, ".") == 0) {
                $result = "intField";
            } else if (is_numeric($value) && substr_count($value, ".") > 0 && preg_match('/[^0-9.]/', $value) == 0 && str_split($value)[0] != "0") {
                $result = "doubleField";
            } else if (is_string($value)) {
                $result = "stringField";
            } else if (is_null($value)) {
                $result = "nullField";
            } else if (is_bool($value)) {
                $result = "booleanField";
            } else if (is_array($value)) {
                $result = "arrayField";
            }

            if (!$result) {
                throw new \Exception("Unknown value type to map");
            }

            return $result;
        };

        foreach ($fields as $field => $value) {
            $fnc = $testValue($value);
            $out["fields"][$field] = $$fnc($value, $testValue);

            if (!$out["fields"][$field]) {
                unset($out["fields"][$field]);
            }
        }

        return $out;
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
