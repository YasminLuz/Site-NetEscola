<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "row",
 *   title = @Translation("Row for columns"),
 *   description = @Translation("Container for cols."),
 *   child_shortcode = "col",
 *   icon = "fa fa-th-large",
 *   description_field = "height"
 * )
 */

class RowShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs = is_array($attrs) ? $attrs : [];
    $attrs['class'] = 'row';
    $theme_array = [
      '#theme' => 'progressive_shortcodes_row',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }
}
