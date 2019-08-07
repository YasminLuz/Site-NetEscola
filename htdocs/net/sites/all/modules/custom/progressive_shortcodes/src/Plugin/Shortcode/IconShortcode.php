<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "icon",
 *   title = @Translation("Icon"),
 *   description = @Translation("FontAwesome or LivIcon."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-rocket",
 * )
 */
class IconShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = isset($attrs['shadow']) ? ' ' . $attrs['shadow'] : '';
    $text = isset($attrs['title']) && $attrs['title'] ? $attrs['title'] : '';
    $attrs['link'] = isset($attrs['link']) ? $attrs['link'] : '#';
    $attrs['href'] = $attrs['link'];
    if ($attrs['type'] == 'fontawesome') {
      if (!isset($attrs['style_color'])) {
        $attrs['style_color'] = '#f89406';
      }
      $attrs['class'] = 'icon icon-' . $attrs['fontawesome_size'] . (isset($attrs['class']) ? ' ' . $attrs['class'] : '');
      $text = '<i class = "' . $attrs['fontawesome_icon'] . '"></i>' . $text;
      return '<a ' . _progressive_shortcodes_shortcode_attributes($attrs) . '>' . $text . '</a>';
    }
    elseif ($attrs['type'] == 'livicon') {
      $attrs['class'] .= 'livicon block';
      $attrs['class'] .= isset($attrs['livicon_shadow']) && $attrs['livicon_shadow'] ? ' shadowed': '';
      $text = '<a ' . _progressive_shortcodes_shortcode_attributes($attrs) . ' data-n="' . $attrs['livicon'] . '" data-s="' . $attrs['livicon_size'] . '"
    ' . (isset($attrs['color_type']) && $attrs['color_type'] && isset($attrs['style_color']) && $attrs['style_color'] ? 'data-c="#' . trim($attrs['style_color'], '#'). '"': '') .
        (isset($attrs['livicon_type']) && $attrs['livicon_type'] == 'static' ? ' data-a="0"': '') .
        (!isset($attrs['livicon_hover']) || !$attrs['livicon_hover'] ? ' data-hc="0"': '') .
        (isset($attrs['livicon_type']) && $attrs['livicon_type'] == 'click_animation' ? ' data-et="click"': '') .
        (isset($attrs['livicon_type']) && $attrs['livicon_type'] == 'click_loop' ? ' data-et="click" data-l = "1"': '') .
        (isset($attrs['livicon_parent_trigger']) && $attrs['livicon_parent_trigger'] ? '': ' data-op="0"') .
        '></a>' . $text;
      unset($attrs['style_color']);
    }
    return $text;
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $form['fontawesome'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          '.icon-type-select' => ['value' => 'fontawesome'],
        ],
      ],
    ];
    $form['fontawesome']['fontawesome_icon'] = [
      '#title' => t('FontAwesome Icon'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'progressive_shortcodes.ajax_fontawesome_icons_autocomplete',
      '#autocomplete_route_parameters' => array('field_name' => 'fontawesome_icon', 'count' => 10),
      '#default_value' => isset($attrs['fontawesome_icon']) ? $attrs['fontawesome_icon'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $sizes = [
      '24' => '24px', '32' => '32px', '40' => '40px', '60' => '60px', '100' => '100px'
    ];
    $form['fontawesome']['fontawesome_size'] = [
      '#title' => t('Font Size'),
      '#type' => 'select',
      '#options' => $sizes,
      '#default_value' => isset($attrs['fontawesome_size']) ? $attrs['fontawesome_size'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];

    $form['livicon'] = [
      '#type' => 'container',
      '#states' => [
        'visible' => [
          '.icon-type-select' => ['value' => 'livicon'],
        ],
      ],
    ];
    $form['livicon']['livicon'] = [
      '#title' => t('Livicon'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'progressive_shortcodes.ajax_livicons_autocomplete',
      '#autocomplete_route_parameters' => array('field_name' => 'livicon', 'count' => 10),
      '#default_value' => isset($attrs['livicon']) ? $attrs['livicon'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-3">',
    ];
    $sizes = [
      '16' => '16px', '24' => '24px', '32' => '32px', '48' => '48px', '56' => '56px', '64' => '64px', '96' => '96px'
    ];
    $form['livicon']['livicon_size'] = [
      '#title' => t('Font Size'),
      '#type' => 'select',
      '#options' => $sizes,
      '#default_value' => isset($attrs['livicon_size']) ? $attrs['livicon_size'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-3">',
    ];
    $types = [
      '' => t('Animate on hover'),
      'static' => t('Static'),
      'click_animation' => t('Animate on click'),
      'click_loop' => t('Click Start/Stop'),
    ];
    $form['livicon']['livicon_type'] = [
      '#title' => t('Type'),
      '#type' => 'select',
      '#options' => $types,
      '#default_value' => isset($attrs['livicon_type']) ? $attrs['livicon_type'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class = "col-sm-3">',
    ];
    $form['livicon']['livicon_hover'] = [
      '#title' => t('Hover color effect'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['livicon_hover']) ? $attrs['livicon_hover'] : '',
      '#prefix' => '</div><div class="col-sm-3">',
    ];
    $form['livicon']['livicon_parent_trigger'] = [
      '#title' => t('Parent hover'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['livicon_parent_trigger']) ? $attrs['livicon_parent_trigger'] : '',
    ];
    $form['livicon']['livicon_shadow'] = [
      '#title' => t('Shadow'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['livicon_shadow']) ? $attrs['livicon_shadow'] : '',
      '#suffix' => '</div></div>',
    ];

    $types = [
      'fontawesome' => t('FontAwesome'),
      'livicon' => t('LivIcon'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Icon Type'),
      '#options' => $types,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : 'fontawesome',
      '#attributes' => [
        'class' => [
          'form-control',
          'icon-type-select',
        ],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['title'] = [
      '#type' => 'textfield' ,
      '#title' => t('Title'),
      '#default_value' => isset($attrs['title']) ? $attrs['title'] : '',
      '#attributes' => ['class' => ['form-control'],],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $form['color_type'] = [
      '#title' => t('Custom Color'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['color_type']) ? $attrs['color_type'] : '',
      '#attributes' => [
        'class' => ['color-type-select'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['style_color'] = [
      '#type' => 'textfield' ,
      '#title' => t('Color'),
      '#default_value' => isset($attrs['style_color']) ? $attrs['style_color'] : '',
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

    return $form;
  }
}
