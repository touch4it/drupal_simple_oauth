<?php

namespace Drupal\Tests\simple_oauth\Functional;
use Drupal\user\RoleInterface;

/**
 * Class PasswordFunctionalTest
 *
 * @package Drupal\Tests\simple_oauth\Functional
 *
 * @group simple_oauth
 */
class PasswordFunctionalTest extends TokenBearerFunctionalTestBase {

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
   * Test the GET method.
   */
  public function testPasswordGrant() {
    $num_roles = mt_rand(1, count($this->additionalRoles));
    $requested_roles = array_slice($this->additionalRoles, 0, $num_roles);
    $response = $this->request('POST', $this->url, [
      'form_params' => [
        'grant_type' => 'password',
        'client_id' => $this->client->uuid(),
        'client_secret' => $this->clientSecret,
        'username' => $this->user->getAccountName(),
        'password' => $this->user->pass_raw,
        'scope' => implode(' ', array_map(function (RoleInterface $role) {
          return $role->id();
        }, $requested_roles)),
      ],
    ]);
    $this->assertValidTokenResponse($response, TRUE);
  }

}
