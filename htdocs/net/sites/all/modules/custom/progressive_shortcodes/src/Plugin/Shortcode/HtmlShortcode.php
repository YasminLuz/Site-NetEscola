<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * The image shortcode.
 *
 * @Shortcode(
 *   id = "html",
 *   title = @Translation("HTML"),
 *   description = @Translation("HTML code."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-code",
 *   description_field = "text"
 * )
 */
class HtmlShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $text = str_replace(
      ['<table', '<ul>', '<ol>'],
      ['<table class="table table-bordered table-striped"', '<ul class="list">', '<ol class="list">'],
      $text
    );
    if (isset($attrs['format'])) {
      $text = check_markup($text, $attrs['format']);
    }
    $attrs_output = _progressive_shortcodes_shortcode_attributes($attrs);
    if ($attrs_output) {
      return '<div ' . $attrs_output . '>' . $text . '</div>';
    }
    return $text;
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    return nd_visualshortcodes_shortcode_html_settings($attrs, $text);
  }
}
