<?php

/**
 * @file
 * Contains \Drupal\progressive_cms\Plugin\Block\NdProgressivePageTitle.
 */

namespace Drupal\progressive_cms\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "nd_progressive_page_title",
 *   admin_label = @Translation("Progressive: Page Title")
 * )
 */
class NdProgressivePageTitle extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $theme_array = [
      '#theme' => 'progressive_cms_page_title',
      '#title' => drupal_get_title(),
    ];

    return [
      '#markup' => render($theme_array),
    ];
  }
}
