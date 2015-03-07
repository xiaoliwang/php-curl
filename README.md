curl
====
extension for Yii, Yii2 framework

## Requirements
- php 5.4+

## function

### main Methods
* GET
* POST
* PUT
* DELETE
* HEAD

### auxiliary Methods
* setOption
* unsetOption
* getOption
* setParams

### Properties
* response
* responseCode

## API
### GET
@params  

* string $url  
>the url you want to visit. 

* boolean $raw  
>if $raw is true, you can get a string of response directly. Else you can get an array encoded by json of response.

@return

* boolean $flag  
>the $flag shows your request is succeed or failed.

@example
```
<?php
require('Curl.php');
$curl = new Curl;
$url = 'http://www.baidu.com';
if($curl->get($url)){
	print_r($curl->responseCode);
	print_r($curl->response);
}else{
	echo 'Internal Error';
}
?>
```

### POST
@params  

* string $url  
>the url you want to visit. 

* boolean $raw  
>if $raw is true, you can get a string of response directly. Else you can get an array encoded by json of response.

@return

* boolean $flag  
>the $flag shows your request is succeed or failed.  

@example
```
<?php
require('Curl.php');
$curl = new Curl;
$url = 'http://www.baidu.com';
$formData = ['test'=>'test'];
$curl->setParams($formData);
if($curl->post($url)){
	print_r($curl->responseCode);
	print_r($curl->response);
}else{
	echo 'Internal Error';
}
?>
```