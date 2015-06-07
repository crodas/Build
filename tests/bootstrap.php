<?php

require __DIR__ . "/../vendor/autoload.php";

crodas\FileUtil\File::overrideFilepathGenerator(function($prefix) {
    return __DIR__ . '/generated/';
});

foreach (glob(__DIR__ . '/generated/*') as $f) {
    unlink($f);
}
