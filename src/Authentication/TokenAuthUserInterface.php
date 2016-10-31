<?php

namespace Drupal\simple_oauth\Authentication;

use Drupal\simple_oauth\Oauth2TokenInterface;
use Drupal\user\UserInterface;

interface TokenAuthUserInterface extends \IteratorAggregate, UserInterface {

  /**
   * Get the token.
   *
   * @return Oauth2TokenInterface
   *   The provided OAuth2 token.
   */
  public function getToken();

}
