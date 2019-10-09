<?php

namespace Drupal\axelerant_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Database\Driver\mysql\Connection;
use Drupal\Core\Config\ConfigFactoryInterface;

/**
 * Class CustomUrlController.
 */
class JsonResponse extends ControllerBase {

    /**
     * Drupal\Core\Entity\EntityTypeManagerInterface definition.
     *
     * @var \Drupal\Core\Entity\EntityTypeManagerInterface
     */
    protected $entityTypeManager;

    /**
     * Drupal\Core\Database\Driver\mysql\Connection definition.
     *
     * @var \Drupal\Core\Database\Driver\mysql\Connection
     */
    protected $database;

    /**
     * Drupal\Core\Config\ConfigFactoryInterface definition.
     *
     * @var Drupal\Core\Config\ConfigFactoryInterface
     */
    protected $config;

    /**
     * Constructs a new CustomUrlController object.
     */
    public function __construct(EntityTypeManagerInterface $entity_type_manager, Connection $database, ConfigFactoryInterface $config) {
        $this->entityTypeManager = $entity_type_manager;
        $this->database = $database;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
        return new static(
                $container->get('entity_type.manager'), $container->get('database'),$container->get('config.factory')
        );
    }

    /**
     * Generateurl.
     *
     * @return string
     *   Return Hello string.
     */
    public function generate($siteapikey, $nid) {
        $key = $this->config->getEditable('system.site')->get('siteapikey');
        $node = $this->entityTypeManager->getStorage('node')->load($nid);
        if ($siteapikey == $key && $node->getType() == 'page') {
            $serializer = \Drupal::service('serializer');
            $data = $serializer->serialize($node, 'json', ['plugin_id' => 'entity']);
            
            return [
                '#type' => 'markup',
                '#markup' => $this->t("$data")
            ];
        } else {
            throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
        }
    }

}
