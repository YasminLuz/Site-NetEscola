<?php

/**
 * @file
 * Contains \Drupal\progressive_cms\Plugin\Block\NdProgressiveMenu.
 */

namespace Drupal\progressive_cms\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\search\Form\SearchBlockForm;
use Drupal\block\Entity\Block;
use Drupal\Component\Utility\Unicode;

// Cart
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\commerce_cart\CartProviderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Drupal\Core\Block\BlockBase gives us a very useful set of basic functionality
 * for this configurable block. We can just fill in a few of the blanks with
 * defaultConfiguration(), blockForm(), blockSubmit(), and build().
 *
 * @Block(
 *   id = "nd_progressive_menu",
 *   admin_label = @Translation("Progressive: Main Menu")
 * )
 */
class NdProgressiveMenu extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The cart provider.
   *
   * @var \Drupal\commerce_cart\CartProviderInterface
   */
  protected $cartProvider;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Stores flag counts per flag and user.
   *
   * @var array
   */
  protected $userFlagCounts = [];

  /**
   * @return int
   */
  protected function getCartCount() {
    $cacheable_metadata = new CacheableMetadata();
    $cacheable_metadata->addCacheContexts(['user', 'session']);

    /** @var \Drupal\commerce_order\Entity\OrderInterface[] $carts */
    $carts = $this->cartProvider->getCarts();
    $carts = array_filter($carts, function ($cart) {
      /** @var \Drupal\commerce_order\Entity\OrderInterface $cart */
      // There is a chance the cart may have converted from a draft order, but
      // is still in session. Such as just completing check out. So we verify
      // that the cart is still a cart.
      return $cart->hasItems() && $cart->cart->value;
    });

    $count = 0;
    if (!empty($carts)) {
      foreach ($carts as $cart_id => $cart) {
        foreach ($cart->getItems() as $order_item) {
          $count += (int) $order_item->getQuantity();
        }
        $cacheable_metadata->addCacheableDependency($cart);
      }
    }

    return $count;
  }

  /**
   * Constructs a new NdProgressiveMenu.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\commerce_cart\CartProviderInterface $cart_provider
   *   The cart provider.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CartProviderInterface $cart_provider = NULL, EntityTypeManagerInterface $entity_type_manager = NULL) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->cartProvider = $cart_provider;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      \Drupal::moduleHandler()->moduleExists('commerce_cart') ? $container->get('commerce_cart.cart_provider') : [],
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'menu' => 'main',
      'one_page_pages' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = [];
    $form['menu'] = [
      '#type' => 'select',
      '#title' => $this->t('Menu'),
      '#default_value' => $this->configuration['menu'],
      '#options' => menu_ui_get_menus(),
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-4">',
      '#suffix' => '</div></div>',
    ];
    $form['one_page_pages'] = [
      '#type' => 'textarea',
      '#rows' => 3,
      '#title' => t('One Page URLs'),
      '#default_value' => $this->configuration['one_page_pages'],
      '#attributes' => [
        'class' => ['form-control'],
      ],
      '#prefix' => '<div class="row"><div class="col-sm-4">',
      '#suffix' => '</div></div>',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $pages = $form_state->getValue('one_page_pages');

    $this->configuration['menu'] = $form_state->getValue('menu');
    $this->configuration['one_page_pages'] = $pages;

    \Drupal::configFactory()
      ->getEditable('progressive_cms.settings')
      ->set('one_page_pages', $pages)
      ->save();
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $user = \Drupal::currentUser();

    // Is One page.
    $one_page = _is_one_page();

    // Top Box.
    $layout = _nikadevs_cms_get_active_layout();
    $enable_top_box = theme_get_setting('header_top_menu') && !$one_page ? TRUE : FALSE;

    // Language.
    $lang_name = FALSE;
    $language_block = FALSE;
    if (theme_get_setting('language') && \Drupal::moduleHandler()->moduleExists('language')) {
      $block = \Drupal::entityTypeManager()
        ->getStorage('block')
        ->load('languageswitcher')
        ->getPlugin()
        ->build();
      $language_block = render($block);
      $lang_name = \Drupal::languageManager()->getCurrentLanguage()->getName();
    }

    // Flags.
    $compare = FALSE;
    $wishlist = FALSE;
    $flag_exists = FALSE;
    $compare_header_class = 'hidden';
    $wishlist_header_class = 'hidden';
    if (\Drupal::moduleHandler()->moduleExists('flag')) {
      $flag_exists = TRUE;
      $flags = \Drupal::service('flag')->getAllFlags('commerce_product');
      $session_manager = \Drupal::service('session_manager');
      $session_id = $session_manager->getId();
      if (isset($flags['compare'])) {
        $compare = \Drupal::service('flag.count')
          ->getUserFlagFlaggingCount($flags['compare'], $user, $session_id);
        $compare_header_class = '';
      }
      if (isset($flags['wishlist'])) {
        $wishlist = \Drupal::service('flag.count')
          ->getUserFlagFlaggingCount($flags['wishlist'], $user, $session_id);
        $wishlist_header_class = '';
      }
    }

    // Cart.
    $cart_count = 0;
    $cart_block = '';
    $commerce_cart_exists = FALSE;
    if (\Drupal::moduleHandler()->moduleExists('commerce_cart')) {
      $commerce_cart_exists = TRUE;
      $cart_count = $this->getCartCount();

      $block = Block::load('cart');
      $cart_block = \Drupal::entityTypeManager()
        ->getViewBuilder('block')
        ->view($block);
    }

    // Search.
    $search = FALSE;
    $search_block = '';
    if (\Drupal::moduleHandler()->moduleExists('search')) {
      $search = TRUE;
      $form = \Drupal::formBuilder()->getForm(SearchBlockForm::class);
      $search_block = render($form);
    }

    // Phones.
    $enable_phones = theme_get_setting('phones') != '' ? TRUE : FALSE;
//    $phone = explode("\n", theme_get_setting('phones'));
//    $phone = is_array($phone) ? array_shift($phone) : '';

    // Navigation.
    $menu_name = $this->configuration['menu'];
    if ($one_page) {
      $rows = [];
      foreach ($layout['rows'] as $row_num => $row) {
        $region_name = Unicode::strtolower($row['name']);
        $rows[$row_num]['path'] = '#' . preg_replace('/[^\p{L}\p{N}]/u', '-', $row['name']);
        $rows[$row_num]['name'] = $row['name'];
        // @todo: This functionality ('dropdown_links' & 'menu_link_..') is already missing in the Layout Builder settings.
        if (isset($row['settings']['dropdown_links']) && $row['settings']['dropdown_links']) {
          $rows[$row_num]['dropdown_links'] = TRUE;
          foreach ($row['settings'] as $key => $value) {
            if (strpos($key, 'menu_link_url') !== FALSE) {
              $i = str_replace('menu_link_url_', '', $key);
              $rows[$row_num]['submenu']['path'] = $row['settings']['menu_link_url_' . $i];
              $rows[$row_num]['submenu']['class'] = strpos($row['settings']['menu_link_url_' . $i], '#') === 0 ? 'class="scroll"' : '';
              $rows[$row_num]['submenu']['menu_link'] = $row['settings']['menu_link_' . $i];
            }
          }
        }
        // $todo: This functionality ('hide_menu') is already missing in the Layout Builder settings.
        elseif (!isset($row['settings']['hide_menu']) || !$row['settings']['hide_menu']) {
          if (strpos($region_name, 'footer') === FALSE) {
            $rows[$row_num]['show_menu'] = TRUE;
          }
        }
      }
      $menu_theme = [
        '#theme' => 'progressive_cms_menu_one_page',
        '#rows' => $rows,
      ];
      $navigation = render($menu_theme);
    }
    elseif (\Drupal::moduleHandler()->moduleExists('tb_megamenu')) {
      $tb_megamenu_theme = [
        '#theme' => 'tb_megamenu',
        '#menu_name' => $menu_name,
        '#progressive_menu' => TRUE
      ];
      $navigation = render($tb_megamenu_theme);
    }
    else {
      $navigation = render_menu($menu_name, ['nav', 'navbar-nav', 'navbar-center']);
    }

    // Logo.
    $logo_url = theme_get_setting('logo.url');
    $logo_url = str_replace('.svg', '.png', $logo_url);

    $theme_array = [
      '#theme' => 'progressive_cms_menu',
      '#enable_top_box' => $enable_top_box,
      '#lang_name' => $lang_name,
      '#language_block' => $language_block,
      '#anonymous' => $user->isAnonymous(),
      '#enable_account_login' => theme_get_setting('account_login'),
      '#enable_comparelist' => theme_get_setting('comparelist'),
      '#enable_wishlist' => theme_get_setting('wishlist'),
      '#flag_exists' => $flag_exists,
      '#compare' => $compare,
      '#wishlist' => $wishlist,
      '#compare_header_class' => $compare_header_class,
      '#wishlist_header_class' => $wishlist_header_class,
      '#commerce_cart_exists' => $commerce_cart_exists,
      '#cart_count' => $cart_count,
      '#cart_checkout' => theme_get_setting('cart_checkout'),
      '#cart_block' => $cart_block,
      '#logo_url' => $logo_url,
      '#search' => $search,
      '#search_block' => $search_block,
      '#enable_phones' => $enable_phones,
      '#phone' => theme_get_setting('phones'),
      '#navigation' => $navigation,
    ];

    return [
      '#markup' => render($theme_array),
    ];
  }
}
