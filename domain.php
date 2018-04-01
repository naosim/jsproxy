<?php
interface CurlRepository {
  function get(Url $url):CurlResponse;
}
interface CacheRepository {
  function load(Url $url):CacheResponse;
  function save(Url $url, Body $body);
}

class CurlResponse {
  private $isSuccess;
  private $body;
  public function __construct($isSuccess, Body $body) {
    $this->isSuccess = $isSuccess;
    $this->body = $body;
  }
  public function getBody():Body {
    return $this->body;
  }

  public function isSuccess():bool {
    return $this->isSuccess;
  }  
}

class CacheResponse {
  private $isSuccess;
  private $body;
  public function __construct($isSuccess, Body $body) {
    $this->isSuccess = $isSuccess;
    $this->body = $body;
  }
  public function getBody():Body {
    return $this->body;
  }

  public function isSuccess():bool {
    return $this->isSuccess;
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

class Url extends StringVo {
}

class Body extends StringVo {
}

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