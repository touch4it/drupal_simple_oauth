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
    $header['id'] = $this->t('ID');
    $header['type'] = $this->t('Type');
    $header['user'] = $this->t('User');
    $header['name'] = $this->t('Token');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\simple_oauth\Entity\Oauth2Token */
    $row['id'] = $entity->id();
    $row['type'] = $entity->bundle();
    $row['user'] = NULL;
    if (($user = $entity->get('auth_user_id')) && $user->entity) {
      $row['user'] = Link::createFromRoute($user->entity->label(), 'entity.user.canonical', array(
        'user' => $user->entity->id(),
      ));
    }
    $row['name'] = Link::createFromRoute($entity->label(), 'entity.oauth2_token.canonical', array(
      'oauth2_token' => $entity->id(),
    ));

    return $row + parent::buildRow($entity);
  }

}
