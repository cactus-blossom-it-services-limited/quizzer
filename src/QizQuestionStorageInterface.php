<?php

namespace Drupal\quizzer;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface QizQuestionStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Qiz question revision IDs for a specific Qiz question.
   *
   * @param \Drupal\quizzer\Entity\QizQuestionInterface $entity
   *   The Qiz question entity.
   *
   * @return int[]
   *   Qiz question revision IDs (in ascending order).
   */
  public function revisionIds(QizQuestionInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Qiz question author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Qiz question revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\quizzer\Entity\QizQuestionInterface $entity
   *   The Qiz question entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(QizQuestionInterface $entity);

  /**
   * Unsets the language for all Qiz question with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
