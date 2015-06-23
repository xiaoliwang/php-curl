<?php
/**
 * PHP Curl wrapper 
 * 
 * @category PHP
 * @package tomcao\util
 * @author TomCao <jiepengthegreat@126.com>
 * @copyright 2014 TomCao
 * @lincese MIT
 * @version 1.0.0
 * @link https://github.com/xiaoliwang/php-curl
 */

namespace tomcao\util;
class Curl
{
	/**
	 * @var string $response 发送请求后返回的response
	 */
	public $response = null;
	
	/**
	 * @var integer $responseCode 收到的状态码，200
	 */
	public $responseCode = 0;
	
	private $_options = [];
	
	private $_params = [];
	
	private $_cookies = [];
	
	private $methods = ['GET','POST','PUT','DELETE','HEAD'];
	
	private $_defaultOptions = [
		CURLOPT_USERAGENT => 'TOMCAO HTTP CLIENT',
		CURLOPT_HTTPHEADER => ['content-type:application/x-www-form-urlencoded'],
		CURLOPT_TIMEOUT => 60, 
		CURLOPT_CONNECTTIMEOUT => 30, 
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HEADER => false, 
		CURLOPT_NOBODY => false,
		
	];
	
	public function setOptions()
	{
		$arguments = func_get_args();
		return $this->_addElements('_options',$arguments);
	}
	
	public function setParams()
	{
		$arguments = func_get_args();
		return $this->_addElements('_params',$arguments);
	}
	
	public function setCookies()
	{
		$arguments = func_get_args();
		return $this->_addElements('_cookies',$arguments);
	}
	
	private function _addElements($parameter,$arguments)
	{
		$count = count($arguments);
		$var = &$this->$parameter;
		if($count === 1){
			$arg0 = $arguments[0];
			if(is_array($arg0)){
				$var = $arg0 + $var;
				return $this;
			}else{
				throw new Exception('Argument 1 passed to '.__METHOD__.
						' must be of the type array, '.gettype($arg0).' given'.
						' ,called in '.__FILE__.' on line '.__LINE__.' and defined');
			}
		}elseif($count === 2){
			$arg0 = $arguments[0];
			$arg1 = $arguments[1];
			if($parameter==='_params' || $parameter==='_cookies' ||
				(is_integer($arg0) && $parameter==='_options')){
				$var[$arg0] = $arg1;
				return $this;
			}else{
				throw new Exception('Argument 1 passed to '.__METHOD__.
						' must be of the type integer, '.gettype($arg0).' given'.
						' ,called in '.__FILE__.' on line '.__LINE__.' and defined');
			}
		}else{
			throw new Exception('Wrong arguments '.$count.' for '.__METHOD__.
					',called in '.__FILE__.' on line '.__LINE__.' and defined');
		}
	}
	
	public function unsetOptions()
	{
		$arguments = func_get_args();
		return $this->_delElements('_options',$arguments);
	}
	
	public function unsetParams()
	{
		$arguments = func_get_args();
		return $this->_delElements('_params',$arguments);
	}
	
	public function unsetCookies()
	{
		$arguments = func_get_args();
		return $this->_delElements('_cookies',$arguments);
	}
	
	private function _delElements($parameter,$arguments)
	{
		$count = count($arguments);
		$var = &$this->$parameter; 
		if($count === 0){
			if(isset($var))
				$var = [];
			return $this;
		}elseif($count === 1){
			$arg0 = $arguments[0];
			if(is_array($arg0)){
				array_map(function($key) use(&$var){
					unset($var[$key]);
				}, array_filter($arg0,function($key) use(&$var){
					return isset($var[$key]);
				}));
				return $this;
			}elseif($parameter==='_params' || $parameter==='_cookies' ||
				(is_integer($arg0) && $parameter==='_options')){
				if(isset($var[$arg0]))
					unset($var[$arg0]);
				return $this;
			}else{
				throw new Exception('Argument 1 passed to '.__METHOD__.
						' must be of the type array or string, '.gettype($arg0).' given'.
						' ,called in '.__FILE__.' on line '.__LINE__.' and defined');
			}
		}else{
			throw new Exception('Wrong arguments '.$count.' for '.__METHOD__.
					',called in '.__FILE__.' on line '.__LINE__.' and defined');
		}
	}
	
	public function getOptions()
	{
		$arguments = func_get_args();
		return $this->_getElements('_options',$arguments);
	}
	
	public function getParams(){
		$arguments = func_get_args();
		return $this->_getElements('_params',$arguments);
	}
	
	public function getCookies(){
		$arguments = func_get_args();
		return $this->_getElements('_cookies',$arguments);
	}
	
	private function _getElements($parameter,$arguments){
		$count = count($arguments);
		$var = &$this->$parameter;
		if($parameter === '_options')
			$var = $var + $this->_defaultOptions;
		if($count === 0){
			return $var;
		}elseif($count === 1){
			$arg0 = $arguments[0];
			return isset($var[$arg0]) ? $var[$arg0] : false;
		}else{
			throw new Exception('Wrong arguments '.$count.' for '.__METHOD__.
					',called in '.__FILE__.' on line '.__LINE__.' and defined');
		}
	}
	
	public function __call($method, $args){
		$method = strtoupper($method);
		if(in_array($method, $this->methods, true)){
			if(!isset($args[0])) throw new Exception('Missing argument 1 for '.__METHOD__.
					',called in '.__FILE__.' on line '.__LINE__.' and defined');
			if(!is_string($args[0])) throw new Exception('Argument 1 passed to '.__METHOD__.
						' must be of the type string, '.gettype($arg0).' given'.
						' ,called in '.__FILE__.' on line '.__LINE__.' and defined');
			if(!isset($args[1])) $args[1] = true;
			return $this->_httpRequest($args[0], $args[1], $method);
		}else{
			throw new Exception(__CLASS__. ' and its behaviors do not have a method or closure named '.$method);
		}
	}
	
	private function _httpRequest($url, $raw, $method)
	{
		$this->setOptions(CURLOPT_CUSTOMREQUEST,$method);
	
		if($method === 'HEAD'){
			$this -> _defaultOptions[CURLOPT_NOBODY] = true;
			$this -> _defaultOptions[CURLOPT_HEADER] = true;
		}
		
		if($this->_params){
			if($method === 'HEAD' || $method === 'GET'){
				$query = http_build_query($this->_params);
				$url = $url."?$query";
			}else{
				$this -> setOptions(CURLOPT_POSTFIELDS, $this->_params);
				$this ->_defaultOptions[CURLOPT_HTTPHEADER] = ['content-type:multipart/form-data'];
			}
		}
		
		if($this->_cookies){
			$cookies = '';
			array_map(function($key,$value) use(&$cookies){
				$cookies .= "$key=$value;";
			}, array_keys($this->_cookies),array_values($this->_cookies));
			$this-> setOptions(CURLOPT_COOKIE, $cookies);
		}
	
		$ch = curl_init($url);
		curl_setopt_array($ch, $this->getOptions());
		$this -> response = $raw?curl_exec($ch):json_decode(curl_exec($ch));
		$this -> responseCode = curl_getinfo($ch,CURLINFO_HTTP_CODE );
		curl_close($ch);
		if($this->responseCode>199 && $this->responseCode<300){
			return true;
		}else{
			return false;
		}
	}
}