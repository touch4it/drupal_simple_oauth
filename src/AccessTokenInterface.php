<?php

/**
 * @file
 * Contains \Drupal\token_auth\AccessTokenInterface.
 */

namespace Drupal\token_auth;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Access Token entities.
 *
 * @ingroup token_auth
 */
interface AccessTokenInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {
  // Add get/set methods for your configuration properties here.

}
