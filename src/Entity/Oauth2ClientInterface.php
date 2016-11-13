<?php

namespace Drupal\simple_oauth\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Access Client entities.
 */
interface Oauth2ClientInterface extends ConfigEntityInterface  {

  /**
   * Get the default user associated with a client.
   *
   * @return \Drupal\user\UserInterface|array
   *   The default user associated to the client entity or an empty array if
   *   none could be found.
   */
  public function getDefaultUser();

}
