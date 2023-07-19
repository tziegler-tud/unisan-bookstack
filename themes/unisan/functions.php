<?php

use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;

Theme::listen(ThemeEvents::OIDC_ID_TOKEN_PRE_VALIDATE, function (array $idTokenData, array $accessTokenData) {

    //queries the userinfo endpoint and appends the data to the id_token

    $headers = [
        'Authorization: Bearer ' . $accessTokenData['access_token'],
        'Content-Type: application/json',
    ];

    $defaultIssuer = "https://unisan-server.de/oidc";
    $issuer = $idTokenData['iss'] ?? $defaultIssuer;
    $user_info_endpoint = $issuer . "/me";
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => $user_info_endpoint,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => $headers,
    ]);

    $responseBody = curl_exec($curl);

    curl_close($curl);

    return array_merge(json_decode($responseBody, true), $idTokenData);
});