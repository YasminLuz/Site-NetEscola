<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "slider_item",
 *   title = @Translation("Slider Item"),
 *   description = @Translation("Slider Item."),
 *   icon = "fa fa-long-arrow-right",
 * )
 */

class SliderItemShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = 'col-sm-12 col-md-12' . (isset($attrs['class']) ? $attrs['class'] : '');

    $theme_array = [
      '#theme' => 'progressive_shortcodes_slider_item',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }
}
