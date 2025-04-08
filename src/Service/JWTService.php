<?php

declare(strict_types=1);

namespace App\Service;

use Firebase\JWT\JWT;
use Google\Service\IAMCredentials;

class JWTService
{
    protected \Google_Client $client;
    protected const APPLICATION_ENCODING = 'RS256';
    protected const APPLICATION_CUSTOMER_NAME = 'SF-Elektro green-management';
    protected const APPLICATION_SERVICE_ACCOUNT_ID = 'green-management@sf-elektro-green-management-eu.iam.gserviceaccount.com';
    protected const APPLICATION_CREDENTIALS = 'sf-elektro-green-management-eu-09ac3eed8a4b.json';
    protected const APPLICATION_KID = '09ac3eed8a4b36f57918a1d52a09e362fd8043c6';

    protected function getPayload(string $account = 'green-management@sf-elektro-green-management-eu.iam.gserviceaccount.com', string $delegatedAccount = 'kundenservice@sf-elektro.info'): array
    {
        return [
            'iss' => $account,
            // "sub" => $account,
            'sub' => $delegatedAccount,
            'aud' => 'https://oauth2.googleapis.com/token',
            // "scope" => $this->getGmailScopes(),
            'scope' => 'https://mail.google.com/',
            'iat' => time(),
            'exp' => (time() + 3600),
        ];
//        $iss = '761326798069-r5mljlln1rd4lrbhg75efgigp36m78j5@developer.gserviceaccount.com';
//        $scope = 'https://mail.google.com/';
        // // The sub should be your Google Apps user email address (not a gmail.com address)
//        $sub = 'YOUR_GOOGLE_APPS_USER_EMAIL_ADDRESS';
//        $numSec = 3600;
    }

    protected function getHeader(): array
    {
        return [
            'alg' => 'RS256',
            'typ' => 'JWT',
            'kid' => self::APPLICATION_KID,
        ];
    }

    protected function getGmailScopes(): string
    {
        // return 'https://gmail.googleapis.com/ https://www.googleapis.com/auth/gmail.readonly';
        return 'https://gmail.googleapis.com/ https://www.googleapis.com/auth/gmail.modify https://www.googleapis.com/auth/gmail.send https://www.googleapis.com/auth/gmail.readonly https://www.googleapis.com/auth/gmail.compose';
    }

    /**
     * "private_key_id": "09ac3eed8a4b36f57918a1d52a09e362fd8043c6",
     * sf-elektro-green-management-eu-09ac3eed8a4b.json
     * "client_email": "green-management@sf-elektro-green-management-eu.iam.gserviceaccount.com",
     * "client_id": "112244474206855403365",.
     */
    public function getSecred()
    {
        $j = $this->getCredentialsAsJson();

        return $j->private_key;
    }

    public function getCredentialsAsJson(): mixed
    {
        $credentialsContent = trim(file_get_contents($_ENV['GOOGLE_APPLICATION_CREDENTIALS']), "\xEF\xBB\xBF");

        return json_decode($credentialsContent);
    }
}
