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
* string response or array response
* responseCode

## API
### GET
@params
* string $url  //the url you want to post
* boolean $raw  //If $raw is true, you can get a response string directly. Else you can get an array encode by response json. 
@return
* boolean $flag //the $flag shows your post request is succeed or failed.

```
public function testPost(){
  $url = 'http://www.baidu.com';
  $curl = new Curl;
  if($curl -> POST($url)){
    print_r($curl->response);
  }else{
    echo $curl->responseCode;
  }
}
```


