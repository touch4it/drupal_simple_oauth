<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\AccessTokenListBuilder.
 */

namespace Drupal\oauth2_token;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Access Token entities.
 *
 * @ingroup oauth2_token
 */
class AccessTokenListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['type'] = $this->t('Type');
    $header['user'] = $this->t('Auth User');
    $header['owner'] = $this->t('Owner');
    $header['id'] = $this->t('ID');
    $header['name'] = $this->t('Token');
    $header['resource'] = $this->t('Resource');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\oauth2_token\Entity\AccessToken */
    $type = $entity->get('resource')->target_id == 'authentication' ? t('Refresh Token') : t('Access Token');
    $row['type'] = $type;
    $user = $entity->get('auth_user_id')->entity;
    $row['user'] = $this->l(
      $user->label(),
      new Url(
        'entity.user.canonical', array(
          'user' => $user->id(),
        )
      )
    );
    $owner = $entity->get('user_id')->entity;
    $row['owner'] = $this->l(
      $owner->label(),
      new Url(
        'entity.user.canonical', array(
          'user' => $owner->id(),
        )
      )
    );
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.access_token.edit_form', array(
          'access_token' => $entity->id(),
        )
      )
    );
    $row['resource'] = $entity->get('resource')->entity->label();

    return $row + parent::buildRow($entity);
  }

}
