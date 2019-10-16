<?php

namespace Drupal\quizzer\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Qiz result entities.
 *
 * @ingroup quizzer
 */
interface QizResultInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Qiz result name.
   *
   * @return string
   *   Name of the Qiz result.
   */
  public function getName();

  /**
   * Sets the Qiz result name.
   *
   * @param string $name
   *   The Qiz result name.
   *
   * @return \Drupal\quizzer\Entity\QizResultInterface
   *   The called Qiz result entity.
   */
  public function setName($name);

  /**
   * Gets the Qiz result creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Qiz result.
   */
  public function getCreatedTime();

  /**
   * Sets the Qiz result creation timestamp.
   *
   * @param int $timestamp
   *   The Qiz result creation timestamp.
   *
   * @return \Drupal\quizzer\Entity\QizResultInterface
   *   The called Qiz result entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Qiz result revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Qiz result revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\quizzer\Entity\QizResultInterface
   *   The called Qiz result entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Qiz result revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Qiz result revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\quizzer\Entity\QizResultInterface
   *   The called Qiz result entity.
   */
  public function setRevisionUserId($uid);

}
