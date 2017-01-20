<?php

namespace Drupal\Tests\simple_oauth\Unit;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\simple_oauth\Entity\Oauth2Token;
use Drupal\simple_oauth\ExpiredCollector;
use Drupal\Tests\UnitTestCase;
use Prophecy\Argument;

/**
 * @coversDefaultClass \Drupal\simple_oauth\ExpiredCollector
 * @group simple_oauth
 */
class EntityCollectorTest extends UnitTestCase {

  /**
   * @covers ::collect
   */
  public function testCollect() {
    list($expired_collector, $query,) = $this->buildProphecies();
    $query->condition('expire', 42, '<')->shouldBeCalledTimes(1);
    $this->assertEquals([1 => 1, 52 => 52], array_map(function ($entity) {
      return $entity->id();
    }, $expired_collector->collect()));
  }

  /**
   * @covers ::collect
   */
  public function testDeleteMultipleTokens() {
    list($expired_collector,, $storage) = $this->buildProphecies();
    $storage->delete(['foo'])->shouldBeCalledTimes(1);
    $expired_collector->deleteMultipleTokens(['foo']);
  }

  protected function buildProphecies() {
    $entity_type_manager = $this->prophesize(EntityTypeManagerInterface::class);
    $storage = $this->prophesize(EntityStorageInterface::class);
    $query = $this->prophesize(QueryInterface::class);
    $query->execute()->willReturn([1 => '1', 52 => '52']);
    $storage->getQuery()->willReturn($query->reveal());
    $entity1 = $this->prophesize(Oauth2Token::class);
    $entity1->id()->willReturn(1);
    $entity52 = $this->prophesize(Oauth2Token::class);
    $entity52->id()->willReturn(52);
    $storage->loadMultiple(['1', '52'])->willReturn([
      1 => $entity1->reveal(),
      52 => $entity52->reveal(),
    ]);
    $entity_type_manager->getStorage('oauth2_token')->willReturn($storage->reveal());
    $date_time = $this->prophesize(TimeInterface::class);
    $date_time->getRequestTime()->willReturn(42);
    $expired_collector = new ExpiredCollector($entity_type_manager->reveal(), $date_time->reveal());

    return [
      $expired_collector,
      $query,
      $storage,
    ];
  }

}
