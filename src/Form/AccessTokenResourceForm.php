<?php

/**
 * @file
 * Contains \Drupal\token_auth\Form\AccessTokenResourceForm.
 */

namespace Drupal\token_auth\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AccessTokenResourceForm.
 *
 * @package Drupal\token_auth\Form
 */
class AccessTokenResourceForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /* @var \Drupal\token_auth\AccessTokenResourceInterface $access_token_resource */
    $access_token_resource = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $access_token_resource->label(),
      '#description' => $this->t("Label for the Access Token Resource."),
      '#required' => TRUE,
    );

    $form['description'] = array(
      '#type' => 'textarea',
      '#title' => $this->t('Description'),
      '#default_value' => $access_token_resource->getDescription(),
      '#description' => $this->t("Description for the Access Token Resource."),
      '#required' => FALSE,
    );

    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $access_token_resource->id(),
      '#machine_name' => array(
        'exists' => '\Drupal\token_auth\Entity\AccessTokenResource::load',
      ),
      '#disabled' => !$access_token_resource->isNew(),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $access_token_resource = $this->entity;
    $status = $access_token_resource->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Access Token Resource.', [
          '%label' => $access_token_resource->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Access Token Resource.', [
          '%label' => $access_token_resource->label(),
        ]));
    }
    $form_state->setRedirectUrl($access_token_resource->urlInfo('collection'));
  }

}
