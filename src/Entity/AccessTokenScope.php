<?php

/**
 * @file
 * Contains \Drupal\token_auth\Entity\AccessTokenScope.
 */

namespace Drupal\token_auth\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\token_auth\AccessTokenScopeInterface;

/**
 * Defines the Access Token Scope entity.
 *
 * @ConfigEntityType(
 *   id = "access_token_scope",
 *   label = @Translation("Access Token Scope"),
 *   handlers = {
 *     "list_builder" = "Drupal\token_auth\AccessTokenScopeListBuilder",
 *     "form" = {
 *       "add" = "Drupal\token_auth\Form\AccessTokenScopeForm",
 *       "edit" = "Drupal\token_auth\Form\AccessTokenScopeForm",
 *       "delete" = "Drupal\token_auth\Form\AccessTokenScopeDeleteForm"
 *     }
 *   },
 *   config_prefix = "access_token_scope",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/access_token_scope/{access_token_scope}",
 *     "edit-form" = "/admin/structure/access_token_scope/{access_token_scope}/edit",
 *     "delete-form" = "/admin/structure/access_token_scope/{access_token_scope}/delete",
 *     "collection" = "/admin/structure/visibility_group"
 *   }
 * )
 */
class AccessTokenScope extends ConfigEntityBase implements AccessTokenScopeInterface {
  /**
   * The Access Token Scope ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Access Token Scope label.
   *
   * @var string
   */
  protected $label;

}
