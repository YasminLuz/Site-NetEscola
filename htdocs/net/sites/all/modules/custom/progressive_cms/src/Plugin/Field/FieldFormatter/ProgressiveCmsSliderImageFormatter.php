<?php

namespace Drupal\progressive_cms\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;

/**
 * Plugin implementation of the 'image slider' formatter.
 *
 * @FieldFormatter(
 *   id = "progressive_cms_images_slider",
 *   label = @Translation("Progressive CMS: Image Slider"),
 *   field_types = {
 *     "image",
 *   }
 * )
 */
class ProgressiveCmsSliderImageFormatter extends ImageFormatter implements ContainerFactoryPluginInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * The image style entity storage.
   *
   * @var \Drupal\image\ImageStyleStorageInterface
   */
  protected $imageStyleStorage;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'image_style' => '',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $image_styles = image_style_options(FALSE);
    $description_link = Link::fromTextAndUrl(
      $this->t('Configure Image Styles'),
      Url::fromRoute('entity.image_style.collection')
    );
    $element['image_style'] = [
      '#title' => t('Image style'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('image_style'),
      '#empty_option' => t('None (original image)'),
      '#options' => $image_styles,
      '#description' => $description_link->toRenderable() + [
        '#access' => $this->currentUser->hasPermission('administer image styles')
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    $image_styles = image_style_options(FALSE);
    // Unset possible 'No defined styles' option.
    unset($image_styles['']);
    // Styles could be lost because of enabled/disabled modules that defines
    // their styles in code.
    $image_style_setting = $this->getSetting('image_style');
    $summary[] = isset($image_styles[$image_style_setting]) ? t('Image style: @style', ['@style' => $image_styles[$image_style_setting]]) : t('Original image');

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    $files = $this->getEntitiesToView($items, $langcode);

    // Early opt-out if the field is empty.
    if (empty($files)) {
      return $elements;
    }

    $image_style_setting = $this->getSetting('image_style');
    // Collect cache tags to be added for each item in the field.
    $base_cache_tags = [];
    if (!empty($image_style_setting)) {
      $image_style = $this->imageStyleStorage->load($image_style_setting);
      $base_cache_tags = $image_style->getCacheTags();
    }

    $images = [];
    $theme_array = [];
    if (!empty($files)) {
      $field_slider_title = '';
      $field_slider_body = '';
      $slider_link = '';

      $node = $items->getEntity();
      $fields = $node->getFields();

      if (isset($fields['field_slider_title'])) {
        $field_slider_title = $node->get('field_slider_title')->getValue();
        $field_slider_title = reset($field_slider_title);
        $field_slider_title = $field_slider_title['value'];
      }
      if (isset($fields['field_slider_body'])) {
        $field_slider_body = $node->get('field_slider_body')->getValue();
        $field_slider_body = reset($field_slider_body);
        $field_slider_body = $field_slider_body['value'];
      }
      if (isset($fields['field_slider_link'])) {
        $field_slider_link = $node->get('field_slider_link')->getValue();
        $field_slider_link = reset($field_slider_link);
        $link_url = Url::fromUri($field_slider_link['uri'], [
          'attributes' => [
            'class' => [
              'btn',
              'btn-block',
              'btn-default',
              'btn-lg'
            ]
          ]
        ]);
        $link = Link::fromTextAndUrl($field_slider_link['title'], $link_url)
          ->toString();
        $slider_link = $link->getGeneratedLink();
      }

      foreach ($files as $delta => $file) {
        $cache_contexts = [];
        $cache_contexts[] = 'url.site';
        $cache_tags = Cache::mergeTags($base_cache_tags, $file->getCacheTags());
        // Extract field item attributes for the theme function, and unset them
        // from the $item so that the field template does not re-render them.
        $item = $file->_referringItem;
        $item_attributes = $item->_attributes;
        unset($item->_attributes);

        $image_array = [
          '#theme' => 'image_formatter',
          '#item' => $item,
          '#item_attributes' => $item_attributes,
          '#image_style' => $image_style_setting,
          '#cache' => [
            'tags' => $cache_tags,
            'contexts' => $cache_contexts,
          ],
        ];
        $images[] = [
          'img' => $image_array,
        ];
      }

      $theme_array = [
        '#theme' => 'progressive_cms_slider_image_formatter',
        '#images' => $images,
        '#slider_title' => $field_slider_title,
        '#slider_body' => $field_slider_body,
        '#slider_link' => $slider_link,
      ];
    }

    $elements[0]['#markup'] = render($theme_array);
    return $elements;
  }
}
