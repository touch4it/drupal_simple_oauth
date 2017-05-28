<?php

namespace Drupal\simple_oauth;

use Drupal\Component\Datetime\TimeInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\simple_oauth\Entity\Oauth2ClientInterface;

/**
 * Service in charge of deleting or expiring tokens that cannot be used anymore.
 */
class ExpiredCollector {

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $tokenStorage;

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $clientStorage;

  /**
   * @var \Drupal\Component\Datetime\TimeInterface
   */
  protected $dateTime;

  /**
   * ExpiredCollector constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Component\Datetime\TimeInterface $date_time
   *   The date time service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, TimeInterface $date_time) {
    $this->clientStorage = $entity_type_manager->getStorage('oauth2_client');
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
   * Collect all the tokens associated with the provided account.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The account.
   *
   * @return \Drupal\simple_oauth\Entity\Oauth2TokenInterface[]
   *   The tokens.
   */
  public function collectForAccount(AccountInterface $account) {
    $query = $this->tokenStorage->getQuery();
    $entity_ids = $query->condition('auth_user_id', $account->id())->execute();
    $results = $entity_ids ? $this->tokenStorage->loadMultiple($entity_ids) : [];
    $output = array_values($results);
    // Also collect the tokens of the clients that have this account as the
    // default user.
    $clients = array_values($this->clientStorage->loadByProperties([
      'user_id' => $account->id(),
    ]));
    // Append all the tokens for each of the clients having this account as the
    // default.
    return array_reduce($clients, function ($carry, $client) {
      return array_merge($carry, $this->collectForClient($client));
    }, $output);
  }

  /**
   * Collect all the tokens associated a particular client.
   *
   * @param \Drupal\simple_oauth\Entity\Oauth2ClientInterface $client
   *   The account.
   *
   * @return \Drupal\simple_oauth\Entity\Oauth2TokenInterface[]
   *   The tokens.
   */
  public function collectForClient(Oauth2ClientInterface $client) {
    $query = $this->tokenStorage->getQuery();
    $entity_ids = $query->condition('client', $client->id())->execute();
    $results = $entity_ids ? $this->tokenStorage->loadMultiple($entity_ids) : [];
    return array_values($results);
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
