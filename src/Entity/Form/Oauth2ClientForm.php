<?php

namespace Drupal\simple_oauth\Entity\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Password\PasswordInterface;
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
    $form['new_secret'] = array(
      '#type' => 'password',
      '#title' => $this->t('New Secret'),
      '#description' => $description,
    );
    $form['redirect_uri'] = array(
      '#type' => 'url',
      '#title' => $this->t('Redirect URI'),
      '#default_value' => $entity->get('redirect_uri'),
      '#description' => $this->t('The URI to redirect upon success.'),
    );
    $form['is_confidential'] = array(
      '#type' => 'checkbox',
      '#title' => $this->t('Is confidential?'),
      '#default_value' => $entity->get('is_confidential'),
      '#description' => $this->t('Indicates if the client secret needs to be checked.'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // If the secret was changed, then digest it before saving. If not, then
    // leave it alone.
    if ($new_secret = $form_state->getValue('new_secret')) {
      $form_state->setValue('secret', $this->password->hash($new_secret));
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
