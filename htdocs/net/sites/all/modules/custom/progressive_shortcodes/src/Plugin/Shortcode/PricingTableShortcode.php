<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "pricing_table",
 *   title = @Translation("Pricing Table"),
 *   description = @Translation("Responsive Pricing Table."),
 *   child_shortcode = "pricing_table_row",
 *   icon = "fa fa-usd",
 *   description_field = "title"
 * )
 */

class PricingTableShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = isset($attrs['class']) ? $attrs['class'] . ' ' : '';
    $colors = array('default' => 'package', 'orange' => 'pricing pricing-warning','red' => 'pricing-error pricing', 'green' => 'pricing-success pricing', 'blue' => 'pricing-info pricing');
    $btn_colors = array('default' => 'default', 'orange' => 'warning','red' => 'error', 'green' => 'success', 'blue' => 'info');
    $attrs['class'] .= isset($attrs['color']) && isset($colors[$attrs['color']]) ? $colors[$attrs['color']] : '';
    $link = isset($attrs['link']) ? $attrs['link'] : '#';
    $read_more_link = isset($attrs['read_more_link']) ? $attrs['read_more_link'] : $link;
    $icon_colors = array('orange' => '#1e1e1e', 'red' => '#de2a61', 'green' => '#9ab71a', 'blue' => '#35beeb');
    $icon_color = isset($attrs['color']) && isset($icon_colors[$attrs['color']]) ? $icon_colors[$attrs['color']]  : '#f89406';
    $pricing_table_rows = $text;
    $pricing_table_rows = !empty($pricing_table_rows) ? $pricing_table_rows : '';

    $theme_array = [
      '#theme' => 'progressive_shortcodes_pricing_table',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#stars' => (isset($attrs['stars']) && (int)$attrs['stars'] <= 5) ? 20 * $attrs['stars'] : '',
      '#link' => $link,
      '#title' => $attrs['title'],
      '#livicon' => isset($attrs['livicon']) ? $attrs['livicon'] : '',
      '#color' => isset($attrs['color']) && $attrs['color'] == 'default' ? 'bg-white rounded' : 'circle',
      '#data_s' => isset($attrs['color']) && $attrs['color'] == 'default' ? 62 : 32,
      '#icon_color' => $icon_color,
      '#description' => isset($attrs['description']) ? $attrs['description'] : '',
      '#price_top_text' => isset($attrs['price_top_text']) ? $attrs['price_top_text'] : '',
      '#price' => $attrs['price'],
      '#price_text' => isset($attrs['price_text']) ? $attrs['price_text'] : '',
      '#pricing_table_rows' => !empty($pricing_table_rows) ? $pricing_table_rows : '',
      '#read_more' => isset($attrs['read_more']) ? $attrs['read_more'] : '',
      '#read_more_link' => $read_more_link,
      '#button' => isset($attrs['button']) ? $attrs['button'] : '',
      '#button_class' => isset($attrs['color']) ? 'btn-' . $btn_colors[$attrs['color']] : 'btn-default',
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
      '#prefix' => '<div class="row"><div class="col-sm-8">',
    ];
    $colors = [
      'orange' => t('Orange'),
      'red' => t('Red'),
      'green' => t('Green'),
      'blue' => t('Blue'),
      'default' => t('Default'),
    ];
    $form['color'] = [
      '#type' => 'radios',
      '#title' => t('Color'),
      '#options' => $colors,
      '#default_value' => isset($attrs['color']) ? $attrs['color'] : 'orange',
      '#attributes' => ['class' => ['color-radios'],],
      '#prefix' => '</div><div class="col-sm-4">',
      '#suffix' => '</div></div>',
    ];
    $form['description'] = [
      '#type' => 'textarea',
      '#rows' => 3,
      '#title' => t('Short Description'),
      '#default_value' => isset($attrs['description']) ? $attrs['description'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];
    $form['price_top_text'] = [
      '#type' => 'textfield',
      '#title' => t('Price Top Text'),
      '#default_value' => isset($attrs['price_top_text']) ? $attrs['price_top_text'] : '',
      '#attributes' => ['class' => ['form-control'],],
      '#prefix' => '<div class="row"><div class="col-sm-4">'
    ];
    $form['price'] = [
      '#type' => 'textfield',
      '#title' => t('Price'),
      '#default_value' => isset($attrs['price']) ? $attrs['price'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class = "col-sm-4">',
    ];
    $form['price_text'] = [
      '#type' => 'textfield',
      '#title' => t('Price small text'),
      '#default_value' => isset($attrs['price_text']) ? $attrs['price_text'] : '',
      '#attributes' => ['class' => ['form-control'],],
      '#prefix' => '</div><div class="col-sm-4">',
      '#suffix' => '</div></div>',
    ];
    $stars = ['', 0, 0.5, 1, 1.5, 2, 2.5, 3, 3.5, 4, 4.5, 5];
    $form['stars'] = [
      '#type' => 'select',
      '#title' => t('Stars'),
      '#options' => array_combine($stars, $stars),
      '#default_value' => isset($attrs['stars']) ? $attrs['stars'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['livicon'] = [
      '#type' => 'textfield',
      '#title' => t('LivIcon'),
      '#autocomplete_route_name' => 'progressive_shortcodes.ajax_fontawesome_icons_autocomplete',
      '#default_value' => isset($attrs['livicon']) ? $attrs['livicon'] : '',
      '#attributes' => ['class' => ['form-control'],],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $form['button'] = [
      '#type' => 'textfield',
      '#title' => t('Button text'),
      '#default_value' => isset($attrs['button']) ? $attrs['button'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['link'] = [
      '#type' => 'textfield',
      '#title' => t('Link'),
      '#default_value' => isset($attrs['link']) ? $attrs['link'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $form['read_more'] = [
      '#type' => 'textfield',
      '#title' => t('Read More text'),
      '#default_value' => isset($attrs['read_more']) ? $attrs['read_more'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['read_more_link'] = [
      '#type' => 'textfield',
      '#title' => t('Read More link'),
      '#default_value' => isset($attrs['read_more_link']) ? $attrs['read_more_link'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];

    return $form;
  }
}
