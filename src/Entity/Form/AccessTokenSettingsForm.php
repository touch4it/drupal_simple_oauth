<?php

/**
 * @file
 * Contains \Drupal\oauth2_token\Entity\Form\AccessTokenSettingsForm.
 */

namespace Drupal\oauth2_token\Entity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AccessTokenSettingsForm.
 *
 * @package Drupal\oauth2_token\Form
 *
 * @ingroup oauth2_token
 */
class AccessTokenSettingsForm extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'access_token_settings';
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
    $save = FALSE;
    $settings = $this->configFactory()->getEditable('oauth2_token.settings');
    if ($expiration = $form_state->getValue('expiration')) {
      $settings->set('expiration', $expiration);
      $save = TRUE;
    }
    if ($save) {
      $settings->save();
    }
  }

  /**
   * Defines the settings form for Access Token entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['expiration'] = [
      '#type' => 'number',
      '#title' => $this->t('Expiration time'),
      '#description' => $this->t('The default value, in seconds, to be used as expiration time when creating new tokens. This value may be overridden in the token generation form.'),
      '#default_value' => $this->config('oauth2_token.settings')->get('expiration'),
      '#validate'
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
    return $form;
  }

}
