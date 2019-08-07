<?php
/**
 * @file
 * Contains \Drupal\progressive_cms\Form\ProgressiveCmsBlogTimelineForm.
 */

namespace Drupal\progressive_cms\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\progressive_cms\Ajax\ProgressiveCmsBlogTimelineCommand;

class ProgressiveCmsBlogTimelineForm extends FormBase {

  /**
   * @return string
   */
  public function getFormId() {
    return 'progressive_cms_blog_timeline_form';
  }

  /**
   * {@inheritdoc}.
   * @param array $form
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   * @param null $attrs
   * @return array
   */
  public function buildForm(array $form, FormStateInterface $form_state, $attrs = []) {

    // @todo: this element needs because $form_state in submit does not contain form ID selector.
    $form['form_id_clone'] = [
      '#type' => 'textfield',
      '#title' => t('Form ID clone'),
      '#title_display' => 'invisible',
      '#default_value' => '',
      '#attributes' => [
        'class' => ['hidden'],
      ],
    ];

    $form['id'] = [
      '#type' => 'textfield',
      '#title' => t('Setting ID'),
      '#title_display' => 'invisible',
      '#default_value' => $attrs['id'],
      '#attributes' => [
        'class' => ['hidden'],
      ],
    ];
    $form['livicon'] = [
      '#title' => $this->t('Livicon'),
      '#type' => 'textfield',
      '#autocomplete_route_name' => 'progressive_shortcodes.ajax_livicons_autocomplete',
      '#default_value' => isset($attrs['livicon']) ? $attrs['livicon'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];
    $colors = [
      'black' => t('Black'),
      'danger' => t('Red'),
      'info' => t('Blue'),
      'warning' => t('Orange'),
      'success' => t('Green'),
      'primary' => t('Dark Blue'),
    ];
    $form['color'] = [
      '#type' => 'radios',
      '#title' => $this->t('Color'),
      '#options' => $colors,
      '#default_value' => isset($attrs['color']) ? $attrs['color'] : 'info',
      '#attributes' => [
        'class' => [
          'color-radios',
          'no-styles',
        ],
      ],
    ];
    $form['tranparent_bg'] = [
      '#title' => $this->t('Transparent Background'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['tranparent_bg']) ? $attrs['tranparent_bg'] : TRUE,
      '#attributes' => [
        'class' => ['no-styles'],
      ],
    ];
    $form['title'] = [
      '#title' => $this->t('Show Title'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['title']) ? $attrs['title'] : FALSE,
      '#attributes' => [
        'class' => ['no-styles'],
      ],
    ];
    $form['no_padding'] = [
      '#title' => $this->t('No Padding'),
      '#type' => 'checkbox',
      '#default_value' => isset($attrs['no_padding']) ? $attrs['no_padding'] : FALSE,
      '#attributes' => [
        'class' => ['no-styles'],
      ],
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
      '#attributes' => [
        'class' => ['btn',],
      ],
      '#ajax' => [
        'callback' => '::submitForm',
        'wrapper' => 'doesnt-matter',
        'effect' => 'fade',
        'method' => 'replace',
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $settings = \Drupal::config('progressive_cms.settings')->get('blog_timeline');
    $selector = $form_state->getValue('form_id_clone');
    $values = $form_state->getValues();
    $settings[$form_state->getValue('id')] = $values;

    \Drupal::configFactory()
      ->getEditable('progressive_cms.settings')
      ->set('blog_timeline', $settings)
      ->save();

    $response = new AjaxResponse();
    $response->addCommand(new ProgressiveCmsBlogTimelineCommand($selector, $values));

    return $response;
  }
}
