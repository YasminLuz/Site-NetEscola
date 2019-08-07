<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "service_box",
 *   title = @Translation("ServiceBox"),
 *   description = @Translation("Service box with text."),
 *   icon = "fa fa-star",
 *   description_field = "title"
 * )
 */

class ServiceBoxShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['type'] = isset($attrs['type']) ? $attrs['type'] : 'center_big';
    $attrs['inner_animation'] = isset($attrs['inner_animation']) ? ' data-appear-animation="' . $attrs['inner_animation'] . '"' : '';
    $attrs['inner_icon_animation'] = $attrs['inner_animation'] ? 'data-appear-animation="wobble"' : '';
    $attrs['link_text'] = isset($attrs['link_text']) ? $attrs['link_text'] : t('Read More');
    $target = isset($attrs['new_tab']) && $attrs['new_tab'] ? 'target="_blank"' : '';
    $link_text = '';

    switch($attrs['type']) {
      case 'left_small':
        $attrs['class'] = 'text-small features-block' . (isset($attrs['class']) ? ' ' . $attrs['class'] : '');
        $theme = 'progressive_shortcodes_service_box_left_small';
        break;

      case 'left_medium':
        $attrs['class'] = 'service' . (isset($attrs['class']) ? ' ' . $attrs['class'] : '');
        $theme = 'progressive_shortcodes_service_box_left_medium';
        break;

      case 'left_big':
        $attrs['class'] = (isset($attrs['class']) ? $attrs['class'] : '');
        $theme = 'progressive_shortcodes_service_box_left_big';
        break;

      case 'center_big':
      default:
        $attrs['class'] = 'big-services-box' . (isset($attrs['class']) ? ' ' . $attrs['class'] : '');
        $theme = 'progressive_shortcodes_service_box_center_big';
        $link_text = $attrs['link_text'];
        break;
    }

    $theme_array = [
      '#theme' => $theme,
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#link' => isset($attrs['link']) ? '/' . ltrim($attrs['link'], '/') : '',
      '#target' => $target,
      '#icon' => isset($attrs['icon']) ? $attrs['icon'] : '',
      '#title' => isset($attrs['title']) ? $attrs['title'] : '',
      '#inner_icon_animation' => $attrs['inner_icon_animation'],
      '#inner_animation' => $attrs['inner_animation'],
      '#link_text' => $link_text,
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
      '#type' => 'textfield' ,
      '#title' => t('Title'),
      '#default_value' => isset($attrs['title']) ? $attrs['title'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['link'] = [
      '#type' => 'textfield' ,
      '#title' => t('Link'),
      '#default_value' => isset($attrs['link']) ? $attrs['link'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $types = [
      'left_small' => t('Left Small'),
      'left_medium' => t('Left Medium'),
      'left_big' => t('Left Big'),
      'center_big' => t('Center Big'),
    ];
    $form['type'] = [
      '#title' => t('Icon Type'),
      '#type' => 'select',
      '#options' => $types,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : 'center_big',
      '#attributes' => [
        'class' => [
          'form-control',
          'type-icon-select',
        ],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-2">',
    ];
    $form['icon'] = [
      '#title' => t('Icon'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'progressive_shortcodes.ajax_fontawesome_icons_autocomplete',
      '#default_value' => isset($attrs['icon']) ? $attrs['icon'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class = "col-sm-4">',
    ];
    $form['link_text'] = [
      '#type' => 'textfield' ,
      '#title' => t('Read More text'),
      '#default_value' => isset($attrs['link_text']) ? $attrs['link_text'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#states' => [
        'visible' => [
          '.type-icon-select' => ['value' => 'center_big'],
        ],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>'
    ];
    $form['inner_animation'] = [
      '#type' => 'select',
      '#title' => t('Content Animation'),
      '#options' => _nd_visualshortcodes_list_animations(),
      '#default_value' => isset($attrs['inner_animation']) ? $attrs['inner_animation'] : '',
      '#prefix' => '<div class="row"><div class="col-xs-6 centered">',
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];
    $form['inner_icon_animation'] = [
      '#type' => 'select',
      '#title' => t('Icon Animation'),
      '#options' => _nd_visualshortcodes_list_animations(),
      '#default_value' => isset($attrs['inner_icon_animation']) ? $attrs['inner_icon_animation'] : '',
      '#prefix' => '</div><div class="col-xs-6 centered">',
      '#suffix' => '</div></div>',
      '#attributes' => [
        'class' => ['form-control'],
      ],
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
