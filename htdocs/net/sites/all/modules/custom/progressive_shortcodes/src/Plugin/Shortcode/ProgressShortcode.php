<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "progress",
 *   title = @Translation("Progress Line"),
 *   description = @Translation("Progress line with title and percents."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-tasks",
 *   description_field = "title"
 * )
 */

class ProgressShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = isset($attrs['class']) ? $attrs['class'] : '';
    $attrs['class'] .= isset($attrs['animated']) && $attrs['animated']? ' active' : '';
    $attrs['class'] .= isset($attrs['striped']) && $attrs['striped']? ' progress-striped' : '';
    $attrs['class'] .= isset($attrs['hover']) && $attrs['hover']? ' hover' : '';

    $colors = array('blue' => 'info', 'green' => 'success', 'orange' => 'warning', 'red' => 'danger', 'black' => '');
    $color = (isset($attrs['color']) && isset($colors[$attrs['color']])) ? $attrs['color'] : '';
    $text = isset($attrs['title']) && $attrs['title'] ? $attrs['title'] : $text;
    if (isset($attrs['type']) && $attrs['type'] == 'circle') {
      $attrs['class'] .= ' progress-circular';
      $line_color = isset($attrs['custom_color']) && $attrs['custom_color'] ? '#' . trim($attrs['custom_color'], '#') : '#f2f2f2';
      $theme_array = [
        '#theme' => 'progressive_shortcodes_progress_circle',
        '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
        '#percent' => $attrs['percent'],
        '#line_color' => $line_color,
      ];
    }
    else {
      $attrs['class'] .= ' progress';
      $theme_array = [
        '#theme' => 'progressive_shortcodes_progress',
        '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
        '#percent' => $attrs['percent'],
        '#color' => $colors[$color],
        '#text' => $text,
      ];
    }

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $attrs['title'] = isset($attrs['title']) && $attrs['title'] ? $attrs['title'] : $text;
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => t('Title'),
      '#default_value' => isset($attrs['title']) ? $attrs['title'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-7">',
      '#states' => [
        'invisible' => [
          '.progess-type-select' => ['value' => 'circle'],
        ],
      ],
    ];
    $colors = [
      'blue' => t('Blue'),
      'green' => t('Green'),
      'orange' => t('Orange'),
      'red' => t('Red'),
      'black' => t('Black'),
    ];
    $form['color'] = [
      '#type' => 'radios',
      '#title' => t('Color'),
      '#options' => $colors,
      '#default_value' => isset($attrs['color']) ? $attrs['color'] : 'blue',
      '#attributes' => [
        'class' => ['color-radios'],
      ],
      '#prefix' => '</div><div class="col-sm-3">',
      '#states' => [
        'visible' => [
          '.progess-type-select' => ['value' => 'line'],
        ],
      ],
    ];
    $form['percent'] = [
      '#type' => 'textfield',
      '#title' => t('Percent'),
      '#default_value' => isset($attrs['percent']) ? $attrs['percent'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-2">',
      '#suffix' => '</div></div>',
    ];
    $types = [
      'line' => t('Line'),
      'circle' => t('Circle'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Type'),
      '#options' => $types,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : 'line',
      '#attributes' => [
        'class' => [
          'form-control',
          'progess-type-select',
        ],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-3">',
    ];
    $form['striped'] = [
      '#type' => 'checkbox',
      '#title' => t('Striped'),
      '#default_value' => isset($attrs['striped']) ? $attrs['striped'] : '',
      '#prefix' => '</div><div class="col-sm-3">',
      '#states' => [
        'visible' => [
          '.progess-type-select' => ['value' => 'line'],
        ],
      ],
    ];
    $form['animated'] = [
      '#type' => 'checkbox',
      '#title' => t('Animated'),
      '#default_value' => isset($attrs['animated']) ? $attrs['animated'] : '',
      '#prefix' => '</div><div class="col-sm-3">',
      '#states' => [
        'visible' => [
          '.progess-type-select' => ['value' => 'line'],
        ],
      ],
    ];
    $form['hover'] = [
      '#type' => 'checkbox',
      '#title' => t('Hover Animation'),
      '#default_value' => isset($attrs['hover']) ? $attrs['hover'] : '',
      '#prefix' => '</div><div class="col-sm-3">',
      '#states' => [
        'visible' => [
          '.progess-type-select' => ['value' => 'line'],
        ],
      ],
    ];
    $form['custom_color'] = [
      '#type' => 'textfield' ,
      '#title' => t('Color'),
      '#default_value' => isset($attrs['custom_color']) ? $attrs['custom_color'] : '',
      '#states' => [
        'invisible' => [
          '.progess-type-select' => ['value' => 'line'],
        ],
      ],
      '#attributes' => [
        'class' => [
          'form-control',
          'colorpicker-enable',
        ],
      ],
      '#prefix' => '</div><div class="col-sm-3">',
      '#suffix' => '</div></div>',
    ];

    return $form;
  }
}
