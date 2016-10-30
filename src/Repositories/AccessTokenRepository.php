<?php

namespace Drupal\simple_oauth\Repositories;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Drupal\simple_oauth\Entities\AccessTokenEntity;

class AccessTokenRepository implements AccessTokenRepositoryInterface {

  /**
   * {@inheritdoc}
   */
  public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity) {
    // Some logic here to save the access token to a database
  }

  /**
   * {@inheritdoc}
   */
  public function revokeAccessToken($tokenId) {
    // Some logic here to revoke the access token
  }

  /**
   * {@inheritdoc}
   */
  public function isAccessTokenRevoked($tokenId) {
    return FALSE; // Access token hasn't been revoked
  }

  /**
   * {@inheritdoc}
   */
  public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = NULL) {
    $accessToken = new AccessTokenEntity();
    $accessToken->setClient($clientEntity);
    foreach ($scopes as $scope) {
      $accessToken->addScope($scope);
    }
    $accessToken->setUserIdentifier($userIdentifier);

    return $accessToken;
  }

}
