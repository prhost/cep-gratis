<?php

require_once 'vendor/autoload.php';

use Prhost\CepGratis\CepGratis;
use Prhost\CepGratis\Providers\CepAbertoProvider;

//$cepGratis = new CepGratis();
//$cepGratis->setOptions(['token' => 'f944751e6dd14d7a40bf18d4d8df1741']);
//$cepGratis->addProvider(new CepAbertoProvider());
//$cepGratis->setTimeout(15);
//$address = $cepGratis->resolve('31030080');

$address = CepGratis::search('31030080', ['token' => 'f944751e6dd14d7a40bf18d4d8df1741']);

echo '<pre>';
var_dump($address);
die;