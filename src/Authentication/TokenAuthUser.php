<?php

namespace Drupal\simple_oauth\Authentication;

use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\Core\Cache\CacheableDependencyTrait;
use Drupal\simple_oauth\Entity\Oauth2TokenInterface;
use League\OAuth2\Server\Exception\OAuthServerException;

/**
 * @internal
 */
class TokenAuthUser implements TokenAuthUserInterface {

  use CacheableDependencyTrait {
    getCacheContexts as getCacheContextsFromTrait;
    getCacheMaxAge as getCacheMaxAgeFromTrait;
    getCacheTags as getCacheTagsFromTrait;
  }

  /**
   * The decorator subject.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $subject;

  /**
   * The bearer token.
   *
   * @var \Drupal\simple_oauth\Entity\Oauth2TokenInterface
   */
  protected $token;

  /**
   * Constructs a TokenAuthUser object.
   *
   * @param \Drupal\simple_oauth\Entity\Oauth2TokenInterface $token
   *   The underlying token.
   *
   * @throws \Exception
   *   When there is no user.
   */
  public function __construct(Oauth2TokenInterface $token) {
    if (!$this->subject = $token->get('auth_user_id')->entity) {
      /** @var \Drupal\consumers\Entity\Consumer $client */
      if ($client = $token->get('client')->entity) {
        $this->subject = $client->get('user_id')->entity;
      }
    }
    if (!$this->subject) {
      throw OAuthServerException::invalidClient();
    }
    $this->token = $token;
  }

  /**
   * {@inheritdoc}
   */
  public function getToken() {
    return $this->token;
  }

  /**
   * {@inheritdoc}
   */
  public function getRoles($exclude_locked_roles = FALSE) {
    return array_map(function ($item) {
      return $item['target_id'];
    }, $this->token->get('scopes')->getValue());
  }

  /* ---------------------------------------------------------------------------
  All the methods below are delegated to the decorated account.
  --------------------------------------------------------------------------- */

  /**
   * {@inheritdoc}
   */
  public function hasPermission($permission) {
    // User #1 has all privileges.
    if ((int) $this->id() === 1) {
      return TRUE;
    }

    return $this->getRoleStorage()->isPermissionInRoles($permission, $this->getRoles());
  }

  /**
   * {@inheritdoc}
   */
  public function isAuthenticated() {
    return $this->subject->isAuthenticated();
  }

  /**
   * {@inheritdoc}
   */
  public function isAnonymous() {
    return $this->subject->isAnonymous();
  }

  /**
   * {@inheritdoc}
   */
  public function getPreferredLangcode($fallback_to_default = TRUE) {
    return $this->subject->getPreferredLangcode($fallback_to_default);
  }

  /**
   * {@inheritdoc}
   */
  public function getPreferredAdminLangcode($fallback_to_default = TRUE) {
    return $this->subject->getPreferredAdminLangcode($fallback_to_default);
  }

  /**
   * {@inheritdoc}
   */
  public function getUsername() {
    return $this->subject->getUsername();
  }

  /**
   * {@inheritdoc}
   */
  public function getAccountName() {
    return $this->subject->getAccountName();
  }

  /**
   * {@inheritdoc}
   */
  public function getDisplayName() {
    return $this->subject->getDisplayName();
  }

  /**
   * {@inheritdoc}
   */
  public function getEmail() {
    return $this->subject->getEmail();
  }

  /**
   * {@inheritdoc}
   */
  public function getTimeZone() {
    return $this->subject->getTimeZone();
  }

  /**
   * {@inheritdoc}
   */
  public function getLastAccessedTime() {
    return $this->subject->getLastAccessedTime();
  }

  /**
   * {@inheritdoc}
   */
  public function id() {
    return $this->subject->id();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    if ($this->subject instanceof CacheableDependencyInterface) {
      return $this->subject->getCacheContexts();
    }
    return $this->getCacheContextsFromTrait();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheMaxAge() {
    if ($this->subject instanceof CacheableDependencyInterface) {
      return $this->subject->getCacheMaxAge();
    }
    return $this->getCacheMaxAgeFromTrait();
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    if ($this->subject instanceof CacheableDependencyInterface) {
      return $this->subject->getCacheTags();
    }
    return $this->getCacheTagsFromTrait();
  }

  /**
   * {@inheritdoc}
   */
  public function hasRole($rid) {
    return in_array($rid, $this->getRoles());
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    throw new \Exception('Invalid use of getIterator in token authentication.');
  }

  /**
   * Returns the role storage object.
   *
   * @return \Drupal\user\RoleStorageInterface
   *   The role storage object.
   */
  protected function getRoleStorage() {
    return \Drupal::entityTypeManager()->getStorage('user_role');
  }

}
