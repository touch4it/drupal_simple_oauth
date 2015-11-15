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
 * Default template: access_token.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_access_token(array &$variables) {
  // Fetch AccessToken Entity Object.
  $access_token = $variables['elements']['#access_token'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
