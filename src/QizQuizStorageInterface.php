<?php

namespace Drupal\quizzer;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface QizQuizStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Qiz quiz revision IDs for a specific Qiz quiz.
   *
   * @param \Drupal\quizzer\Entity\QizQuizInterface $entity
   *   The Qiz quiz entity.
   *
   * @return int[]
   *   Qiz quiz revision IDs (in ascending order).
   */
  public function revisionIds(QizQuizInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Qiz quiz author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Qiz quiz revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\quizzer\Entity\QizQuizInterface $entity
   *   The Qiz quiz entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(QizQuizInterface $entity);

  /**
   * Unsets the language for all Qiz quiz with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
