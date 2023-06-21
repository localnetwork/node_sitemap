<?php

namespace Drupal\node_sitemap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\Core\Database\Database;


class NodeSitemapController extends ControllerBase {
    

    public function sitemapXml() {
        $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();

        $config = \Drupal::config('node_sitemap.content_type_config');
        $data = $config->get();
        $content_types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
        $pager_limit = $data['pager_limit'];
        $translated_nodes = [];

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

        foreach ($nodes as $node) {
          $translations = $node->getTranslationLanguages();
          if (isset($translations[$current_language])) {
            $url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()], ['absolute' => TRUE]);



            $translated_nodes[] = array(
                'nodeinfo' => $node,
                'id' => $node->id(),
                'priority' => $data['priority_'. $node->type->target_id], 
            );
          }
        }

        $build = [
            '#theme' => 'sitemap_xml',
            '#nodes' => $translated_nodes,
        ];

        // return $build;  
        $response = new Response(render($build));
        $response->headers->set('Content-Type', 'application/xml');
        return $response;
    }
}
