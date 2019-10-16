<?php

namespace Drupal\quizzer;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Link;

/**
 * Defines a class to build a listing of Qiz quiz entities.
 *
 * @ingroup quizzer
 */
class QizQuizListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('Qiz quiz ID');
    $header['name'] = $this->t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /* @var \Drupal\quizzer\Entity\QizQuiz $entity */
    $row['id'] = $entity->id();
    $row['name'] = Link::createFromRoute(
      $entity->label(),
      'entity.qiz_quiz.edit_form',
      ['qiz_quiz' => $entity->id()]
    );
    return $row + parent::buildRow($entity);
  }

}
