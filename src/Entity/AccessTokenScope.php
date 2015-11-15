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
 *     },
 *     "access" = "Drupal\token_auth\LockableConfigEntityAccessControlHandler"
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

  /**
   * The Access Token Resource label.
   *
   * @var string
   */
  protected $description = '';

  /**
   * Locked status.
   *
   * @var bool
   */
  protected $locked = FALSE;

  /**
   * {@inheritdoc}
   */
  public function getDescription() {
    return $this->description;
  }

  /**
   * {@inheritdoc}
   */
  public function setDescription($description) {
    $this->description = $description;
  }

  /**
   * {@inheritdoc}
   */
  public function isLocked() {
    return $this->locked;
  }

  /**
   * {@inheritdoc}
   */
  public function lock() {
    $this->locked = TRUE;
  }

  /**
   * {@inheritdoc}
   */
  public function unlock() {
    $this->locked = FALSE;
  }

}
