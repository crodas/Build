<?php

class BasicTest extends phpunit_framework_testcase
{
    public function testBuild()
    {
        $GLOBALS['phpunit'] = $this;
        $GLOBALS['builded'] = false;
        $build = new crodas\Build(__DIR__);
        $build->build1([__FILE__]);
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
        $this->assertTrue(is_file($build->build1([__FILE__])));
        $this->assertFalse($GLOBALS['builded']);
        $build->save();
    }

    /**
     *  @dependsOn testRebuildDontBuild
     */
    public function testReBuildRebuild()
    {
        $GLOBALS['phpunit'] = $this;
        $GLOBALS['builded'] = false;
        touch(__DIR__ . '/tasks/one.php', time() + 5);
        $build = new crodas\Build(__DIR__);
        $this->assertTrue(is_file($build->build1([__FILE__])));
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
        touch(__DIR__ . '/tasks/one.php', time() + 15);
        $build = new crodas\Build(__DIR__);
        $build->productionMode();
        $this->assertTrue(is_file($build->build1([__FILE__])));
        $this->assertFalse($GLOBALS['builded']);
    }
}
