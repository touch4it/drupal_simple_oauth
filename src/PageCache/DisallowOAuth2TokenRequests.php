<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\PageCache\DisallowOAuth2TokenRequests.
 */

namespace Drupal\oauth2_token\PageCache;
use Drupal\Core\PageCache\RequestPolicyInterface;
use Drupal\oauth2_token\Authentication\Provider\OAuth2TokenBearerAuthenticationProvider;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DisallowOAuth2TokenRequests.
 *
 * @package Drupal\oauth2_token\PageCache
 */
class DisallowOAuth2TokenRequests implements RequestPolicyInterface {

  /**
   * {@inheritdoc}
   */
  public function check(Request $request) {
    return OAuth2TokenBearerAuthenticationProvider::getTokenValue($request) ? self::DENY : NULL;
  }

}
