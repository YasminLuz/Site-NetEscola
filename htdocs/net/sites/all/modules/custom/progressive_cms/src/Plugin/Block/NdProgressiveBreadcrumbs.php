<?php

/**
 * @file
 * Contains \Drupal\progressive_cms\Plugin\Block\NdProgressiveBreadcrumbs.
 */

namespace Drupal\progressive_cms\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;

/**
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "nd_progressive_breadcrumbs",
 *   admin_label = @Translation("Progressive: Breadcrumbs")
 * )
 */
class NdProgressiveBreadcrumbs extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $breadcrumb_build = [
      Link::createFromRoute(t('Home'), '<front>'),
    ];
    $breadcrumb = [
      '#theme' => 'breadcrumb',
      '#links' => $breadcrumb_build,
    ];
    $breadcrumbs = \Drupal::service('renderer')->render($breadcrumb);    

    $theme_array = [
      '#theme' => 'progressive_cms_breadcrumbs',
      '#items' => str_replace('<ol', '<ol class="breadcrumb"', $breadcrumbs),
    ];

    return [
      '#markup' => render($theme_array),
    ];
  }
}
