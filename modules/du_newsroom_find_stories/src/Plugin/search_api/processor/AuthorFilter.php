<?php

namespace Drupal\du_newsroom_find_stories\Plugin\search_api\processor;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Plugin\PluginFormTrait;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\node\NodeInterface;

/**
 * Provides author filtering for new articles.
 *
 * @SearchApiProcessor(
 *   id = "author_filter",
 *   label = @Translation("Author filter"),
 *   description = @Translation("Filters news authors."),
 *   stages = {
 *     "alter_items" = 0,
 *   },
 * )
 */
class AuthorFilter extends ProcessorPluginBase implements PluginFormInterface {

  use PluginFormTrait;

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index) {
    foreach ($index->getDatasources() as $datasource) {
      if ($datasource->getEntityTypeId() == 'node') {
        return TRUE;
      }
    }
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function alterIndexedItems(array &$items) {
    $valid_authors = valid_authors();

    /** @var \Drupal\search_api\Item\ItemInterface $item */
    foreach ($items as $item_id => $item) {
      $node = $item->getOriginalObject()->getValue();
      if (!($node instanceof NodeInterface)) {
        continue;
      }

      if ($node->bundle() == 'article') {
        $valid = FALSE;
        $authors = $node->get('field_article_byline_author')->referencedEntities();
        foreach ($authors as $author) {
          if (in_array($author->getName(), $valid_authors)) {
            $valid = TRUE;
          }
        }
        if (!$valid) {
          unset($items[$item_id]);
        }
      }
    }
  }

}
