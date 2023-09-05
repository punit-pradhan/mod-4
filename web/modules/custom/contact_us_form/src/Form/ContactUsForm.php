<?php

namespace Drupal\contact_us_form\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the Contact Us Form.
 */
class ContactUsForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId()
  {
    return 'contact_us_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Define form fields
    $form['full_name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Full Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'email',
      '#title' => $this->t('Email Address'),
      '#required' => TRUE,
    ];

    $form['phone'] = [
      '#type' => 'tel',
      '#title' => $this->t('Phone'),
    ];

    $form['messageS'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
      '#maxlength' => 250,
      '#required' => TRUE,
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Get form values
    $values = $form_state->getValues();

    // Send notification to admin
    $mailManager = \Drupal::service('plugin.manager.mail');
    $module = 'Contact_US';
    $key = 'admin_notification';
    $to = 'punitpradhan01@gmail.com';
    $params['message'] = "A new query has been sent:\n\n" .
    "Full Name: " . $values['full_name'] . "\n" .
    "Email: " . $values['email'] . "\n" .
    "Message:\n" . $values['messageS'];
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = true;

    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] !== true) {
      \Drupal::messenger()->addMessage($this->t('Problem in sending message.'));
    } else {
      \Drupal::messenger()->addMessage($this->t('Your message has been sent.'));
    }

    // Send thank you email to user
    $key = 'user_thank_you';
    $to = $values['email'];
    $params['message'] = 'Thank you for your submission. We will get back to you soon.';
    $result = $mailManager->mail($module, $key, $to, $langcode, $params, NULL, $send);
    if ($result['result'] !== true) {
      \Drupal::messenger()->addMessage($this->t('Problem in sending message.'));
    } else {
      \Drupal::messenger()->addMessage($this->t('Your message has been sent.'));
    }
  }
}
