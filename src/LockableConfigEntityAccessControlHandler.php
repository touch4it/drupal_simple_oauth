<?php
/**
 * @file
 * Contains \Drupal\oauth2_token\LockableConfigEntityAccessControlHandler.
 */

namespace Drupal\oauth2_token;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

class LockableConfigEntityAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($operation != 'view' && $entity->isLocked()) {
      return AccessResult::forbidden();
    }
    return parent::checkAccess($entity, $operation, $account);
  }

}