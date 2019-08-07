<?php

/**
 * @file
 * Contains \Drupal\progressive_cms\Plugin\Block\NdProgressiveBreadcrumbsLine.
 */

namespace Drupal\progressive_cms\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "nd_progressive_breadcrumbs_line",
 *   admin_label = @Translation("Progressive: Breadcrumbs line")
 * )
 */
class NdProgressiveBreadcrumbsLine extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $theme_array = [
      '#theme' => 'progressive_cms_breadcrumbs_line',
    ];

    return [
      '#markup' => render($theme_array),
    ];
  }
}
