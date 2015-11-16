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
    $header['id'] = $this->t('Access Token ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\token_auth\Entity\AccessToken */
    $row['id'] = $entity->id();
    $row['name'] = $this->l(
      $entity->label(),
      new Url(
        'entity.access_token.edit_form', array(
          'access_token' => $entity->id(),
        )
      )
    );
    return $row + parent::buildRow($entity);
  }

}
