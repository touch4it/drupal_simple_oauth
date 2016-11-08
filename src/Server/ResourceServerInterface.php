<?php


namespace Drupal\simple_oauth\Server;


use Symfony\Component\HttpFoundation\Request;

interface ResourceServerInterface {

  /**
   * Determine the access token validity.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *
   * @throws \League\OAuth2\Server\Exception\OAuthServerException
   *
   * @return \Symfony\Component\HttpFoundation\Request
   */
  public function validateAuthenticatedRequest(Request $request);
}
