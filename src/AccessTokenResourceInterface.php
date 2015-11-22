<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\AccessTokenResourceInterface.
 */

namespace Drupal\oauth2_token;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Access Token Resource entities.
 */
interface AccessTokenResourceInterface extends ConfigEntityInterface {

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

  /**
   * Checks if the entity is locked against changes.
   *
   * @return bool
   */
  public function isLocked();

  /**
   * Locks the entity.
   */
  public function lock();

  /**
   * Unlocks the entity.
   */
  public function unlock();

}
