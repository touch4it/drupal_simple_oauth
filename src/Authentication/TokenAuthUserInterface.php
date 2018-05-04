<?php

namespace Drupal\simple_oauth\Authentication;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Session\AccountInterface;

interface TokenAuthUserInterface extends \IteratorAggregate, AccountInterface, CacheableDependencyInterface {

  /**
   * Get the token.
   *
   * @return \Drupal\simple_oauth\Entity\Oauth2TokenInterface
   *   The provided OAuth2 token.
   */
  public function getToken();

  /**
   * Whether a user has a certain role.
   *
   * @param string $rid
   *   The role ID to check.
   *
   * @return bool
   *   Returns TRUE if the user has the role, otherwise FALSE.
   */
  public function hasRole($rid);

}
