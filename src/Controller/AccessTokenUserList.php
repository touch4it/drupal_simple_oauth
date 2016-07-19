<?php

namespace Drupal\simple_oauth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Render\Element;
use Drupal\user\Entity\User;

class AccessTokenUserList extends ControllerBase {

  /**
   * Provide a list of tokens.
   */
  public function tokenList(User $user) {
    $entity_type = 'access_token';
    $storage = $this
      ->entityManager()
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
    $view_controller = $this->entityManager()->getViewBuilder($entity_type);
    $tokens = $storage->loadMultiple($ids);

    return $view_controller->viewMultiple($tokens);
  }

}
