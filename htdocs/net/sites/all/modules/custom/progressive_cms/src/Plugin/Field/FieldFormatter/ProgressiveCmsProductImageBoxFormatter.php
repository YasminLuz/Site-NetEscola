<?php

namespace Drupal\progressive_cms\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Cache\Cache;
use Drupal\image\Entity\ImageStyle;
use Drupal\image\Plugin\Field\FieldFormatter\ImageFormatter;

/**
 * Plugin implementation of the 'image box' formatter.
 *
 * @FieldFormatter(
 *   id = "progressive_cms_product_image_box",
 *   label = @Translation("Progressive CMS: Product Image Box"),
 *   field_types = {
 *     "image",
 *   }
 * )
 */
class ProgressiveCmsProductImageBoxFormatter extends ImageFormatter implements ContainerFactoryPluginInterface {

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
   * @param $entity_object
   * @param $field_name
   * @return mixed|string
   */
  private function _getCornerText($entity_object, $field_name) {
    $field_corner_text = '';
    $fields = $entity_object->getFields();
    if (!empty($field_name) && isset($fields[$field_name])) {
      $field_corner_text = $entity_object->get($field_name)->getValue();
      $field_corner_text = reset($field_corner_text);
      $field_corner_text = $field_corner_text != FALSE ? $field_corner_text['value'] : '';
    }
    return $field_corner_text;
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
        'image_style' => '',
        'small_image_style' => '',
        'corner_text_field' => '',
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
      '#title' => t('Image Style for Main Image'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('image_style'),
      '#empty_option' => t('None (original image)'),
      '#options' => $image_styles,
      '#description' => $description_link->toRenderable() + [
          '#access' => $this->currentUser->hasPermission('administer image styles')
        ],
    ];
    $element['small_image_style'] = [
      '#title' => t('Image Style for Small Images'),
      '#type' => 'select',
      '#default_value' => $this->getSetting('small_image_style'),
      '#empty_option' => t('None (original image)'),
      '#options' => $image_styles,
      '#description' => $description_link->toRenderable() + [
          '#access' => $this->currentUser->hasPermission('administer image styles')
        ],
    ];
    $element['corner_text_field'] = [
      '#title' => t('Corner text field machine name.'),
      '#type' => 'textfield',
      '#default_value' => $this->getSetting('corner_text_field'),
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
    $small_image_style_setting = $this->getSetting('small_image_style');
    $corner_text_field = $this->getSetting('corner_text_field');
    $summary[] = isset($image_styles[$image_style_setting]) ? t('Image style: @style', ['@style' => $image_styles[$image_style_setting]]) : t('Original image');
    $summary[] = isset($image_styles[$small_image_style_setting]) ? t('Small Image style: @style', ['@style' => $image_styles[$small_image_style_setting]]) : t('Original image');
    $summary[] = !empty($corner_text_field) ? t('Corner text field machine name: @corner_text_field', ['@corner_text_field' => $corner_text_field]) : '';

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
    $small_image_style_setting = $this->getSetting('small_image_style');
    $corner_text_field = $this->getSetting('corner_text_field');
    $corner_text_field = trim($corner_text_field);
    // Collect cache tags to be added for each item in the field.
    $base_cache_tags = [];
    $small_base_cache_tags = [];
    if (!empty($image_style_setting)) {
      $image_style = $this->imageStyleStorage->load($image_style_setting);
      $base_cache_tags = $image_style->getCacheTags();
    }
    if (!empty($small_image_style_setting)) {
      $small_image_style = $this->imageStyleStorage->load($small_image_style_setting);
      $small_base_cache_tags = $small_image_style->getCacheTags();
    }

    $entity = $items->getEntity();
    $corner_text = $this->_getCornerText($entity, $corner_text_field);

    $image = '';
    $small_images = [];
    foreach ($files as $delta => $file) {
      $cache_contexts = [];
      $cache_contexts[] = 'url.site';
      $cache_tags = Cache::mergeTags($base_cache_tags, $file->getCacheTags());
      $cache_tags_small = Cache::mergeTags($small_base_cache_tags, $file->getCacheTags());

      // Extract field item attributes for the theme function, and unset them
      // from the $item so that the field template does not re-render them.
      $item = $file->_referringItem;
      $item_attributes = $item->_attributes;
      unset($item->_attributes);

      // Only first Image for Main item.
      if (empty($image)) {
        $image = [
          '#theme' => 'image_formatter',
          '#item' => $item,
          '#item_attributes' => $item_attributes,
          '#image_style' => $image_style_setting,
          '#cache' => [
            'tags' => $cache_tags,
            'contexts' => $cache_contexts,
          ],
        ];
      }

      $small_images[$delta]['small_img'] = [
        '#theme' => 'image_formatter',
        '#item' => $item,
        '#item_attributes' => $item_attributes,
        '#image_style' => $small_image_style_setting,
        '#cache' => [
          'tags' => $cache_tags_small,
          'contexts' => $cache_contexts,
        ],
      ];

      $filename = $file->getFileUri();
      $small_images[$delta]['data_zoom'] = file_create_url($filename);
      $small_images[$delta]['data_image'] = ImageStyle::load($image_style_setting)->buildUrl($filename);
    }

    $theme_array = [
      '#theme' => 'progressive_cms_product_image_box_formatter',
      '#image' => $image,
      '#small_images' => $small_images,
      '#corner_text' => $corner_text,
    ];

    $elements[0]['#markup'] = render($theme_array);
    return $elements;
  }
}
