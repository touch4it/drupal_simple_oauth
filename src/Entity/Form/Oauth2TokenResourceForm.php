<?php

namespace Drupal\simple_oauth\Entity\Form;

use Drupal\Core\Controller\ControllerResolverInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\user\PermissionHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Oauth2TokenResourceForm.
 *
 * @package Drupal\simple_oauth\Entity\Form
 */
class Oauth2TokenResourceForm extends EntityForm {

  /**
   * @var \Drupal\Core\Controller\ControllerResolverInterface
   */
  protected $controllerResolver;

  /**
   * Oauth2TokenResourceForm constructor.
   *
   * @param \Drupal\Core\Controller\ControllerResolverInterface $controller_resolver
   *   The controller resolver.
   */
  public function __construct(ControllerResolverInterface $controller_resolver) {
    $this->controllerResolver = $controller_resolver;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('controller_resolver'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /* @var \Drupal\simple_oauth\Oauth2TokenResourceInterface $oauth2_token_resource */
    $oauth2_token_resource = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $oauth2_token_resource->label(),
      '#description' => $this->t('Label for the Access Token Resource.'),
      '#required' => TRUE,
    );

    $form['description'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $oauth2_token_resource->getDescription(),
      '#description' => $this->t('Description for the Access Token Resource.'),
      '#required' => FALSE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $oauth2_token_resource->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\simple_oauth\Entity\Oauth2TokenResource::load',
      ),
      '#disabled' => !$oauth2_token_resource->isNew(),
    );

    $permissions_list = [];
    $permission_handler = new PermissionHandler($this->moduleHandler, $this->stringTranslation, $this->controllerResolver);
    foreach ($permission_handler->getPermissions() as $permission => $permission_info) {
      $permissions_list[$permission] = $permission_info['title'] . " ($permission)";
    }
    $form['permissions'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Permissions'),
      '#default_value' => $oauth2_token_resource->get('permissions') ?: [],
      '#description' => $this->t('A collection of permissions around a given feature. If a user is authenticated with a token that grants access to this scope, that user will ony be granted access (at most) to the permissions in this list. This will not grant access to any permissions forbidden to the user by their roles.'),
      '#options' => $permissions_list,
      '#required' => TRUE,
      '#attached' => [
        'library' => ['simple_oauth/drupal.oauth2_token'],
      ],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get an array of strings with the permissions names.
    $permissions = array_keys(array_filter($form_state->getValue('permissions')));
    $form_state->setValue('permissions', $permissions);
    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $oauth2_token_resource = $this->getEntity();
    $status = $oauth2_token_resource->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Access Token Resource.', [
          '%label' => $oauth2_token_resource->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Access Token Resource.', [
          '%label' => $oauth2_token_resource->label(),
        ]));
    }
    $form_state->setRedirectUrl($oauth2_token_resource->urlInfo('collection'));
  }

}
