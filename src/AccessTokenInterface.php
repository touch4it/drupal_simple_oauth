<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\AccessTokenInterface.
 */

namespace Drupal\oauth2_token;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Access Token entities.
 *
 * @ingroup oauth2_token
 */
interface AccessTokenInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.

}
