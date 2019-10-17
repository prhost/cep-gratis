<?php

namespace Prhost\CepGratis\Providers;

use Prhost\CepGratis\Address;
use Prhost\CepGratis\Contracts\HttpClientContract;
use Prhost\CepGratis\Contracts\ProviderContract;

class ViaCepProvider implements ProviderContract
{
    /**
     * @return Address|null
     */
    public function getAddress($cep, HttpClientContract $client, array $option = [])
    {
        $response = $client->get('https://viacep.com.br/ws/'.$cep.'/json/');

        if (!is_null($response)) {
            $data = json_decode($response, true);

            if($data && is_array($data) && !isset($data['erro'])) {
                return Address::create([
                    'zipcode'      => $cep,
                    'street'       => $data['logradouro'],
                    'neighborhood' => $data['bairro'],
                    'city'         => $data['localidade'],
                    'state'        => $data['uf'],
                ]);
            }
        }
    }
}
