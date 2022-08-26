<?php

use MvcLTE\Http\Request;
use MvcLTE\Core\Application;
use MvcLTE\Http\Client\Factory;
use MvcLTE\Captcha\CaptchaFactory;

require_once(__DIR__."/../vendor/autoload.php");

$App = new Application();

$CaptchaFactory = new CaptchaFactory(new Request(),
	$App->make(Factory::class),
);

$Captcha = $CaptchaFactory->make('nullcaptcha',[
	"key" => 'NOT-NEEDED',
	"secret" => 'NOT-NEEDED',
]);

//Captcha style sheet
dump($Captcha->getStyle());

//Captcha Code
dump($Captcha->getCaptcha());

//Captcha script code
dump($Captcha->getScript());

