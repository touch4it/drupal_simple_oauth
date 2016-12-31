<?php

namespace Drupal\simple_oauth;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class ExpiredCollector {

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $tokenStorage;

  /**
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $dateTime;

  /**
   * ExpiredCollector constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\Component\Datetime\TimeInterface $date_time
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, TimeInterface $date_time) {
    $this->tokenStorage = $entity_type_manager->getStorage('oauth2_token');
    $this->dateTime = $date_time;
  }

  /**
   * Collect all expired token ids.
   *
   * @return \Drupal\simple_oauth\Entity\Oauth2TokenInterface[]
   *   The expired tokens.
   */
  public function collect() {
    $query = $this->tokenStorage->getQuery();
    $query->condition('expire', $this->dateTime->getRequestTime(), '<');
    if (!$results = $query->execute()) {
      return [];
    }
    /** @var \Drupal\simple_oauth\Entity\Oauth2TokenInterface[] $tokens */
    $tokens = $this->tokenStorage->loadMultiple(array_values($results));

    return $tokens;
  }

  /**
   * Deletes multiple tokens based on ID.
   *
   * @param \Drupal\simple_oauth\Entity\Oauth2TokenInterface[] $tokens
   *   The token entity IDs.
   */
  public function deleteMultipleTokens(array $tokens = []) {
    $this->tokenStorage->delete($tokens);
  }

}
