<?php

namespace Drupal\simple_oauth\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\simple_oauth\Oauth2TokenResourceInterface;

/**
 * Defines the OAuth2 Token Resource entity.
 *
 * @ConfigEntityType(
 *   id = "oauth2_token_resource",
 *   label = @Translation("OAuth2 Token Resource"),
 *   handlers = {
 *     "list_builder" = "Drupal\simple_oauth\AccessTokenResourceListBuilder",
 *     "form" = {
 *       "add" = "Drupal\simple_oauth\Entity\Form\Oauth2TokenResourceForm",
 *       "edit" = "Drupal\simple_oauth\Entity\Form\Oauth2TokenResourceForm",
 *       "delete" = "Drupal\simple_oauth\Entity\Form\Oauth2TokenResourceDeleteForm"
 *     },
 *     "access" = "Drupal\simple_oauth\LockableConfigEntityAccessControlHandler"
 *   },
 *   config_prefix = "oauth2_token_resource",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/people/accounts/oauth2_token_resource/{oauth2_token_resource}",
 *     "edit-form" = "/admin/config/people/accounts/oauth2_token_resource/{oauth2_token_resource}/edit",
 *     "delete-form" = "/admin/config/people/accounts/oauth2_token_resource/{oauth2_token_resource}/delete",
 *     "collection" = "/admin/config/people/accounts/visibility_group"
 *   }
 * )
 */
class Oauth2TokenResource extends ConfigEntityBase implements Oauth2TokenResourceInterface {

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
