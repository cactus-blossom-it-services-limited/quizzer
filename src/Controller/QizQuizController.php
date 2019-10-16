<?php

namespace Drupal\quizzer\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\quizzer\Entity\QizQuizInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class QizQuizController.
 *
 *  Returns responses for Qiz quiz routes.
 */
class QizQuizController extends ControllerBase implements ContainerInjectionInterface {


  /**
   * The date formatter.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * The renderer.
   *
   * @var \Drupal\Core\Render\Renderer
   */
  protected $renderer;

  /**
   * Constructs a new QizQuizController.
   *
   * @param \Drupal\Core\Datetime\DateFormatter $date_formatter
   *   The date formatter.
   * @param \Drupal\Core\Render\Renderer $renderer
   *   The renderer.
   */
  public function __construct(DateFormatter $date_formatter, Renderer $renderer) {
    $this->dateFormatter = $date_formatter;
    $this->renderer = $renderer;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('date.formatter'),
      $container->get('renderer')
    );
  }

  /**
   * Displays a Qiz quiz revision.
   *
   * @param int $qiz_quiz_revision
   *   The Qiz quiz revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($qiz_quiz_revision) {
    $qiz_quiz = $this->entityTypeManager()->getStorage('qiz_quiz')
      ->loadRevision($qiz_quiz_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('qiz_quiz');

    return $view_builder->view($qiz_quiz);
  }

  /**
   * Page title callback for a Qiz quiz revision.
   *
   * @param int $qiz_quiz_revision
   *   The Qiz quiz revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($qiz_quiz_revision) {
    $qiz_quiz = $this->entityTypeManager()->getStorage('qiz_quiz')
      ->loadRevision($qiz_quiz_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $qiz_quiz->label(),
      '%date' => $this->dateFormatter->format($qiz_quiz->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Qiz quiz.
   *
   * @param \Drupal\quizzer\Entity\QizQuizInterface $qiz_quiz
   *   A Qiz quiz object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(QizQuizInterface $qiz_quiz) {
    $account = $this->currentUser();
    $qiz_quiz_storage = $this->entityTypeManager()->getStorage('qiz_quiz');

    $langcode = $qiz_quiz->language()->getId();
    $langname = $qiz_quiz->language()->getName();
    $languages = $qiz_quiz->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $qiz_quiz->label()]) : $this->t('Revisions for %title', ['%title' => $qiz_quiz->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all qiz quiz revisions") || $account->hasPermission('administer qiz quiz entities')));
    $delete_permission = (($account->hasPermission("delete all qiz quiz revisions") || $account->hasPermission('administer qiz quiz entities')));

    $rows = [];

    $vids = $qiz_quiz_storage->revisionIds($qiz_quiz);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\quizzer\QizQuizInterface $revision */
      $revision = $qiz_quiz_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $qiz_quiz->getRevisionId()) {
          $link = $this->l($date, new Url('entity.qiz_quiz.revision', [
            'qiz_quiz' => $qiz_quiz->id(),
            'qiz_quiz_revision' => $vid,
          ]));
        }
        else {
          $link = $qiz_quiz->link($date);
        }

        $row = [];
        $column = [
          'data' => [
            '#type' => 'inline_template',
            '#template' => '{% trans %}{{ date }} by {{ username }}{% endtrans %}{% if message %}<p class="revision-log">{{ message }}</p>{% endif %}',
            '#context' => [
              'date' => $link,
              'username' => $this->renderer->renderPlain($username),
              'message' => [
                '#markup' => $revision->getRevisionLogMessage(),
                '#allowed_tags' => Xss::getHtmlTagList(),
              ],
            ],
          ],
        ];
        $row[] = $column;

        if ($latest_revision) {
          $row[] = [
            'data' => [
              '#prefix' => '<em>',
              '#markup' => $this->t('Current revision'),
              '#suffix' => '</em>',
            ],
          ];
          foreach ($row as &$current) {
            $current['class'] = ['revision-current'];
          }
          $latest_revision = FALSE;
        }
        else {
          $links = [];
          if ($revert_permission) {
            $links['revert'] = [
              'title' => $this->t('Revert'),
              'url' => $has_translations ?
              Url::fromRoute('entity.qiz_quiz.translation_revert', [
                'qiz_quiz' => $qiz_quiz->id(),
                'qiz_quiz_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.qiz_quiz.revision_revert', [
                'qiz_quiz' => $qiz_quiz->id(),
                'qiz_quiz_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.qiz_quiz.revision_delete', [
                'qiz_quiz' => $qiz_quiz->id(),
                'qiz_quiz_revision' => $vid,
              ]),
            ];
          }

          $row[] = [
            'data' => [
              '#type' => 'operations',
              '#links' => $links,
            ],
          ];
        }

        $rows[] = $row;
      }
    }

    $build['qiz_quiz_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
