<?php

use Symfony\Component\Console\Application;

require_once realpath(__DIR__.'/../vendor/autoload.php');
require_once realpath(__DIR__.'/CodeCoverageGenerator.php');

$console = new Application();
$console->add(new CodeCoverageGenerator());
$console->run();
