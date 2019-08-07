<?php
/**
 * @file
 * Contains \Drupal\progressive_cms\Ajax\ProgressiveCmsBlogTimelineCommand.
 */

namespace Drupal\progressive_cms\Ajax;

use Drupal\Core\Ajax\CommandInterface;

class ProgressiveCmsBlogTimelineCommand implements CommandInterface {

  /**
   * @var
   */
  protected $selector;

  /**
   * @var
   */
  protected $data;

  /**
   * ProgressiveCmsBlogTimelineCommand constructor.
   * @param $selector
   * @param $data
   */
  public function __construct($selector, $data) {
    $this->selector = $selector;
    $this->data = $data;
  }

  /**
   * Implements Drupal\Core\Ajax\CommandInterface:render().
   * @return array
   */
  public function render() {
    return [
      'command' => 'progressive_cms_blog_timeline',
      'selector' => $this->selector,
      'data' => $this->data,
    ];
  }
}
