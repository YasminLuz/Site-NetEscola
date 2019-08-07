<?php

/**
 * @file
 * Contains \Drupal\progressive_cms\Plugin\Block\NdProgressiveFooterCopyright.
 */

namespace Drupal\progressive_cms\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Block\BlockBase;

/**
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "nd_progressive_footer_copyright",
 *   admin_label = @Translation("Progressive: Footer Copyright")
 * )
 */
class NdProgressiveFooterCopyright extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'copyright' => '',
      'phones' => '',
      'address' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['copyright'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Copyright Text'),
      '#default_value' => $this->configuration['copyright'],
    );
    $form['phones'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Phones'),
      '#default_value' => $this->configuration['phones'],
      '#rows' => 3
    );
    $form['address'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Address'),
      '#default_value' => $this->configuration['address'],
      '#rows' => 3
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['copyright'] = $form_state->getValue('copyright');
    $this->configuration['phones'] = $form_state->getValue('phones');
    $this->configuration['address'] = $form_state->getValue('address');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $request_time = \Drupal::time()->getRequestTime();
    $theme_array = [
      '#theme' => 'progressive_cms_footer_copyright',
      '#copyright' => !empty($this->configuration['copyright']) ? $this->configuration['copyright'] : FALSE,
      '#phones' => !empty($this->configuration['phones']) ? $this->configuration['phones'] : FALSE,
      '#address' => !empty($this->configuration['address']) ? $this->configuration['address'] : FALSE,
      '#sitename' => \Drupal::config('system.site')->get('name'),
      '#current_year' => \Drupal::service('date.formatter')->format($request_time, 'custom', 'Y'),
    ];

    return [
      '#markup' => render($theme_array),
    ];
  }
}
