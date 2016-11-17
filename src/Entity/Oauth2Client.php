<?php

namespace Drupal\simple_oauth\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\user\UserInterface;

/**
 * Defines the Oauth2 Token entity.
 *
 * @ingroup simple_oauth
 *
 * @ContentEntityType(
 *   id = "oauth2_client",
 *   label = @Translation("OAuth2 client"),
 *   handlers = {
 *     "list_builder" = "Drupal\simple_oauth\Oauth2ClientListBuilder",
 *     "form" = {
 *       "delete" = "Drupal\simple_oauth\Entity\Form\Oauth2ClientDeleteForm",
 *     },
 *     "access" = "Drupal\simple_oauth\AccessTokenAccessControlHandler",
 *   },
 *   base_table = "oauth2_client",
 *   admin_permission = "administer simple_oauth entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid"
 *   },
 *   links = {
 *     "canonical" = "/admin/content/simple_oauth/{oauth2_client}",
 *     "edit-form" = "/admin/content/simple_oauth/{oauth2_client}/edit",
 *     "delete-form" = "/admin/content/simple_oauth/{oauth2_client}/delete"
 *   }
 * )
 */
class Oauth2Client extends ContentEntityBase implements Oauth2ClientInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    foreach (array_keys($this->getTranslationLanguages()) as $langcode) {
      $translation = $this->getTranslation($langcode);

      // If no owner has been set explicitly, make the anonymous user the owner.
      if (!$translation->getOwner()) {
        $translation->setOwnerId(0);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['owner_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Authored by'))
      ->setDescription(new TranslatableMarkup('The username of the client author.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setDefaultValueCallback('Drupal\simple_oauth\Entity\Oauth2Client::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['label'] = BaseFieldDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Label'))
      ->setDescription(new TranslatableMarkup('The client label.'))
      ->setRequired(TRUE)
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setSettings(array(
        'max_length' => 128,
        'text_processing' => 0,
      ))
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'timestamp',
        'weight' => 4,
      ))
      ->setDisplayOptions('form', array(
        'weight' => 2,
      ));

    $fields['secret'] = BaseFieldDefinition::create('password')
      ->setLabel(new TranslatableMarkup('Secret'))
      ->setDescription(new TranslatableMarkup('The secret key of this client (hashed).'));

    $fields['confidential'] = BaseFieldDefinition::create('boolean')
      ->setLabel(new TranslatableMarkup('Is Confidential?'))
      ->setDescription(new TranslatableMarkup('A boolean indicating whether the client secret needs to be validated or not.'))
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'boolean',
        'weight' => 8,
      ))
      ->setRevisionable(TRUE)
      ->setTranslatable(TRUE)
      ->setDefaultValue(TRUE);

    $fields['redirect'] = BaseFieldDefinition::create('uri')
      ->setLabel(new TranslatableMarkup('Redirect URI'))
      ->setDescription(new TranslatableMarkup('The URI this client will redirect to when needed.'))
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'weight' => 4,
      ))
      ->setDisplayConfigurable('view', TRUE)
      ->setTranslatable(TRUE)
      // URIs are not length limited by RFC 2616, but we can only store 255
      // characters in our comment DB schema.
      ->setSetting('max_length', 255);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('User'))
      ->setDescription(new TranslatableMarkup('When no specific user is authenticated Drupal will use this user as the author of all the actions made.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'entity_reference_label',
        'weight' => 1,
      ))
      ->setCardinality(1)
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ));

    $fields['roles'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(new TranslatableMarkup('Scopes'))
      ->setDescription(new TranslatableMarkup('The roles for this Client. OAuth2 scopes are implemented as Drupal roles.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user_role')
      ->setSetting('handler', 'default')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setTranslatable(FALSE)
      ->setDisplayOptions('view', array(
        'label' => 'inline',
        'type' => 'entity_reference_label',
        'weight' => 3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete_tags',
        'weight' => 3,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('owner_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->getEntityKey('owner_id');
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('owner_id', $uid);

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('owner_id', $account->id());

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getSecret() {
    return $this->get('secret')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setSecret($secret) {
    $this->get('secret')->value = $secret;
    return $this;
  }

  /**
   * Default value callback for 'uid' base field definition.
   *
   * @see ::baseFieldDefinitions()
   *
   * @return array
   *   An array of default values.
   */
  public static function getCurrentUserId() {
    return array(\Drupal::currentUser()->id());
  }

}
