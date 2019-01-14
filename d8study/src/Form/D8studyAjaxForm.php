<?php

/**
 * @file
 * Contains \Drupal\d8study\src\Form.
 */

namespace Drupal\d8study\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\node\Entity\Node;

class D8studyAjaxForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'd8study';
  }

  /**
   * {@inheritdoc}
   */

  public function buildForm(array $form, FormStateInterface $form_state) {
      $form['system_messages'] = [
        '#markup' => '<div id="form-system-messages"></div>',
        '#weight' => -100,
      ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Name'),
      '#required' => TRUE,
    ];

    $form['email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email'),
      '#required' => TRUE,
    ];

    $form['telephone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Telephone'),
      '#required' => TRUE,
    ];

    $form['messages'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Message'),
      '#size' => 120,
      '#maxlength' => 255,
      '#required' => TRUE,
    ];

    $form['attach'] = [
      '#type' => 'managed_file',
      '#upload_location' => 'public://',
      '#title' => $this->t('Attach a file'),
      '#description' => t('Upload a file, allowed extensions: txt, pdf, xls'),
      "#upload_validators"  => array("file_validate_extensions" => array("txt pdf xls"))
    ];

    $form['submit'] = [
      '#type' => 'submit',
      '#name' => 'submit',
      '#value' => 'Submit this form',
      '#ajax' => [
        'callback' => '::ajaxSubmitCallback',
        'event' => 'click',
        'progress' => [
          'type' => 'throbber',
        ],
      ],
    ];

    return $form;

  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    drupal_set_message('Thanks for your submit! ');
  }

  public function ajaxSubmitCallback(array &$form, FormStateInterface $form_state) {

    $data = $form_state->getValues();
    $ajax_response = new AjaxResponse();
    $node_data = [
      'type' => 'contact',
      'title' => 'd8study',
      'uid' => '1',
      'field_name' => $data['name'],
      'field_email' => $data['email'],
      'field_telephone' => $data['telephone'],
      'field_message' => $data['messages'],
    ];

    if (isset($data['attach']) && !empty($data['attach'])) {
      $node_data['field_attach_file'] = $data['attach'];
    }
    // ddl($node_data);

    $node = Node :: create($node_data);
    $node->save();

    $message = [
      '#theme' => 'status_messages',
      '#message_list' => drupal_get_messages(),
      '#status_headings' => [
        'status' => t('Status message'),
        'error' => t('Error message'),
        'warning' => t('Warning message'),
      ],
    ];
    $messages = \Drupal::service('renderer')->render($message);
    $ajax_response->addCommand(new HtmlCommand('#form-system-messages', $messages));
    return $ajax_response;
  }
}
