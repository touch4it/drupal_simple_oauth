<?php

/**
 * @file
 * Contains \Drupal\simple_oauth\Entity\Form\AccessTokenForm.
 */

namespace Drupal\simple_oauth\Entity\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Language\Language;

/**
 * Form controller for Access Token edit forms.
 *
 * @ingroup simple_oauth
 */
class AccessTokenForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\simple_oauth\Entity\AccessToken */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;

    $form['langcode'] = array(
      '#title' => $this->t('Language'),
      '#type' => 'language_select',
      '#default_value' => $entity->getUntranslated()->language()->getId(),
      '#languages' => Language::STATE_ALL,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $status = $entity->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Access Token.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Access Token.', [
          '%label' => $entity->label(),
        ]));
    }
    $form_state->setRedirect('user.access_token.collection', ['user' => $entity->get('auth_user_id')->target_id]);
  }

}
