<?php

namespace Drupal\quizzer;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface QizResultStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Qiz result revision IDs for a specific Qiz result.
   *
   * @param \Drupal\quizzer\Entity\QizResultInterface $entity
   *   The Qiz result entity.
   *
   * @return int[]
   *   Qiz result revision IDs (in ascending order).
   */
  public function revisionIds(QizResultInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Qiz result author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Qiz result revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

  /**
   * Counts the number of revisions in the default language.
   *
   * @param \Drupal\quizzer\Entity\QizResultInterface $entity
   *   The Qiz result entity.
   *
   * @return int
   *   The number of revisions in the default language.
   */
  public function countDefaultLanguageRevisions(QizResultInterface $entity);

  /**
   * Unsets the language for all Qiz result with the given language.
   *
   * @param \Drupal\Core\Language\LanguageInterface $language
   *   The language object.
   */
  public function clearRevisionsLanguage(LanguageInterface $language);

}
