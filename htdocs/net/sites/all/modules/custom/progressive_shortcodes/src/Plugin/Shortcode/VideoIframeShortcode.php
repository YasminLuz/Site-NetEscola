<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;

/**
 * @Shortcode(
 *   id = "video",
 *   title = @Translation("Video Iframe"),
 *   description = @Translation("Iframe adaptive video."),
 *   icon = "fa fa-video-camera",
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   description_field = "url"
 * )
 */

class VideoIframeShortcode extends ShortcodeBase {

  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $attrs['class'] = isset($attrs['class']) ? ' ' . $attrs['class'] : '';
    $attrs['url'] = isset($attrs['url']) && $attrs['url'] ? $attrs['url'] : $text;
    $iframe_attrs = (isset($attrs['width']) ? 'width="' . $attrs['width'] .'"' : '') . (isset($attrs['height']) ? ' height ="' . $attrs['height'] . '"' : '');
    if (!$iframe_attrs) {
      $attrs['class'] .= ' video-box';
    }
    $video_url = '';
    if (strpos($attrs['url'], 'vimeo') !== FALSE) {
      $attrs['class'] .= ' vimeo';
      preg_match('|/(\d+)|', $attrs['url'], $matches);
      $video_url = '//player.vimeo.com/video/' . $matches[1] . '';
    }
    elseif (strpos($attrs['url'], 'youtube') !== FALSE) {
      $attrs['class'] .= ' youtube';
      $id = substr($attrs['url'], strpos($attrs['url'], '?v=') + 3);
      $video_url = '//www.youtube.com/embed/' . $id .'?showinfo=0&amp;wmode=opaque&rel=0';
    }

    $theme_array = [
      '#theme' => 'progressive_shortcodes_video',
      '#attrs' => _progressive_shortcodes_shortcode_attributes($attrs),
      '#video_url' => $video_url,
      '#iframe_attrs' => $iframe_attrs,
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $attrs['url'] = isset($attrs['url']) && $attrs['url'] ? $attrs['url'] : $text;
    $form['url'] = [
      '#type' => 'textfield' ,
      '#title' => t('Video Url'),
      '#default_value' => isset($attrs['url']) ? $attrs['url'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#description' => t('Supports: YouTube and Vimeo'),
    ];
    $form['width'] = [
      '#type' => 'textfield' ,
      '#title' => t('Width'),
      '#default_value' => isset($attrs['width']) ? $attrs['width'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-6">',
    ];
    $form['height'] = [
      '#type' => 'textfield' ,
      '#title' => t('Height'),
      '#default_value' => isset($attrs['height']) ? $attrs['height'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '</div><div class="col-sm-6">',
      '#suffix' => '</div></div>',
    ];

    return $form;
  }
}
