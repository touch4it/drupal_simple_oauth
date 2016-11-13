<?php


namespace Drupal\simple_oauth\Server;

use Drupal\Core\Config\ConfigFactoryInterface;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\ClientCredentialsGrant;
use League\OAuth2\Server\Grant\PasswordGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class AuthorizationServerFactory implements AuthorizationServerFactoryInterface {

  /**
   * @var \League\OAuth2\Server\Repositories\ClientRepositoryInterface
   */
  protected $clientRepository;

  /**
   * @var \League\OAuth2\Server\Repositories\ScopeRepositoryInterface
   */
  protected $scopeRepository;

  /**
   * @var \League\OAuth2\Server\Repositories\UserRepositoryInterface
   */
  protected $userRepository;

  /**
   * @var \League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface
   */
  protected $accessTokenRepository;

  /**
   * @var \League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface
   */
  protected $refreshTokenRepository;

  /**
   * @var \League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface
   */
  protected $authCodeRepository;

  /**
   * @var string
   */
  protected $privateKeyPath;

  /**
   * @var string
   */
  protected $publicKeyPath;

  /**
   * @var \DateTime
   */
  protected $expiration;

  /**
   * Construct a new AuthorizationServerFactory object.
   */
  public function __construct(
    ClientRepositoryInterface $client_repository,
    ScopeRepositoryInterface $scope_repository,
    UserRepositoryInterface $user_repository,
    AccessTokenRepositoryInterface $access_token_repository,
    RefreshTokenRepositoryInterface $refresh_token_repository,
    AuthCodeRepositoryInterface $auth_code_repository,
    ConfigFactoryInterface $config_factory
  ) {
    $this->clientRepository = $client_repository;
    $this->scopeRepository = $scope_repository;
    $this->userRepository = $user_repository;
    $this->accessTokenRepository = $access_token_repository;
    $this->refreshTokenRepository = $refresh_token_repository;
    $this->authCodeRepository = $auth_code_repository;
    $settings = $config_factory->get('simple_oauth.settings');
    $this->publicKeyPath = $settings->get('public_key');
    $this->privateKeyPath = $settings->get('private_key');
    if (!file_exists($this->publicKeyPath) || !file_exists($this->privateKeyPath)) {
      throw new \InvalidArgumentException(sprintf('You need to set the OAuth2 secret and private keys.'));
    }
    $this->expiration = new \DateInterval(sprintf('PT%dS', $settings->get('expiration')));
  }

  /**
   * {@inheritdoc}
   */
  public function createInstance($grant_type_id) {
    $grant = $this->grantFactory($grant_type_id);
    $server = new AuthorizationServer(
      $this->clientRepository,
      $this->accessTokenRepository,
      $this->scopeRepository,
      $this->privateKeyPath,
      $this->publicKeyPath
    );
    // Enable the password grant on the server with a token TTL of X hours.
    $server->enableGrantType(
      $grant,
      $this->expiration
    );

    return $server;
  }

  /**
   * {@inheritdoc}
   */
  public function grantFactory($grant_type_id) {
    if ($grant_type_id == 'password') {
      return new PasswordGrant($this->userRepository, $this->refreshTokenRepository);
    }
    elseif ($grant_type_id == 'refresh_token') {
      return new RefreshTokenGrant($this->refreshTokenRepository);
    }
    elseif ($grant_type_id == 'client_credentials') {
      return new ClientCredentialsGrant();
    }
    throw new \InvalidArgumentException(sprintf('The grant type %s for OAuth2 does not exist.', $grant_type_id));
  }

}
