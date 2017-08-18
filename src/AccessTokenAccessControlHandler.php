<?php

namespace Drupal\simple_oauth;

use Drupal\simple_oauth_consumers\AccessControlHandler;

/**
 * Access controller for the Access Token entity.
 *
 * @see \Drupal\simple_oauth\Entity\AccessToken.
 */
class AccessTokenAccessControlHandler extends AccessControlHandler {

  protected static $name = 'simple_oauth';

}
