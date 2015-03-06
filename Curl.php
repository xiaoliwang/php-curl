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
	
	public function setOption()
	{
		$numargs = func_num_args();
		if($numargs === 1){
			$arg0 = func_get_arg(0);
			if(gettype($arg0) === 'array'){
				foreach ($arg0 as $key => $value)
					$this->_options[$key] = $value;
				return $this;
			}else{
				throw new Exception('Argument 1 passed to '.__METHOD__.
						' must be of the type array, '.typeof($arg0).' given'.
						' ,called in '.__FILE__.' on line '.__LINE__.' and defined');
			}
		}elseif($numargs === 2){
			$arg0 = func_get_arg(0);
			$arg1 = func_get_arg(1);
			if(gettype($arg0) === 'string'){
				$this->_options[$arg0] = $arg1;
				return $this;
			}else{
				throw new Exception('Argument 1 passed to '.__METHOD__.
						' must be of the type string, '.typeof($arg0).' given'.
						' ,called in '.__FILE__.' on line '.__LINE__.' and defined');
			}
		}else{
			throw new Exception('Wrong arguments '.$numargs.' for '.__METHOD__.
					',called in '.__FILE__.' on line '.__LINE__.' and defined');
		}
		
	}
	
	public function unsetOption()
	{
		$numargs = func_num_args();
		if($numargs === 0){
			if(isset($this->_options))
				$this->_options = [];
			return $this;
		}elseif($numargs === 1){
			$arg0 = func_get_arg(0);
			if(gettype($arg0) === 'string'){
				if(isset($this->_options[$arg0]))
					unset($this->_options[$arg0]);
				return $this;
			}elseif(gettype($arg0) === 'array'){
				foreach ($arg0 as $key)
					if(isset($this->_options[$key]))
						unset($this->_options[$key]);
					return $this;
			}else{
				throw new Exception('Argument 1 passed to '.__METHOD__.
						' must be of the type array or string, '.typeof($arg0).' given'.
						' ,called in '.__FILE__.' on line '.__LINE__.' and defined');
			}
		}else{
			throw new Exception('Wrong arguments '.$numargs.' for '.__METHOD__.
					',called in '.__FILE__.' on line '.__LINE__.' and defined');
		}
	}
	
	public function getOption()
	{
		$options = $this->_options + $this->_defaultOptions;
		$numargs = func_num_args();
		if($numargs === 0){
			return $options;
		}elseif($numargs === 1){
			if(gettype($arg0) === 'string'){
				return isset($allOptions[$arg0]) ? $allOptions[$arg0] : false;
			}else{
				throw new Exception('Argument 1 passed to '.__METHOD__.
						' must be of the type string, '.typeof($arg0).' given'.
						' ,called in '.__FILE__.' on line '.__LINE__.' and defined');
			}
		}else{
			throw new Exception('Wrong arguments '.$numargs.' for '.__METHOD__.
					',called in '.__FILE__.' on line '.__LINE__.' and defined');
		}
	}
	
	public function setParams($params)
	{
		if(gettype($params) === 'string'){
			$this->setOption(CURLOPT_POSTFIELDS, urlencode($params));
		}elseif(gettype($params) === 'array'){
			$this->setOption(CURLOPT_POSTFIELDS, $params);
		}else{
			throw new Exception('Argument 1 passed to '.__METHOD__.
						' must be of the type string or array, '.typeof($arg0).' given'.
						' ,called in '.__FILE__.' on line '.__LINE__.' and defined');
		}
	}
	
	public function __call($method, $args){
		$method = strtoupper($method);
		if(in_array($method, $this->methods, true)){
			if(!isset($args[0])) throw new Exception('Missing argument 1 for '.__METHOD__.
					',called in '.__FILE__.' on line '.__LINE__.' and defined');
			if(!isset($args[1])) $args[1] = true;
			return $this->_httpRequest($args[0], $args[1], $method);
		}else{
			throw new Exception(__CLASS__. ' and its behaviors do not have a method or closure named '.$method);
		}
	}
	
	private function _httpRequest($url, $raw, $method)
	{
		$this->setOption(CURLOPT_CUSTOMREQUEST,$method);
	
		if($method === 'HEAD'){
			$this -> setOption(CURLOPT_NOBODY, true);
			$this -> setOption(CURLOPT_HEADER, true);
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