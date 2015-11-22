<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\AccessTokenAccessControlHandler.
 */

namespace Drupal\oauth2_token;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Access Token entity.
 *
 * @see \Drupal\oauth2_token\Entity\AccessToken.
 */
class AccessTokenAccessControlHandler extends EntityAccessControlHandler {
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view access token entities');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit access token entities');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete access token entities');
    }

    return AccessResult::allowed();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add access token entities');
  }

}
