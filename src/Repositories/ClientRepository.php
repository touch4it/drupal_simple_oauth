<?php

namespace Drupal\simple_oauth\Repositories;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Password\PasswordInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use Drupal\simple_oauth\Entities\ClientEntity;

class ClientRepository implements ClientRepositoryInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Password\PasswordInterface
   */
  protected $passwordChecker;

  /**
   * Constructs a ClientRepository object.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, PasswordInterface $password_checker) {
    $this->entityTypeManager = $entity_type_manager;
    $this->passwordChecker = $password_checker;
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
      $client_entity->get('isConfidential') == TRUE &&
      $this->passwordChecker->check($client_secret, $client_entity->get('secret')) === FALSE
    ) {
      return NULL;
    }

    $client = new ClientEntity();
    $client->setIdentifier($client_identifier);
    $client->setName($client_entity->get('label'));
    $client->setRedirectUri($client_entity->get('redirectUri'));

    return $client;
  }

}
