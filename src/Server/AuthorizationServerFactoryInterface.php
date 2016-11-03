<?php

namespace Drupal\simple_oauth\Server;

interface AuthorizationServerFactoryInterface {

  /**
   * Creates a AuthorizationServer with the corresponding grant type.
   *
   * @param string $grant_type_id
   *   The name of the OAuth grant type for this server.
   *
   * @return \League\OAuth2\Server\AuthorizationServer
   *   The authorization server to process the requests.
   */
  public function createInstance($grant_type_id);

  /**
   * Get a grant object based on the grant type ID.
   *
   * @param string $grant_type_id
   *   The ID of the grant.
   *
   * @throws \InvalidArgumentException
   *   If the grant type is not supported.
   *
   * @return \League\OAuth2\Server\Grant\GrantTypeInterface
   */
  public function grantFactory($grant_type_id);

}
