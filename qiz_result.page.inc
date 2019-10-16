<?php

/**
 * @file
 * Contains qiz_result.page.inc.
 *
 * Page callback for Qiz result entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Qiz result templates.
 *
 * Default template: qiz_result.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_qiz_result(array &$variables) {
  // Fetch QizResult Entity Object.
  $qiz_result = $variables['elements']['#qiz_result'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
