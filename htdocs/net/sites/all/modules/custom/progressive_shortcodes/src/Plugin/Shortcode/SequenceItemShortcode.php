<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * @Shortcode(
 *   id = "sequence_item",
 *   title = @Translation("Sequence Item"),
 *   description = @Translation("Sequence Item."),
 *   child_shortcode = "slider_item",
 *   icon = "fa fa-long-arrow-down",
 *   description_field = "type"
 * )
 */

class SequenceItemShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = 'step ' . (isset($attrs['class']) ? $attrs['class'] : '');
    $attrs['class'] .= 'border-' . $attrs['color'];
    $attrs['class'] .= in_array($attrs['type'], ['background', 'image_background']) ? ' white bg-' . $attrs['color'] : '';
    $bg_img = FALSE;
    $inner_attrs = '';
    if ($attrs['type'] == 'image_background' && isset($attrs['image_fid']) && $attrs['image_fid']) {
      $inner_attrs = [
        'class' => 'bg-image',
        'style_background_image' => $attrs['image_fid'],
      ];
      $bg_img = TRUE;
    }

    $theme_array = [
      '#theme' => 'progressive_shortcodes_sequence_item',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#inner_attrs' => _progressive_shortcodes_shortcode_attributes($inner_attrs),
      '#bg_img' => $bg_img,
      '#text' => $text,
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $colors = [
      'warning' => t('Orange'),
      'error' => t('Red'),
      'success' => t('Green'),
      'info' => t('Blue'),
      'grey' => t('Grey'),
    ];
    $form['color'] = [
      '#type' => 'radios',
      '#title' => t('Color'),
      '#options' => $colors,
      '#default_value' => isset($attrs['color']) ? $attrs['color'] : 'warning',
      '#attributes' => [
        'class' => ['color-radios'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
    ];
    $types = [
      'background' => t('Background'),
      'border' => t('Border'),
      'image_background' => t('Image Background'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#options' => $types,
      '#title' => t('Type'),
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : 'background',
      '#attributes' => [
        'class' => [
          'form-control',
          'type-sequence-select',
        ],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];

    $form['image_fid'] = [
      '#type' => 'textfield',
      '#title' => t('Image'),
      '#default_value' => isset($attrs['image_fid']) ? $attrs['image_fid'] : '',
      '#states' => [
        'visible' => [
          '.type-sequence-select' => ['value' => 'image_background'],
        ],
      ],
      '#attributes' => [
        'class' => ['image-media-upload hidden'],
      ],
      '#field_suffix' => '<div class="preview-image"></div><a href="#" class="vc-gallery-images-select button">' . t('Upload Image') . '</a><a href="#" class="gallery-remove button">' . t('Remove Image') . '</a>',
    ];
    if (isset($attrs['image_fid']) && !empty($attrs['image_fid'])) {
      $file = isset($attrs['image_fid']) && !empty($attrs['image_fid']) ? File::load($attrs['image_fid']) : '';
      if ($file) {
        $filename = $file->getFileUri();
        $image = ImageStyle::load('medium')->buildUrl($filename);
        $form['image_fid']['#prefix'] = '<div class="col-sm-4"><div class="image-gallery-upload has_image">';
        $form['image_fid']['#field_suffix'] = '<div class="preview-image"><img src="' . $image . '"></div><a href="#" class="vc-gallery-images-select button">' . t('Upload Image') . '</a><a href="#" class="gallery-remove button">' . t('Remove Image') . '</a>';
      }
    }

    return $form;
  }
}
