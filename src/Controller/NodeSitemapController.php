<?php

namespace Drupal\node_sitemap\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\Response;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\Core\Database\Database;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class NodeSitemapController extends ControllerBase {

  /**
   * The configuration factory.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a NodeSitemapController object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The configuration factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  public function sitemapXml(Request $request) {
    $current_language = \Drupal::languageManager()->getCurrentLanguage()->getId();

    $config = $this->configFactory->get('node_sitemap.content_type_config');
    if ($config === NULL) {
      throw new \Exception('Unable to load node_sitemap.content_type_config configuration.');
    }
    $data = $config->get();
    $content_types = \Drupal::entityTypeManager()->getStorage('node_type')->loadMultiple();
    $pager_limit = $data['pager_limit'];
    $translated_nodes = [];

    $typeFilter = $request->query->get('type'); // Get the 'type' parameter from the URL query

    foreach ($content_types as $type) {
      if ($typeFilter && $typeFilter !== $type->id()) {
        continue; // Skip this content type if it doesn't match the filter
      }
      if ($data[$type->id()] === 1) {
        $types[$type->id()] = $type->id();
      }
    }

    $query = \Drupal::entityQuery('node')
      ->condition('type', $types, 'IN')
      ->condition('status', 1)
      ->sort('changed', 'DESC')
      ->pager($pager_limit);
    $nids = $query->execute();
    $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadMultiple($nids);

    foreach ($nodes as $node) {
      $translations = $node->getTranslationLanguages();
      if (isset($translations[$current_language])) {
        $url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()], ['absolute' => TRUE]);

        $translated_nodes[] = [
          'nodeinfo' => $node,
          'id' => $node->id(),
          'priority' => $data['priority_'. $node->type->target_id], 
        ];
      }
    }

    $build = [
      '#theme' => 'sitemap_xml',
      '#nodes' => $translated_nodes,
    ];

    $response = new Response(render($build));
    $response->headers->set('Content-Type', 'application/xml');
    return $response;
  }
}