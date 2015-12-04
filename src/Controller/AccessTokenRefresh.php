<?php

/**
 * @file
 * Contains \Drupal\simple_oauth\Controller\AccessTokenRefresh.
 */
namespace Drupal\simple_oauth\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\simple_oauth\AccessTokenInterface;
use Drupal\simple_oauth\Authentication\TokenAuthUserInterface;
use Symfony\Component\HttpFoundation\Response;

class AccessTokenRefresh {

  /**
   * Controller to return the access token when a refresh token is provided.
   *
   * @todo: Get some flood protection for this, since the request is uncacheable
   * because of the expire counter. Also, there has to be some other better way
   * to render JSON. Investigate that too!
   */
  public function refresh() {
    // TODO: Inject the current user service.
    $user = \Drupal::currentUser()->getAccount();
    // If the account is not a token account, then bail.
    if (!$user instanceof TokenAuthUserInterface) {
      // TODO: Set the error headers appropriately.
      return NULL;
    }
    $refresh_token = $user->getToken();
    if (!$refresh_token || !$refresh_token->isRefreshToken()) {
      // TODO: Set the error headers appropriately.
      return NULL;
    }
    // Find / generate the access token for this refresh token.
    // TODO: Inject the entity manager service.
    if (!$access_token = $refresh_token->get('access_token_id')->entity) {
      // If there is no token to be found, refresh it by generating a new one.
      $values = [
        'expire' => $refresh_token::defaultExpiration(),
        'user_id' => $refresh_token->get('user_id')->target_id,
        'auth_user_id' => $refresh_token->get('auth_user_id')->target_id,
        'resource' => $refresh_token->get('resource')->target_id,
        'created' => REQUEST_TIME,
        'changed' => REQUEST_TIME,
      ];
      /* @var AccessTokenInterface $access_token */
      $access_token = \Drupal::entityManager()
        ->getStorage('access_token')
        ->create($values);
      // This refresh token is no longer needed.
      $refresh_token->delete();
      // Saving this token will generate a refresh token for that one.
      $access_token->save();
    }
    return Response::create($this->serialize($access_token));
  }

  /**
   * Serializes the token either using the serializer or manually.
   *
   * @param AccessTokenInterface $token
   *   The token.
   *
   * @return string
   *   The serialized token.
   */
  protected function serialize(AccessTokenInterface $token) {
    $storage = \Drupal::entityManager()
      ->getStorage('access_token');
    $ids = $storage
      ->getQuery()
      ->condition('access_token_id', $token->id())
      ->condition('expire', REQUEST_TIME, '>')
      ->condition('resource', 'authentication')
      ->range(0, 1)
      ->execute();
    if (empty($ids)) {
      // TODO: Add appropriate error handling. Maybe throw an exception?
      return '{}';
    }
    $refresh_token = $storage->load(reset($ids));
    if (!$refresh_token || !$refresh_token->isRefreshToken()) {
      // TODO: Add appropriate error handling. Maybe throw an exception?
      return '{}';
    }
    return Json::encode([
      'access_token' => $token->get('value')->value,
      'token_type' => 'Bearer',
      'expires_in' => $token->get('expire')->value - REQUEST_TIME,
      'refresh_token' => $refresh_token->get('value')->value,
    ]);
  }

}
