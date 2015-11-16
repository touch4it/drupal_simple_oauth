<?php

/**
 * @file
 * Contains \Drupal\token_auth\AccessTokenValueInterface.
 */

namespace Drupal\token_auth;


interface AccessTokenValueInterface {

  /**
   * Digest the values to produce a token.
   *
   * @return string
   */
  public function digest();

  /**
   * Factory method to create a token value from the entity values.
   *
   * @param array $values
   *   The values to digest.
   *
   * @return AccessTokenValueInterface
   *   The token value.
   */
  public static function createFromValues(array $values);

  /**
   * Gets the values.
   *
   * @return array
   *   The values.
   */
  public function getValues();

  /**
   * Sets the values.
   *
   * @param array $values
   *   The values.
   */
  public function setValues($values);

}