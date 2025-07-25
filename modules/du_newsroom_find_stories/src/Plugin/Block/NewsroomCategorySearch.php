<?php

namespace Drupal\du_newsroom_find_stories\Plugin\Block;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Block\BlockPluginInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides Category Search Block on Newsroom Find Stories.
 *
 * @Block(
 *   id = "du_news_stories_by_category_block",
 *   admin_label = @Translation("Find News Stories by Category"),
 *   category = @Translation("Custom news stories by categories block")
 * )
 */
class NewsroomCategorySearch extends BlockBase implements BlockPluginInterface {

  /**
   * {@inheritdoc}
   */
  public function build() {

    // Get article categories.
    $tree = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree(
      'article_categories', 0, 1, TRUE
    );

    $terms = [];

    foreach ($tree as $term) {
      $terms[] = $term->get('tid')->getString();
    }

    return [
      'categories' => $terms,
      '#theme' => 'block__du_news_stories_by_category_block',
      // '#categories' => $terms,.
      '#form' => NULL,
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function blockAccess(AccountInterface $account) {
    return AccessResult::allowedIfHasPermission($account, 'access content');
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $config = $this->getConfiguration();

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['my_block_settings'] = $form_state->getValue('my_block_settings');
  }

}
