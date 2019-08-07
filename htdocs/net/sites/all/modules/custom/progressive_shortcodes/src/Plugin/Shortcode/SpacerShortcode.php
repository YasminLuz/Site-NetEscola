<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "spacer",
 *   title = @Translation("Spacer"),
 *   description = @Translation("Spacer without any content."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-square-o",
 *   description_field = "height"
 * )
 */

class SpacerShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = 'spacer ' . (isset($attrs['class']) ? $attrs['class'] : '');
    $theme_array = [
      '#theme' => 'progressive_shortcodes_spacer',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $form['style_height'] = [
      '#type' => 'textfield',
      '#title' => t('Height'),
      '#default_value' => isset($attrs['style_height']) ? $attrs['style_height'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];

    return $form;
  }
}
