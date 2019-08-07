<?php

/**
 * @file
 * Definition of Drupal\d8views\Plugin\views\field\ProgressiveCmsImageUrl
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
 * @ViewsField("progressive_cms_image_url")
 */
class ProgressiveCmsImageUrl extends FieldPluginBase {

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
    $options['field_images_fid'] = ['default' => ''];
    $options['image_style_name'] = ['default' => ''];
    return $options;
  }

  /**
   * Provide the options form.
   * @param $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    $form['field_images_fid'] = array(
      '#title' => $this->t('Image FID field machine name'),
      '#type' => 'textfield',
      '#default_value' => $this->options['field_images_fid'],
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
   * @param \Drupal\views\ResultRow $values
   * @return \Drupal\Core\GeneratedUrl|string
   */
  public function render(ResultRow $values) {
    $url = '';
    if (isset($this->options['field_images_fid']) && !empty($this->options['field_images_fid'])) {
      $field_images_fid = $this->options['field_images_fid'];
      $image_style_name = $this->options['image_style_name'];

      if (isset($values->{$field_images_fid})) {
        $fid = $values->{$field_images_fid};
        $uri = File::load($fid)->getFileUri();
        $url = !empty($image_style) ? ImageStyle::load($image_style_name)->buildUrl($uri) : file_create_url($uri);
      }
    }
    return $url;
  }
}
