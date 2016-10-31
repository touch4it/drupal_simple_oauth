<?php


namespace Drupal\simple_oauth\Repositories;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\simple_oauth\Normalizer\TokenEntityNormalizerInterface;

trait RevocableTokenRepositoryTrait {

  protected static $entity_type_id = '';
  protected static $entity_class = '';
  protected static $entity_interface = '';

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * @var \Drupal\simple_oauth\Normalizer\TokenEntityNormalizerInterface
   */
  protected $normalizer;

  /**
   * Construct a revocable token.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\simple_oauth\Normalizer\TokenEntityNormalizerInterface $normalizer
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, TokenEntityNormalizerInterface $normalizer) {
    $this->storage = $entity_type_manager->getStorage(static::$entity_type_id);
    $this->normalizer = $normalizer;
  }

  /**
   * {@inheritdoc}
   */
  public function persistNew($token_entity) {
    if (!is_a($token_entity, static::$entity_interface)){
      throw new \InvalidArgumentException(sprintf('%s does not implement %s.', get_class($token_entity), static::$entity_interface));
    }
    $values = $this->normalizer->normalize($token_entity);
    $new_token = $this->storage->create($values);
    $new_token->save();
  }

  /**
   * {@inheritdoc}
   */
  public function revoke($token_id) {
    $token = $this->storage->load($token_id);
    $token->revoke();
    $token->save();
  }

  /**
   * {@inheritdoc}
   */
  public function isRevoked($token_id) {
    $token = $this->storage->load($token_id);
    return $token->isRevoked();
  }

  /**
   * {@inheritdoc}
   */
  public function getNew() {
    $class = static::$entity_class;
    return new $class();
  }

}
