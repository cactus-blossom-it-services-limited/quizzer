<?php

namespace Drupal\quizzer;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\quizzer\Entity\QizQuizInterface;

/**
 * Defines the storage handler class for Qiz quiz entities.
 *
 * This extends the base storage class, adding required special handling for
 * Qiz quiz entities.
 *
 * @ingroup quizzer
 */
class QizQuizStorage extends SqlContentEntityStorage implements QizQuizStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(QizQuizInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {qiz_quiz_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {qiz_quiz_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(QizQuizInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {qiz_quiz_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('qiz_quiz_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
