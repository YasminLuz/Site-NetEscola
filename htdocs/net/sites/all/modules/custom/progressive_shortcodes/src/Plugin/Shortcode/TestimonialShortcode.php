<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * @Shortcode(
 *   id = "testimonial",
 *   title = @Translation("Testimonial"),
 *   description = @Translation("Testimonial with image."),
 *   icon = "fa fa-comment",
 *   child_shortcode = "html",
 *   description_field = "author"
 * )
 */

class TestimonialShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = 'respond ' . (isset($attrs['type']) ? $attrs['type'] : '');
    $class = 'description ' . $attrs['type'] . (isset($attrs['color']) && $attrs['color'] != 'default-testimonial'? '-' . $attrs['color'] : '');

    $file = isset($attrs['image_fid']) && !empty($attrs['image_fid']) ? File::load($attrs['image_fid']) : FALSE;
    $img = $file ? $file->getFileUri() : '';

    $image_array = $img ? ['#theme' => 'image', '#title' => 'Image', '#alt' => 'image', '#uri' => $img, '#attributes' => ['class' => ['img-circle'],],] : '';
    $img = $this->render($image_array);

    $author = isset($attrs['author']) && $attrs['author'] ? $attrs['author'] : FALSE;
    $author_info = isset($attrs['author_info']) && $attrs['author_info'] ? $attrs['author_info'] : FALSE;

    $theme_array = [
      '#theme' => 'progressive_shortcodes_testimonial',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#class' => $class,
      '#text' => $text,
      '#img' => $img,
      '#author' => $author,
      '#author_info' => $author_info,
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $types = [
      'border' => t('Border color'),
      'bg' => t('Background color'),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#options' => $types,
      '#title' => t('Type'),
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : 'border-',
      '#attributes' => [
        'class' => [
          'form-control',
          'blockquote-shortcode-flag',
        ],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $colors = [
      'default-testimonial' => t('Default'),
      'primary' => t('Dark Blue'),
      'warning' => t('Orange'),
      'error' => t('Red'),
      'success' => t('Green'),
      'info' => t('Blue'),
    ];
    $form['color'] = [
      '#type' => 'radios',
      '#title' => t('Color'),
      '#options' => $colors,
      '#default_value' => isset($attrs['color']) ? $attrs['color'] : '',
      '#attributes' => [
        'class' => [
          'color-radios',
          'form-control',
        ],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $form['author'] = [
      '#type' => 'textfield',
      '#title' => t('Author'),
      '#default_value' => isset($attrs['author']) ? $attrs['author'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['author_info'] = [
      '#type' => 'textfield',
      '#title' => t('Author info'),
      '#default_value' => isset($attrs['author_info']) ? $attrs['author_info'] : '',
      '#attributes' => ['class' => ['form-control']],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
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

    return $form;
  }
}
