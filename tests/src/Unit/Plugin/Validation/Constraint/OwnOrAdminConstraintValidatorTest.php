<?php

namespace Drupal\Tests\simple_oauth\Unit\Plugin\Validation\Constraint;

use Drupal\Core\Session\AccountInterface;
use Drupal\Core\TypedData\Validation\ExecutionContext;
use Drupal\simple_oauth\Plugin\Validation\Constraint\OwnOrAdminConstraint;
use Drupal\simple_oauth\Plugin\Validation\Constraint\OwnOrAdminConstraintValidator;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;

/**
 * Class OwnOrAdminConstraintValidatorTest.
 *
 * @package Drupal\Tests\simple_oauth\Unit\Plugin\Validation\Constraint
 *
 * @coversDefaultClass \Drupal\simple_oauth\Plugin\Validation\Constraint\OwnOrAdminConstraintValidator
 * @group simple_oauth
 */
class OwnOrAdminConstraintValidatorTest extends UnitTestCase {

  /**
   * @covers ::validate
   * @dataProvider validateProvider
   */
  public function testValidate($uid, $has_permission, $success) {
    $constraint = $this->prophesize(OwnOrAdminConstraint::class);
    $account = $this->prophesize(AccountInterface::class);
    $account->id()->willReturn(6);
    $account->hasPermission('lorem ipsum dolor')->willReturn($has_permission);
    $constraint->getAccount()->willReturn($account->reveal());
    $constraint->getPermission()->willReturn('lorem ipsum dolor');
    $context = $this->prophesize(ExecutionContext::class);
    $constraint_validator = new OwnOrAdminConstraintValidator();
    $constraint_validator->initialize($context->reveal());
    $revealed_constraint = $constraint->reveal();
    $revealed_constraint->message = 'Foo';
    $constraint_validator->validate($uid, $revealed_constraint);
    $method = $success ? 'shouldNotHaveBeenCalled' : 'shouldHaveBeenCalled';
    $context->addViolation(Argument::type('string'), Argument::type('array'))->{$method}();
  }

  public function validateProvider() {
    return [
      // 1. Success: same user id.
      [6, TRUE, TRUE],
      // 2. Success: different user ID but has permission.
      [2, TRUE, TRUE],
      // 3. Fail.
      [2, FALSE, FALSE],
    ];
  }

}
