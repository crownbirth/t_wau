<?php require_once('../Connections/tams.php'); ?> 
<?php require_once('lib/nusoap.php'); ?>
<?php

// Create a new SOAP client
$client = new nusoap_client("http://localhost/wauucurr/transaction/transaction.php?wsdl", 'wsdl');
$c = $client->getProxy();

// Always create a parameter array
$params = array();

//$result = $c->checkTransaction('2015-A3962E-AC');

$result = $c->approveTransaction(
            array(
                'transId' => '2015-A3962E-AC',
                'bankRef' => '2015-A3962E-AC',
                'payDate' => '20150122'
            )
        );


var_dump($result);