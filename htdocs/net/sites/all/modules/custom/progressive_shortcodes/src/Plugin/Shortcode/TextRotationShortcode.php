<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "textrotation",
 *   title = @Translation("Text Rotation"),
 *   description = @Translation("Text Rotation."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-rotate-right",
 *   description_field = "title"
 * )
 */

class TextRotationShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = 'word-rotate' . (isset($attrs['class']) ? ' ' . $attrs['class'] : '');
    $words = isset($attrs['words']) && !empty($attrs['words']) ? explode(',', $attrs['words']) : [];
    $attrs['tag'] = isset($attrs['tag']) ? $attrs['tag'] : 'div';
    $attrs['prefix'] = isset($attrs['prefix']) ? $attrs['prefix'] : '';
    $attrs['suffix'] = isset($attrs['title']) ? $attrs['title'] : '';

    $theme_array = [
      '#theme' => 'progressive_shortcodes_textrotation',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#tag' => $attrs['tag'],
      '#words' => $words,
      '#words_prefix' => $attrs['prefix'],
      '#words_suffix' => $attrs['suffix'],
    ];
    return $this->render($theme_array);
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
    $form['prefix'] = [
      '#type' => 'textfield',
      '#title' => t('Prefix text'),
      '#default_value' => isset($attrs['prefix']) ? $attrs['prefix'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ]
    ];
    $form['title'] = [
      '#type' => 'textfield',
      '#title' => t('Suffix text'),
      '#default_value' => $text ? $text : (isset($attrs['title']) ? $attrs['title'] : ''),
      '#attributes' => [
        'class' => ['form-control'],
      ]
    ];
    $types = [
      'div' => 'Div',
      'p' => 'P',
      'strong' => t('Strong'),
      'span' => 'Span',
      'h1' => 'H1',
      'h2' => 'H2',
      'h3' => 'H3',
      'h4' => 'H4',
      'h5' => 'H5',
      'h6' => 'H6',
    ];
    $form['tag'] = [
      '#type' => 'select',
      '#title' => t('Tag Type'),
      '#options' => $types,
      '#default_value' => isset($attrs['tag']) ? $attrs['tag'] : 'div',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $form['words'] = [
      '#type' => 'textfield',
      '#title' => t('Phrases'),
      '#description' => t('Enter a comma separated list of phrases.'),
      '#default_value' => isset($attrs['words']) ? $attrs['words'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-12">',
      '#suffix' => '</div></div>',
    ];

    return $form;
  }
}
