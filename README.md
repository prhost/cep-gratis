# CEP Grátis

Com esse pacote você poderá realizar consultas de CEP gratuitamente.

Para evitar problemas com indisponibilidade de serviços, a consulta é realizada paralelamente em providers diferentes:

* [Correios](http://www.buscacep.correios.com.br/sistemas/buscacep/)
* [Viacep](https://viacep.com.br/)
* [CEP Aberto](http://cepaberto.com/)
* [Widenet](https://apps.widenet.com.br/busca-cep/api-de-consulta)
* [Republica Virtual](https://www.republicavirtual.com.br/cep/)

A library irá retornar para você a resposta mais rápida, aumentando assim a performance da consulta.

### Como utilizar

Adicione a library

```shell
$ composer require Prhost/cep-gratis
```
    
Adicione o autoload.php do composer no seu arquivo PHP.

```php
require_once 'vendor/autoload.php';  
```

Agora basta chamar o método `CepGratis::search($cep)`

```php
use Prhost\CepGratis\CepGratis;

$address = CepGratis::search('31030080'); 
```

Um exemplo passando opções como o token do CEP Aberto

```php
use Prhost\CepGratis\CepGratis;

$address = CepGratis::search('31030080', ['token' => '123abc']); 
```

Outras formas:

```php
use Prhost\CepGratis\CepGratis;
use Prhost\CepGratis\Providers\CepAbertoProvider;

$cepGratis = new CepGratis();
$cepGratis->setOptions(['token' => '123abc']);
$cepGratis->addProvider(new CepAbertoProvider());
$cepGratis->setTimeout(15);
$address = $cepGratis->resolve('31030080'); 
```

### License

The MIT License (MIT)
