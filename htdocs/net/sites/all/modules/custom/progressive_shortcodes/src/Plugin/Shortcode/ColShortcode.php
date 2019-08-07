<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "col",
 *   title = @Translation("Column"),
 *   description = @Translation("Column with size settings."),
 *   process_backend_callback = "_nd_visualshortcodes_backend_element",
 *   icon = "fa fa-columns",
 * )
 */

class ColShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = isset($attrs['class']) ? $attrs['class'] : '';
    if (isset($attrs['phone'])) {
      $attrs['class'] .= ' col-xs-' . $attrs['phone'];
    }
    if (isset($attrs['tablet'])) {
      $attrs['class'] .= ' col-sm-' . $attrs['tablet'];
    }
    if (isset($attrs['desktop'])) {
      $attrs['class'] .= ' col-md-' . $attrs['desktop'];
    }
    if (isset($attrs['wide'])) {
      $attrs['class'] .= ' col-lg-' . $attrs['wide'];
    }

    $theme_array = [
      '#theme' => 'progressive_shortcodes_col',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return nd_visualshortcodes_shortcode_col_settings($attrs, $text);
  }
}
