<?php

namespace Drupal\simple_oauth\Controller;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\simple_oauth\Entities\UserEntity;
use Drupal\simple_oauth\Server\AuthorizationServerFactoryInterface;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Oauth2Token extends ControllerBase {

  /**
   * @var \Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface
   */
  protected $messageFactory;

  /**
   * @var \Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface
   */
  protected $foundationFactory;

  /**
   * @var \Drupal\simple_oauth\Server\AuthorizationServerFactoryInterface
   */
  protected $authServerFactory;

  /**
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Oauth2Token constructor.
   *
   * @param \Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface $message_factory
   * @param \Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface $foundation_factory
   * @param \Drupal\simple_oauth\Server\AuthorizationServerFactoryInterface $auth_server_factory
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   */
  public function __construct(HttpMessageFactoryInterface $message_factory, HttpFoundationFactoryInterface $foundation_factory, AuthorizationServerFactoryInterface $auth_server_factory, ConfigFactoryInterface $config_factory) {
    $this->messageFactory = $message_factory;
    $this->foundationFactory = $foundation_factory;
    $this->authServerFactory = $auth_server_factory;
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('psr7.http_message_factory'),
      $container->get('psr7.http_foundation_factory'),
      $container->get('simple_oauth.server.authorization_server.factory'),
      $container->get('config.factory')
    );
  }

  /**
   * Proceses POST requests to /oauth/token.
   */
  public function token(Request $request) {
    // Transform the HTTP foundation request object into a PSR-7 object. The
    // OAuth library expects a PSR-7 request.
    $psr7_request = $this->messageFactory->createRequest($request);
    // Extract the grant type from the request body.
    $grant_type_id = $request->get('grant_type') ?: 'implicit';
    // Get the auth server object from that uses the Leage library.
    $auth_server = $this->authServerFactory->createInstance($grant_type_id);
    // Instantiate a new PSR-7 response object so the library can fill it.
    $response = new Response();
    try {
      // The implicit grant is a bit different.
      if ($grant_type_id != 'implicit') {
        // Respond to the incoming request and fill in the response.
        $response = $auth_server->respondToAccessTokenRequest($psr7_request, $response);
      }
      else {
        if (!$this->configFactory->get('simple_oauth.settings')->get('use_implicit')) {
          $translated_hint = $this->t('Enable the use of the implicit grant in the Drupal module configuration form.');
          throw OAuthServerException::invalidGrant($translated_hint);
        }
        // Validate the HTTP request and return an AuthorizationRequest object.
        // The auth request object can be serialized into a user's session
        $auth_request = $auth_server->validateAuthorizationRequest($psr7_request);
        // Once the user has logged in set the user on the AuthorizationRequest
        $user = new UserEntity();
        /** @var \Drupal\simple_oauth\Entities\ClientEntityInterface $client */
        $client = $auth_request->getClient();
        $user->setIdentifier($client
          ->getDrupalEntity()
          ->getDefaultUser()
          ->id());
        $auth_request->setUser($user);
        // Once the user has approved or denied the client update the status
        // (true = approved, false = denied)
        $auth_request->setAuthorizationApproved(TRUE);

        // Return the HTTP redirect response
        $response = $auth_server->completeAuthorizationRequest($auth_request, $response);
      }
    }
    catch (OAuthServerException $exception) {
      $response = $exception->generateHttpResponse($response);
    }
    // Transform the PSR-7 response into an HTTP foundation response so Drupal
    // can process it.
    return $this->foundationFactory->createResponse($response);
  }

  /**
   * Processes a GET request.
   */
  public function debug(Request $request) {
    $user = \Drupal::currentUser();
    $permissions_list = \Drupal::service('user.permissions')->getPermissions();
    $permission_info = [];
    // Loop over all the permissions and check if the user has access or not.
    foreach ($permissions_list as $permission_id => $permission) {
      $permission_info[$permission_id] = [
        'title' => $permission['title'],
        'access' => $user->hasPermission($permission_id),
      ];
      if (!empty($permission['description'])) {
        $permission_info['description'] = $permission['description'];
      }
    }
    return new JsonResponse([
      'token' => str_replace('Bearer ', '', $request->headers->get('Authorization')),
      'id' => $user->id(),
      'roles' => $user->getRoles(),
      'permissions' => $permission_info,
    ]);
  }

  /**
   * Debug auth code.
   */
  public function codeDebug(Request $request) {
    return JsonResponse::create($request->get('code'));
  }

}
