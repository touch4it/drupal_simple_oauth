<?php


namespace Drupal\simple_oauth\Entities;

use \League\OAuth2\Server\Entities\ClientEntityInterface as LeagueClientEntityInterface;

interface ClientEntityInterface extends LeagueClientEntityInterface {

  /**
   * Set the name of the client.
   *
   * @param string $name
   *   The name to set.
   */
  public function setName($name);

  /**
   * Set the URI for the redirection.
   *
   * @param string $uri
   *   The URI to set.
   */
  public function setRedirectUri($uri);

  /**
   * Returns the associated Drupal entity.
   *
   * @return \Drupal\simple_oauth\Entity\Oauth2ClientInterface
   *   The Drupal entity.
   */
  public function getEntity();

}
