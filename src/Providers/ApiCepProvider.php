<?php

namespace Prhost\CepGratis\Providers;

use Prhost\CepGratis\Address;
use Prhost\CepGratis\Contracts\HttpClientContract;
use Prhost\CepGratis\Contracts\ProviderContract;

class ApiCepProvider implements ProviderContract
{
    /**
     * @return Address|null
     */
    public function getAddress($cep, HttpClientContract $client, array $option = [])
    {
        $response = $client->get('https://ws.apicep.com/busca-cep/api/cep/' . $cep . '.json');

        if (!is_null($response)) {
            $content = json_decode($response);
            if (is_object($content) && !isset($content->erro)) {
                if (isset($content->status) && ($content->status == 0 || $content->status == 404)) {
                    return null;
                }

                $address = '';
                if (property_exists($content, 'address') && $content->address) {
                    $address = trim(explode('- até', $content->address)[0] ?? '');
                    $address = trim(explode('- de', $address)[0] ?? '');
                }

                return Address::create([
                    'zipcode'      => $cep,
                    'street'       => $address,
                    'neighborhood' => property_exists($content, 'district') ? $content->district : '',
                    'city'         => property_exists($content, 'city') ? $content->city : '',
                    'state'        => property_exists($content, 'state') ? $content->state : '',
                ]);
            }
        }

        return null;
    }
}
