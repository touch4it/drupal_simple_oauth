<?php

namespace Drupal\simple_oauth;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Access Token entity.
 *
 * @see \Drupal\simple_oauth\Entity\Oauth2Token.
 */
class Oauth2TokenAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    // Permissions only apply to own entities.
    if ($account->id() != $entity->get('auth_user_id')->target_id) {
      return AccessResult::forbidden();
    }
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view own simple_oauth entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit own simple_oauth entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete own simple_oauth entities');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add simple_oauth entities');
  }

}
