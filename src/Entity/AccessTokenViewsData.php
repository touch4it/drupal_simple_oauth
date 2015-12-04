<?php

/**
 * @file
 * Contains \Drupal\simple_oauth\Entity\AccessToken.
 */

namespace Drupal\simple_oauth\Entity;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Access Token entities.
 */
class AccessTokenViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['access_token']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Access Token'),
      'help' => $this->t('The Access Token ID.'),
    );

    return $data;
  }

}
