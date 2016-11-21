<?php


namespace Drupal\simple_oauth\Plugin\Oauth2Grant;

use Drupal\simple_oauth\Plugin\Oauth2GrantBase;
use League\OAuth2\Server\Grant\RefreshTokenGrant;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RefreshToken
 *
 * @package Drupal\simple_oauth\Plugin\Oauth2Grant
 *
 * @Oauth2Grant(
 *   id = "refresh_token",
 *   label = @Translation("Refresh Token")
 * )
 */
class RefreshToken extends Oauth2GrantBase {

  /**
   * @var \League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface
   */
  protected $refreshTokenRepository;

  /**
   * Class constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, RefreshTokenRepositoryInterface $refresh_token_repository) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->refreshTokenRepository = $refresh_token_repository;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('simple_oauth.repositories.refresh_token')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getGrantType() {
    return new RefreshTokenGrant($this->refreshTokenRepository);
  }

}
