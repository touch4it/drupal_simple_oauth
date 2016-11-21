<?php


namespace Drupal\simple_oauth\Plugin\Oauth2Grant;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\simple_oauth\Plugin\Oauth2GrantBase;
use League\OAuth2\Server\Grant\ImplicitGrant;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Implicit
 *
 * @package Drupal\simple_oauth\Plugin\Oauth2Grant
 *
 * @Oauth2Grant(
 *   id = "implicit",
 *   label = @Translation("Implicit")
 * )
 */
class Implicit extends Oauth2GrantBase {

  /**
   * @var \DateTime
   */
  protected $expiration;

  /**
   * Class constructor.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactoryInterface $config_factory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $settings = $config_factory->get('simple_oauth.settings');
    $this->expiration = new \DateInterval(sprintf('PT%dS', $settings->get('expiration')));
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getGrantType() {
    return new ImplicitGrant($this->expiration);
  }

}
