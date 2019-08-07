<?php

/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\field\ProgressiveCmsSingleImageUrl
 */

namespace Drupal\progressive_cms\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\file\Entity\File;
use Drupal\image\Entity\ImageStyle;

/**
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("progressive_cms_single_image_url")
 */
class ProgressiveCmsSingleImageUrl extends FieldPluginBase {

  /**
   * @{inheritdoc}
   */
  public function query() {
    // Leave empty to avoid a query on this field.
  }

  /**
   * Define the available options
   * @return array
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['image_field_name'] = ['default' => ''];
    $options['image_style_name'] = ['default' => ''];
    return $options;
  }

  /**
   * Provide the options form.
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['image_field_name'] = array(
      '#title' => $this->t('Image field machine name'),
      '#type' => 'textfield',
      '#default_value' => $this->options['image_field_name'],
      '#description' => $this->t('Enter the image field machine name that is used in the entity.'),
    );
    $form['image_style_name'] = array(
      '#title' => $this->t('Image style'),
      '#type' => 'select',
      '#default_value' => $this->options['image_style_name'],
      '#options' => image_style_options(),
      '#description' => $this->t('Select which Image style should be applied.'),
    );

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * @{inheritdoc}
   * @param $field_entity
   * @param $image_style
   * @return \Drupal\Core\GeneratedUrl|string
   */
  function _get_image_url($field_entity, $image_style) {
    $file_url = '';
    if ($field_entity->getFieldDefinition()->getType() == 'image') {
      $fid = $field_entity->target_id;
      $file = File::load($fid);
      if ($file) {
        $uri = File::load($fid)->getFileUri();
        $file_url = !empty($image_style) ? ImageStyle::load($image_style)->buildUrl($uri) : file_create_url($uri);
      }
    }
    return $file_url;
  }

  /**
   * @{inheritdoc}
   * @param \Drupal\views\ResultRow $values
   * @return \Drupal\Core\GeneratedUrl|string
   */
  public function render(ResultRow $values) {
    $url = '';
    if (isset($this->options['image_field_name']) && !empty($this->options['image_field_name'])) {
      $image_field_name = $this->options['image_field_name'];
      $image_style_name = $this->options['image_style_name'];
      $entity = $values->_entity;
      $fields = $entity->getFields();

      switch ($entity->getEntityTypeId()) {
        case 'commerce_product':
          if (array_key_exists($image_field_name, $fields)) {
            $url = $this->_get_image_url($fields[$image_field_name], $image_style_name);
          }
          elseif ($entity->hasVariations()) {
            $variations = $entity->getVariations();
            $variation = reset($variations);
            $variation_fields = $variation->getFields();
            if (array_key_exists($image_field_name, $variation_fields)) {
              $url = $this->_get_image_url($variation_fields[$image_field_name], $image_style_name);
            }
          }
          break;

        case 'node':
          if (array_key_exists($image_field_name, $fields)) {
            $url = $this->_get_image_url($fields[$image_field_name], $image_style_name);
          }
          break;
      }
    }
    return $url;
  }
}
