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
* setParams
* setCookies
* unsetOptions
* unsetParams
* unsetCookies
* getOptions
* getParams
* getCookies

### Properties
* response
* responseCode

## API
### GET,POST
> 使用GET或者POST方法，发送一个request请求。 

@params  

* string $url  
> 你要访问的url

* boolean $raw  
> 如果返回的response为json格式，则设置false可转换成array。

@return

* boolean $flag  
> 请求是否成功



### HEAD,PUT,DELETE
> 基本与GET，POST相同。HEAD默认只打印头部信息

### setOptions,setParams,setCookies
> setOptions：设置curl选项  
> setParams：设置需要传递的参数  
> setCookies：设置Cookies

@params  

* array $arguments  //设置多个键值对

或者

* mixed $key
* mixed $value

@return

* Curl $curl

### unsetOptions,unsetParams,unsetCookies
> unsetOptions：取消设置curl选项  
> unsetParams：取消设置需要传递的参数  
> unsetCookies：取消设置Cookies

@params  

* array $keys //传递一个键数组，并取消所有存在的键的设置

或者

* mixed $key //传递一个键，并取消该键的设置

或者

* 不传参数 //取消所有的设置

@return

* Curl $curl

### getOptions,getParams,getCookies
> getOptions: 获取curl选项  
> getParams: 获取需要传递的参数
> getCookies：获取Cookies

@params 

* mixed $key

@returns

* mixed $value //获取键对应的值

或者

@不传递参数

@returns

* array $array //获取所有已经设置的值

###例子
```
<?php
require('Curl.php');
$curl = new Curl;
$url = 'http://ku.u.360.cn/single.php'; //设置需要访问的url
$params = [ 'start'=> 96, 'tag'=>'精品单机','order'=>'download']; //设置传递的参数
$cookies = ['test' => 'test'];

$curl->setParams($params)
	->setCookies($cookies)
	->setOptions(1234,'test');

//print_r($curl->getCookies());	//打印所有设置的cookie值
//print_r($curl->getCookies('test')); //打印名为test的cookie值

$curl->unsetCookies()->unsetOptions(1234);	//删除所有的cookie和名为1234的option

//print_r($curl->getCookies());
//print_r($curl->getCookies('test'));

if($curl->post($url)){	//使用post方法传递
	//print_r($curl->responseCode); //打印返回的responseCode
	print_r($curl->response);	//打印返回的response
}else{
	print_r($curl->responseCode);
	echo 'Internal Error';
}
?>
```