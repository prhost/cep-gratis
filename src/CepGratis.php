<?php

namespace Prhost\CepGratis;

use Prhost\CepGratis\Clients\CurlHttpClient;
use Prhost\CepGratis\Contracts\HttpClientContract;
use Prhost\CepGratis\Contracts\ProviderContract;
use Prhost\CepGratis\Exceptions\CepGratisInvalidParameterException;
use Prhost\CepGratis\Exceptions\CepGratisTimeoutException;
use Prhost\CepGratis\Providers\ApiCepProvider;
use Prhost\CepGratis\Providers\CepAbertoProvider;
use Prhost\CepGratis\Providers\CorreiosProvider;
use Prhost\CepGratis\Providers\RepublicaVirtualProvider;
use Prhost\CepGratis\Providers\ViaCepProvider;

/**
 * Class to query CEP.
 */
class CepGratis
{
    /**
     * @var HttpClientContract
     */
    private $client;

    /**
     * @var ProviderContract[]
     */
    private $providers = [];

    /**
     * @var int in seconds
     */
    private $timeout = 5;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * CepGratis constructor.
     */
    public function __construct()
    {
        $this->client = new CurlHttpClient();
    }

    /**
     * Search CEP on all providers.
     *
     * @param string $cep CEP
     * @param array $options
     * @param int $timeout in seconds (optional)
     * @return Address
     * @throws CepGratisInvalidParameterException
     * @throws CepGratisTimeoutException
     */
    public static function search(string $cep, array $options = [], int $timeout = null)
    {
        $cepGratis = new self();
        $cepGratis->options = $options;
        $cepGratis->timeout = $timeout ? $timeout : $cepGratis->timeout;

        $cepGratis->addProvider(new ViaCepProvider());
        $cepGratis->addProvider(new CorreiosProvider());
        $cepGratis->addProvider(new ApiCepProvider());
        $cepGratis->addProvider(new CepAbertoProvider());
        $cepGratis->addProvider(new RepublicaVirtualProvider());

        $address = $cepGratis->resolve($cep);

        return $address;
    }

    /**
     * Performs provider CEP search.
     *
     * @param string $cep CEP
     * @return Address
     * @throws CepGratisInvalidParameterException
     * @throws CepGratisTimeoutException
     */
    public function resolve($cep)
    {
        $cep = $this->clearCep($cep);

        if (strlen($cep) != 8 && filter_var($cep, FILTER_VALIDATE_INT) === false) {
            throw new CepGratisInvalidParameterException('CEP is invalid');
        }

        if (count($this->providers) == 0) {
            throw new CepGratisInvalidParameterException('No providers were informed');
        }

        /*
         * Execute
         */
        $time = time();

        do {
            foreach ($this->providers as $provider) {
                $address = $provider->getAddress($cep, $this->client, $this->options);
                if (!is_null($address)) {
                    break;
                }
            }

            if (!is_null($address)) {
                break;
            }

            if ((time() - $time) >= $this->timeout) {
                throw new CepGratisTimeoutException("Maximum execution time of $this->timeout seconds exceeded in PHP");
            }
        } while (is_null($address));

        /*
         * Return
         */
        return $address;
    }

    /**
     * Set client http.
     *
     * @param HttpClientContract $client
     */
    public function setClient(HttpClientContract $client)
    {
        $this->client = $client;
    }

    /**
     * Set array providers.
     *
     * @param HttpClientContract $client
     */
    public function addProvider(ProviderContract $provider)
    {
        $this->providers[] = $provider;
    }

    /**
     * Set timeout.
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    /**
     * Limpa os caracteres especiais do CEP
     *
     * @param string $cep
     * @return string
     */
    protected function clearCep(string $cep): string
    {
        return preg_replace('#[^0-9]#', '', $cep);
    }
}
