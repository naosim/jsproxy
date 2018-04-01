<?php
require_once 'domain.php';

class CurlRepositoryImpl implements CurlRepository {
  function get(Url $url):CurlResponse {
      $option = [
          CURLOPT_RETURNTRANSFER => true, //文字列として返す
          CURLOPT_TIMEOUT        => 3, // タイムアウト時間
      ];
  
      $ch = curl_init($url->getValue());
      curl_setopt_array($ch, $option);
  
      $result    = curl_exec($ch);
      $info    = curl_getinfo($ch);
      $errorNo = curl_errno($ch);
  
      // OK以外はエラーなので空白配列を返す
      if ($errorNo !== CURLE_OK) {
          // 詳しくエラーハンドリングしたい場合はerrorNoで確認
          // タイムアウトの場合はCURLE_OPERATION_TIMEDOUT
          return new CurlResponse(false, null);
      }
  
      // 200以外のステータスコードは失敗とみなし空配列を返す
      if ($info['http_code'] !== 200) {
          return new CurlResponse(false, null);
      }
  
      return new CurlResponse(true, new Body($result));
  }
}

class CacheRepositoryImpl implements CacheRepository {
  function convertFile(Url $url) {
      return './cache/' . md5($url->getValue());
  }

  function load(Url $url):CacheResponse {
      $result = file_get_contents($this->convertFile($url));
      if($result === false) {
          return new CacheResponse(false, null);
      }
      return new CacheResponse(true, new Body($result));

  }
  function save(Url $url, Body $body) {
      file_put_contents($this->convertFile($url), $body->getValue());
  }
}