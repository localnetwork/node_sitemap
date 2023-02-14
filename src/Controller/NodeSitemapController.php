<?php

namespace Drupal\node_sitemap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;



class NodeSitemapController extends ControllerBase {
    

    public function sitemapXml() {
        $config = \Drupal::config('node_sitemap.content_type_config');
        $data = $config->get();
        $content_types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
        $pager_limit = $data['pager_limit'];
        foreach($content_types as $type) {
            if($data[$type->id()] === 1) {
                $types[$type->id()] = $type->id();
            }
            
        }

//        $nodes = \Drupal::entityTypeManager()
//      ->getStorage('node')
//      ->loadByProperties(['type' => $types, 'status' => 1]);
        $query = \Drupal::entityQuery('node')
          ->condition('type', $types, 'IN')
          ->condition('status', 1)
          ->sort('created', 'DESC')
          ->pager($pager_limit);
        $nids = $query->execute();
        $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);
        $build = [
            '#theme' => 'sitemap_xml',
            '#nodes' => $nodes,
        ]; 
        $response = new Response(render($build));
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }
}
