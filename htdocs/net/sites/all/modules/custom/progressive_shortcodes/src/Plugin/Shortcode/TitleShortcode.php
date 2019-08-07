<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "title",
 *   title = @Translation("Title"),
 *   description = @Translation("Title with optional underline."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-text-width",
 *   description_field = "title"
 * )
 */

class TitleShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = isset($attrs['class']) ? ' ' . $attrs['class'] : '';
    $attrs['class'] .= isset($attrs['underline']) && $attrs['underline'] && (!isset($attrs['type']) || $attrs['type'] != 'page') ? ' title-box' : '';
    $attrs['class'] .= isset($attrs['title_align']) && $attrs['title_align']? ' ' . $attrs['title_align'] : '';
    $tag = isset($attrs['type']) && $attrs['type'] ? $attrs['type'] : 'h3';
    $tag = $tag == 'page' ? 'h1' : $tag;
    $text = trim($text) ? $text : (isset($attrs['title']) ? $attrs['title'] : '');
    $page = FALSE;
    if (isset($attrs['type']) && $attrs['type'] == 'page') {
      $page = TRUE;
      $attrs['class'] .= ' page-header';
    }

    $theme_array = [
      '#theme' => 'progressive_shortcodes_title',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#tag' => $tag,
      '#page' => $page,
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
      '#default_value' => $text ? $text : (isset($attrs['title']) ? $attrs['title'] : ''),
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];
    $types = [
      'h1' => 'H1',
      'h2' => 'H2',
      'h3' => 'H3',
      'h4' => 'H4',
      'h5' => 'H5',
      'h6' => 'H6',
      'page' => t('Page Header'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Type'),
      '#options' => $types,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : 'h2',
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];
    $form['underline'] = [
      '#type' => 'checkbox',
      '#title' => t('Underline'),
      '#default_value' => isset($attrs['underline']) ? $attrs['underline'] : 1,
    ];
    $aligns = [
      '' => t('Left'),
      'text-center' => t('Center'),
      'text-right' => t('Right'),
    ];
    $form['title_align'] = [
      '#type' => 'select',
      '#title' => t('Align'),
      '#options' => $aligns,
      '#default_value' => isset($attrs['title_align']) ? $attrs['title_align'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];

    return $form;
  }
}
