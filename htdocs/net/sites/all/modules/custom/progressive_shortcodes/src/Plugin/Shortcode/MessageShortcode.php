<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "message",
 *   title = @Translation("Notification"),
 *   description = @Translation("Dismissable Message."),
 *   icon = "fa fa-info-circle",
 *   description_field = "icon"
 * )
 */

class MessageShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = 'alert ' . (isset($attrs['extra_class']) ? $attrs['extra_class'] : '');
    $attrs['class'] .= isset($attrs['dismissable']) && $attrs['dismissable'] ? ' fade in' : '';
    $attrs['class'] .= isset($attrs['color']) && $attrs['color'] ? ' ' . $attrs['color'] : '';
    $attrs['class'] .= isset($attrs['center']) && $attrs['center'] ? ' text-center' : '';
    $attrs['class'] .= isset($attrs['type']) && $attrs['type'] == 'border' ? ' alert-border alert-dismissable' : '';
    if (isset($attrs['type']) && $attrs['type'] == 'border' && isset($attrs['color_type']) && $attrs['color_type'] && $attrs['custom_color']) {
      $attrs['style_border_color'] = '#' . trim($attrs['custom_color'], '#');
    }

    $theme_array = [
      '#theme' => 'progressive_shortcodes_message',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#icon' => isset($attrs['icon']) && $attrs['icon'] ? $attrs['icon'] : FALSE,
      '#dismissable' => isset($attrs['dismissable']) && $attrs['dismissable'],
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $form['color_type'] = [
      '#title' => t('Custom Color'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['color_type']) ? $attrs['color_type'] : FALSE,
      '#attributes' => [
        'class' => ['color-type-select'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $colors = [
      'grey' => t('Grey'),
      'alert-black' => t('Black'),
      'alert-danger' => t('Red'),
      'alert-info' => t('Blue'),
      'alert-warning' => t('Orange'),
      'alert-success' => t('Green'),
    ];
    $form['color'] = [
      '#type' => 'radios',
      '#title' => t('Color'),
      '#options' => $colors,
      '#default_value' => isset($attrs['color']) ? $attrs['color'] : '',
      '#attributes' => [
        'class' => ['color-radios'],
      ],
      '#states' => [
        'visible' => [
          '.color-type-select' => ['checked' => FALSE],
        ],
      ],
    ];
    $form['custom_color'] = [
      '#type' => 'textfield' ,
      '#title' => t('Color'),
      '#default_value' => isset($attrs['custom_color']) ? $attrs['custom_color'] : '',
      '#states' => [
        'visible' => [
          '.color-type-select' => ['checked' => TRUE],
        ],
      ],
      '#attributes' => [
        'class' => [
          'form-control',
          'colorpicker-enable',
        ],
      ],
    ];
    $form['dismissable'] = [
      '#type' => 'checkbox',
      '#title' => t('Dismissable'),
      '#default_value' => isset($attrs['dismissable']) ? $attrs['dismissable'] : TRUE,
      '#prefix' => '</div><div class="col-sm-6">',
    ];
    $form['icon'] = [
      '#title' => t('Icon'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'progressive_shortcodes.ajax_fontawesome_icons_autocomplete',
      '#default_value' => isset($attrs['icon']) ? $attrs['icon'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $options = [
      'background' => t('Background'),
      'border' => t('Border'),
    ];
    $form['type'] = [
      '#title' => t('Colored'),
      '#type' => 'select',
      '#options' => $options,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['center'] = [
      '#title' => t('Center Align'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['center']) ? $attrs['center'] : FALSE,
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>'
    ];

    return $form;
  }
}
