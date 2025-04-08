<?php

declare(strict_types=1);

namespace App\Provider;

class GoogleServiceProvider
{
    public function registerMail()
    {
        $client = new \Google_Client();
        $client->setSubject('kundenservice@sf-elektro.info');

        // set the authorization configuration using the 2.0 style
        $client->setAuthConfig([
            'type' => 'service_account',
            'client_email' => '395545742105@developer.gserviceaccount.com',
            'client_id' => '395545742105.apps.googleusercontent.com',
            'private_key' => 'yourkey',
        ]);
    }
}
