<?php

/**
 * @file
 * Contains qiz_question.page.inc.
 *
 * Page callback for Qiz question entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Qiz question templates.
 *
 * Default template: qiz_question.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_qiz_question(array &$variables) {
  // Fetch QizQuestion Entity Object.
  $qiz_question = $variables['elements']['#qiz_question'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
