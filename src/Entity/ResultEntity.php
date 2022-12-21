<?php

namespace Drupal\asenext_final\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\asenext_final\ResultEntityInterface;

/**
 * Defines the result entity entity class.
 *
 * @ContentEntityType(
 *   id = "result_entity",
 *   label = @Translation("Result Entity"),
 *   label_collection = @Translation("Result Entities"),
 *   label_singular = @Translation("result entity"),
 *   label_plural = @Translation("result entities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count result entities",
 *     plural = "@count result entities",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\asenext_final\ResultEntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\asenext_final\ResultEntityAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\asenext_final\Form\ResultEntityForm",
 *       "edit" = "Drupal\asenext_final\Form\ResultEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "result_entity",
 *   admin_permission = "administer result entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "result_subject",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/result-entity",
 *     "add-form" = "/admin/structure/result-entity/add",
 *     "canonical" = "/admin/structure/result-entity/{result_entity}",
 *     "edit-form" = "/admin/structure/result-entity/{result_entity}/edit",
 *     "delete-form" = "/admin/structure/result-entity/{result_entity}/delete",
 *   },
 *   field_ui_base_route = "entity.result_entity.settings",
 * )
 */
class ResultEntity extends ContentEntityBase implements ResultEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['result_rollno'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Result Roll No'))
      ->setRequired(TRUE)
      ->setDescription(t('The student that result belongs to.'))
      ->setSetting('target_type', 'student_entity')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => -7,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -7,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['result_subject'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Subject'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default:taxonomy_term')
      ->setSetting('handler_settings',
          array(
        'target_bundles' => array(
         'subjects' => 'subjects'
        )))
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => -6,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => -6,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '10',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['result_score'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Score'))
      ->setRequired(TRUE)
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
