<?php
use tomcao\util;
class CurlTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @test
	 */
	public function CanBeNew(){
		require_once(__dir__.'/../src/Curl.php');
		$curl = new util\Curl;
		$url = 'http://www.missevan.cn';
		if($curl->get($url)){
			echo gettype($curl->response);
			$this->expectOutputString('test');
		}
	}
}
