<?php

namespace Drupal\simple_oauth\Authentication;

use Drupal\Core\Session\AccountProxyInterface;

interface TokenAuthUserInterface extends AccountProxyInterface  {

  /**
   * Get the token.
   *
   * @return \Drupal\simple_oauth\Entity\Oauth2TokenInterface
   *   The provided OAuth2 token.
   */
  public function getToken();

}
