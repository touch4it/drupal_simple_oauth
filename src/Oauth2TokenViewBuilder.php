<?php

namespace Drupal\simple_oauth;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

/**
 * Class Oauth2TokenViewBuilder.
 *
 * @package Drupal\simple_oauth
 */
class Oauth2TokenViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  protected function alterBuild(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode) {
    parent::alterBuild($build, $entity, $display, $view_mode);
    if ($entity->id()) {
      $build['#contextual_links']['oauth2_token'] = array(
        'route_parameters' =>array('oauth2_token' => $entity->id()),
      );
    }
  }


}
