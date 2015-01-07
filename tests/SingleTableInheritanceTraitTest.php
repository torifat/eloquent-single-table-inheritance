<?php

use Mockery as m;

class SingleTableInheritanceTraitTest extends PHPUnit_Framework_TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testExtendedClassesShouldReturnProperTableName()
    {
        $child = m::mock('FirstChildModelStub');
        $child->shouldDeferMissing();
        $this->assertEquals('sti_base_table', $child->getTable());
    }
}

// Stubs

class ParentModelStub extends \Illuminate\Database\Eloquent\Model
{
    use \Rifat\EloquentSingleTableInheritance\SingleTableInheritanceTrait;

    protected $table = 'sti_base_table';
}

class FirstChildModelStub extends ParentModelStub
{

}
