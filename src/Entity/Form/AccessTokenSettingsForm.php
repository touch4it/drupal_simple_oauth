<?php

/**
 * @file
 * Contains \Drupal\token_auth\Entity\Form\AccessTokenSettingsForm.
 */

namespace Drupal\token_auth\Entity\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AccessTokenSettingsForm.
 *
 * @package Drupal\token_auth\Form
 *
 * @ingroup token_auth
 */
class AccessTokenSettingsForm extends FormBase {
  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'AccessToken_settings';
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
    // Empty implementation of the abstract submit class.
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
    $form['AccessToken_settings']['#markup'] = 'Settings form for Access Token entities. Manage field settings here.';
    return $form;
  }

}
