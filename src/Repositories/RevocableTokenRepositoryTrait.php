<?php


namespace Drupal\simple_oauth\Repositories;

trait RevocableTokenRepositoryTrait {

  protected static $entity_type_id = '';
  protected static $entity_class = '';
  protected static $entity_interface = '';

  /**
   * {@inheritdoc}
   */
  public function persistNew($token_entity) {
    if (!is_a($token_entity, static::$entity_interface)){
      throw new \InvalidArgumentException(sprintf('%s does not implement %s.', get_class($token_entity), static::$entity_interface));
    }
    $values = \Drupal::service('simple_oauth.normalizer')->normalize($token_entity);
    $new_token = \Drupal::entityTypeManager()->getStorage(static::$entity_type_id)->create($values);
    $new_token->save();
  }

  /**
   * {@inheritdoc}
   */
  public function revoke($token_id) {
    $token = \Drupal::entityTypeManager()->getStorage(static::$entity_type_id)->load($token_id);
    $token->revoke();
    $token->save();
  }

  /**
   * {@inheritdoc}
   */
  public function isRevoked($token_id) {
    $token = \Drupal::entityTypeManager()->getStorage(static::$entity_type_id)->load($token_id);
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
