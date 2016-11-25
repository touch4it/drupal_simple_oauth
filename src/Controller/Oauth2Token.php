<?php

namespace Drupal\simple_oauth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\simple_oauth\Plugin\Oauth2GrantManagerInterface;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
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
   * @var \Drupal\simple_oauth\Plugin\Oauth2GrantManagerInterface
   */
  protected $grantManager;

  /**
   * Oauth2Token constructor.
   *
   * @param \Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface $message_factory
   * @param \Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface $foundation_factory
   * @param \Drupal\simple_oauth\Plugin\Oauth2GrantManagerInterface $grant_manager
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   */
  public function __construct(HttpMessageFactoryInterface $message_factory, HttpFoundationFactoryInterface $foundation_factory, Oauth2GrantManagerInterface $grant_manager) {
    $this->messageFactory = $message_factory;
    $this->foundationFactory = $foundation_factory;
    $this->grantManager = $grant_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('psr7.http_message_factory'),
      $container->get('psr7.http_foundation_factory'),
      $container->get('plugin.manager.oauth2_grant.processor')
    );
  }

  /**
   * Processes POST requests to /oauth/token.
   */
  public function token(Request $request) {
    // Transform the HTTP foundation request object into a PSR-7 object. The
    // OAuth library expects a PSR-7 request.
    $psr7_request = $this->messageFactory->createRequest($request);
    // Extract the grant type from the request body.
    $grant_type_id = $request->get('grant_type') ?: 'implicit';
    // Get the auth server object from that uses the League library.
    try {
      // Respond to the incoming request and fill in the response.
      $auth_server = $this->grantManager->getAuthorizationServer($grant_type_id);
      $response = $this->handleToken($psr7_request, $auth_server);
    }
    catch (OAuthServerException $exception) {
      $response = $exception->generateHttpResponse(new Response());
    }
    // Transform the PSR-7 response into an HTTP foundation response so Drupal
    // can process it.
    return $this->foundationFactory
      ->createResponse($response);
  }

  /**
   * Handles the token processing.
   *
   * @param \Psr\Http\Message\ServerRequestInterface $psr7_request
   *
   * @return \Psr\Http\Message\ResponseInterface
   */
  protected function handleToken(ServerRequestInterface $psr7_request, AuthorizationServer $auth_server) {
    // Instantiate a new PSR-7 response object so the library can fill it.
    return $auth_server->respondToAccessTokenRequest($psr7_request, new Response());
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

}
