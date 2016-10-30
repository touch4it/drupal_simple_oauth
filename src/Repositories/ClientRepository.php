<?php

namespace Drupal\simple_oauth\Repositories;

use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Drupal\simple_oauth\Entities\ClientEntity;

class ClientRepository implements ClientRepositoryInterface {

  /**
   * {@inheritdoc}
   */
  public function getClientEntity($clientIdentifier, $grantType, $clientSecret = NULL, $mustValidateSecret = TRUE) {
    $clients = [
      'myawesomeapp' => [
        'secret' => password_hash('abc123', PASSWORD_BCRYPT),
        'name' => 'My Awesome App',
        'redirect_uri' => 'http://foo/bar',
        'is_confidential' => TRUE,
      ],
    ];

    // Check if client is registered
    if (array_key_exists($clientIdentifier, $clients) === FALSE) {
      return;
    }

    if (
      $mustValidateSecret === TRUE
      && $clients[$clientIdentifier]['is_confidential'] === TRUE
      && password_verify($clientSecret, $clients[$clientIdentifier]['secret']) === FALSE
    ) {
      return;
    }

    $client = new ClientEntity();
    $client->setIdentifier($clientIdentifier);
    $client->setName($clients[$clientIdentifier]['name']);
    $client->setRedirectUri($clients[$clientIdentifier]['redirect_uri']);

    return $client;
  }

}
