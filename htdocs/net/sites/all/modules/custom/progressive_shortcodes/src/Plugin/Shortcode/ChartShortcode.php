<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\Component\Serialization\Json;

/**
 * @Shortcode(
 *   id = "chart",
 *   title = @Translation("Chart"),
 *   description = @Translation("Chart."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-bar-chart-o",
 *   description_field = "type"
 * )
 */

class ChartShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $values = [];
    switch ($attrs['type']) {
      case t('Simple Line'):
        $attrs['class'] = 'chart';
        $attrs['data-line'] = isset($attrs['values']) && !empty($attrs['values']) ? $attrs['values'] : [];
        $attrs['data-line-color'] = '#6bdcff';
        $attrs['data-spot-color'] = '#00c1fd';
        $attrs['data-min-spot-color'] = '#ff9d00';
        $attrs['data-max-spot-color'] = '#3e8e00';
        $output = '<div ' . _progressive_shortcodes_shortcode_attributes($attrs) . '>'. t('Loading...'). '</div>';
        break;

      case t('Simple Bar'):
        $attrs['class'] = 'chart';
        $attrs['data-line'] = isset($attrs['values']) && !empty($attrs['values']) ? $attrs['values'] : [];
        $attrs['data-type'] = 'bar';
        $attrs['data-bar-color'] = 'rgba(255,115,0,.8)';
        $output = '<div ' . _progressive_shortcodes_shortcode_attributes($attrs) . '>'. t('Loading...'). '</div>';
        break;

      case t('Bar with Titles'):
        static $bar_id = 0;
        $id = 'bar-title-' . (++$bar_id);
        $values_array = explode(',', $attrs['item_values']);
        $titles_array = explode(',', $attrs['item_titles']);

        foreach ($values_array as $key => $value) {
          $values[] = (object)[
            'item' => trim($titles_array[$key]),
            'value' => (int)trim($value)
          ];
        }

        $attrs['class'] = 'graph-resize bar-with-title';
        $attrs['data-values'] = Json::encode($values);
        $attrs['data-label'] = $attrs['value_title'];
        $attrs['id'] = $id;
        $output_ = '<div ' . _progressive_shortcodes_shortcode_attributes($attrs) . '></div>';
        $output = $output_;
        break;

      case t('Donut'):
        static $donut_id = 0;
        $id = 'donut-graph-' . (++$donut_id);
        $colors = [];
        $titles_array = explode(',', $attrs['titles']);
        $colors_array = explode(',', $attrs['colors']);
        $values_array = explode(',', $attrs['values_percent']);
        foreach ($titles_array as $key => $title) {
          $values[] = (object)[
            'label' => trim($title),
            'value' => (int)trim($values_array[$key])
          ];
          $colors[] = '#' . trim(!empty(trim($colors_array[$key])) ? $colors_array[$key] : '000000', ' #');
          $titles[] = trim($titles_array[$key]);
        }

        $attrs['id'] = $id;
        $attrs['class'] = 'graph-resize donut-graph';
        $attrs['style'] = 'height: 245px;';
        $attrs['data-type'] = 'pie';
        $attrs['data-values'] = Json::encode($values);
        $attrs['data-colors'] = Json::encode($colors);
        $output = '<div ' . _progressive_shortcodes_shortcode_attributes($attrs) . '>'. t('Loading...'). '</div>';
        break;
        break;

      case t('Pie'):
        $colors = [];
        $values_array = explode(',', $attrs['values']);
        $colors_array = explode(',', $attrs['colors']);
        foreach ($values_array as $key => $value) {
          $values[] = trim($value);
          $colors[] = '#' . trim(!empty(trim($colors_array[$key])) ? $colors_array[$key] : 'ff9d00', ' #');
        }
        $attrs['class'] = 'chart';
        $attrs['data-line'] = implode(', ', $values);
        $attrs['data-type'] = 'pie';
        $attrs['data-slice-colors'] = implode(', ', $colors);
        $output = '<div ' . _progressive_shortcodes_shortcode_attributes($attrs) . '>'. t('Loading...'). '</div>';
        break;

      case t('Tristate'):
        $attrs['class'] = 'chart';
        $attrs['data-line'] = isset($attrs['values']) && !empty($attrs['values']) ? $attrs['values'] : [];
        $attrs['data-type'] = 'tristate';
        $attrs['data-pos-bar-color'] = '#3e8e00';
        $output = '<div ' . _progressive_shortcodes_shortcode_attributes($attrs) . '>'. t('Loading...'). '</div>';
        break;

      default:
        $output = '';
    }
    return $output;
  }

  /**
   * {@inheritdoc}
   *
   * @param $attrs
   * @param $text
   * @param string $langcode
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @return array
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $types = [
      t('Simple Line'),
      t('Simple Bar'),
      t('Bar with Titles'),
      t('Donut'),
      t('Pie'),
      t('Tristate'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Type'),
      '#options' => array_combine($types, $types),
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : t('Simple Line'),
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-12">',
      '#suffix' => '</div></div>',
    ];

    // Bar with Titles.
    $form['value_title'] = [
      '#type' => 'textfield',
      '#title' => t('Value title'),
      '#default_value' => isset($attrs['value_title']) ? $attrs['value_title'] : '',
      '#attributes' => [
        'class' => ['form-control']
      ],
      '#prefix' => '<div class="row"><div class="col-sm-9">',
      '#suffix' => '</div></div>',
      '#states' => [
        'visible' => [
          'select[name="type"]' => [
            ['value' => t('Bar with Titles')],
          ],
        ],
      ],
    ];
    $form['item_titles'] = [
      '#type' => 'textfield',
      '#title' => t('Item titles'),
      '#description' => t('Enter a comma separated list of titles.'),
      '#default_value' => isset($attrs['item_titles']) ? $attrs['item_titles'] : '',
      '#attributes' => [
        'placeholder' => '...',
        'class' => ['form-control']
      ],
      '#states' => [
        'visible' => [
          'select[name="type"]' => [
            ['value' => t('Bar with Titles')],
          ],
        ],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-12">',
      '#suffix' => '</div>',
    ];
    $form['item_values'] = [
      '#type' => 'textfield',
      '#title' => t('Item values'),
      '#description' => t('Enter a comma separated list of values.'),
      '#default_value' => isset($attrs['item_values']) ? $attrs['item_values'] : '',
      '#attributes' => [
        'placeholder' => '...',
        'class' => ['form-control']
      ],
      '#states' => [
        'visible' => [
          'select[name="type"]' => [
            ['value' => t('Bar with Titles')],
          ],
        ],
      ],
      '#prefix' => '<div class="col-sm-12">',
      '#suffix' => '</div>',
    ];

    // Donut, Pie
    $form['titles'] = [
      '#type' => 'textfield',
      '#title' => t('Titles'),
      '#description' => t('Enter a comma separated list of titles.'),
      '#default_value' => isset($attrs['titles']) ? $attrs['titles'] : '',
      '#attributes' => [
        'placeholder' => '...',
        'class' => ['form-control']
      ],
      '#states' => [
        'visible' => [
          'select[name="type"]' => [
            ['value' => t('Donut')],
          ],
        ],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-12">',
      '#suffix' => '</div></div>',
    ];
    $form['colors'] = [
      '#type' => 'textfield',
      '#title' => t('Colors'),
      '#description' => t('Enter a comma separated list of colors in hex.'),
      '#default_value' => isset($attrs['colors']) ? $attrs['colors'] : '',
      '#attributes' => [
        'placeholder' => '...',
        'class' => ['form-control']
      ],
      '#states' => [
        'visible' => [
          'select[name="type"]' => [
            ['value' => t('Donut')],
            ['value' => t('Pie')],
          ],
        ],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-12">',
      '#suffix' => '</div></div>',
    ];
    $form['values_percent'] = [
      '#type' => 'textfield',
      '#title' => t('Values %'),
      '#description' => t('Enter a comma separated list of values.'),
      '#default_value' => isset($attrs['values_percent']) ? $attrs['values_percent'] : '',
      '#attributes' => [
        'placeholder' => '...',
        'class' => ['form-control']
      ],
      '#states' => [
        'visible' => [
          'select[name="type"]' => [
            ['value' => t('Donut')],
          ],
        ],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-12">',
      '#suffix' => '</div></div>',
    ];

    // Simple Line, Simple Bar, Tristate, Pie
    $form['values'] = [
      '#type' => 'textfield',
      '#title' => t('Values'),
      '#description' => t('Enter a comma separated list of values.'),
      '#default_value' => isset($attrs['values']) ? $attrs['values'] : '',
      '#attributes' => [
        'placeholder' => '...',
        'class' => ['form-control']
      ],
      '#prefix' => '<div class="row"><div class="col-sm-12">',
      '#suffix' => '</div></div>',
      '#states' => [
        'visible' => [
          'select[name="type"]' => [
            ['value' => t('Simple Line')],
            ['value' => t('Simple Bar')],
            ['value' => t('Tristate')],
            ['value' => t('Pie')],
          ],
        ],
      ],
    ];

    return $form;
  }
}
