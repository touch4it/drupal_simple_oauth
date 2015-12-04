<?php

/**
 * @file
 * Contains \Drupal\simple_oauth\Plugin\Validation\Constraint\OwnOrAdminConstraintInterface.
 */

namespace Drupal\simple_oauth\Plugin\Validation\Constraint;

/**
 * Interface OwnOrAdminConstraintInterface.
 *
 * @package Drupal\simple_oauth\Plugin\Validation\Constraint
 */
interface OwnOrAdminConstraintInterface {

  /**
   * Gets the permission.
   *
   * @return string
   *   The permission.
   */
  public function getPermission();

  /**
   * Gets the account.
   *
   * @return \Drupal\Core\Session\AccountInterface
   *   The user.
   */
  public function getAccount();

}
