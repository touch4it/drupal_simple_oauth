<?php

namespace Drupal\simple_oauth\Normalizer;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\serialization\Normalizer\NormalizerBase;
use Symfony\Component\Serializer\Normalizer\scalar;

class TokenEntityNormalizer extends NormalizerBase implements TokenEntityNormalizerInterface {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var string|array
   */
  protected $supportedInterfaceOrClass = '\League\OAuth2\Server\Entities\TokenInterface';

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }


  /**
   * {@inheritdoc}
   */
  public function normalize($token_entity, $format = NULL, array $context = array()) {
    /** @var \League\OAuth2\Server\Entities\TokenInterface $token_entity */

    $scopes = array_map(function ($scope_entity) {
      /** @var \League\OAuth2\Server\Entities\ScopeEntityInterface $scope_entity */
      return ['target_id' => $scope_entity->getIdentifier()];
    }, $token_entity->getScopes());

    $client_entities = $this->entityTypeManager->getStorage('oauth2_client')->loadByProperties([
      'uuid' => $token_entity->getClient()->getIdentifier(),
    ]);
    $client_entity = reset($client_entities);

    return [
      'auth_user_id' => ['target_id' => $token_entity->getUserIdentifier()],
      'client' => ['target_id' => $client_entity->id()],
      'scopes' => $scopes,
      'value' => $token_entity->getIdentifier(),
      'expire' => $token_entity->getExpiryDateTime()->format('U'),
      'status' => TRUE,
    ];
  }

}
