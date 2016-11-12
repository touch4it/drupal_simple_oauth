<?php


namespace Drupal\simple_oauth\Repositories;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\Serializer\Serializer;

trait RevocableTokenRepositoryTrait {

  protected static $entity_type_id = 'oauth2_token';

  /**
   * @var \Drupal\Core\Entity\EntityStorageInterface
   */
  protected $storage;

  /**
   * @var \Symfony\Component\Serializer\Serializer
   */
  protected $serializer;

  /**
   * Construct a revocable token.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   * @param \Drupal\simple_oauth\Normalizer\TokenEntityNormalizerInterface $normalizer
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, Serializer $serializer) {
    $this->storage = $entity_type_manager->getStorage(static::$entity_type_id);
    $this->serializer = $serializer;
  }

  /**
   * {@inheritdoc}
   */
  public function persistNew($token_entity) {
    if (!is_a($token_entity, static::$entity_interface)){
      throw new \InvalidArgumentException(sprintf('%s does not implement %s.', get_class($token_entity), static::$entity_interface));
    }
    $values = $this->serializer->normalize($token_entity);
    $values['bundle'] = static::$bundle_id;
    $new_token = $this->storage->create($values);
    $new_token->save();
  }

  /**
   * {@inheritdoc}
   */
  public function revoke($token_id) {
    if (!$tokens = $this->storage->loadByProperties(['value' => $token_id])) {
      return;
    }
    /** @var \Drupal\simple_oauth\Entity\Oauth2TokenInterface $token */
    $token = reset($tokens)->isRevoked();
    $token->revoke();
    $token->save();
  }

  /**
   * {@inheritdoc}
   */
  public function isRevoked($token_id) {
    if (!$tokens = $this->storage->loadByProperties(['value' => $token_id])) {
      return TRUE;
    }
    /** @var \Drupal\simple_oauth\Entity\Oauth2TokenInterface $token */
    $token = reset($tokens);

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
