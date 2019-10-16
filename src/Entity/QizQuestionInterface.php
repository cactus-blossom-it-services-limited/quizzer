<?php

namespace Drupal\quizzer\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Qiz question entities.
 *
 * @ingroup quizzer
 */
interface QizQuestionInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Qiz question name.
   *
   * @return string
   *   Name of the Qiz question.
   */
  public function getName();

  /**
   * Sets the Qiz question name.
   *
   * @param string $name
   *   The Qiz question name.
   *
   * @return \Drupal\quizzer\Entity\QizQuestionInterface
   *   The called Qiz question entity.
   */
  public function setName($name);

  /**
   * Gets the Qiz question creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Qiz question.
   */
  public function getCreatedTime();

  /**
   * Sets the Qiz question creation timestamp.
   *
   * @param int $timestamp
   *   The Qiz question creation timestamp.
   *
   * @return \Drupal\quizzer\Entity\QizQuestionInterface
   *   The called Qiz question entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Qiz question revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Qiz question revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\quizzer\Entity\QizQuestionInterface
   *   The called Qiz question entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Qiz question revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Qiz question revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\quizzer\Entity\QizQuestionInterface
   *   The called Qiz question entity.
   */
  public function setRevisionUserId($uid);

}
