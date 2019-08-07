<?php

namespace Drupal\progressive_shortcodes\Plugin\Shortcode;

use Drupal\Core\Language\Language;
use Drupal\shortcode\Plugin\ShortcodeBase;
use Drupal\Core\Menu\MenuTreeParameters;

/**
 * @Shortcode(
 *   id = "quicklinks",
 *   title = @Translation("Quick Links"),
 *   description = @Translation("Expandable menu links."),
 *   process_backend_callback = "nd_visualshortcodes_backend_nochilds",
 *   icon = "fa fa-th-list",
 * )
 */

class QuickLinksShortcode extends ShortcodeBase {
  /**
   * @param $menu_name
   * @return mixed
   */
/*
  function render_drop_down($menu_name) {
    $menu_tree = \Drupal::menuTree();
    $parameters = new MenuTreeParameters();
    $parameters->setMaxDepth(2)->onlyEnabledLinks();
    // Load the tree based on this set of parameters.
    $tree = $menu_tree->load($menu_name, $parameters);
    // Transform the tree using the manipulators you want.
    $manipulators = [
      // Only show links that are accessible for the current user.
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      // Use the default sorting of menu links.
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $menu_tree->transform($tree, $manipulators);
    // Finally, build a renderable array from the transformed tree.
    $menu = $menu_tree->build($tree);
    $items = isset($menu['#items']) ? $menu['#items'] : [];
    $menu = _progressive_shortcodes_simple_menu($items);

    return \Drupal::service('renderer')->render($menu);
  }
*/
  /**
   * {@inheritdoc}
   */
  public function process(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $menu = '';
    if (isset($attrs['machine_name']) && $attrs['machine_name']) {
      $menu_name = $attrs['machine_name'];
      $menu_tree = \Drupal::menuTree();
      $parameters = new MenuTreeParameters();
      $parameters->setMaxDepth(2)->onlyEnabledLinks();
      // Load the tree based on this set of parameters.
      $tree = $menu_tree->load($menu_name, $parameters);
      // Transform the tree using the manipulators you want.
      $manipulators = [
        // Only show links that are accessible for the current user.
        ['callable' => 'menu.default_tree_manipulators:checkAccess'],
        // Use the default sorting of menu links.
        ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
      ];
      $tree = $menu_tree->transform($tree, $manipulators);
      // Finally, build a renderable array from the transformed tree.
      $menu = $menu_tree->build($tree);
      $items = isset($menu['#items']) ? $menu['#items'] : [];
      $menu = _progressive_shortcodes_simple_menu($items);
      $menu = $this->render($menu);
    }

    $theme_array = [
      '#theme' => 'progressive_shortcodes_quicklinks',
      '#tree' => $menu,
    ];

    return $this->render($theme_array);
  }

  /**
   * {@inheritdoc}
   */
  public function settings(array $attrs, $text, $langcode = Language::LANGCODE_NOT_SPECIFIED) {
    $form = [];
    $form['machine_name'] = [
      '#title' => t('Menu'),
      '#type' => 'select',
      '#options' => menu_ui_get_menus(),
      '#default_value' => isset($attrs['machine_name']) ? $attrs['machine_name'] : '',
      '#attributes' => [
        'class' => ['form-control'],
      ],
    ];

    return $form;
  }
}
