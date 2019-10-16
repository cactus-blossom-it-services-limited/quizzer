<?php

namespace Drupal\quizzer\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\quizzer\Entity\QizQuestionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class QizQuestionController.
 *
 *  Returns responses for Qiz question routes.
 */
class QizQuestionController extends ControllerBase implements ContainerInjectionInterface {


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
   * Constructs a new QizQuestionController.
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
   * Displays a Qiz question revision.
   *
   * @param int $qiz_question_revision
   *   The Qiz question revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($qiz_question_revision) {
    $qiz_question = $this->entityTypeManager()->getStorage('qiz_question')
      ->loadRevision($qiz_question_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('qiz_question');

    return $view_builder->view($qiz_question);
  }

  /**
   * Page title callback for a Qiz question revision.
   *
   * @param int $qiz_question_revision
   *   The Qiz question revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($qiz_question_revision) {
    $qiz_question = $this->entityTypeManager()->getStorage('qiz_question')
      ->loadRevision($qiz_question_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $qiz_question->label(),
      '%date' => $this->dateFormatter->format($qiz_question->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Qiz question.
   *
   * @param \Drupal\quizzer\Entity\QizQuestionInterface $qiz_question
   *   A Qiz question object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(QizQuestionInterface $qiz_question) {
    $account = $this->currentUser();
    $qiz_question_storage = $this->entityTypeManager()->getStorage('qiz_question');

    $langcode = $qiz_question->language()->getId();
    $langname = $qiz_question->language()->getName();
    $languages = $qiz_question->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $qiz_question->label()]) : $this->t('Revisions for %title', ['%title' => $qiz_question->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all qiz question revisions") || $account->hasPermission('administer qiz question entities')));
    $delete_permission = (($account->hasPermission("delete all qiz question revisions") || $account->hasPermission('administer qiz question entities')));

    $rows = [];

    $vids = $qiz_question_storage->revisionIds($qiz_question);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\quizzer\QizQuestionInterface $revision */
      $revision = $qiz_question_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $qiz_question->getRevisionId()) {
          $link = $this->l($date, new Url('entity.qiz_question.revision', [
            'qiz_question' => $qiz_question->id(),
            'qiz_question_revision' => $vid,
          ]));
        }
        else {
          $link = $qiz_question->link($date);
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
              Url::fromRoute('entity.qiz_question.translation_revert', [
                'qiz_question' => $qiz_question->id(),
                'qiz_question_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.qiz_question.revision_revert', [
                'qiz_question' => $qiz_question->id(),
                'qiz_question_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.qiz_question.revision_delete', [
                'qiz_question' => $qiz_question->id(),
                'qiz_question_revision' => $vid,
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

    $build['qiz_question_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
