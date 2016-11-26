<?php

namespace Drupal\Tests\simple_oauth\Functional;

use Drupal\Component\Serialization\Json;
use Drupal\user\RoleInterface;

/**
 * Class ClientCredentialsFunctionalTest
 *
 * @package Drupal\Tests\simple_oauth\Functional
 *
 * @group simple_oauth
 */
class ClientCredentialsFunctionalTest extends TokenBearerFunctionalTestBase {

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
  protected $path;

  /**
   * Test the valid ClientCredentials grant.
   */
  public function testClientCredentialsGrant() {
    // 1. Test the valid response.
    $num_roles = mt_rand(1, count($this->additionalRoles));
    $requested_roles = array_slice($this->additionalRoles, 0, $num_roles);
    $valid_payload = [
      'grant_type' => 'client_credentials',
      'client_id' => $this->client->uuid(),
      'client_secret' => $this->clientSecret,
      'scope' => implode(' ', array_map(function (RoleInterface $role) {
        return $role->id();
      }, $requested_roles)),
    ];
    $response = $this->request('POST', $this->url, [
      'form_params' => $valid_payload,
    ]);
    $this->assertValidTokenResponse($response, FALSE);

    // 2. Test the valid without scopes.
    $payload_no_scope = $valid_payload;
    unset($payload_no_scope['scope']);
    $response = $this->request('POST', $this->url, [
      'form_params' => $payload_no_scope,
    ]);
    $this->assertValidTokenResponse($response, FALSE);

  }

  /**
   * Test invalid ClientCredentials grant.
   */
  public function testInvalidClientCredentialsGrant() {
    $num_roles = mt_rand(1, count($this->additionalRoles));
    $requested_roles = array_slice($this->additionalRoles, 0, $num_roles);
    $valid_payload = [
      'grant_type' => 'client_credentials',
      'client_id' => $this->client->uuid(),
      'client_secret' => $this->clientSecret,
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