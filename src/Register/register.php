<?php

use Console\App\Commands\CreateProjectCommand;
use Symfony\Component\Console\Application;

$app = new Application();
$app->add(new CreateProjectCommand());
$app->run();
