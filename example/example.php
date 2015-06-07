<?php

require __DIR__ . "/../vendor/autoload.php";

$b = new crodas\Build([__DIR__]);
$c = $b->build1(__FILE__);
var_dump($c);
