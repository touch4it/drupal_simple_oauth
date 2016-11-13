<?php

namespace Drupal\simple_oauth\Authentication\Provider;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\simple_oauth\Server\ResourceServerInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SimpleOauthAuthenticationProvider.
 *
 * @package Drupal\simple_oauth\Authentication\Provider
 */
class SimpleOauthAuthenticationProvider implements SimpleOauthAuthenticationProviderInterface {

  /**
   * @var \Drupal\simple_oauth\Server\ResourceServerInterface
   */
  protected $resourceServer;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a HTTP basic authentication provider object.
   *
   * @param \Drupal\simple_oauth\Server\ResourceServerInterface $resource_server
   *   The resource server object.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   */
  public function __construct(ResourceServerInterface $resource_server, EntityTypeManagerInterface $entity_type_manager) {
    $this->resourceServer = $resource_server;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function applies(Request $request) {
    // Check for the presence of the token.
    return $this->hasTokenValue($request);
  }

  /**
   * {@inheritdoc}
   */
  public static function hasTokenValue(Request $request) {
    // Check the header. See: http://tools.ietf.org/html/rfc6750#section-2.1
    $auth_header = trim($request->headers->get('Authorization', '', TRUE));

    return strpos($auth_header, 'Bearer ') !== FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function authenticate(Request $request) {
    // Update the request with the OAuth information.
    try {
      $request = $this->resourceServer->validateAuthenticatedRequest($request);
    }
    catch (OAuthServerException $exception) {
      // Procedural code here is hard to avoid.
      watchdog_exception('simple_oauth', $exception);

      return NULL;
    }

    if (!$uid = $request->get('oauth_user_id')) {
      // No user could be found, but the client was successfully validated,
      // therefore the request is entitled to the default user for that client.
      return $this->getClientDefaultUser($request);
    }

    return $this->entityTypeManager->getStorage('user')->load($uid);
  }

  /**
   * Get the default user associated with a client.
   *
   * @param \Symfony\Component\HttpFoundation\Request $client_identifier
   *   The OAuth processed request.
   *
   * @return \Drupal\user\UserInterface|array
   *   The default user associated to the client entity or an empty array if
   *   none could be found.
   */
  protected function getClientDefaultUser(Request $request) {
    if (!$client_identifier = $request->get('oauth_client_id')) {
      return NULL;
    }
    // Try to get the Client entity and load the default user from it.
    /* @var \Drupal\simple_oauth\Entity\Oauth2ClientInterface $client_entity */
    $client_entity = $this->entityTypeManager
      ->getStorage('oauth2_client')
      ->load($client_identifier);

    return $client_entity ?
      $client_entity->getDefaultUser() :
      NULL;
  }

}
