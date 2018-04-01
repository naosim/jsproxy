<?php
interface CurlRepository {
  /**
   * Return response from web.
   * @return CurlResponse
   */
  function get(Url $url):CurlResponse;
}
class CurlResponse {
  private $isSuccess;
  private $body;
  private function __construct($isSuccess, Body $body) {
    $this->isSuccess = $isSuccess;
    $this->body = $body;
  }
  public function getBody():Body {
    if($this->body == null) {
      throw new RuntimeException("body is null");
    }
    return $this->body;
  }

  public function isSuccess():bool {
    return $this->isSuccess;
  }

  public function isFailed():bool {
    return !$this->isSuccess();
  }

  public static function success($body):CurlResponse {
    return new CurlResponse(true, $body);
  }

  public static function failed():CurlResponse {
    return new CurlResponse(false, null);
  }
}

interface CacheRepository {
  function load(Url $url):CacheResponse;
  function save(Url $url, Body $body);
}
class CacheResponse {
  private $isSuccess;
  private $body;
  public function __construct($isSuccess, Body $body) {
    $this->isSuccess = $isSuccess;
    $this->body = $body;
  }
  public function getBody():Body {
    if($this->body == null) {
      throw new RuntimeException("body is null");
    }
    return $this->body;
  }

  public function isSuccess():bool {
    return $this->isSuccess;
  }

  public function isFailed():bool {
    return !$this->isSuccess();
  }

  public static function success($body):CacheResponse {
    return new CacheResponse(true, $body);
  }

  public static function failed():CacheResponse {
    return new CacheResponse(false, null);
  } 
}

class StringVo {
  private $value;
  public function __construct($value) {
    $this->value = $value;
  }
  public function getValue(): string {
    return $this->value;
  }
}

class Url extends StringVo {}
class Body extends StringVo {}

class FindWebService {
  private $curlRepository;
  private $cacheRepository;
  public function __construct(
    CurlRepository $curlRepository, 
    CacheRepository $cacheRepository
  ) {
    $this->curlRepository = $curlRepository;
    $this->cacheRepository = $cacheRepository;
  }

  public function get(Url $url):Body {
    // find from web
    $result = $this->curlRepository->get($url);
    if($result->isSuccess()) {
      $this->cacheRepository->save($url, $result->getBody());
      return $result->getBody();
    }

    // find from cache file
    $cacheRes = $this->cacheRepository->load($url);
    if($cacheRes->isSuccess()) {
        return $cacheRes->getBody();
    }

    throw new RuntimeException("content not found:" + $url->getValue());
  }
}

class FindCacheService {
  private $curlRepository;
  private $cacheRepository;
  public function __construct(
    CurlRepository $curlRepository, 
    CacheRepository $cacheRepository
  ) {
    $this->curlRepository = $curlRepository;
    $this->cacheRepository = $cacheRepository;
  }

  public function get(Url $url):Body {
    // find from cache file
    $cacheRes = $this->cacheRepository->load($url);
    if($cacheRes->isSuccess()) {
        return $cacheRes->getBody();
    }

    // find from web
    $result = $this->curlRepository->get($url);
    if($result->isSuccess()) {
      $this->cacheRepository->save($url, $result->getBody());
      return $result->getBody();
    }

    throw new RuntimeException("content not found:" + $url->getValue());
  }

}