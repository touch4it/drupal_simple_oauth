<?php

namespace Drupal\simple_oauth\Repositories;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Drupal\simple_oauth\Entities\ClientEntity;

class ClientRepository implements ClientRepositoryInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a ClientRepository object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getClientEntity($client_identifier, $grant_type, $client_secret = NULL, $must_validate_secret = TRUE) {
    $client_entity = $this->entityTypeManager
      ->getStorage('oauth2_client')
      ->load($client_identifier);

    // Check if the client is registered.
    if (!$client_entity) {
      return NULL;
    }

    if (
      $must_validate_secret === TRUE &&
      $client_entity->get('is_confidential') == TRUE &&
      password_verify($client_secret, $client_entity->get('secret')) === FALSE
    ) {
      return NULL;
    }

    $client = new ClientEntity();
    $client->setIdentifier($client_identifier);
    $client->setName($client_entity->get('label'));
    $client->setRedirectUri($client_entity->get('redirect_uri'));

    return $client;
  }

}
