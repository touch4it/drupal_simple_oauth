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
class Oauth2ClientListBuilder extends EntityListBuilder {

  use TranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['label'] = $this->t('Label');
    $header['secret'] = $this->t('Hashed Secret');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var $entity \Drupal\simple_oauth\Entity\Oauth2ClientInterface */
    $row['id'] = $entity->id();
    $row['label'] = Link::createFromRoute($entity->label(), 'entity.oauth2_client.edit_form', array(
      'oauth2_client' => $entity->id(),
    ));
    $row['secret'] = $entity->get('secret');

    return $row + parent::buildRow($entity);
  }

}
