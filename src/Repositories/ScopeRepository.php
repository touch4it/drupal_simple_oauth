<?php

namespace Drupal\simple_oauth\Repositories;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use Drupal\simple_oauth\Entities\ScopeEntity;

class ScopeRepository implements ScopeRepositoryInterface {

  /**
   * {@inheritdoc}
   */
  public function getScopeEntityByIdentifier($scopeIdentifier) {
    $scopes = [
      'basic' => [
        'description' => 'Basic details about you',
      ],
      'email' => [
        'description' => 'Your email address',
      ],
    ];

    if (array_key_exists($scopeIdentifier, $scopes) === FALSE) {
      return;
    }

    $scope = new ScopeEntity();
    $scope->setIdentifier($scopeIdentifier);

    return $scope;
  }

  /**
   * {@inheritdoc}
   */
  public function finalizeScopes(
    array $scopes,
    $grantType,
    ClientEntityInterface $clientEntity,
    $userIdentifier = NULL
  ) {
    // Example of programatically modifying the final scope of the access token
    if ((int) $userIdentifier === 1) {
      $scope = new ScopeEntity();
      $scope->setIdentifier('email');
      $scopes[] = $scope;
    }

    return $scopes;
  }

}
