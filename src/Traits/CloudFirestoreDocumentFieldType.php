<?php

namespace Wead\Firestore\Traits;

trait CloudFirestoreDocumentFieldType
{
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

    private function delete($doc)
    {
        $doc->name = substr($doc->name, -1) == '/' ? substr($doc->name, 0, -1) : $doc->name;
        $doc->fullName = substr($doc->fullName, -1) == '/' ? substr($doc->fullName, 0, -1) : $doc->fullName;

        $doc->name = self::clearName($doc->name);

        $uri = $this->getBaseUri($doc->fullName);

        $response = $this->makeRequestApi('DELETE', $uri);

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

        foreach ($fields as $field => $value) {
            $fnc = self::testValue($value);

            $out["fields"][$field] = call_user_func([self::class, $fnc], $value);

            // if (!$out["fields"][$field]) {
            //     unset($out["fields"][$field]);
            // }
        }

        return $out;
    }

    private static function intField ($val) {
        return ["integerValue" => trim($val)];
    }

    private static function stringField ($val) {
        return ["stringValue" => (string) trim($val)];
    }

    private static function doubleField ($val) {
        return ["doubleValue" => trim($val)];
    }

    private static function nullField ($val) {
        return ["nullValue" => null];
    }

    private static function booleanField ($val) {
        return ["booleanValue" => (bool) trim($val)];
    }

    private static function carbonTimestampField ($val) {
        return ["timestampValue" => $val->toIso8601ZuluString()];
    }

    private static function mapField($val) {
        $result = [
            "mapValue" => [
                "fields" => new \stdClass,
            ],
        ];

        foreach ($val as $k => $v) {
            if(is_array($v)) {
                $fnc = self::testValue($v);
                $tmpValue = call_user_func([self::class, $fnc], $v);

                $result["mapValue"]["fields"]->{$k} = $tmpValue;
            } else {
                $fnc = self::testValue($v);
                $tmpValue = call_user_func([self::class, $fnc], $v);

                if(is_numeric($k)) {
                    while(isset($result["mapValue"]["fields"]->{$k})) {
                        $k++;
                    }
                }

                $result["mapValue"]["fields"]->{$k} = $tmpValue;
            }
        }

        return $result;
    }

    private static function arrayField($val) {
        $result = [
            "arrayValue" => [
                "values" => [],
            ],
        ];

        foreach ($val as $k => $v) {
            if ($v) {
                $fnc = self::testValue($v);
                $result["arrayValue"]["values"][] = call_user_func([self::class, $fnc], $v);
            }
        }

        return sizeof($result["arrayValue"]["values"]) > 0 ? $result : false;
    }

    private static function testValue ($value) {
        $result = null;

        if (is_object($value)) {
            if (!$value instanceof \Carbon\Carbon) {
                throw new \Exception("Unknown object type");
            }

            $result = "carbonTimestampField";
        } else if (is_numeric($value) && (filter_var($value, FILTER_VALIDATE_INT, ['min_range' => 0]) != false || $value == "0" ) && substr_count($value, ".") == 0) {
            $result = "intField";
        } else if (is_numeric($value) && substr_count($value, ".") > 0 && preg_match('/[^0-9.]/', $value) == 0 && str_split($value)[0] != "0" && substr($value, -1) != "0") {
            $result = "doubleField";
        } else if (is_string($value)) {
            $result = "stringField";
        } else if (is_null($value)) {
            $result = "nullField";
        } else if (is_bool($value)) {
            $result = "booleanField";
        } else if (is_array($value)) {
            $result = "arrayField";

            foreach($value as $k => $v) {
                if(is_string($k) || self::getCallingMethodName() == 'arrayField') {
                    $result = "mapField";
                    break;
                }
            }
        }

        if (!$result) {
            throw new \Exception("Unknown value type to map");
        }

        return $result;
    }

    protected static function getCallingMethodName() {
        list(,, $caller) = debug_backtrace(false);
        return $caller['function'];
    }
}
