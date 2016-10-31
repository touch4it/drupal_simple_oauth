<?php

/**
 * @file
 * Contains oauth2_token.page.inc..
 *
 * Page callback for Access Token entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Access Token templates.
 *
 * Default template: oauth2-token.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_oauth2_token(array &$variables) {
  // Fetch Oauth2Token Entity Object.
  $oauth2_token = $variables['oauth2_token'];

  $class = $oauth2_token['#oauth2_token']->isRefreshToken() ? 'oauth2_token--refresh' : 'oauth2_token--access';
  $variables['attributes']['class'] = empty($variables['attributes']['class']) ? [] : $variables['attributes']['class'];
  $variables['attributes']['class'][] = $class;

  // Helpful $content variable for templates.
  foreach (Element::children($oauth2_token) as $key) {
    $variables['content'][$key] = $oauth2_token[$key];
    $variables['content']['#attached'] = [
      'library' => ['simple_oauth/drupal.oauth2_token'],
    ];
  }
}
