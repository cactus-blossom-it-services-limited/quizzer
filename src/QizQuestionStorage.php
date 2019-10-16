<?php

namespace Drupal\quizzer;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\quizzer\Entity\QizQuestionInterface;

/**
 * Defines the storage handler class for Qiz question entities.
 *
 * This extends the base storage class, adding required special handling for
 * Qiz question entities.
 *
 * @ingroup quizzer
 */
class QizQuestionStorage extends SqlContentEntityStorage implements QizQuestionStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(QizQuestionInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {qiz_question_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {qiz_question_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(QizQuestionInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {qiz_question_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('qiz_question_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
