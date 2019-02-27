<?php

namespace Drupal\stacks\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class WidgetEntityAddController.
 *
 * @package Drupal\stacks\Controller
 */
class WidgetEntityAddController extends ControllerBase {
    public function __construct(EntityStorageInterface $storage, EntityStorageInterface $type_storage) {
      $this->storage = $storage;
      $this->typeStorage = $type_storage;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container) {
      /** @var EntityTypeManagerInterface $entity_type_manager */
      $entity_type_manager = $container->get('entity_type.manager');
      return new static(
        $entity_type_manager->getStorage('widget_entity'),
        $entity_type_manager->getStorage('widget_entity_type')
      );
    }

    /**
     * Displays add links for available bundles/types for entity widget_entity .
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *   The current request object.
     *
     * @return array
     *   A render array for a list of the widget_entity bundles/types that can be added or
     *   if there is only one type/bunlde defined for the site, the function returns the add page for that bundle/type.
     */
    public function add(Request $request) {
      $types = $this->typeStorage->loadMultiple();
      if ($types && count($types) == 1) {
        $type = reset($types);
        return $this->addForm($type, $request);
      }
      if (count($types) === 0) {
        return [
          '#markup' => t('You have not created any %bundle types yet. @link to add a new type.', [
            '%bundle' => 'Widget Entity',
            '@link' => $this->l(t('Go to the type creation page'), Url::fromRoute('entity.widget_entity_type.add_form')),
          ]),
        ];
      }
      return ['#theme' => 'widget_entity_content_add_list', '#content' => $types];
    }

    /**
     * Presents the creation form for widget_entity entities of given bundle/type.
     *
     * @param EntityInterface $widget_entity_type
     *   The custom bundle to add.
     * @param \Symfony\Component\HttpFoundation\Request $request
     *   The current request object.
     *
     * @return array
     *   A form array as expected by drupal_render().
     */
    public function addForm(EntityInterface $widget_entity_type, Request $request) {
      $entity = $this->storage->create([
        'type' => $widget_entity_type->id()
      ]);
      return $this->entityFormBuilder()->getForm($entity);
    }

    /**
     * Provides the page title for this controller.
     *
     * @param EntityInterface $widget_entity_type
     *   The custom bundle/type being added.
     *
     * @return string
     *   The page title.
     */
    public function getAddFormTitle(EntityInterface $widget_entity_type) {
      return t('Create of bundle @label',
        ['@label' => $widget_entity_type->label()]
      );
    }
}
