<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "social_box",
 *   title = @Translation("Social Icon Box"),
 *   description = @Translation("Social Icon Link with text."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-facebook",
 * )
 */
class SocialIconBoxShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = (isset($attrs['class']) ? $attrs['class'] : '') . 'sbtnf sbtnf-rounded color color-hover ' . (isset($attrs['icon']) ? $attrs['icon'] : '');
    $theme_array = [
      '#theme' => 'progressive_shortcodes_social_box',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#link' => isset($attrs['link']) ? $attrs['link'] : '#',
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $form['icon'] = [
      '#title' => t('Icon'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'progressive_shortcodes.ajax_social_icons_autocomplete',
      '#default_value' => isset($attrs['icon']) ? $attrs['icon'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
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
