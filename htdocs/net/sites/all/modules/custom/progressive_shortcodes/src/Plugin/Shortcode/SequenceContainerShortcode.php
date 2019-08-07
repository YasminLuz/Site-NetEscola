<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "sequence",
 *   title = @Translation("Sequence Container"),
 *   description = @Translation("Sequence for any content."),
 *   child_shortcode = "sequence_item",
 *   icon = "fa fa-sort-numeric-asc",
 * )
 */

class SequenceContainerShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = 'steps ' . (isset($attrs['class']) ? $attrs['class'] : '');

    $theme_array = [
      '#theme' => 'progressive_shortcodes_sequence_container',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }
}
