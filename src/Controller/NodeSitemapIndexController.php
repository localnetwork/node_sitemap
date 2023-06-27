<?php

namespace Drupal\node_sitemap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Url;

class NodeSitemapIndexController extends ControllerBase {

  public function NodeSitemapIndexXml() {
    $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $config = \Drupal::config('node_sitemap.content_type_config');
    $data = $config->get();
    
    $pager_limit = $data['pager_limit'];

    $content_types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();

    $host = \Drupal::request()->getSchemeAndHttpHost();

    foreach($content_types as $type) {
        if($data[$type->id()] === 1) {
            $types[$type->id()] = $type->id();
        }
        
    }

    // Determine the total number of pages in the sitemap index.
    $totalPages = ceil($this->getTotalUrlCount() / $pager_limit);


    // Generate URLs for each page of the sitemap index.
    for ($page = 0; $page < $totalPages; $page++) {
      $url = Url::fromRoute('node_sitemap.node_sitemap_xml', ['page' => $page], ['absolute' => TRUE]);


      $host = \Drupal::request()->getSchemeAndHttpHost();

      $sitemapUrls[] = $url->toString();
    }

    $build = [
        '#theme' => 'node_sitemap_index',
        '#nodes' => $sitemapUrls,
    ]; 

    // return $build;  
    $response = new Response(render($build));
    $response->headers->set('Content-Type', 'application/xml'); 
    return $response;
  }

  /**
   * Get the total number of URLs for the sitemap.
   *
   * @return int
   *   The total number of URLs.
   */
  private function getTotalUrlCount() {
    $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();
    $config = \Drupal::config('node_sitemap.content_type_config');
    $data = $config->get();
    $content_types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();

    foreach($content_types as $type) {
      if($data[$type->id()] === 1) {
          $types[$type->id()] = $type->id();
      }
    }

    $query = \Drupal::entityQuery('node')
      ->sort('changed', 'DESC')
      ->condition('status', 1)
      ->condition('type', $types, 'IN')
      ->condition('langcode', $current_language);
    return $query->count()->execute();
  }

}