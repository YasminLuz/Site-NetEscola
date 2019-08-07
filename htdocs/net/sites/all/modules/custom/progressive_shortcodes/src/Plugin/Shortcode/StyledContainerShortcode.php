<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 * @Shortcode(
 *   id = "styledcontainer",
 *   title = @Translation("Styled Container"),
 *   description = @Translation("Styled Frame container."),
 *   icon = "fa fa-strikethrough",
 *   description_field = "type"
 * )
 */

class StyledContainerShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = isset($attrs['class']) ? ' ' . $attrs['class'] : '';
    $attrs['class'] .= isset($attrs['type']) ? ' ' . $attrs['type'] : '';
    $attrs['class'] .= isset($attrs['border']) ? ' ' . $attrs['border'] : '';
    $attrs['class'] .= isset($attrs['shadow']) ? ' ' . $attrs['shadow'] : '';
    if (isset($attrs['degree']) && $attrs['degree']) {
      $wrap_attrs = [];
      foreach (['-webkit-', '-moz-', '-ms', '-o-', ''] as $suffix) {
        $wrap_attrs['style'] = (isset($attrs['styles']) ? $attrs['styles'] . ' ' : ' ') . $suffix . 'transform: rotate(' . $attrs['degree'] . 'deg);';
      }
    }
    $overlay = isset($attrs['overlay']) && $attrs['overlay'] ? '<div class="overlay"></div>' : '';
    $bgs = ['fixed', 'static', 'paralax', 'blur'];
    if (isset($attrs['background_image']) && $attrs['background_image'] && isset($attrs['background']) && in_array($attrs['background'], $bgs)) {
      $attrs['class'] .= ' full-width-box';
      if (isset($attrs['background']) && $attrs['background'] == 'static') {
        $attrs['style_background_image'] = $attrs['background_image'];
      }
      if (isset($attrs['background']) && $attrs['background'] == 'fixed') {
        $inner_attrs = [
          'style_background_image' => $attrs['background_image']
        ];
        $text = '<div class="fwb-bg fwb-' . $attrs['background'] . '" ' . _progressive_shortcodes_shortcode_attributes($inner_attrs) . '>' . $overlay . '</div>' . $text;
      }
      if (isset($attrs['background']) && $attrs['background'] == 'paralax') {
        $inner_attrs = [
          'data-speed' => isset($attrs['stellar_background_ratio']) ? $attrs['stellar_background_ratio'] : '2',
          'style_background_image' => $attrs['background_image']
        ];
        $text = '<div class="fwb-bg fwb-' . $attrs['background'] . '" ' . _progressive_shortcodes_shortcode_attributes($inner_attrs) . '>' . $overlay . '</div>' . $text;
      }
      if (isset($attrs['background']) && $attrs['background'] == 'blur') {
        $file = File::load($attrs['background_image']);
        $inner_attrs = [
          'data-blur-amount' => isset($attrs['blur_amount']) ? $attrs['blur_amount'] : '2',
          'data-blur-image' => $file ? file_create_url($file->getFileUri()) : '',
        ];
        $text = '<div class="fwb-bg fwb-' . $attrs['background'] . '" ' . _progressive_shortcodes_shortcode_attributes($inner_attrs) . '>' . $overlay . '</div>' . $text;
      }
    }
    if (isset($attrs['background']) && $attrs['background'] == 'video' && isset($attrs['video']) && $attrs['video']) {
      static $video_id = 999;
      $attrs['class'] = 'full-width-box';
      $attrs['data-stellar-background-ratio'] = isset($attrs['stellar_background_ratio']) ? $attrs['stellar_background_ratio'] : '0.5';
      $attrs['id'] = 'wrap-' . ++$video_id;
      $text = '<a class="player" data-property="{videoURL: \'' . $attrs['video'] . '\', containment:\'#' . $attrs['id'] . '\', autoPlay:true, showControls:true, loop:true, mute:true, startAt:0, opacity:1, addRaster:false}"></a>' . $overlay . $text;
    }
    $output = '<div ' . _progressive_shortcodes_shortcode_attributes($attrs) . '>' . $text . '</div>';
    if (isset($wrap_attrs)) {
      $output = '<div ' . _progressive_shortcodes_shortcode_attributes($wrap_attrs) . '>' . $output . '</div>';
    }
    return $output;
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
    $types = [
      'content-block' => t('Container'),
      'frame-padding' => t('Frame'),
      '' => t(' - No Paddings - '),
    ];
    $form['type'] = [
      '#type' => 'select',
      '#title' => t('Padding type'),
      '#options' => $types,
      '#default_value' => isset($attrs['type']) ? $attrs['type'] : 'content-block',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];

    $border = [
      '' => t(' - None - '),
      'frame' => t('Border'),
      'frame border-radius' => t('Border with Radius'),
    ];
    $form['border'] = [
      '#type' => 'select',
      '#title' => t('Border'),
      '#options' => $border,
      '#default_value' => isset($attrs['border']) ? $attrs['border'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class = "col-sm-6">',
      '#suffix' => '</div></div>',
    ];

    $shadows = [
      '' => t(' - None - '),
      'frame-shadow' => t('Shadow'),
      'frame-shadow-lifted' => t('Shadow Lifted'),
      'frame-shadow-raised' => t('Shadow Raised'),
      'frame-shadow-curved' => t('Shadow Curved'),
    ];
    $form['shadow'] = [
      '#type' => 'select',
      '#title' => t('Shadow'),
      '#options' => $shadows,
      '#default_value' => isset($attrs['shadow']) ? $attrs['shadow'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['degree'] = [
      '#type' => 'textfield' ,
      '#title' => t('Rotate Degree'),
      '#default_value' => isset($attrs['degree']) ? $attrs['degree'] : '',
      '#description' => t('Any degree value, for example: -90'),
      '#attributes' => ['class' => ['form-control']],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];
    $bgs = [
      '' => t(' - None - '),
      'static' => t('Static Image'),
      'fixed' => t('Fixed'),
      'paralax' => t('Parallax'),
      'blur' => t('Blurred Image'),
      'video' => t('Video'),
    ];
    $form['background'] = [
      '#type' => 'select',
      '#title' => t('Advanced Background'),
      '#options' => $bgs,
      '#default_value' => isset($attrs['background']) ? $attrs['background'] : '',
      '#attributes' => [
        'class' => ['form-control background-select'],
      ]
    ];
    $form['overlay'] = [
      '#type' => 'checkbox' ,
      '#title' => t('Overlay'),
      '#default_value' => isset($attrs['overlay']) ? $attrs['overlay'] : '',
      '#states' => [
        'visible' => [
          '.background-select, abcd' => ['!value' => ''],
          '.background-select, abc' => ['!value' => 'static'],
        ],
      ],
    ];
    $form['video'] = [
      '#type' => 'textfield',
      '#title' => t('Video URL'),
      '#default_value' => isset($attrs['video']) ? $attrs['video'] : '',
      '#states' => [
        'visible' => [
          '.background-select' => ['value' => 'video'],
        ],
      ],
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];

    $form['background_image'] = [
      '#type' => 'textfield',
      '#title' => t('Image'),
      '#default_value' => isset($attrs['background_image']) ? $attrs['background_image'] : '',
      '#prefix' => '<div class="image-gallery-upload ">',
      '#attributes' => ['class' => ['image-gallery-upload hidden']],
      '#field_suffix' => '<div class="preview-image"></div><a href="#" class="vc-gallery-images-select button">' . t('Upload Image') .'</a><a href="#" class="gallery-remove button">' . t('Remove Image') .'</a>',
      '#states' => [
        'visible' => [
          '.background-select, abc' => ['!value' => ''],
          '.background-select, abcd' => ['!value' => 'video'],
        ],
      ],
    ];

    if (isset($attrs['background_image'])) {
      $file = File::load($attrs['background_image']);
      if ($file) {
        $filename = $file->getFileUri();
        $filename = ImageStyle::load('medium')->buildUrl($filename);
        $form['background_image']['#prefix'] = '<div class="image-gallery-upload has_image">';
        $form['background_image']['#field_suffix'] = '<div class="preview-image"><img src="' . $filename . '"></div><a href="#" class="vc-gallery-images-select button">' . t('Upload Image') .'</a><a href="#" class="gallery-remove button">' . t('Remove Image') .'</a>';
      }
    }

    $form['stellar_background_ratio'] = [
      '#type' => 'textfield',
      '#title' => t('Stellar ratio'),
      '#default_value' => isset($attrs['stellar_background_ratio']) ? $attrs['stellar_background_ratio'] : 0.5,
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#states' => [
        'visible' => [
          '.background-select, ab' => ['!value' => ''],
          '.background-select, abc' => ['!value' => 'static'],
          '.background-select, abcd' => ['!value' => 'fixed'],
          '.background-select, abcde' => ['!value' => 'blur'],
        ],
      ],
      '#description' => t('Default value: 0.5')
    ];
    $form['blur_amount'] = [
      '#type' => 'textfield',
      '#title' => t('Blur Amount'),
      '#default_value' => isset($attrs['blur_amount']) ? $attrs['blur_amount'] : 2,
      '#states' => [
        'visible' => [
          '.background-select' => ['value' => 'blur'],
        ],
      ],
      '#description' => t('Default value: 2')
    ];

    return $form;
  }
}
