<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\Authentication\Provider\OAuth2TokenBearerAuthenticationProviderInterface.
 */

namespace Drupal\oauth2_token\Authentication\Provider;
use Drupal\Core\Authentication\AuthenticationProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class OAuth2TokenBearerAuthenticationProviderInterface.
 *
 * @package Drupal\oauth2_token\Authentication\Provider
 */
interface OAuth2TokenBearerAuthenticationProviderInterface extends AuthenticationProviderInterface {


  /**
   * Gets the access token from the request.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request object.
   *
   * @return string
   *   The access token.
   *
   * @see http://tools.ietf.org/html/rfc6750
   */
  public static function getTokenValue(Request $request);

}
