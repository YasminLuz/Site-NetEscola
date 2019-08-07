<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "sitemap",
 *   title = @Translation("Sitemap"),
 *   description = @Translation("Website Sitemap."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-sitemap",
 *   description_field = "machine_name"
 * )
 */

class SitemapShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $menu_tree = (isset($attrs['machine_name']) && $attrs['machine_name']) ? render_menu($attrs['machine_name'], 'sitemap') : '';
    $theme_array = [
      '#theme' => 'progressive_shortcodes_sitemap',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#menu_tree' => $menu_tree,
    ];

    return $this->render($theme_array);
  }
}
