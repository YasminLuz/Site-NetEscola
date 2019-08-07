<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * @Shortcode(
 *   id = "nd_image",
 *   title = @Translation("Image With Caption"),
 *   description = @Translation("Image with caption."),
 *   icon = "fa fa-file-image-o",
 * )
 */

class ImageWithCaptionShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = isset($attrs['class']) ? ' ' . $attrs['class'] : '';
    $attrs['class'] = ' ' . (isset($attrs['type']) && $attrs['type'] == 'title_overlay' ? 'image_overlay' : 'caption');
    $img_class = isset($attrs['img_rounded']) && $attrs['img_rounded'] ? 'class="img-rounded"' : '';
    $attrs['caption'] = isset($attrs['caption']) && $attrs['caption'] ? $attrs['caption'] : trim($text);

    $file = isset($attrs['fid']) && !empty($attrs['fid']) ? File::load($attrs['fid']) : '';
    $filename = $file ? $file->getFileUri() : $attrs['path'];
    $filename = file_create_url($filename);
//    $alt = $file && isset($file->values->alt) ? $file->get('alt') : '';
//    $title = $file && isset($file->values->type) ? $file->get('title') : '';
//    $title = $file->get('uri')->getValue();
    $alt = '';
    $title = '';

    $theme_array = [
      '#theme' => 'progressive_shortcodes_image',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#filename' => $filename,
      '#img_class' => $img_class,
      '#alt' => $alt,
      '#title' => $title,
      '#zoom' => isset($attrs['zoom']) && $attrs['zoom'] ? TRUE : FALSE,
      '#caption' => $attrs['caption'],
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    if (!isset($attrs['fid']) && !$attrs['fid'] && isset($attrs['path'])) {
      $form['path'] = [
        '#type' => 'textfield',
        '#default_value' => isset($attrs['path']) ? $attrs['path'] : '',
        '#attributes' => [
          'class' => ['hidden'],
        ],
      ];
    }

    $form['fid'] = [
      '#type' => 'textfield',
      '#title' => t('Image'),
      '#default_value' => isset($attrs['fid']) ? $attrs['fid'] : '',
      '#prefix' => '<div class="row"><div class="col-sm-6"><div class="image-gallery-upload ">',
      '#suffix' => '</div></div>',
      '#attributes' => ['class' => ['image-gallery-upload hidden']],
      '#field_suffix' => '<div class="preview-image"></div><a href="#" class="vc-gallery-images-select button">' . t('Upload Image') .'</a><a href="#" class="gallery-remove button">' . t('Remove Image') .'</a>'
    ];

    if (isset($attrs['fid'])) {
      $file = File::load($attrs['fid']);
      if ($file) {
        $filename = $file->getFileUri();
        $filename = ImageStyle::load('medium')->buildUrl($filename);
        $form['fid']['#prefix'] = '<div class="row"><div class="col-sm-6"><div class="image-gallery-upload has_image">';
        $form['fid']['#field_suffix'] = '<div class="preview-image"><img src="' . $filename . '"></div><a href="#" class="vc-gallery-images-select button">' . t('Upload Image') .'</a><a href="#" class="gallery-remove button">' . t('Remove Image') .'</a>';
      }
    }


    $form['img_rounded'] = [
      '#type' => 'checkbox',
      '#title' => t('Rounded Image'),
      '#default_value' => isset($attrs['img_rounded']) ? $attrs['img_rounded'] : '',
      '#prefix' => '<div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $form['caption'] = [
      '#type' => 'textfield',
      '#title' => t('Caption'),
      '#default_value' => isset($attrs['caption']) ? $attrs['caption'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $types = [
      'title_overlay' => t('Overlay'),
      'caption' => t('Caption'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Title type'),
      '#options' => $types,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $form['zoom'] = [
      '#type' => 'checkbox',
      '#title' => t('Zoom'),
      '#default_value' => isset($attrs['zoom']) ? $attrs['zoom'] : '',
      '#prefix' => '<div class="row"><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];

    return $form;
  }
}
