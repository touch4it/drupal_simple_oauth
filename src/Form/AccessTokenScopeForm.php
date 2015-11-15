<?php

/**
 * @file
 * Contains \Drupal\token_auth\Form\AccessTokenScopeForm.
 */

namespace Drupal\token_auth\Form;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AccessTokenScopeForm.
 *
 * @package Drupal\token_auth\Form
 */
class AccessTokenScopeForm extends EntityForm {
  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $access_token_scope = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $access_token_scope->label(),
      '#description' => $this->t("Label for the Access Token Scope."),
      '#required' => TRUE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $access_token_scope->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\token_auth\Entity\AccessTokenScope::load',
      ),
      '#disabled' => !$access_token_scope->isNew(),
    );

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $access_token_scope = $this->entity;
    $status = $access_token_scope->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Access Token Scope.', [
          '%label' => $access_token_scope->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Access Token Scope.', [
          '%label' => $access_token_scope->label(),
        ]));
    }
    $form_state->setRedirectUrl($access_token_scope->urlInfo('collection'));
  }

}
