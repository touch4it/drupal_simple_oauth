<?php

namespace Drupal\simple_oauth\PageCache;

use Drupal\Core\PageCache\RequestPolicyInterface;
use Drupal\simple_oauth\Authentication\Provider\SimpleOauthAuthenticationProvider;
use Drupal\simple_oauth\Server\ResourceServerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DisallowSimpleOauthRequests.
 *
 * @package Drupal\simple_oauth\PageCache
 */
class DisallowSimpleOauthRequests implements RequestPolicyInterface {

  /**
   * {@inheritdoc}
   */
  public function check(Request $request) {
    return SimpleOauthAuthenticationProvider::hasTokenValue($request) ? self::DENY : NULL;
  }

}
