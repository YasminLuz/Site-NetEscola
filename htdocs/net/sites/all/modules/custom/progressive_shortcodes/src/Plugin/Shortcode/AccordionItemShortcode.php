<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "accordion",
 *   title = @Translation("Accordion Item"),
 *   description = @Translation("Accordion item."),
 *   icon = "fa fa-minus",
 *   description_field = "title"
 * )
 */

class AccordionItemShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    global $accordion_count, $accordion_id, $accordion_filter;
    if (empty($accordion_id)) {
      $accordion_id = rand(1, 999999);
    }
    if (!isset($accordion_filter)) {
      $accordion_filter = [];
    }
    $accordion_count = empty($accordion_count) ? rand(1, 999999) : $accordion_count + 1;
    $attrs['class'] = (isset($attrs['class']) ? $attrs['class'] . ' ': '') . 'panel ';
    $attrs['class'] .= isset($attrs['color']) && $attrs['color'] ? $attrs['color'] : 'panel-default';
    $attrs['class'] .= isset($attrs['active']) && $attrs['active'] ? ' active' : '';
    if (isset($attrs['filter_category'])) {
      $filter_category = isset($attrs['filter_category']) ? strtolower(preg_replace('/[^\w]/', '-', $attrs['filter_category'])) : '';
      $attrs['class'] .= ' ' . $filter_category;
      $accordion_filter[$filter_category] = $attrs['filter_category'];
    }

    $theme_array = [
      '#theme' => 'progressive_shortcodes_accordion_item',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#accordion_id' => $accordion_id,
      '#accordion_count' => $accordion_count,
      '#title' => $attrs['title'],
      '#active' => isset($attrs['active']) && $attrs['active'] ? ' in' : '',
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $form['active'] = [
      '#title' => t('Active'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['active']) ? $attrs['active'] : '',
    ];
    $colors = [
      'panel-default' => t('Default'),
      'panel-danger' => t('Red'),
      'panel-info' => t('Blue'),
      'panel-success' => t('Green'),
      'panel-primary' => t('Dark Blue'),
    ];
    $form['color'] = [
      '#type' => 'radios',
      '#title' => t('Color'),
      '#options' => $colors,
      '#default_value' => isset($attrs['color']) ? $attrs['color'] : 'panel-default',
      '#attributes' => [
        'class' => ['color-radios'],
      ],
    ];
    $form['title'] = [
      '#type' => 'textfield' ,
      '#title' => t('Title'),
      '#default_value' => isset($attrs['title']) ? $attrs['title'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];
    $form['filter_category'] = [
      '#type' => 'textfield' ,
      '#title' => t('Filter Category'),
      '#default_value' => isset($attrs['filter_category']) ? $attrs['filter_category'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ]
    ];

    return $form;
  }
}
