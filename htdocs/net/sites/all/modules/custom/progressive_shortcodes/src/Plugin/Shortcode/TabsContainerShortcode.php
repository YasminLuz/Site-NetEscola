<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "tabs",
 *   title = @Translation("Tabs Container"),
 *   description = @Translation("jQuery Tabs."),
 *   icon = "fa fa-folder-open",
 *   child_shortcode = "tab",
 *   description_field = "type"
 * )
 */

class TabsContainerShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    global $tab_content;
    $attrs['class'] = 'tabs ' . (isset($attrs['class']) ? $attrs['class'] : '') . (isset($attrs['type']) ? $attrs['type'] : '');

    $theme_array = [
      '#theme' => 'progressive_shortcodes_tabs_container',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#tab_content' => $tab_content,
      '#text' => $text,
    ];
    $tab_content = '';

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $types = [
      '' => t('Horizontal'),
      'tabs-left' => t('Vertical Left'),
      'tabs-right' => t('Vertical Right'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Type'),
      '#options' => $types,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-4">',
      '#suffix' => '</div></div>'
    ];

    return $form;
  }
}
