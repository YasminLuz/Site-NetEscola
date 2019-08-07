<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "accordions",
 *   title = @Translation("Accordion Container"),
 *   description = @Translation("Animated Accordions wrapper."),
 *   child_shortcode = "accordion",
 *   icon = "fa fa-bars",
 * )
 */

class AccordionContainerShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    global $accordion_id, $accordion_filter;

    $attrs = is_array($attrs) ? $attrs : [];
    $attrs['class'] = 'panel-group';
    $attrs['class'] = $attrs['class'] . (isset($attrs['multi_collapse']) && $attrs['multi_collapse'] ? ' multi-collapse' : '');
    if (!empty($accordion_filter)) {
      $attrs['class'] = $attrs['class'] . ' filter-elements';
    }

    $theme_array = [
      '#theme' => 'progressive_shortcodes_accordion_container',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#accordion_id' => $accordion_id,
      '#accordion_filter' => $accordion_filter,
      '#text' => $text,
    ];

    $accordion_id++;
    $accordion_filter = [];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $form['multi_collapse'] = [
      '#type' => 'checkbox',
      '#title' => t('Multi Collapse mode'),
      '#default_value' => isset($attrs['multi_collapse']) ? $attrs['multi_collapse'] : '',
    ];

    return $form;
  }
}
