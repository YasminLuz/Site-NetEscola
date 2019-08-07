<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "nd_button",
 *   title = @Translation("Button link"),
 *   description = @Translation("Button linked to any page or content."),
 *   icon = "fa fa-bold",
 *   description_field = "text"
 * )
 */

class ButtonShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['extra_classes'] = isset($attrs['extra_classes']) ? $attrs['extra_classes'] : '';
    $attrs['extra_classes'] .= isset($attrs['color']) && (!isset($attrs['color_type']) || !$attrs['color_type']) ? ' ' . $attrs['color'] : '';
    $attrs['extra_classes'] .= isset($attrs['size']) ? ' ' . $attrs['size'] : '';
    $attrs['type'] = isset($attrs['type']) ? $attrs['type'] : 'default';
    $styles = [
      'default' => 'btn',
      'outlined' => 'btn btn-border',
      'social_squared' => 'sbtnf sbtnf-squere sbtnf-icon-white sbtnf-icon-bg-black color',
      'social_rounded' => 'sbtnf sbtnf-rounded sbtnf-icon-white sbtnf-icon-bg-black color',
      'social_heavily_rounded' => 'sbtnf sbtnf-circle sbtnf-icon-white sbtnf-icon-bg-black color',
      'block' => 'btn btn-block'
    ];
    $attrs['extra_classes'] .= ' ' . $styles[$attrs['type']];
    if (isset($attrs['color_type']) && $attrs['color_type'] && isset($attrs['custom_color']) && $attrs['custom_color']) {
      $attrs['style_background_color'] = trim($attrs['custom_color'], '#');
      if (strtolower($attrs['style_background_color']) == 'ffffff') {
        $attrs['extra_classes'] .= ' btn-white';
      }
    }
    $attrs['link'] = isset($attrs['link']) ? $attrs['link'] : '#';
    $attrs['href'] = $attrs['link'];
    $attrs['target'] = isset($attrs['new_tab']) && $attrs['new_tab'] ? '_blank' : '';
    $text .= isset($attrs['text']) ? $attrs['text'] : '';

    $theme_array = [
      '#theme' => 'progressive_shortcodes_button',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#icon' => isset($attrs['icon']) && $attrs['icon'] ? $attrs['icon'] : FALSE,
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $form['icon'] = [
      '#title' => t('Icon'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'progressive_shortcodes.ajax_fontawesome_icons_autocomplete',
      '#default_value' => isset($attrs['icon']) ? $attrs['icon'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $styles = [
      'default' => t('Default'),
      'outlined' => t('Outlined'),
      'social_squared' => t('Social Squared'),
      'social_rounded' => t('Social Smoothly Rounded'),
      'social_heavily_rounded' => t('Social Heavily Rounded'),
      'block' => t('Block button'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Style'),
      '#options' => $styles,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
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
      'btn-danger' => t('Red'),
      'btn-info' => t('Blue'),
      'btn-warning' => t('Orange'),
      'btn-success' => t('Green'),
      'btn-primary' => t('Dark Blue'),
      'btn-inverse' => t('Inverse'),
    ];
    $form['color'] = [
      '#type' => 'radios',
      '#title' => t('Color'),
      '#options' => $colors,
      '#default_value' => isset($attrs['color']) ? $attrs['color'] : 'btn-info',
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
    $sizes = [
      'btn-xs' => t('Small'),
      'btn-sm' => t('Medium'),
      '' => t('Default'),
      'btn-lg' => t('Large'),
    ];
    $form['size'] = [
      '#type' => 'select',
      '#title' => t('Size'),
      '#options' => $sizes,
      '#default_value' => isset($attrs['size']) ? $attrs['size'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $form['text'] = [
      '#title' => t('Text'),
      '#type' => 'textfield',
      '#default_value' => isset($attrs['text']) ? $attrs['text'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
      '#maxlength' => 512,
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
    $form['new_tab'] = [
      '#title' => t('Open link in new tab'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['new_tab']) ? $attrs['new_tab'] : FALSE,
      '#prefix' => '<div class="row"><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];

    return $form;
  }
}
