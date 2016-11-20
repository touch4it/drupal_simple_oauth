<?php


namespace Drupal\simple_oauth\Controller;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\TrustedRedirectResponse;
use Drupal\Core\Url;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Oauth2AuthorizeForm extends FormBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a Oauth2AuthorizeForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'simple_oauth_authorize_form';
  }

  /**
   * Form constructor.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   The form structure.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $request = $this->getRequest();
    $manager = $this->entityTypeManager;
    $form = [
      '#type' => 'container',
    ];

    $client_uuid = $request->get('client_id');
    $client_drupal_entities = $manager->getStorage('oauth2_client')->loadByProperties([
      'uuid' => $client_uuid,
    ]);
    if (empty($client_drupal_entities)) {
      throw OAuthServerException::invalidClient();
    }
    $client_drupal_entity = reset($client_drupal_entities);

    // Gather all the role ids.
    $scope_ids = array_merge(
      explode(' ', $request->get('scope')),
      array_map(function ($item) {
        return $item['target_id'];
      }, $client_drupal_entity->get('roles')->getValue())
    );
    $user_roles = $manager->getStorage('user_role')->loadMultiple($scope_ids);
    $form['client'] = $manager->getViewBuilder('oauth2_client')->view($client_drupal_entity);
    $client_drupal_entity->addCacheableDependency($form['client']);
    $form['scopes'] = [
      '#title' => $this->t('Permissions'),
      '#theme' => 'item_list',
      '#items' => [],
    ];
    foreach ($user_roles as $user_role) {
      $user_role->addCacheableDependency($form['scopes']);
      $form['scopes']['#items'][] = $user_role->label();
    }

    $form['redirect_uri'] = [
      '#type' => 'hidden',
      '#value' => $request->get('redirect_uri') ?
        $request->get('redirect_uri') :
        $client_drupal_entity->get('redirect')->value,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Grant'),
    ];

    return $form;
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $response = TrustedRedirectResponse::create($form_state->getValue('redirect_uri'));
    $form_state->setResponse($response);
  }


}
