<?php

namespace Drupal\simple_oauth\Entity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class Oauth2TokenSettingsForm.
 *
 * @package Drupal\simple_oauth\Form
 *
 * @ingroup simple_oauth
 */
class Oauth2TokenSettingsForm extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'oauth2_token_settings';
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
    $settings = $this->configFactory()->getEditable('simple_oauth.settings');
    $settings->set('expiration', $form_state->getValue('expiration'));
    $settings->set('use_implicit', $form_state->getValue('use_implicit'));
    $settings->set('public_key', $form_state->getValue('public_key'));
    $settings->set('private_key', $form_state->getValue('private_key'));
    $settings->save();
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
      '#default_value' => $this->config('simple_oauth.settings')->get('expiration'),
    ];
    $form['use_implicit'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable the implicit grant?'),
      '#description' => $this->t('The implicit grant has the potential to be used in an insecure way. Only enable this if you understand the risks. See https://tools.ietf.org/html/rfc6819#section-4.4.2 for more information.'),
      '#default_value' => $this->config('simple_oauth.settings')->get('use_implicit'),
    ];
    $form['public_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Public Key'),
      '#description' => $this->t('The path to the public key file.'),
      '#default_value' => $this->config('simple_oauth.settings')->get('public_key'),
      '#element_validate' => ['::validateExistingFile'],
      '#required' => TRUE,
    ];
    $form['private_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Private Key'),
      '#description' => $this->t('The path to the private key file.'),
      '#default_value' => $this->config('simple_oauth.settings')->get('private_key'),
      '#element_validate' => ['::validateExistingFile'],
      '#required' => TRUE,
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    ];
    return $form;
  }

  /**
   * Validates if the file exists.
   *
   * @param array $element
   *   The element being processed.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   */
  public function validateExistingFile(&$element, FormStateInterface $form_state, &$complete_form) {
    if (!empty($element['#value'])) {
      $path = $element['#value'];
      if (!file_exists($path)) {
        $form_state->setError($element, $this->t('The %field file does not exist.', ['%field' => $element['#title']]));
      }
    }
  }

}
