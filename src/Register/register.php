<?php

use Console\App\Commands\CreateProjectCommand;
use Symfony\Component\Console\Application;

$app =  new Symfony\Component\Console\Application('GetInnotized Installer', '1.0.0');
$app->add(new CreateProjectCommand());
$app->run();
