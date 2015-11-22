<?php
/**
 * @file
 * Contains \Drupal\oauth2_token\Controller\AccessTokenUserList.
 */

namespace Drupal\oauth2_token\Controller;

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
    $elements = $view_controller->viewMultiple($tokens);
    $children = Element::children($elements);
    foreach ($children as $delta) {
      $elements[$delta]['operations'] = [
        '#type' => 'operations',
        '#links' => [
          'edit' => [
            'title' => $this->t('Edit'),
            'weight' => 10,
            'url' => $elements[$delta]['#access_token']->urlInfo('edit-form'),
          ],
          'delete' => [
            'title' => $this->t('Delete'),
            'weight' => 100,
            'url' => $elements[$delta]['#access_token']->urlInfo('delete-form'),
          ],
        ],
        '#weight' => -100,
      ];
    }

    return $elements;
  }

}
