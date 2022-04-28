<?php

namespace Wead\Firestore\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Wead\Firestore\Traits\CloudFirestoreAccountService;
use Wead\Firestore\Traits\CloudFirestoreCollectionResource;
use Wead\Firestore\Traits\CloudFirestoreDocumentResource;

trait CloudFirestoreRestClient
{
    use CloudFirestoreAccountService, CloudFirestoreCollectionResource, CloudFirestoreDocumentResource;

    protected $baseUri = "https://firestore.googleapis.com/v1beta1/projects/**PROJECT_ID**/databases/(default)";

    abstract public function getDocument($collection, $name);
    abstract public function removeDocument($doc);

    private function getBaseUri($append = null)
    {
        $append = $append[0] == "/" ? $append : "/{$append}";

        $uri = str_replace('**PROJECT_ID**', $this->serviceAccount->project_id, $this->baseUri) . $append;

        $uri = self::replaceSpecialCharacters($uri);

        return $uri;
    }

    private static function replaceSpecialCharacters($text)
    {
        $text = preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities($text));

        return $text;
    }

    private static function clearName($name)
    {
        $name = self::replaceSpecialCharacters($name);
        $name = str_replace(['/'], '', $name);

        return $name;
    }

    private function makeRequestApi($method, $uri, $options = [])
    {
        $headers = [
            "Authorization" => "Bearer " . $this->fetchAuthToken()['access_token'],
            "Accept" => "application/json",
        ];

        $client = new Client(['base_uri' => $this->getBaseUri()]);

        if ($method == 'PATCH') {
            try {
                $response = $client->patch($uri, [
                    'headers' => $headers,
                    'json' => $options,
                    'debug' => false,
                ]);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                print_r('Firestore PATCH document error: ' . chr(13) . chr(10) . $e->getResponse()->getBody()->getContents() . chr(13) . chr(10) . 'Request: ' . chr(13) . chr(10) . json_encode([
                    'uri' => $uri,
                    'method' => $method,
                    'headers' => $headers,
                    'json' => $options,
                ], JSON_PRETTY_PRINT));

                print_r($e->getResponse()->getBody()->getContents());
                echo chr(13) . chr(10);
                exit;
            }
        } else if ($method == 'GET') {
            try {
                $response = $client->get($uri, [
                    'headers' => $headers,
                    'query' => $options,
                    'debug' => false,
                ]);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                print_r('Firestore error to read document: ' . chr(13) . chr(10) . $e->getResponse()->getBody()->getContents() . chr(13) . chr(10) . 'Request: ' . chr(13) . chr(10) . json_encode([
                    'uri' => $uri,
                    'method' => $method,
                    'headers' => $headers,
                    'query' => $options,
                ], JSON_PRETTY_PRINT));

                print_r($e->getResponse()->getBody()->getContents());
                echo chr(13) . chr(10);
                exit;
            }
        } else if ($method == 'DELETE') {
            try {
                $response = $client->delete($uri, [
                    'headers' => $headers,
                    'query' => $options,
                    'debug' => false,
                ]);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                print_r('Firestore error to remove document: ' . chr(13) . chr(10) . $e->getResponse()->getBody()->getContents() . chr(13) . chr(10) . 'Request: ' . chr(13) . chr(10) . json_encode([
                    'uri' => $uri,
                    'method' => $method,
                    'headers' => $headers,
                    'query' => $options,
                ], JSON_PRETTY_PRINT));

                print_r($e->getResponse()->getBody()->getContents());
                echo chr(13) . chr(10);
                exit;
            }
        } else {
            try {
                $request = new Request($method, $this->getBaseUri($uri), $headers);
                $response = $client->send($request, ['debug' => false]);
            } catch (\GuzzleHttp\Exception\ClientException $e) {
                print_r('Firestore error to read document: ' . chr(13) . chr(10) . $e->getResponse()->getBody()->getContents() . chr(13) . chr(10) . 'Request: ' . chr(13) . chr(10) . json_encode([
                    'uri' => $uri,
                    'method' => $method,
                    'headers' => $headers,
                    'form_params' => $options,
                ], JSON_PRETTY_PRINT));

                print_r($e->getResponse()->getBody()->getContents());
                echo chr(13) . chr(10);
                exit;
            }
        }

        if (!$response || !$response->getStatusCode() == 200) {
            throw new \Exception("Cloud Firebase does not response http 200");
        }

        return json_decode($response->getBody()->getContents());
    }
}
