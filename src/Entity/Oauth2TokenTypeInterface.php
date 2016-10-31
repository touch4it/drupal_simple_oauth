<?php

namespace Drupal\simple_oauth\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Access Token Type entities.
 */
interface Oauth2TokenTypeInterface extends ConfigEntityLockableInterface  {

  /**
   * Get the description.
   *
   * @return string
   *   The description
   */
  public function getDescription();

  /**
   * Set the description.
   *
   * @param string $description
   *   The description
   */
  public function setDescription($description);

}
