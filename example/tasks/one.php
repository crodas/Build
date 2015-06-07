<?php

/**
 *  @Task(build1)
 */
function task($output, Array $files, Array $args, $builder)
{
    $args = microtime(true);
    $builder->watchFile(__FILE__);
    file_put_contents($output, serialize([$files, $args]));
}
