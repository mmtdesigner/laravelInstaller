<?php 

require "vendor/autoload.php";

use Symfony\Component\Console\Application;
use MMT\HelloCommand;
use MMT\LaravelInstaller;

$app = new Application("MMT custom console app", '1.0');

$app->add(new HelloCommand());
$app->add(new LaravelInstaller());

$app->run();
