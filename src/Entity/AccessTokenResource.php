<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\Entity\AccessTokenResource.
 */

namespace Drupal\oauth2_token\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\oauth2_token\AccessTokenResourceInterface;

/**
 * Defines the Access Token Resource entity.
 *
 * @ConfigEntityType(
 *   id = "access_token_resource",
 *   label = @Translation("Access Token Resource"),
 *   handlers = {
 *     "list_builder" = "Drupal\oauth2_token\AccessTokenResourceListBuilder",
 *     "form" = {
 *       "add" = "Drupal\oauth2_token\Entity\Form\AccessTokenResourceForm",
 *       "edit" = "Drupal\oauth2_token\Entity\Form\AccessTokenResourceForm",
 *       "delete" = "Drupal\oauth2_token\Entity\Form\AccessTokenResourceDeleteForm"
 *     },
 *     "access" = "Drupal\oauth2_token\LockableConfigEntityAccessControlHandler"
 *   },
 *   config_prefix = "access_token_resource",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/people/accounts/access_token_resource/{access_token_resource}",
 *     "edit-form" = "/admin/config/people/accounts/access_token_resource/{access_token_resource}/edit",
 *     "delete-form" = "/admin/config/people/accounts/access_token_resource/{access_token_resource}/delete",
 *     "collection" = "/admin/config/people/accounts/visibility_group"
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
