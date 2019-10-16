<?php

namespace Drupal\quizzer\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Qiz quiz entities.
 *
 * @ingroup quizzer
 */
interface QizQuizInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Qiz quiz name.
   *
   * @return string
   *   Name of the Qiz quiz.
   */
  public function getName();

  /**
   * Sets the Qiz quiz name.
   *
   * @param string $name
   *   The Qiz quiz name.
   *
   * @return \Drupal\quizzer\Entity\QizQuizInterface
   *   The called Qiz quiz entity.
   */
  public function setName($name);

  /**
   * Gets the Qiz quiz creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Qiz quiz.
   */
  public function getCreatedTime();

  /**
   * Sets the Qiz quiz creation timestamp.
   *
   * @param int $timestamp
   *   The Qiz quiz creation timestamp.
   *
   * @return \Drupal\quizzer\Entity\QizQuizInterface
   *   The called Qiz quiz entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Qiz quiz revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Qiz quiz revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\quizzer\Entity\QizQuizInterface
   *   The called Qiz quiz entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Qiz quiz revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Qiz quiz revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\quizzer\Entity\QizQuizInterface
   *   The called Qiz quiz entity.
   */
  public function setRevisionUserId($uid);

}
