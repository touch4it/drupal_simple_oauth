<?php

namespace Drupal\Tests\simple_oauth\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\simple_oauth\Entities\RefreshTokenEntity;
use Drupal\user\RoleInterface;

/**
 * Class RefreshFunctionalTest
 *
 * @package Drupal\Tests\simple_oauth\Functional
 *
 * @group simple_oauth
 */
class RefreshFunctionalTest extends TokenBearerFunctionalTestBase {

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
    $this->refreshToken = '';
  }


  /**
   * Test the valid Refresh grant.
   */
  public function testRefreshGrant() {
    // 1. Test the valid response.
    $num_roles = mt_rand(1, count($this->additionalRoles));
    $requested_roles = array_slice($this->additionalRoles, 0, $num_roles);
    $valid_payload = [
      'grant_type' => 'refresh_token',
      'client_id' => $this->client->uuid(),
      'client_secret' => $this->clientSecret,
      'refresh_token' => $this->refreshToken,
      'scope' => implode(' ', array_map(function (RoleInterface $role) {
        return $role->id();
      }, $requested_roles)),
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
  public function testInvalidRefreshGrant() {
    $num_roles = mt_rand(1, count($this->additionalRoles));
    $requested_roles = array_slice($this->additionalRoles, 0, $num_roles);
    $valid_payload = [
      'grant_type' => 'refresh_token',
      'client_id' => $this->client->uuid(),
      'client_secret' => $this->clientSecret,
      'refresh_token' => $this->refreshToken,
      'scope' => implode(' ', array_map(function (RoleInterface $role) {
        return $role->id();
      }, $requested_roles)),
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

}
