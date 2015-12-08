<?php

/**
 * @file
 * Contains access_token.page.inc..
 *
 * Page callback for Access Token entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Access Token templates.
 *
 * Default template: access-token.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_access_token(array &$variables) {
  // Fetch AccessToken Entity Object.
  $access_token = $variables['access_token'];

  $class = $access_token['#access_token']->isRefreshToken() ? 'access_token--refresh' : 'access_token--access';
  $variables['attributes']['class'] = empty($variables['attributes']['class']) ? [] : $variables['attributes']['class'];
  $variables['attributes']['class'][] = $class;

  // Helpful $content variable for templates.
  foreach (Element::children($access_token) as $key) {
    $variables['content'][$key] = $access_token[$key];
    $variables['content']['#attached'] = [
      'library' => ['simple_oauth/drupal.access_token'],
    ];
  }
}
