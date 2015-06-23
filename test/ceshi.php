<?php 
use tomcao\util;
require_once(__dir__.'/../src/Curl.php');

$curl = new util\Curl;

$curl->get('http://testslb.missevan.cn/mobile/site/version');

//echo $curl->response;
