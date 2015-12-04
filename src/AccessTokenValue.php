<?php

/**
 * @file
 * Contains \Drupal\simple_oauth\AccessTokenValue.
 */

namespace Drupal\simple_oauth;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\Crypt;
use Drupal\Core\Site\Settings;

class AccessTokenValue implements AccessTokenValueInterface {

  /**
   * The entity values to digest.
   *
   * @var array
   */
  protected $values;

  /**
   * {@inheritdoc}
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * {@inheritdoc}
   */
  public function setValues($values) {
    $this->values = $values;
  }

  /**
   * {@inheritdoc}
   */
  public static function createFromValues(array $values) {
    $token_value = new static();
    $token_value->setValues($values);

    return $token_value;
  }

  /**
   * {@inheritdoc}
   */
  public function digest() {
    return Crypt::hmacBase64(Json::encode($this->getValues()), Settings::getHashSalt());
  }

}
