<?php

namespace JansenFelipe\CepGratis;

use JansenFelipe\CepGratis\Clients\CurlHttpClient;
use JansenFelipe\CepGratis\Contracts\HttpClientContract;
use JansenFelipe\CepGratis\Contracts\ProviderContract;
use JansenFelipe\CepGratis\Exceptions\CepGratisInvalidParameterException;
use JansenFelipe\CepGratis\Exceptions\CepGratisTimeoutException;
use JansenFelipe\CepGratis\Providers\CepAbertoProvider;
use JansenFelipe\CepGratis\Providers\CorreiosProvider;
use JansenFelipe\CepGratis\Providers\RepublicaVirtualProvider;
use JansenFelipe\CepGratis\Providers\ViaCepProvider;
use JansenFelipe\CepGratis\Providers\WidenetProvider;

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
     * @var int
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
     * @return Address
     * @throws CepGratisInvalidParameterException
     * @throws CepGratisTimeoutException
     */
    public static function search(string $cep, array $options = [])
    {
        $cepGratis = new self();
        $cepGratis->options = $options;

        $cepGratis->addProvider(new ViaCepProvider());
        $cepGratis->addProvider(new CorreiosProvider());
        $cepGratis->addProvider(new WidenetProvider());
        $cepGratis->addProvider(new CepAbertoProvider());
        $cepGratis->addProvider(new RepublicaVirtualProvider());

        $address = $cepGratis->resolve($cep);

        return $address;
    }

    /**
     * Performs provider CEP search.
     *
     * @param string $cep CEP
     * @return Contracts\Address
     * @throws CepGratisInvalidParameterException
     * @throws CepGratisTimeoutException
     */
    public function resolve($cep)
    {
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
}
