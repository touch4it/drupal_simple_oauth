<?php

namespace Drupal\Tests\simple_oauth\Unit\Authentication\Provider;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\simple_oauth\Authentication\Provider\SimpleOauthAuthenticationProvider;
use Drupal\Tests\UnitTestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SimpleOauthAuthenticationTest.
 *
 * @package Drupal\Tests\simple_oauth\Unit\Authentication\Provider
 *
 * @coversDefaultClass \Drupal\simple_oauth\Authentication\Provider\SimpleOauthAuthenticationProvider
 * @group simple_oauth
 */
class SimpleOauthAuthenticationTest extends UnitTestCase {

  /**
   * The authentication provider.
   *
   * @var \Drupal\simple_oauth\Authentication\Provider\SimpleOauthAuthenticationProviderInterface
   */
  protected $provider;

  /**
   * @covers ::getTokenValue
   * @covers ::applies
   *
   * @dataProvider getTokenValueProvider
   */
  public function testGetTokenValue(Request $request, $token) {
    $this->assertSame($token, $this->provider->getTokenValue($request));
  }

  public function getTokenValueProvider() {
    $data = [];

    // 1. Authentication header.
    $token = $this->getRandomGenerator()->name();
    $request = new Request();
    $request->headers->set('Authorization', 'Bearer ' . $token);
    $data[] = [$request, $token];

    // 2. Authentication header. Fail: wrong token.
    $token = $this->getRandomGenerator()->name();
    $request = new Request();
    $request->headers->set('Authorization', 'Bearer fail--' . $token);
    $data[] = [$request, 'fail--' . $token];

    // 3. Authentication header. Fail: no token.
    $token = $this->getRandomGenerator()->name();
    $request = new Request();
    $data[] = [$request, NULL];

    // 4. Form encoded parameter.
    $token = $this->getRandomGenerator()->name();
    $request = new Request();
    $request->setMethod(Request::METHOD_POST);
    $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
    $request->request->set('access_token', $token);
    $data[] = [$request, $token];

    // 5. Form encoded parameter. Fail: missing content type.
    $token = $this->getRandomGenerator()->name();
    $request = new Request();
    $request->setMethod(Request::METHOD_POST);
    $request->request->set('access_token', $token);
    $data[] = [$request, NULL];

    // 6. Form encoded parameter. Fail: missing token.
    $request = new Request();
    $request->setMethod(Request::METHOD_POST);
    $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
    $data[] = [$request, NULL];

    // 7. Form encoded parameter. Fail: wrong method.
    $token = $this->getRandomGenerator()->name();
    $request = new Request();
    $request->setMethod(Request::METHOD_GET);
    $request->headers->set('Content-Type', 'application/x-www-form-urlencoded');
    $request->request->set('access_token', $token);
    $data[] = [$request, NULL];

    return $data;
  }

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $config_factory = $this->prophesize(ConfigFactoryInterface::class);
    $entity_manager = $this->prophesize(EntityManagerInterface::class);
    $this->provider = new SimpleOauthAuthenticationProvider(
      $config_factory->reveal(),
      $entity_manager->reveal()
    );
  }



}
