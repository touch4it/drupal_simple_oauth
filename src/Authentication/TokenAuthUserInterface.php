<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\Authentication\TokenAuthUserInterface.
 */

namespace Drupal\oauth2_token\Authentication;


use Drupal\oauth2_token\AccessTokenInterface;
use Drupal\user\UserInterface;

interface TokenAuthUserInterface extends \IteratorAggregate, UserInterface {

  /**
   * Get the token.
   *
   * @return AccessTokenInterface
   *   The provided OAuth2 token.
   */
  public function getToken();

}