<?php

/**
 *  @Task(build1)
 */
function build1($output, Array $files, Array $args, $builder)
{
    $GLOBALS['phpunit']->assertTrue($builder instanceof crodas\Build);
    $GLOBALS['builded'] = true;
    $builder->watch(__FILE__);
    file_put_contents($output, 'foobar-' . uniqid(True));
}
