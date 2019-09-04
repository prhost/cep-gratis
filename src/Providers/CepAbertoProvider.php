<?php

namespace Prhost\CepGratis\Providers;

use Prhost\CepGratis\Address;
use Prhost\CepGratis\Contracts\HttpClientContract;
use Prhost\CepGratis\Contracts\ProviderContract;

class CepAbertoProvider implements ProviderContract
{
    /**
     * @return Address|null
     */
    public function getAddress($cep, HttpClientContract $client, array $options = [])
    {
        $token = $options['token'] ?? null;
        $client->setHeaders(['Authorization: Token token=' . $token]);
        $response = $client->get('http://www.cepaberto.com/api/v3/cep?cep=' . $cep);

        if (!is_null($response)) {
            $content = json_decode($response);

            if (is_object($content) && !isset($content->erro)) {
                if (isset($content->status) && $content->status == 0) {
                    return null;
                }

                $address = trim(explode(', de', $content->logradouro)[0]);
                $address = trim(explode(', até', $address)[0]);

                return Address::create([
                    'zipcode'      => $cep,
                    'street'       => $address,
                    'neighborhood' => $content->bairro,
                    'city'         => $content->cidade->nome,
                    'state'        => $content->estado->sigla,
                ]);
            }
        }

        return null;
    }
}
