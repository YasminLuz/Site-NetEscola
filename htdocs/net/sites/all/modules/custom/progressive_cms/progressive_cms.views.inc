<?php
/**
 * Implements hook_views_data_alter().
 */
function progressive_cms_views_data_alter(array &$data) {
  $data['views']['progressive_cms_comment_count'] = [
    'title' => t('Progressive CMS Comment count'),
    'field' => [
      'title' => t('Progressive CMS Comment count'),
      'help' => t('Progressive CMS Comment count instead standard Comment count.'),
      'id' => 'progressive_cms_comment_count',
    ],
  ];
  $data['views']['progressive_cms_entity_path'] = [
    'title' => t('Progressive CMS Entity path'),
    'field' => [
      'title' => t('Progressive CMS Entity path'),
      'help' => t('Progressive CMS Entity path field.'),
      'id' => 'progressive_cms_entity_path',
    ],
  ];
  $data['views']['progressive_cms_single_image_url'] = [
    'title' => t('Progressive CMS Single Image Url'),
    'field' => [
      'title' => t('Progressive CMS Single Image Url'),
      'help' => t('Progressive CMS Single Image field url.'),
      'id' => 'progressive_cms_single_image_url',
    ],
  ];
  $data['views']['progressive_cms_image_url'] = [
    'title' => t('Progressive CMS Image Url'),
    'field' => [
      'title' => t('Progressive CMS Image Url'),
      'help' => t('Progressive CMS Image field url by File ID.'),
      'id' => 'progressive_cms_image_url',
    ],
  ];
  $data['views']['progressive_cms_user_access'] = [
    'title' => t('Progressive CMS User Access'),
    'field' => [
      'title' => t('Progressive CMS User Access'),
      'help' => t('Implements user_access().'),
      'id' => 'progressive_cms_user_access',
    ],
  ];
}
