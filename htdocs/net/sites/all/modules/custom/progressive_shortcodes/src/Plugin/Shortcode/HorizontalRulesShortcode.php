<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "hr",
 *   title = @Translation("Horizontal Rules"),
 *   description = @Translation("Horizontal Line with spaces."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-ellipsis-h",
 *   description_field = "type"
 * )
 */

class HorizontalRulesShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = (isset($attrs['class']) ? $attrs['class'] : '') . (isset($attrs['type']) ? ' ' . $attrs['type'] : '');

    $theme_array = [
      '#theme' => 'progressive_shortcodes_hr',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $types = [
      '' => t('Default'),
      'no-line' => t('No Line'),
      'shadow' => t('Shadow'),
      'dotted' => t('Dotted'),
      'dashed' => t('Dashed'),
      'double' => t('Double'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Type'),
      '#options' => $types,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];

    return $form;
  }
}
