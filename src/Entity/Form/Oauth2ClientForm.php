<?php

namespace Drupal\simple_oauth\Entity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Password\PasswordInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\user\UserInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Oauth2ClientForm extends EntityForm  {

  /**
   * Password service.
   *
   * @var \Drupal\Core\Password\PasswordInterface
   */
  protected $password;

  /**
   * Oauth2ClientForm constructor.
   */
  public function __construct(PasswordInterface $password) {
    $this->password = $password;
  }


  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('password'));
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);
    $entity = $this->getEntity();

    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $entity->label(),
      '#description' => $this->t('Label for the Access Token Resource.'),
      '#required' => TRUE,
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#maxlength' => 64,
      '#description' => $this->t('A unique name for this block instance. Must be alpha-numeric and underscore separated.'),
      '#default_value' => $entity->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\simple_oauth\Entity\Oauth2TokenResource::load',
        'replace_pattern' => '[^a-z0-9_.]+',
        'source' => array('label'),
      ),
      '#required' => TRUE,
      '#disabled' => !$entity->isNew(),
    );
    $description = $this->t('Use this field to create a hash of the secret key. This module will never store your client key, only a hash of it. Current hash: "%hash".', [
      '%hash' => $entity->get('secret'),
    ]);
    $form['newSecret'] = array(
      '#type' => 'password',
      '#title' => $this->t('New Secret'),
      '#description' => $description,
    );
    $form['redirectUri'] = array(
      '#type' => 'url',
      '#title' => $this->t('Redirect URI'),
      '#default_value' => $entity->get('redirectUri'),
      '#description' => $this->t('The URI to redirect upon success.'),
    );
    $form['isConfidential'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Is confidential?'),
      '#default_value' => $entity->get('isConfidential'),
      '#description' => $this->t('Indicates if the client secret needs to be checked.'),
    );

    // Load all the Role entities.
    $role_storage = $this->entityTypeManager->getStorage('user_role');
    $role_ids = $role_storage
      ->getQuery()
      ->condition('id', [AccountInterface::ANONYMOUS_ROLE, AccountInterface::AUTHENTICATED_ROLE], 'NOT IN')
      ->execute();
    $roles = $role_storage->loadMultiple($role_ids);
    $options = array_map(function ($role) {
      return $role->label();
    }, $roles);
    $form['roles'] = array(
      '#type' => 'checkboxes',
      '#title' => $this->t('Roles'),
      '#options' => $options,
      '#default_value' => $entity->get('roles') ?: [],
      '#description' => $this->t('When no user is identified from the client,
      requests for this client will be considered as using these roles. It is
      highly recommended to create a new role containing only the permissions
      necessary to operate under those conditions.'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // If the secret was changed, then digest it before saving. If not, then
    // leave it alone.
    if ($newSecret = $form_state->getValue('newSecret')) {
      $form_state->setValue('secret', $this->password->hash($newSecret));
    }
    else {
      $secret = $this->getEntity()->get('secret');
      $form_state->setValue('secret', $secret);
    }

    parent::submitForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->getEntity();
    $status = $entity->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label OAuth Client.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label OAuth Client.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirectUrl($entity->toUrl('collection'));
  }

}
