<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\Entity\AccessToken.
 */

namespace Drupal\oauth2_token\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\oauth2_token\AccessTokenInterface;
use Drupal\oauth2_token\AccessTokenValue;
use Drupal\user\UserInterface;

/**
 * Defines the Access Token entity.
 *
 * @ingroup oauth2_token
 *
 * @ContentEntityType(
 *   id = "access_token",
 *   label = @Translation("Access Token"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\oauth2_token\AccessTokenListBuilder",
 *     "views_data" = "Drupal\oauth2_token\Entity\AccessTokenViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\oauth2_token\Entity\Form\AccessTokenForm",
 *       "add" = "Drupal\oauth2_token\Entity\Form\AccessTokenForm",
 *       "edit" = "Drupal\oauth2_token\Entity\Form\AccessTokenForm",
 *       "delete" = "Drupal\oauth2_token\Entity\Form\AccessTokenDeleteForm",
 *     },
 *     "access" = "Drupal\oauth2_token\AccessTokenAccessControlHandler",
 *   },
 *   base_table = "access_token",
 *   admin_permission = "administer AccessToken entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "value",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/content/access_token/{access_token}",
 *     "edit-form" = "/admin/content/access_token/{access_token}/edit",
 *     "delete-form" = "/admin/content/access_token/{access_token}/delete"
 *   },
 *   field_ui_base_route = "access_token.settings"
 * )
 */
class AccessToken extends ContentEntityBase implements AccessTokenInterface {
  use EntityChangedTrait;

  /**
   * The default time while the token is valid.
   *
   * @var int
   */
  const DEFAULT_EXPIRATION_PERIOD = 120;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Access Token entity.'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Access Token entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Creator'))
      ->setDescription(t('The user ID of author of the Access Token entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'hidden',
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['auth_user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setDescription(t('The user ID of the user this access token is authenticating.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['resource'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Resource'))
      ->setDescription(t('The resource for this Access Token.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'access_token_resource')
      ->setSetting('handler', 'default')
      ->setDefaultValue('global')
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'entity_reference_label',
        'weight' => 4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 4,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['access_token_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Refresh Token'))
      ->setDescription(t('The Refresh Token to re-create an Access Token.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'access_token')
      ->setSetting('handler', 'default')
      // TODO: Only allow referencing tokens to the auth resource.
      // ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(FALSE)
      ->setDisplayOptions('form', array(
        'type' => 'hidden',
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['value'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Token'))
      ->setDescription(t('The token value.'))
      ->setSettings(array(
        'max_length' => 128,
        'text_processing' => 0,
      ))
      ->setRequired(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'string',
        'weight' => -4,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'hidden',
      ))
      ->setDisplayConfigurable('form', FALSE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'))
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'timestamp',
        'weight' => 1,
      ))
      ->setDisplayConfigurable('view', TRUE);

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    $fields['expire'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Expire'))
      ->setDefaultValueCallback(__CLASS__ . '::defaultExpiration')
      ->setDescription(t('The time when the token expires.'))
      ->setDisplayOptions('form', array(
        'type' => 'datetime_timestamp',
        'weight' => 1,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'timestamp',
        'weight' => 1,
      ))
      ->setRequired(TRUE)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);
    // Create the token value as a digestion of the values in the token. This
    // will allow us to check integrity of the token later.
    if ($this->get('value')->isEmpty()) {
      $value = AccessTokenValue::createFromValues($this->normalize())->digest();
      $this->set('value', $value);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);
    // If this is not a refresh token then create one.
    if (!$this->isRefreshToken()) {
      $this->addRefreshToken();
    }
    // If there is an access token for those conditions (resource + user) then
    // delete it.
    if ($this->isDuplicated()) {
      $this->deleteDuplicates();
    }
  }

  /**
   * Helper function that indicates if a token is a refresh token.
   *
   * @return bool
   *   TRUE if this is a refresh token. FALSE otherwise.
   */
  protected function isRefreshToken() {
    return !$this->get('access_token_id')
      ->isEmpty() && $this->get('resource')->target_id == 'authentication';
  }

  /**
   * Adds a refresh token and links it to this entity.
   */
  protected function addRefreshToken() {
    // Only add the refresh token of there is none associated.
    $has_refresh_token = (bool) $this
      ->entityManager()
      ->getStorage($this->getEntityTypeId())
      ->getQuery()
      ->condition('access_token_id', $this->id())
      ->count()
      ->execute();
    if ($has_refresh_token) {
      return;
    }
    $values = [
      'expire' => $this->get('expire')->value,
      'auth_user_id' => $this->get('auth_user_id')->target_id,
      'access_token_id' => $this->id(),
      'resource' => 'authentication',
      'created' => $this->getCreatedTime(),
      'changed' => $this->getChangedTime(),
      'access_id' => $this->id(),
    ];
    $refresh_token = $this
      ->entityManager()
      ->getStorage($this->getEntityType()->id())
      ->create($values);
    $refresh_token->save();
  }

  /**
   * Normalize the entity by extracting its important values.
   *
   * @return array
   *   The normalized entity.
   */
  protected function normalize() {
    $keys = ['auth_user_id', 'expire', 'created', 'resource'];
    $values = array_map(function ($item) {
      return $this->get($item)->getValue();
    }, $keys);
    return $values;
  }

  /**
   * Returns the defaul expiration.
   *
   * @return array
   *   The default expiration timestamp.
   */
  public static function defaultExpiration() {
    $expiration = \Drupal::config('oauth2_token.settings')->get('expiration') || static::DEFAULT_EXPIRATION_PERIOD;
    return [REQUEST_TIME + $expiration];
  }

  /**
   * Checks if there is already a token for the conditions of the current one.
   *
   * @return bool
   *   TRUE if there is at least one toke for the same conditions. FALSE
   *   otherwise.
   */
  protected function isDuplicated() {
    $query = $this->queryForDuplicates();
    return (bool) $query->count()->execute();
  }

  /**
   * Deletes the duplicated access tokens.
   *
   * @return int
   *   The number of deleted duplicates.
   */
  protected function deleteDuplicates() {
    $query = $this->queryForDuplicates();
    $results = $query->execute();
    if (empty($results)) {
      return 0;
    }
    $storage = $this->entityManager()->getStorage($this->getEntityTypeId());
    $tokens = $storage->loadMultiple(array_keys($results));
    $storage->delete($tokens);
    return count($results);
  }

  /**
   * Get the query to detect the duplicates for this token.
   *
   * @return \Drupal\Core\Entity\Query\QueryInterface
   */
  protected function queryForDuplicates() {
    $query = $this
      ->entityManager()
      ->getStorage($this->getEntityTypeId())
      ->getQuery('AND');
    $query->condition('id', $this->id(), '<>');
    $query->condition('auth_user_id', $this->get('auth_user_id')->target_id);
    $query->condition('resource', $this->get('resource')->target_id);
    return $query;
  }

}
