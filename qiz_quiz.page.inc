<?php

/**
 * @file
 * Contains qiz_quiz.page.inc.
 *
 * Page callback for Qiz quiz entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Qiz quiz templates.
 *
 * Default template: qiz_quiz.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_qiz_quiz(array &$variables) {
  // Fetch QizQuiz Entity Object.
  $qiz_quiz = $variables['elements']['#qiz_quiz'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
