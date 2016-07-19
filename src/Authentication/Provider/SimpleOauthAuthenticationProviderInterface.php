<?php

namespace Drupal\simple_oauth\Authentication\Provider;

use Drupal\Core\Authentication\AuthenticationProviderInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SimpleOauthAuthenticationProviderInterface.
 *
 * @package Drupal\simple_oauth\Authentication\Provider
 */
interface SimpleOauthAuthenticationProviderInterface extends AuthenticationProviderInterface {


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
