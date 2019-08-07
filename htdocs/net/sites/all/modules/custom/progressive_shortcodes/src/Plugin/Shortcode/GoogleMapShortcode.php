<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "gmap",
 *   title = @Translation("Google Map"),
 *   description = @Translation("GoogleMap."),
 *   icon = "fa fa-map-marker",
 * )
 */

class GoogleMapShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = 'map-box' . (isset($attrs['class']) ? ' ' . $attrs['class'] : '');

    $theme_array = [
      '#theme' => 'progressive_shortcodes_gmap',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#is_map_overlay' => isset($attrs['tooltip']) && $attrs['tooltip'] == 'map_overlay',
      '#height' => isset($attrs['height']) ? $attrs['height'] : 276,
      '#zoom' => isset($attrs['zoom']) ? $attrs['zoom'] : 6,
      '#lat' => isset($attrs['lat']) ? $attrs['lat'] : 0,
      '#lng' => isset($attrs['lng']) ? $attrs['lng'] : 0,
      '#marker' => base_path() . drupal_get_path('theme', 'progressive') . '/img/svg/map-marker.svg',
      '#type' => isset($attrs['type']) ? 'data-type="' . $attrs['type'] . '"' : '',
      '#title' => isset($attrs['title']) && $attrs['title'] ? 'data-title="' . $attrs['title'] . '"' : '',
      '#is_marker_tooltip' => !isset($attrs['tooltip']) || $attrs['tooltip'] == 'marker_tooltip',
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
      '#prefix' => '<div class="row"><div class="col-sm-9">',
    ];
    $types = [
      '' => t('Default'),
      'terrain' => t('Terrain'),
      'satellite' => t('Satellite'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Type'),
      '#options' => $types,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-3">',
      '#suffix' => '</div></div>',
    ];
    $form['lat'] = [
      '#type' => 'textfield',
      '#title' => t('Latitude'),
      '#default_value' => isset($attrs['lat']) ? $attrs['lat'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-3">',
    ];
    $form['lng'] = [
      '#type' => 'textfield',
      '#title' => t('Longitude'),
      '#default_value' => isset($attrs['lng']) ? $attrs['lng'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-3">',
    ];
    $form['zoom'] = [
      '#type' => 'textfield',
      '#title' => t('Zoom'),
      '#default_value' => isset($attrs['zoom']) ? $attrs['zoom'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-3">',
    ];
    $form['height'] = [
      '#type' => 'textfield',
      '#title' => t('Height'),
      '#default_value' => isset($attrs['height']) ? $attrs['height'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-3">',
      '#suffix' => '</div></div>',
    ];
    $types = [
      'marker_tooltip' => t('Marker Toolip'),
      'map_overlay' => t('Map Overlay Box'),
    ];
    $form['tooltip'] = [
      '#type' => 'select',
      '#title' => t('Append inner content to:'),
      '#options' => $types,
      '#default_value' => isset($attrs['tooltip']) ? $attrs['tooltip'] : 'marker_tooltip',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];

    return $form;
  }
}
