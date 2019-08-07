<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "pricing_table_row",
 *   title = @Translation("Pricing Table Row"),
 *   description = @Translation("Pricing Table element."),
 *   icon = "fa fa-money",
 *   description_field = "title"
 * )
 */
class PricingTableRowShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['dis'] = isset($attrs['disabled']) ? $attrs['disabled'] : (isset($attrs['dis']) ? $attrs['dis'] : 0);
    $attrs['class'] = (isset($attrs['class']) ? $attrs['class'] : '') . ($attrs['dis'] ? '' : 'active');
    $title = isset($attrs['title']) ? $attrs['title'] : '';

    $theme_array = [
      '#theme' => 'progressive_shortcodes_pricing_table_row',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#checked' => isset($attrs['check']) ? $attrs['check'] : FALSE,
      '#title' => $title,
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => isset($attrs['title']) ? $attrs['title'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#description' => t('You can also insert in this shortcode any other shortcode')
    ];
    $attrs['dis'] = isset($attrs['disabled']) ? $attrs['disabled'] : (isset($attrs['dis']) ? $attrs['dis'] : 0);
    $form['dis'] = [
      '#type' => 'checkbox',
      '#title' => t('Disabled'),
      '#default_value' => $attrs['dis'],
    ];
    $form['check'] = [
      '#type' => 'checkbox',
      '#title' => t('Check icon'),
      '#default_value' => isset($attrs['check']) ? $attrs['check'] : 0,
    ];

    return $form;
  }
}
