<?php

namespace crodas\Build;

class TFunction
{
    protected $file;
    protected $callable;
    protected $static;

    public function __construct($file, $callable, $static = false)
    {
        $this->file = $file;
        $this->callable = $callable;
        $this->static = $static;
    }

    public function getName()
    {
        return $this->callable;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function exec()
    {
        $args = func_get_args();
        $fnc  = $this->callable;
        if (!is_callable($fnc)) {
            require $this->file;
        }
        if (!$this->static && is_array($fnc)) {
            $fnc[0] = new $fnc[0];
        }
        return call_user_func_array($fnc, $args);
    }
}
