<?php

class BasicTest extends phpunit_framework_testcase
{
    public function testBuild()
    {
        $GLOBALS['phpunit'] = $this;
        $GLOBALS['builded'] = false;
        $build = new crodas\Build(__DIR__);
        $build->build1([__FILE__, __DIR__]);
        $this->assertTrue($GLOBALS['builded']);
        $build->save();
    }

    /**
     *  @dependsOn testBuild
     */
    public function testRebuildDontBuild()
    {
        $GLOBALS['phpunit'] = $this;
        $GLOBALS['builded'] = false;
        $build = new crodas\Build(__DIR__);
        $this->assertTrue(is_file($build->build1([__FILE__, __DIR__])));
        $this->assertFalse($GLOBALS['builded']);
        $build->save();
    }

    protected function touch($file)
    {
        touch($file, filemtime($file) + 5);
    }

    /**
     *  @dependsOn testRebuildDontBuild
     */
    public function testReBuildRebuild()
    {
        $GLOBALS['phpunit'] = $this;
        $GLOBALS['builded'] = false;
        $this->touch(__DIR__ . '/tasks/one.php');
        $build = new crodas\Build(__DIR__);
        $this->assertTrue(is_file($build->build1([__FILE__, __DIR__])));
        $this->assertTrue($GLOBALS['builded']);
        $build->save();
    }

    /**
     *  @dependsOn testRebuildRebuild
     */
    public function testProductionModeDontBuild()
    {
        $GLOBALS['phpunit'] = $this;
        $GLOBALS['builded'] = false;
        $this->touch(__DIR__ . '/tasks/one.php');
        crodas\Build::productionMode();
        $build = new crodas\Build(__DIR__);
        $file  = $build->build1([__FILE__, __DIR__]);
        $this->assertTrue(is_file($file));
        $this->assertFalse($GLOBALS['builded']);

        unlink($file);
        $this->assertFalse(is_file($file));

        $build = new crodas\Build(__DIR__);
        $file2 = $build->build1([__FILE__, __DIR__]);
        $this->assertTrue(is_file($file2));
        $this->assertEquals($file, $file2);
        $this->assertTrue($GLOBALS['builded']);
    }
}
