<?php

namespace Prhost\CepGratis\Providers;

use Prhost\CepGratis\Address;
use Prhost\CepGratis\Contracts\HttpClientContract;
use Prhost\CepGratis\Contracts\ProviderContract;

class RepublicaVirtualProvider implements ProviderContract
{
    /**
     * @return Address|null
     */
    public function getAddress($cep, HttpClientContract $client, array $option = [])
    {
        $response = $client->get('http://cep.republicavirtual.com.br/web_cep.php?cep=' . $cep . '&formato=json');

        if (!is_null($response)) {
            $content = json_decode($response);
            if (is_object($content) && $content->resultado != 0) {

                return Address::create([
                    'zipcode'      => $cep,
                    'street'       => trim($content->tipo_logradouro . ' ' . $content->logradouro),
                    'neighborhood' => $content->bairro,
                    'city'         => $content->cidade,
                    'state'        => $content->uf,
                ]);
            }
        }

        return null;
    }
}
