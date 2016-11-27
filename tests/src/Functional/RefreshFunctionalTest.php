<?php

namespace Drupal\Tests\simple_oauth\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\Component\Uuid\Uuid;
use Drupal\simple_oauth\Entities\RefreshTokenEntity;
use Drupal\simple_oauth\Entity\Oauth2Token;
use Drupal\user\RoleInterface;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\CryptTrait;

/**
 * Class RefreshFunctionalTest
 *
 * @package Drupal\Tests\simple_oauth\Functional
 *
 * @group simple_oauth
 */
class RefreshFunctionalTest extends TokenBearerFunctionalTestBase {

  use CryptTrait;

  public static $modules = [
    'image',
    'node',
    'simple_oauth',
    'serialization',
    'text',
  ];

  /**
   * @var string
   */
  protected $refreshToken;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $expiration = (new \DateTime())->add(new \DateInterval('P1D'))->format('U');
    $access_token_entity = Oauth2Token::create([
      'bundle' => 'access_token',
      'auth_user_id' => mt_rand(3, 20),
      'client' => ['target_id' => $this->client->id()],
      'scopes' => explode(' ', $this->scope),
      'value' => $this->getRandomGenerator()->string(16),
      'expire' => $expiration,
      'status' => TRUE,
    ]);
    $access_token_entity->save();

    $refresh_token_entity = Oauth2Token::create([
      'bundle' => 'refresh_token',
      'auth_user_id' => 0,
      'scopes' => explode(' ', $this->scope),
      'value' => $this->getRandomGenerator()->string(16),
      'expire' => $expiration,
      'status' => TRUE,
    ]);
    $refresh_token_entity->save();

    $refresh_token_plain = json_encode([
      'client_id' => $this->client->uuid(),
      'refresh_token_id' => $refresh_token_entity->get('value')->value,
      'access_token_id' => $access_token_entity->get('value')->value,
      'scopes' => explode(' ', $this->scope),
      'user_id' => $access_token_entity->get('auth_user_id')->target_id,
      'expire_time' => $refresh_token_entity->get('expire')->value,
    ]);

    // Encrypt the token.
    $this->setPrivateKey(new CryptKey($this->privateKeyPath));
    $this->setPublicKey(new CryptKey($this->publicKeyPath));
    $this->refreshToken = $this->encrypt($refresh_token_plain);
  }

  /**
   * Test the valid Refresh grant.
   */
  public function testRefreshGrant() {
    // 1. Test the valid response.
    $valid_payload = [
      'grant_type' => 'refresh_token',
      'client_id' => $this->client->uuid(),
      'client_secret' => $this->clientSecret,
      'refresh_token' => $this->refreshToken,
      'scope' => $this->scope,
    ];
    $response = $this->request('POST', $this->url, [
      'form_params' => $valid_payload,
    ]);
    $this->assertValidTokenResponse($response, TRUE);

    // 2. Test the valid without scopes.
    $payload_no_scope = $valid_payload;
    unset($payload_no_scope['scope']);
    $response = $this->request('POST', $this->url, [
      'form_params' => $payload_no_scope,
    ]);
    $this->assertValidTokenResponse($response, TRUE);

  }

  /**
   * Test invalid Refresh grant.
   */
  public function testMissingRefreshGrant() {
    $valid_payload = [
      'grant_type' => 'refresh_token',
      'client_id' => $this->client->uuid(),
      'client_secret' => $this->clientSecret,
      'refresh_token' => $this->refreshToken,
      'scope' => $this->scope,
    ];

    $data = [
      'grant_type' => [
        'error' => 'invalid_grant',
        'code' => 400,
      ],
      'client_id' => [
        'error' => 'invalid_request',
        'code' => 400,
      ],
      'client_secret' => [
        'error' => 'invalid_client',
        'code' => 401,
      ],
      'refresh_token' => [
        'error' => 'invalid_request',
        'code' => 400,
      ],
    ];
    foreach ($data as $key => $value) {
      $invalid_payload = $valid_payload;
      unset($invalid_payload[$key]);
      $response = $this->request('POST', $this->url, [
        'form_params' => $invalid_payload,
      ]);
      $parsed_response = Json::decode($response->getBody()->getContents());
      $this->assertSame($value['code'], $response->getStatusCode());
      $this->assertSame($value['error'], $parsed_response['error']);
    }
  }

  /**
   * Test invalid Refresh grant.
   */
  public function testInvalidRefreshGrant() {
    $valid_payload = [
      'grant_type' => 'refresh_token',
      'client_id' => $this->client->uuid(),
      'client_secret' => $this->clientSecret,
      'refresh_token' => $this->refreshToken,
      'scope' => $this->scope,
    ];

    $data = [
      'grant_type' => [
        'error' => 'invalid_grant',
        'code' => 400,
      ],
      'client_id' => [
        'error' => 'invalid_client',
        'code' => 401,
      ],
      'client_secret' => [
        'error' => 'invalid_client',
        'code' => 401,
      ],
      'refresh_token' => [
        'error' => 'invalid_request',
        'code' => 400,
      ],
    ];
    foreach ($data as $key => $value) {
      $invalid_payload = $valid_payload;
      $invalid_payload[$key] = $this->getRandomGenerator()->string();
      $response = $this->request('POST', $this->url, [
        'form_params' => $invalid_payload,
      ]);
      $parsed_response = Json::decode($response->getBody()->getContents());
      $this->assertSame($value['code'], $response->getStatusCode());
      $this->assertSame($value['error'], $parsed_response['error']);
    }
  }

}
