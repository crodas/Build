<?php

class xxx 
{

/**
 *  @Task(build1)
 */
function build1($output, Array $files, Array $args, $builder)
{
    $GLOBALS['phpunit']->assertTrue(!empty($args));
    $GLOBALS['phpunit']->assertTrue($builder instanceof crodas\Build);
    $GLOBALS['builded'] = true;
    $builder->watch(__FILE__);
    $builder->watch(__DIR__);
    file_put_contents($output, 'foobar-' . uniqid(True));
}

}

