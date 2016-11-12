<?php

namespace Drupal\simple_oauth\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\simple_oauth\Server\AuthorizationServerFactoryInterface;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Bridge\PsrHttpMessage\HttpMessageFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
   * Constructs a CommentController object.
   *
   * @param AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   */
  public function __construct(HttpMessageFactoryInterface $message_factory, HttpFoundationFactoryInterface $foundation_factory, AuthorizationServerFactoryInterface $auth_server_factory) {
    $this->messageFactory = $message_factory;
    $this->foundationFactory = $foundation_factory;
    $this->authServerFactory = $auth_server_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('psr7.http_message_factory'),
      $container->get('psr7.http_foundation_factory'),
      $container->get('simple_oauth.server.authorization_server.factory')
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
    $grant_type_id = $request->get('grant_type');
    // Get the auth server object from that uses the Leage library.
    $auth_server = $this->authServerFactory->createInstance($grant_type_id);
    // Instantiate a new PSR-7 response object so the library can fill it.
    $response = new Response();
    // Respond to the incoming request and fill in the response.
    try {
      $response = $auth_server->respondToAccessTokenRequest($psr7_request, $response);
    }
    catch (OAuthServerException $exception) {
      $response = $exception->generateHttpResponse($response);
    }
    // Transform the PSR-7 response into an HTTP foundation response so Drupal
    // can process it.
    return $this->foundationFactory->createResponse($response);
  }

}
