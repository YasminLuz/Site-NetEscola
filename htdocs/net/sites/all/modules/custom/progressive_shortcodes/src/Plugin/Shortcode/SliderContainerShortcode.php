<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "slider",
 *   title = @Translation("Slider Container"),
 *   description = @Translation("Slider for any content."),
 *   child_shortcode = "slider_item",
 *   icon = "fa fa-arrows-h",
 *   description_field = "type"
 * )
 */

class SliderContainerShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = 'respond-carousel carousel-box overflow load ' . (isset($attrs['class']) ? $attrs['class'] : '');
    $pagination = FALSE;
    $pagination_switches = FALSE;
    switch($attrs['type']) {
      case 'autoscroll':
        $attrs['data-carousel-autoplay'] = 'true';
        $attrs['data-carousel-nav'] = 'false';
        $attrs['data-carousel-one'] = 'true';
        $attrs['data-duration'] = isset($attrs['duration']) ? $attrs['duration'] : 1000;
        break;
      case 'pagination':
        $attrs['data-carousel-pagination'] = 'true';
        $attrs['data-carousel-nav'] = 'false';
        $attrs['data-carousel-one'] = 'true';
        $pagination_switches = TRUE;
        break;
      case 'navigation':
        $attrs['style_margin_top'] = isset($attrs['style_margin_top']) ? $attrs['style_margin_top'] : '-40px';
        $attrs['data-carousel-one'] = 'true';
        $attrs['class'] .= ' allow-overflow';
        $pagination = TRUE;
        break;
    }

    $theme_array = [
      '#theme' => 'progressive_shortcodes_slider_container',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#pagination' => $pagination,
      '#pagination_switches' => $pagination_switches,
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $types = [
      'autoscroll' => t('Autoscroll'),
      'pagination' => t('Pagination'),
      'navigation' => t('Navigation'),
      'pagination_navigation' => t('Pagination & Navigation'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#options' => $types,
      '#title' => t('Type'),
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : 'autoscroll',
      '#attributes' => [
        'class' => [
          'form-control',
          'slider-type-selector',
        ],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['duration'] = [
      '#type' => 'textfield',
      '#title' => t('Auto Scroll Duration (ms)'),
      '#default_value' => isset($attrs['duration']) ? $attrs['duration'] : '1000',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
      '#states' => [
        'visible' => [
          '.slider-type-selector' => ['value' => 'autoscroll'],
        ],
      ],
    ];

    return $form;
  }
}
