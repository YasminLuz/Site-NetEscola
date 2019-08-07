<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "container",
 *   title = @Translation("Container box"),
 *   description = @Translation("Container for content."),
 *   icon = "fa fa-hdd-o",
 * )
 */

class ContainerShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs = [], $text = '', $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    //dpm($attrs);
    $attrs = !is_array($attrs) ? array() : $attrs;
    $attrs['class'] = 'container ' . (isset($attrs['class']) ? $attrs['class'] : '');
    $theme_array = [
      '#theme' => 'progressive_shortcodes_container',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }
}
