<?php

namespace Drupal\quizzer\Controller;

use Drupal\Component\Utility\Xss;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\Renderer;
use Drupal\Core\Url;
use Drupal\quizzer\Entity\QizResultInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class QizResultController.
 *
 *  Returns responses for Qiz result routes.
 */
class QizResultController extends ControllerBase implements ContainerInjectionInterface {


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
   * Constructs a new QizResultController.
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
   * Displays a Qiz result revision.
   *
   * @param int $qiz_result_revision
   *   The Qiz result revision ID.
   *
   * @return array
   *   An array suitable for drupal_render().
   */
  public function revisionShow($qiz_result_revision) {
    $qiz_result = $this->entityTypeManager()->getStorage('qiz_result')
      ->loadRevision($qiz_result_revision);
    $view_builder = $this->entityTypeManager()->getViewBuilder('qiz_result');

    return $view_builder->view($qiz_result);
  }

  /**
   * Page title callback for a Qiz result revision.
   *
   * @param int $qiz_result_revision
   *   The Qiz result revision ID.
   *
   * @return string
   *   The page title.
   */
  public function revisionPageTitle($qiz_result_revision) {
    $qiz_result = $this->entityTypeManager()->getStorage('qiz_result')
      ->loadRevision($qiz_result_revision);
    return $this->t('Revision of %title from %date', [
      '%title' => $qiz_result->label(),
      '%date' => $this->dateFormatter->format($qiz_result->getRevisionCreationTime()),
    ]);
  }

  /**
   * Generates an overview table of older revisions of a Qiz result.
   *
   * @param \Drupal\quizzer\Entity\QizResultInterface $qiz_result
   *   A Qiz result object.
   *
   * @return array
   *   An array as expected by drupal_render().
   */
  public function revisionOverview(QizResultInterface $qiz_result) {
    $account = $this->currentUser();
    $qiz_result_storage = $this->entityTypeManager()->getStorage('qiz_result');

    $langcode = $qiz_result->language()->getId();
    $langname = $qiz_result->language()->getName();
    $languages = $qiz_result->getTranslationLanguages();
    $has_translations = (count($languages) > 1);
    $build['#title'] = $has_translations ? $this->t('@langname revisions for %title', ['@langname' => $langname, '%title' => $qiz_result->label()]) : $this->t('Revisions for %title', ['%title' => $qiz_result->label()]);

    $header = [$this->t('Revision'), $this->t('Operations')];
    $revert_permission = (($account->hasPermission("revert all qiz result revisions") || $account->hasPermission('administer qiz result entities')));
    $delete_permission = (($account->hasPermission("delete all qiz result revisions") || $account->hasPermission('administer qiz result entities')));

    $rows = [];

    $vids = $qiz_result_storage->revisionIds($qiz_result);

    $latest_revision = TRUE;

    foreach (array_reverse($vids) as $vid) {
      /** @var \Drupal\quizzer\QizResultInterface $revision */
      $revision = $qiz_result_storage->loadRevision($vid);
      // Only show revisions that are affected by the language that is being
      // displayed.
      if ($revision->hasTranslation($langcode) && $revision->getTranslation($langcode)->isRevisionTranslationAffected()) {
        $username = [
          '#theme' => 'username',
          '#account' => $revision->getRevisionUser(),
        ];

        // Use revision link to link to revisions that are not active.
        $date = $this->dateFormatter->format($revision->getRevisionCreationTime(), 'short');
        if ($vid != $qiz_result->getRevisionId()) {
          $link = $this->l($date, new Url('entity.qiz_result.revision', [
            'qiz_result' => $qiz_result->id(),
            'qiz_result_revision' => $vid,
          ]));
        }
        else {
          $link = $qiz_result->link($date);
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
              Url::fromRoute('entity.qiz_result.translation_revert', [
                'qiz_result' => $qiz_result->id(),
                'qiz_result_revision' => $vid,
                'langcode' => $langcode,
              ]) :
              Url::fromRoute('entity.qiz_result.revision_revert', [
                'qiz_result' => $qiz_result->id(),
                'qiz_result_revision' => $vid,
              ]),
            ];
          }

          if ($delete_permission) {
            $links['delete'] = [
              'title' => $this->t('Delete'),
              'url' => Url::fromRoute('entity.qiz_result.revision_delete', [
                'qiz_result' => $qiz_result->id(),
                'qiz_result_revision' => $vid,
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

    $build['qiz_result_revisions_table'] = [
      '#theme' => 'table',
      '#rows' => $rows,
      '#header' => $header,
    ];

    return $build;
  }

}
