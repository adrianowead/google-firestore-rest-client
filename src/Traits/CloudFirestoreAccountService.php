<?php

namespace Wead\Firestore\Traits;

trait CloudFirestoreAccountService
{
    protected $auth;

    private $TOKEN_CREDENTIAL_URI = 'https://oauth2.googleapis.com/token';
    private $ENV_VAR = 'GOOGLE_APPLICATION_CREDENTIALS';
    private $WELL_KNOWN_PATH = 'gcloud/application_default_credentials.json';
    private $NON_WINDOWS_WELL_KNOWN_PATH_BASE = '.config';
    private $AUTH_METADATA_KEY = 'authorization';

    protected $authScope = [
        'https://www.googleapis.com/auth/cloud-platform',
        'https://www.googleapis.com/auth/firebase',
        'https://www.googleapis.com/auth/firebase.database',
        'https://www.googleapis.com/auth/firebase.messaging',
        'https://www.googleapis.com/auth/firebase.remoteconfig',
        'https://www.googleapis.com/auth/userinfo.email',
    ];

    private static function accountFromJsonFile($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("Google Account Services file does not exists");
        }

        $credentials = json_decode(file_get_contents($filePath));

        return $credentials;
    }

    private function fetchAuthToken()
    {
        $this->auth = new CloudFirestoreOAuth2([
            'audience' => $this->TOKEN_CREDENTIAL_URI,
            'issuer' => $this->serviceAccount->client_email,
            'scope' => $this->authScope,
            'signingAlgorithm' => 'RS256',
            'signingKey' => $this->serviceAccount->private_key,
            'sub' => null,
            'tokenCredentialUri' => $this->TOKEN_CREDENTIAL_URI,
        ]);

        return $this->auth->fetchAuthToken();
    }
}
