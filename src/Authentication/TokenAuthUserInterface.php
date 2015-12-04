<?php

/**
 * @file
 * Contains \Drupal\simple_oauth\Authentication\TokenAuthUserInterface.
 */

namespace Drupal\simple_oauth\Authentication;


use Drupal\simple_oauth\AccessTokenInterface;
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
