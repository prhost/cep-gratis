<?php

namespace Prhost\CepGratis\Contracts;

interface ProviderContract
{
    /**
     * @return Address
     */
    public function getAddress($cep, HttpClientContract $client, array $options = []);
}
