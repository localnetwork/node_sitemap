<?php

namespace Drupal\node_sitemap\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class NodeSitemapContentTypeConfigForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'node_sitemap_content_type_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'node_sitemap.content_type_config',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('node_sitemap.content_type_config');
    $data = $config->get();
    $content_types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    $values = $form_state->getValues();
    
    $form['pager_limit'] = [
        '#type' => 'number',
        '#title' => 'Items per Page',
        '#default_value' => $config->get('pager_limit'),
        // '#max' => 2000,
    ];
    $form['content_types'] = [
        '#type' => 'fieldset',
        '#title' => t('Content Types Index'),
        '#collapsible' => TRUE,
    ];

    $form['priorities'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Content Type Priorities'),
      '#collapsible' => TRUE,
    ];

    foreach ($content_types as $content_type) {
      // Form for indexes.
      $form['content_types'][$content_type->id()] = [
        '#type' => 'checkbox',
        '#title' => $content_type->label(),
        '#default_value' => $config->get($content_type->id()),
      ];

      // Form for priorities.
      $form['priorities']['priority_'.$content_type->id()] = [
        '#type' => 'number',
        '#title' => $content_type->label(),
        '#min' => 0,
        '#max' => 1,
        '#step' => 0.1,
        '#default_value' => $data['priority_'. $content_type->id()] ? $data['priority_'. $content_type->id()] : 0.5,
      ]; 
    }

    
    
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config('node_sitemap.content_type_config');
    $values = $form_state->getValues();


    foreach ($values as $content_type_id => $value) {
      $config->set($content_type_id, $value);
    }


    $config->set('pager_limit', $form_state->getValue('pager_limit'));
    $config->save();
    
  }

}
