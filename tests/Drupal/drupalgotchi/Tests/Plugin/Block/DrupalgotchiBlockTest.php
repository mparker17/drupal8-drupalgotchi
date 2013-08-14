<?php

namespace Drupal\drupalgotchi\Tests;

use Drupal\drupalgotchi\Plugin\Block\DrupalgotchiBlock;
use Drupal\Tests\UnitTestCase;

class DrupalgotchiBlockTest extends UnitTestCase {
  public static function getInfo() {
    return array(
      'name' => 'Drupalgotchi block test',
    );
  }

  public function setUp() {
    parent::setUp();
    include_once DRUPAL_ROOT . '/modules/drupalgotchi/lib/Drupal/drupalgotchi/Plugin/Block/DrupalgotchiBlock.php';
  }

  public function testDrupalgotchiBlock() {
    // Mock the Drupal installation state.
    $mock_state = $this->getMockBuilder('\Drupal\Core\KeyValueStore\KeyValueStoreInterface')
      ->disableOriginalConstructor()
      ->getMock();
    $mock_state->expects($this->once())
      ->method('get')
      ->with('drupalgotchi.attention')
      ->will($this->returnValue('10'));

    // Mock the module's config.
    $mock_config = $this->getMockBuilder('\Drupal\Core\Config\Config')
      ->disableOriginalConstructor()
      ->getMock();
    $mock_config->expects($this->once())
      ->method('get')
      ->with('name')
      ->will($this->returnValue('Bob'));

    // Now actually test.
    $block_under_test = new DrupalgotchiBlock(array(), 0, array('module' => 'drupalgotchi', 'id' => 'drupalgotchi_hello'), $mock_state, $mock_config);
    $render_array = $block_under_test->build();

    $this->assertEquals('drupalgotchi_status_block', $render_array['#theme']);
    $this->assertEquals(10, $render_array['#attention']);
    $this->assertEquals('Bob', $render_array['#name']);
  }
}
