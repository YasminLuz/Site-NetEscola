<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * @Shortcode(
 *   id = "nd_quote",
 *   title = @Translation("Quote"),
 *   description = @Translation("Replace the given text formatted like as a quote."),
 *   icon = "fa fa-quote-right",
 *   child_shortcode = "html",
 *   description_field = "author"
 * )
 */

class QuoteShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    if (isset($attrs['blockquote']) && $attrs['blockquote']) {
      $theme_array = [
        '#theme' => 'progressive_shortcodes_quote_blockquote',
        '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
        '#author' => $attrs['author'] ? '<small>' . $attrs['author'] . '</small>' : '',
        '#text' => $text,
      ];
    }
    else {
      $attrs['class'] = (isset($attrs['class']) ? $attrs['class'] : '') . ' quote centered';
      if (isset($attrs['image']) && $attrs['image']) {
        $img = $attrs['image'];
      }
      elseif (isset($attrs['image_fid']) && $attrs['image_fid']) {
        $file = isset($attrs['image_fid']) && !empty($attrs['image_fid']) ? File::load($attrs['image_fid']) : FALSE;
        $img = $file ? $file->getFileUri() : (isset($attrs['image']) ? $attrs['image'] : '');
      }
      $h = isset($img) ? '4' : '2';
      $image_array = isset($img) ? ['#theme' => 'image', '#title' => 'Image', '#alt' => 'image', '#uri' => $img, '#attributes' => ['class' => ['img-circle'],],] : '';
      $img = $this->render($image_array);

      $theme_array = [
        '#theme' => 'progressive_shortcodes_quote_no_blockquote',
        '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
        '#img' => $img,
        '#h' => $h,
        '#author' => $attrs['author'] ? '<small>' . $attrs['author'] . '</small>' : '',
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
    $form['blockquote'] = [
      '#type' => 'checkbox',
      '#title' => t('Blockquote'),
      '#default_value' => isset($attrs['blockquote']) ? $attrs['blockquote'] : 0,
      '#attributes' => [
        'class' => ['blockquote-shortcode-flag'],
      ],
    ];
    $form['author'] = [
      '#type' => 'textfield',
      '#title' => t('Author'),
      '#default_value' => isset($attrs['author']) ? $attrs['author'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];

    $form['image_fid'] = [
      '#type' => 'textfield',
      '#title' => t('Image'),
      '#default_value' => isset($attrs['image_fid']) ? $attrs['image_fid'] : '',
      '#states' => [
        'visible' => [
          '.blockquote-shortcode-flag' => ['checked' => FALSE],
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

    // Added only to save the old image attribute
    if (isset($attrs['image']) && !isset($attrs['image_fid']) && !$attrs['image_fid']) {
      $form['image'] = [
        '#type' => 'textfield',
        '#default_value' => $attrs['image'],
        '#attributes' => [
          'class' => [
            'hidden',
            'fid-old-field',
          ],
        ],
      ];
    }

    return $form;
  }
}
