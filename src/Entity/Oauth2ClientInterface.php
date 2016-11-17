<?php

namespace Drupal\simple_oauth\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Access Token entities.
 *
 * @ingroup simple_oauth
 */
interface Oauth2ClientInterface extends ContentEntityInterface, EntityOwnerInterface {

  /**
   * Returns the hashed secret.
   *
   * @return string
   *   The secret password.
   */
  public function getSecret();

  /**
   * Sets the client secret.
   *
   * @param string $secret
   *   The new unhashed secret.
   *
   * @return \Drupal\simple_oauth\Entity\Oauth2ClientInterface
   *   The called client entity.
   */
  public function setSecret($secret);

}
