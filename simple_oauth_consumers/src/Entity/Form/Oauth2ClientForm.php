<?php

namespace Drupal\simple_oauth_consumers\Entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Url;
use Drupal\user\RoleInterface;

/**
 * Form controller for Client edit forms.
 *
 * @ingroup simple_oauth
 */
class Oauth2ClientForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\simple_oauth_consumers\Entity\Oauth2ClientInterface */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    $form['langcode'] = [
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $entity->getUntranslated()->language()->getId(),
      '#languages' => Language::STATE_ALL,
      '#weight' => -5,
    ];

    // Remove automatic roles and administrator roles.
    unset($form['roles']['widget']['#options'][RoleInterface::ANONYMOUS_ID]);
    unset($form['roles']['widget']['#options'][RoleInterface::AUTHENTICATED_ID]);
    // Get the admin role.
    $admin_roles = $this->entityTypeManager->getStorage('user_role')->getQuery()
      ->condition('is_admin', TRUE)
      ->execute();
    $default_value = reset($admin_roles);
    unset($form['roles']['widget']['#options'][$default_value]);
    $recommendation_text = $this->t(
      'Create a <a href=":url">role</a> for every logical group of permissions you want to make available to a consumer.',
      [':url' => Url::fromRoute('entity.user_role.collection')->toString()]
    );
    $form['roles']['widget']['#description'] .= '<br>' . $recommendation_text;
    if (empty($form['roles']['widget']['#options'])) {
      drupal_set_message($recommendation_text, 'error');
      $form['actions']['#disabled'] = TRUE;
    }

    $description = $this->t(
      'Use this field to create a hash of the secret key. This module will never store your client key, only a hash of it. Current hash: "%hash".',
      ['%hash' => $entity->getSecret()]
    );
    $form['new_secret'] = [
      '#type' => 'password',
      '#title' => $this->t('New Secret'),
      '#description' => $description,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    // If the secret was changed, then digest it before saving. If not, then
    // leave it alone.
    if ($new_secret = $form_state->getValue('new_secret')) {
      $this->getEntity()->setSecret($new_secret);
    }


  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $status = $this->entity->save();
    $label = $this->entity->label();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Client.', [
          '%label' => $label,
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Client.', [
          '%label' => $label,
        ]));
    }
    $form_state->setRedirect('entity.oauth2_client.collection');
  }

}
