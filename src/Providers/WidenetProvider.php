<?php

namespace Prhost\CepGratis\Providers;

use Prhost\CepGratis\Address;
use Prhost\CepGratis\Contracts\HttpClientContract;
use Prhost\CepGratis\Contracts\ProviderContract;

class WidenetProvider implements ProviderContract
{
    /**
     * @return Address|null
     */
    public function getAddress($cep, HttpClientContract $client, array $option = [])
    {
        $response = $client->get('http://apps.widenet.com.br/busca-cep/api/cep/' . $cep . '.json');

        if (!is_null($response)) {
            $content = json_decode($response);
            if (is_object($content) && !isset($content->erro)) {
                if (isset($content->status) && $content->status == 0) {
                    return null;
                }

                $address = trim(explode('- até', $content->address)[0]);
                $address = trim(explode('- de', $address)[0]);

                return Address::create([
                    'zipcode'      => $cep,
                    'street'       => $address,
                    'neighborhood' => $content->district,
                    'city'         => $content->city,
                    'state'        => $content->state,
                ]);
            }
        }

        return null;
    }
}
