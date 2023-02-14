<?php

namespace Drupal\node_sitemap\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class NodeSitemapConfigForm extends ConfigFormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'node_sitemap_config_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'node_sitemap.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('node_sitemap.settings');

    $form['include_node_content_type'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include node content type in sitemap'),
      '#default_value' => $config->get('include_node_content_type'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('node_sitemap.settings')
      ->set('include_node_content_type', $form_state->getValue('include_node_content_type'))
      ->save();
  }
}
