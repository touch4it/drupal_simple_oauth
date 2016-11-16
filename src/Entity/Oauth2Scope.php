<?php

namespace Drupal\simple_oauth\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;

/**
 * Defines the OAuth2 Scope entity.
 *
 * @ConfigEntityType(
 *   id = "oauth2_scope",
 *   label = @Translation("OAuth2 Scope"),
 *   handlers = {
 *     "list_builder" = "Drupal\simple_oauth\Oauth2ScopeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\simple_oauth\Entity\Form\Oauth2ScopeForm",
 *       "edit" = "Drupal\simple_oauth\Entity\Form\Oauth2ScopeForm",
 *       "delete" = "Drupal\simple_oauth\Entity\Form\Oauth2ScopeDeleteForm"
 *     },
 *     "access" = "Drupal\simple_oauth\LockableConfigEntityAccessControlHandler"
 *   },
 *   config_prefix = "oauth2_scope",
 *   admin_permission = "administer simple_oauth entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/people/accounts/oauth2_scope/{oauth2_scope}",
 *     "edit-form" = "/admin/config/people/accounts/oauth2_scope/{oauth2_scope}/edit",
 *     "delete-form" = "/admin/config/people/accounts/oauth2_scope/{oauth2_scope}/delete",
 *     "collection" = "/admin/config/people/accounts/visibility_group"
 *   }
 * )
 */
class Oauth2Scope extends ConfigEntityBase implements Oauth2ScopeInterface {

  use ConfigEntityLockableTrait;

  /**
   * The Access Scope ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Access Scope label.
   *
   * @var string
   */
  protected $label;

}
