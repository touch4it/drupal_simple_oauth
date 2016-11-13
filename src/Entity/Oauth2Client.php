<?php

namespace Drupal\simple_oauth\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityDescriptionInterface;

/**
 * Defines the OAuth2 Client entity.
 *
 * @ConfigEntityType(
 *   id = "oauth2_client",
 *   label = @Translation("OAuth2 Client"),
 *   handlers = {
 *     "list_builder" = "Drupal\simple_oauth\Oauth2ClientListBuilder",
 *     "form" = {
 *       "add" = "Drupal\simple_oauth\Entity\Form\Oauth2ClientForm",
 *       "edit" = "Drupal\simple_oauth\Entity\Form\Oauth2ClientForm",
 *       "delete" = "Drupal\Core\Entity\EntityDeleteForm"
 *     },
 *     "access" = "Drupal\Core\Entity\EntityAccessControlHandler"
 *   },
 *   config_prefix = "oauth2_client",
 *   admin_permission = "administer site configuration",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/config/people/simple_oauth/oauth2_client/{oauth2_client}",
 *     "edit-form" = "/admin/config/people/simple_oauth/oauth2_client/{oauth2_client}/edit",
 *     "delete-form" = "/admin/config/people/simple_oauth/oauth2_client/{oauth2_client}/delete",
 *     "collection" = "/admin/config/people/simple_oauth/clients"
 *   }
 * )
 */
class Oauth2Client extends ConfigEntityBase implements Oauth2ClientInterface, EntityDescriptionInterface {

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

  /**
   * The Client description.
   *
   * @var string
   */
  protected $description = '';

  /**
   * Redirect URI.
   *
   * @var string
   */
  protected $redirectUri;

  /**
   * Is confidential?
   *
   * @var bool
   */
  protected $isConfidential;

  /**
   * Client secret hash
   *
   * @var string
   */
  protected $secret;

  /**
   * Default user UUID.
   *
   * @var string
   */
  protected $defaultUserUuid;

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
  public function getDefaultUser() {
    $users = [];
    if ($user_uuid = $this->get('defaultUserUuid')) {
      $users = $this->entityTypeManager()
        ->getStorage('user')
        ->loadByProperties(['uuid' => $user_uuid]);
    }

    return reset($users);
  }


}
