<?php

namespace Drupal\quizzer;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Language\LanguageInterface;
use Drupal\quizzer\Entity\QizResultInterface;

/**
 * Defines the storage handler class for Qiz result entities.
 *
 * This extends the base storage class, adding required special handling for
 * Qiz result entities.
 *
 * @ingroup quizzer
 */
class QizResultStorage extends SqlContentEntityStorage implements QizResultStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(QizResultInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {qiz_result_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {qiz_result_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function countDefaultLanguageRevisions(QizResultInterface $entity) {
    return $this->database->query('SELECT COUNT(*) FROM {qiz_result_field_revision} WHERE id = :id AND default_langcode = 1', [':id' => $entity->id()])
      ->fetchField();
  }

  /**
   * {@inheritdoc}
   */
  public function clearRevisionsLanguage(LanguageInterface $language) {
    return $this->database->update('qiz_result_revision')
      ->fields(['langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED])
      ->condition('langcode', $language->getId())
      ->execute();
  }

}
