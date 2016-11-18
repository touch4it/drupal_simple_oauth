<?php

namespace Drupal\simple_oauth\Authentication;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Session\AccountProxy;
use Drupal\simple_oauth\AccessTokenInterface;
use Drupal\simple_oauth\Entity\Oauth2TokenInterface;
use Drupal\user\Entity\User;
use Drupal\user\UserInterface;

/**
 * Class TokenAuthUser.
 *
 * @package Drupal\simple_oauth\Authentication
 */
class TokenAuthUser extends AccountProxy implements TokenAuthUserInterface {

  /**
   * The decorator subject.
   *
   * @var UserInterface
   */
  protected $subject;

  /**
   * The bearer token.
   *
   * @var \Drupal\simple_oauth\Entity\Oauth2TokenInterface
   */
  protected $token;

  /**
   * {@inheritdoc}
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * Constructs a TokenAuthUser object.
   *
   * @param \Drupal\simple_oauth\Entity\Oauth2TokenInterface $token
   *   The underlying token.
   * @throws \Exception
   *   When there is no user.
   */
  public function __construct(Oauth2TokenInterface $token) {
    if (!$this->account = $token->get('auth_user_id')->entity) {
      /** @var \Drupal\simple_oauth\Entity\Oauth2ClientInterface $client */
      if ($client = $token->get('client')->entity) {
        $this->account = $client->getDefaultUser();
      }
    }
    if (!$this->account) {
      throw new \Exception('The access token does not link to a user.');
    }
    $this->token = $token;
  }

}
