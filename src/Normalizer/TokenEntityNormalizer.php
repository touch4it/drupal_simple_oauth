<?php

namespace Drupal\simple_oauth\Normalizer;

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
   * {@inheritdoc}
   */
  public function normalize($token_entity, $format = NULL, array $context = array()) {
    /** @var \League\OAuth2\Server\Entities\TokenInterface $token_entity */

    $scopes = array_map(function ($scope_entity) {
      /** @var \League\OAuth2\Server\Entities\ScopeEntityInterface $scope_entity */
      return ['target_id' => $scope_entity->getIdentifier()];
    }, $token_entity->getScopes());

    return [
      'auth_user_id' => ['target_id' => $token_entity->getUserIdentifier()],
      'client' => ['target_id' => $token_entity->getClient()->getIdentifier()],
      'scopes' => $scopes,
      'value' => $token_entity->getIdentifier(),
      'expire' => $token_entity->getExpiryDateTime()->format('U'),
      'status' => TRUE,
    ];
  }

}
