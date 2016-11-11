<?php

namespace Drupal\simple_oauth;

use Drupal\Console\Command\TranslationTrait;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Access Token entities.
 *
 * @ingroup simple_oauth
 */
class Oauth2TokenListBuilder extends EntityListBuilder {

  use TranslationTrait;

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
    /* @var $entity \Drupal\simple_oauth\Entity\Oauth2Token */
    $row['resource'] = NULL;
    $row['type'] = NULL;
    if ($resource = $entity->get('resource')) {
      $row['resource'] = $resource->entity->label();
      $type = $resource->target_id == 'authentication' ? t('Refresh Token') : t('Access Token');
      $row['type'] = $type;
    }
    $row['user'] = NULL;
    if ($user = $entity->get('auth_user_id')) {
      $row['user'] = Link::createFromRoute($user->entity->label(), 'entity.user.canonical', array(
        'user' => $user->entity->id(),
      ));
    }
    $row['owner'] = NULL;
    if ($owner = $entity->get('user_id')) {
      $row['owner'] = Link::createFromRoute($owner->entity->label(), 'entity.user.canonical', array(
        'user' => $owner->entity->id(),
      ));
    }
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute($entity->label(), 'entity.oauth2_token.edit_form', array(
      'oauth2_token' => $entity->id(),
    ));
    $row['resource'] = NULL;

    return $row + parent::buildRow($entity);
  }

}
