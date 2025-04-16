<?php

namespace Flossiraptor\Imds4azure\Service;

use GuzzleHttp\ClientInterface;
use Flossiraptor\Imds4azure\Token;
use Flossiraptor\Imds4azure\Exception\ApcuNotAvailableException;

/**
 * Add APCu caching to the Identity service.
 */
class IdentityCachedByApcu extends Identity {

  /**
   * {@inheritdoc}
   */
  public function __construct(ClientInterface $client) {
    if (!function_exists('apcu_enabled')) {
      throw new ApcuNotAvailableException('The APCu extension is not installed.');
    }
    parent::__construct($client);
  }

  /**
   * Default cache-key prefix for data stored in the APCu cache.
   */
  const DEFAULT_CACHE_KEY_PREFIX = 'MI_TOKEN';

  /**
   * Prefix to apply to the cache key.
   *
   * @var string
   */
  protected string $cacheKeyPrefix = self::DEFAULT_CACHE_KEY_PREFIX;

  /**
   * {@inheritdoc}
   */
  public function getCacheKeyPrefix() : string {
    return $this->cacheKeyPrefix;
  }

  /**
   * {@inheritdoc}
   */
  public function setCacheKeyPrefix(string $prefix) : self {
    $this->cacheKeyPrefix = $prefix;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getToken(string $resource, ?string $client_id = NULL) : Token {
    if (!\apcu_enabled()) {
      return parent::getToken($resource, $client_id);
    }

    $cacheKey = $this->getCacheKey($resource, $client_id);

    /** @var \Flossiraptor\Imds4azure\Token $token */
    $success = NULL;
    $token = \apcu_fetch($cacheKey, $success);
    if ($success) {
      $token->ensureTokenIsValid();
      return $token;
    }

    $token = parent::getToken($resource, $client_id);
    $ttl = $token->ttl();
    if ($ttl) {
      \apcu_store($cacheKey, $token, $ttl);
    }
   return $token;
  }

  /**
   * {@inheritdoc}
   */
  protected function getCacheKey(string $resource, ?string $client_id = NULL) : string {
    return sprintf(
      '%s:%s:%s',
      $this->cacheKeyPrefix,
      $resource,
      $client_id,
    );
  }

}
