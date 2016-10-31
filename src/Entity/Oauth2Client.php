<?php

namespace Drupal\simple_oauth\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the OAuth2 Client entity.
 *
 * @ConfigEntityType(
 *   id = "oauth2_client",
 *   label = @Translation("OAuth2 Client"),
 *   handlers = {
 *     "access" = "Drupal\simple_oauth\LockableConfigEntityAccessControlHandler"
 *   },
 *   config_prefix = "oauth2_client",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/people/accounts/oauth2_client/{oauth2_client}",
 *   }
 * )
 */
class Oauth2Client extends ConfigEntityBase implements Oauth2ClientInterface {

  /**
   * The Access Client ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Access Client label.
   *
   * @var string
   */
  protected $label;

}
