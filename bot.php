<?php
require_once __DIR__ . '/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use AdielSeffrinBot\AdielSeffrinBot;

(new DotEnv())->load(__DIR__ . DIRECTORY_SEPARATOR. '.env');
$adiel = new AdielSeffrinBot();
$adiel->run();
