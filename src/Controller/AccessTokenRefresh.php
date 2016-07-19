<?php

namespace Drupal\simple_oauth\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\simple_oauth\AccessTokenInterface;
use Drupal\simple_oauth\Authentication\TokenAuthUserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class AccessTokenRefresh extends ControllerBase {

  /**
   * The response object.
   *
   * @var JsonResponse
   */
  protected $response;

  /**
   * Constructs a CommentController object.
   *
   * @param AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   */
  public function __construct(AccountInterface $current_user, EntityManagerInterface $entity_manager, JsonResponse $response) {
    $this->currentUser = $current_user;
    $this->entityManager = $entity_manager;
    $this->response = $response;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('current_user'),
      $container->get('entity.manager'),
      new JsonResponse()
    );
  }

  /**
   * Controller to return the access token when a refresh token is provided.
   *
   * @todo: Get some flood protection for this, since the request is uncacheable
   * because of the expire counter. Also, there has to be some other better way
   * to render JSON. Investigate that too!
   */
  public function refresh() {
    $account = $this->currentUser()->getAccount();
    // If the account is not a token account, then bail.
    if (!$account instanceof TokenAuthUserInterface) {
      // TODO: Set the error headers appropriately.
      return NULL;
    }
    $refresh_token = $account->getToken();
    if (!$refresh_token) {
      // TODO: Set the error headers appropriately.
      return NULL;
    }

    // Find / generate the access token for this refresh token.
    $access_token = $refresh_token->refresh();

    if(!$access_token) {
      // TODO: Set the error headers appropriately.
      return NULL;
    }

    $this->response->setData($this->normalize($access_token));
    return $this->response;
  }

  /**
   * Serializes the token either using the serializer or manually.
   *
   * @param AccessTokenInterface $token
   *   The token.
   *
   * @return string
   *   The serialized token.
   */
  protected function normalize(AccessTokenInterface $token) {
    $storage = $this->entityManager()
      ->getStorage('access_token');
    $ids = $storage
      ->getQuery()
      ->condition('access_token_id', $token->id())
      ->condition('expire', REQUEST_TIME, '>')
      ->condition('resource', 'authentication')
      ->range(0, 1)
      ->execute();
    if (empty($ids)) {
      // TODO: Add appropriate error handling. Maybe throw an exception?
      return [];
    }
    $refresh_token = $storage->load(reset($ids));
    if (!$refresh_token || !$refresh_token->isRefreshToken()) {
      // TODO: Add appropriate error handling. Maybe throw an exception?
      return [];
    }
    return [
      'access_token' => $token->get('value')->value,
      'token_type' => 'Bearer',
      'expires_in' => $token->get('expire')->value - REQUEST_TIME,
      'refresh_token' => $refresh_token->get('value')->value,
    ];
  }

}
