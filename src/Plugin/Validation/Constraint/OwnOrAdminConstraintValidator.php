<?php

namespace Drupal\simple_oauth\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class OwnOrAdminConstraintValidator.
 *
 * @package Drupal\simple_oauth\Plugin\Validation\Constraint
 */
class OwnOrAdminConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($value, Constraint $constraint) {
    /* @var OwnOrAdminConstraintInterface $constraint */
    $account = $constraint->getAccount();
    if ($value == $account->id()) {
      // No violation if the user is the same as the provided one.
      return NULL;
    }
    if ($account->hasPermission($constraint->getPermission())) {
      // No violation if the current user has admin rights.
      return NULL;
    }
    $this->context->addViolation($constraint->message, [
      '@permission' => $constraint->getPermission(),
    ]);
  }

}
