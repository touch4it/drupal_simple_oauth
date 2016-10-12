<?php

namespace Drupal\simple_oauth\Cache\Context;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\Context\RequestStackCacheContextBase;
use Drupal\simple_oauth\Authentication\Provider\SimpleOauthAuthenticationProvider;

/**
 * Defines the BearerTokenCacheContext service, for "per bearer token" caching.
 *
 * Cache context ID: 'bearer_token'.
 */
class BearerTokenCacheContext extends RequestStackCacheContextBase {

  /**
   * {@inheritdoc}
   */
  public static function getLabel() {
    return t('Bearer Token');
  }

  /**
   * {@inheritdoc}
   */
  public function getContext() {
    $request = $this->requestStack->getCurrentRequest();
    return SimpleOauthAuthenticationProvider::getTokenValue($request);
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheableMetadata() {
    return new CacheableMetadata();
  }

}
