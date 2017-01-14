<?php

namespace Drupal\simple_oauth\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\user\UserAuthInterface;
use Drupal\simple_oauth\Entity\AccessToken;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;


class AccessTokenIssue extends ControllerBase {

  /**
   * The user authentication object.
   *
   * @var \Drupal\user\UserAuthInterface
   */
  protected $userAuth;

  /**
   * The response object.
   *
   * @var JsonResponse
   */
  protected $response;

  /**
   * @param \Drupal\Core\Entity\EntityManagerInterface $entity_manager
   *   The entity manager service.
   */
  public function __construct(EntityManagerInterface $entity_manager, UserAuthInterface $user_auth, JsonResponse $response) {
    $this->entityManager = $entity_manager;
    $this->userAuth = $user_auth;
    $this->response = $response;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity.manager'),
      $container->get('user.auth'),
      new JsonResponse()
    );
  }


  public function issue(Request $request) {
    $body = Json::decode($request->getContent());

    if (!$body['grant_type'] == 'password') {
      throw new HttpException(422, 'Only grant_type=password is supported');
    }

    $scope = 'global';
    if (!empty($body['scope'])) {
      $scope = $body['scope'];
      $resource = $this->entityManager->getStorage('access_token_resource')
        ->load($scope);
      if (!$resource) {
        throw new HttpException(422, sprintf('Unknown scope %s', $scope));
      }
    }

    $uid = $this->userAuth->authenticate($body['username'], $body['password']);
    /** @var \Drupal\user\UserInterface $user */
    $user = $this->entityManager->getStorage('user')->load($uid);
    if (!$user || $user->isBlocked()) {
      throw new HttpException(401, 'Authentication failed.');
    }
    $values = [
      'expire' => AccessToken::defaultExpiration(),
      'user_id' => $uid,
      'auth_user_id' => $uid,
      'resource' => $scope,
    ];
    $store = $this->entityManager->getStorage('access_token');
    /** @var \Drupal\simple_oauth\Entity\AccessToken $token */
    $token = $store->create($values);
    $token->save();
    $this->response->setData($this->normalize($token));

    return $this->response;
  }

  /**
   * Manually normalize a token entity into an structured array.
   *
   * @todo Use the serialization system.
   *
   * @param \Drupal\simple_oauth\Entity\AccessToken $token
   *   The token entity to normalize.
   *
   * @return array
   *   The structured array.
   */
  protected function normalize(AccessToken $token) {
    $output = [
      'access_token' => $token->get('value')->value,
      'token_type' => 'Bearer',
      'expires_in' => $token->get('expire')->value - REQUEST_TIME,
    ];

    // Try to load the associated refresh token.
    $storage = $this->entityManager()->getStorage('access_token');
    $ids = $storage
      ->getQuery()
      ->condition('access_token_id', $token->id())
      ->condition('expire', REQUEST_TIME, '>')
      ->condition('resource', 'authentication')
      ->range(0, 1)
      ->execute();
    if (!empty($ids)) {
      $output['refresh_token'] = $storage->load(reset($ids))->get('value')->value;
    }

    return $output;
  }


}
