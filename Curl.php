<?php
/**
 * Curl wrapper PHP
 * @author TomCao <jiepengthegreat@126.com>
 * @version 1.0.0
 * @link 
 *
 */

class Curl
{
	public $response = null;
	
	public $responseCode = null;
	
	private $_options = [];
	
	private $_params = [];
	
	private $methods = ['GET','POST','PUT','DELETE','HEAD'];
	
	private $_defaultOptions = [
		CURLOPT_USERAGENT => 'Missevan Service Client',
		CURLOPT_HTTPHEADER => ['content-type:application/x-www-form-urlencoded'],
		CURLOPT_TIMEOUT => 60, 
		CURLOPT_CONNECTTIMEOUT => 30, 
		CURLOPT_RETURNTRANSFER => true,
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
			if($parameter==='_params' || 
				(is_integer($arg0) && $parameter==='_options')){
				$var[$arg0] = $arg1;
				return $this;
			}else{
				throw new Exception('Argument 1 passed to '.__METHOD__.
						' must be of the type integer, '.gettype($arg0).' given'.
						' ,called in '.__FILE__.' on line '.__LINE__.' and defined');
			}
		}else{
			throw new Exception('Wrong arguments '.$numargs.' for '.__METHOD__.
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
				foreach ($arg0 as $key)
					if(isset($var[$key]))
						unset($var[$key]);
					return $this;
			}elseif($parameter==='_params' || 
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
		$numargs = func_num_args();
		$allOptions = $this->_options + $this->_defaultOptions;
		if($numargs === 0){
			return $allOptions;
		}elseif($numargs === 1){
			$arg0 = func_get_arg(0);
			return isset($allOptions[$arg0]) ? $allOptions[$arg0] : false;
		}else{
			throw new Exception('Wrong arguments '.$numargs.' for '.__METHOD__.
					',called in '.__FILE__.' on line '.__LINE__.' and defined');
		}
	}
	
	public function getParams(){
		$numargs = func_num_args();
		$params = $this->_params;
		if($numargs === 0){
			return $params;
		}elseif($numargs === 1){
			$arg0 = func_get_arg(0);
			return isset($params[$arg0]) ? $params[$arg0] : false;
		}else{
			throw new Exception('Wrong arguments '.$numargs.' for '.__METHOD__.
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
			$this -> setOptions(CURLOPT_NOBODY, true);
			$this -> setOptions(CURLOPT_HEADER, true);
		}
		
		if($method === 'HEAD' || $method === 'GET'){
			$query = http_build_query($this->_params);
			$url = $url."?$query";
		}else{
			$this -> setOptions(CURLOPT_POSTFIELDS, $this->_params);
			$this -> setOptions(CURLOPT_HTTPHEADER,['content-type:multipart/form-data']);
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