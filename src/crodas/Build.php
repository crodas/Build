<?php

namespace crodas;

use crodas\FileUtil\File;
use Notoj\Filesystem;

class Build
{
    protected $notoj;
    protected $tmp;
    protected $stack;
    protected $times = array();
    protected $isDry = false;
    protected $prod  = false;

    public function productionMode()
    {
        $this->prod = true;
        return $this;
    }
    
    public function save()
    {
        if ($this->isDry) {
            $this->times = array_filter($this->times);
            $content = file_get_contents(__DIR__ . '/Template.php');
            $content = str_replace('__RETURN__', var_export($this->times, true), $content);
            File::write($this->tmp, $content);
            $this->isDry = true;
        }
    }

    public function __destruct()
    {
        $this->save();
    }

    public function __construct($files, $tmp = '')
    {
        $this->tmp   = empty($tmp) ? File::generateFilePath('build', serialize($files)) : $tmp;
        $this->notoj = new Filesystem((array)$files);
        foreach ($this->notoj->get('Task', 'Callable') as $ann) {
            $name = current($ann->getArgs());
            $this->tasks[$name] = $ann->getObject();
        }
        if (is_readable($this->tmp)) {
            $this->times = (array)include $this->tmp;
        }
    }

    public function watch($files)
    {
        $arg = $this->stack[ count($this->stack)-1 ];
        foreach ((array)$files as $file) {
            $this->times['custom'][$arg][$file] = true;
        }
    }

    protected function watchFiles($target, $watching)
    {
        clearstatcache();
        if (!empty($this->times['custom'][$target])) {
            foreach ($this->times['custom'][$target] as $key => $f) {
                if (is_readable($key)) {
                    $this->times['custom'][$target][$key] = filemtime($key);
                } else {
                    unset($this->times['custom'][$target][$key]);
                }
            }
        }

        foreach ($watching as $file) {
            $this->times[$file] = filemtime($file);
        }
    }

    // needBuilding {{{
    /**
     *  needBuilding
     */
    protected function needBuilding($target, $watching)
    {
        if ($this->prod) {
            return !file_exists($target);
        }
        $needsBuild = false;
        foreach ($watching as $file) {
            if (empty($this->times[$file]) || !is_readable($file) || filemtime($file) > $this->times[$file]) {
                $needsBuild = true;
                break;
            }
        }

        if (!empty($this->times['custom'][$target])) {
            foreach ($this->times['custom'][$target] as $file => $ts) {
                if (!is_file($file) || filemtime($file) > $ts) {
                    $needsBuild = true;
                    break;
                }
            }
        }

        return $needsBuild;
    }
    // }}}

    // build($name, $target, Array $fils, Array $args) {{{
    /**
     *  build($name, $target, Array $files, Array $args = array())
     *
     *  Build a given task with the given arguments.
     *
     *  @return bool    Return true if the build happen or false otherwise
     */
    public function build($name, $target, Array $files, Array $args = array())
    {
        if (empty($this->tasks[$name])) {
            throw new \RuntimeException("Don't konw how to build {$name}");
        }

        $watching = array_merge([$target], $files);
        if (!$this->needBuilding($target, $watching)) {
            return $target;
        }

        $this->stack[] = $target;
        $this->tasks[$name]->exec($target, $files, $args, $this);
        array_pop($this->stack);

        $this->watchFiles($target, $watching);

        $this->isDry = true;
        return $target;
    }
    // }}}

    public function __call($name, Array $arguments)
    {
        $strid = serialize($arguments);
        $files = [];
        $args  = [];

        if (is_array($arguments[0])) {
            $files = $arguments[0];
        } else {
            $files = $arguments;
        }

        if (count($arguments) == 2 && is_array($arguments[1])) {
            $args = $arguments[1];
        }

        return $this->build($name, File::generateFilePath('build', $name, $strid), $files, $args);
    }
}
