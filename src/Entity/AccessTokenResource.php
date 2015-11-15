<?php

/**
 * @file
 * Contains \Drupal\token_auth\Entity\AccessTokenResource.
 */

namespace Drupal\token_auth\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\token_auth\AccessTokenResourceInterface;

/**
 * Defines the Access Token Resource entity.
 *
 * @ConfigEntityType(
 *   id = "access_token_resource",
 *   label = @Translation("Access Token Resource"),
 *   handlers = {
 *     "list_builder" = "Drupal\token_auth\AccessTokenResourceListBuilder",
 *     "form" = {
 *       "add" = "Drupal\token_auth\Form\AccessTokenResourceForm",
 *       "edit" = "Drupal\token_auth\Form\AccessTokenResourceForm",
 *       "delete" = "Drupal\token_auth\Form\AccessTokenResourceDeleteForm"
 *     }
 *   },
 *   config_prefix = "access_token_resource",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/structure/access_token_resource/{access_token_resource}",
 *     "edit-form" = "/admin/structure/access_token_resource/{access_token_resource}/edit",
 *     "delete-form" = "/admin/structure/access_token_resource/{access_token_resource}/delete",
 *     "collection" = "/admin/structure/visibility_group"
 *   }
 * )
 */
class AccessTokenResource extends ConfigEntityBase implements AccessTokenResourceInterface {
  /**
   * The Access Token Resource ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The Access Token Resource label.
   *
   * @var string
   */
  protected $label;

}
