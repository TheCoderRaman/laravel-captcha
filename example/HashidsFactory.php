<?php

require_once(__DIR__."/../vendor/autoload.php");

use MvcLTE\Hashids\HashidsFactory;

$HashidsFactory = new HashidsFactory();

$Hashids = $HashidsFactory->make([
	"salt" => 'Factory Test',
	"length" => 10,
	"alphabet" => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
]);

//Original ID
echo "Original: ".$ID = 4815162342;

//New line
echo "\n";

// We're done here - how easy was that, it just works!
echo "Encoded: ".$ID = $Hashids->encode($ID);

//New line
echo "\n";

// This example is simple and there are far more methods available.
echo "Decoded: ".$Hashids->decode($ID)[0];


