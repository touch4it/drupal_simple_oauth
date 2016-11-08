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
   * @return bool
   *   TRUE if there is an access token. FALSE otherwise.
   */
  public static function hasTokenValue(Request $request);

}
