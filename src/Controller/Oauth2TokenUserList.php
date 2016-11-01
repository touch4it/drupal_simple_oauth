<?php

namespace Drupal\simple_oauth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Element;
use Drupal\user\Entity\User;

class Oauth2TokenUserList extends ControllerBase {

  /**
   * Provide a list of tokens.
   */
  public function tokenList(User $user) {
    $entity_type = 'oauth2_token';
    $storage = $this
      ->entityTypeManager()
      ->getStorage($entity_type);
    $ids = $storage
      ->getQuery()
      ->condition('auth_user_id', $user->id())
      ->execute();
    if (empty($ids)) {
      return [
        '#markup' => $this->t('There are no tokens for this user.'),
      ];
    }
    $view_controller = $this->entityTypeManager()->getViewBuilder($entity_type);
    $tokens = $storage->loadMultiple($ids);

    return $view_controller->viewMultiple($tokens);
  }

}
