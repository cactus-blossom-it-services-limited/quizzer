<?php

namespace Drupal\quizzer\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Qiz result entities.
 */
class QizResultViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
