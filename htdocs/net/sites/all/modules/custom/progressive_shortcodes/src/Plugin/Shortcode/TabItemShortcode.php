<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "tab",
 *   title = @Translation("Tab Item"),
 *   description = @Translation("Tab content item."),
 *   icon = "fa fa-folder",
 *   description_field = "title"
 * )
 */

class TabItemShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    global $tab_counter;
    global $tab_content;
    $tab_counter++;
    $attrs['class'] = isset($attrs['class']) ? $attrs['class'] : '';
    $attrs['class'] .= $tab_content == '' ? ' active' : '';
    $class = progressive_shortcode_add_class($attrs['class']);

    if ($tab_content == '') {
      $class .= ' in';
    }

    $tab_content_theme_array = [
      '#theme' => 'progressive_shortcodes_tab_item_tab_content',
      '#class' => $class,
      '#tab_counter' => $tab_counter,
      '#text' => $text,
    ];
    $tab_content .= $this->render($tab_content_theme_array);

    $theme_array = [
      '#theme' => 'progressive_shortcodes_tab_item',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#tab_counter' => $tab_counter,
      '#icon' => isset($attrs['icon']) && $attrs['icon'] ? $attrs['icon'] : '',
      '#title' => isset($attrs['title']) ? $attrs['title'] : '',
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
    $form['icon'] = [
      '#title' => t('Icon'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'progressive_shortcodes.ajax_fontawesome_icons_autocomplete',
      '#default_value' => isset($attrs['icon']) ? $attrs['icon'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-3">',
      '#suffix' => '</div></div>'
    ];

    return $form;
  }
}
