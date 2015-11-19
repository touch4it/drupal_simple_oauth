<?php

/**
 * @file
 * Contains \Drupal\token_auth\AccessTokenListBuilder.
 */

namespace Drupal\token_auth;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Access Token entities.
 *
 * @ingroup token_auth
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
    $header['scopes'] = $this->t('Scopes');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\token_auth\Entity\AccessToken */
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
    $scopes = [];
    foreach ($entity->get('scopes') as $scope) {
      $scopes[] = $scope->entity->label();
    }
    $row['scopes'] = empty($scope) ? t('- None -') : implode(', ', $scopes);

    return $row + parent::buildRow($entity);
  }

}
