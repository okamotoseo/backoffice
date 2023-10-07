<?php 




\CNovaApiLojistaV2\client\Configuration::$apiKey['client_id'] = $moduleConfig['client_id'];
\CNovaApiLojistaV2\client\Configuration::$apiKey['access_token'] = $moduleConfig['token'];

$api_client = new \CNovaApiLojistaV2\client\ApiClient('https://sandbox-mktplace.viavarejo.com.br/api/v2');



?>