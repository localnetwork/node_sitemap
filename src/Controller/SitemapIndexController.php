<?php

namespace Drupal\node_sitemap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Url;

class SitemapIndexController extends ControllerBase {

  public function sitemapIndexXml() {
    $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $config = \Drupal::config('node_sitemap.content_type_config');
    $data = $config->get();
    $content_types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    $pager_limit = $data['pager_limit'];

    $host = \Drupal::request()->getSchemeAndHttpHost();

    // $sitemapUrls = [
    //   $host . '/node/sitemaps.xml?page=1',
    //   $host . '/node/sitemaps.xml?page=2',
    //   $host . '/node/sitemaps.xml?page=3',
    //   $host . '/node/sitemaps.xml?page=4',
    // ];

    // Determine the total number of pages in the sitemap index.
    $totalPages = ceil($this->getTotalUrlCount() / $pager_limit);


    // Generate URLs for each page of the sitemap index.
    for ($page = 0; $page < $totalPages; $page++) {
      $url = Url::fromRoute('node_sitemap.sitemap_xml', ['page' => $page], ['absolute' => TRUE]);


      $host = \Drupal::request()->getSchemeAndHttpHost();

      $sitemapUrls[] = $url->toString();
    }

    $build = [
        '#theme' => 'sitemap_index',
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
    $query = \Drupal::entityQuery('node')
      ->condition('status', 1);
    return $query->count()->execute();
  }

}