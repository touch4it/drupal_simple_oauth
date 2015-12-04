<?php

/**
 * @file
 * Contains \Drupal\simple_oauth\Plugin\Validation\Constraint\OwnOrAdminConstraint.
 */

namespace Drupal\simple_oauth\Plugin\Validation\Constraint;
use Symfony\Component\Validator\Constraint;

/**
 * Class OwnOrAdminConstraint.
 *
 * @package Drupal\simple_oauth\Plugin\Validation\Constraint
 *
 * @Constraint(
 *   id = "OwnOrAdmin",
 *   label = @Translation("Own or admin", context = "Validation")
 * )
 */
class OwnOrAdminConstraint extends Constraint implements OwnOrAdminConstraintInterface {

  /**
   * The current user uid.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $account;

  /**
   * The current user uid.
   *
   * @var string
   */
  protected $permission;

  public $message = 'Only users with permission @permission can set this field to other users.';

  /**
   * {@inheritdoc}
   */
  public function getPermission() {
    return $this->permission;
  }

  /**
   * {@inheritdoc}
   */
  public function getAccount() {
    return $this->account;
  }
}
