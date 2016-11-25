<?php

namespace Drupal\simple_oauth_3p\Controller;

use Drupal\simple_oauth\Controller\Oauth2Token;
use Drupal\simple_oauth\Entities\UserEntity;
use GuzzleHttp\Psr7\Response;
use League\OAuth2\Server\AuthorizationServer;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class Oauth2Token3p extends Oauth2Token  {

  /**
   * Debug auth code.
   */
  public function codeDebug(Request $request) {
    return JsonResponse::create($request->get('code'));
  }

}
