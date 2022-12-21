<?php

namespace Drupal\asenext_final\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\asenext_final\StudentEntityInterface;

/**
 * Defines the student entity entity class.
 *
 * @ContentEntityType(
 *   id = "student_entity",
 *   label = @Translation("Student Entity"),
 *   label_collection = @Translation("Student Entities"),
 *   label_singular = @Translation("student entity"),
 *   label_plural = @Translation("student entities"),
 *   label_count = @PluralTranslation(
 *     singular = "@count student entities",
 *     plural = "@count student entities",
 *   ),
 *   handlers = {
 *     "list_builder" = "Drupal\asenext_final\StudentEntityListBuilder",
 *     "views_data" = "Drupal\views\EntityViewsData",
 *     "access" = "Drupal\asenext_final\StudentEntityAccessControlHandler",
 *     "form" = {
 *       "add" = "Drupal\asenext_final\Form\StudentEntityForm",
 *       "edit" = "Drupal\asenext_final\Form\StudentEntityForm",
 *       "delete" = "Drupal\Core\Entity\ContentEntityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\Core\Entity\Routing\AdminHtmlRouteProvider",
 *     }
 *   },
 *   base_table = "student_entity",
 *   admin_permission = "administer student entity",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "student_rollno",
 *     "uuid" = "uuid",
 *   },
 *   links = {
 *     "collection" = "/admin/content/student-entity",
 *     "add-form" = "/admin/structure/student-entity/add",
 *     "canonical" = "/admin/structure/student-entity/{student_entity}",
 *     "edit-form" = "/admin/structure/student-entity/{student_entity}/edit",
 *     "delete-form" = "/admin/structure/student-entity/{student_entity}/delete",
 *   },
 *   field_ui_base_route = "entity.student_entity.settings",
 * )
 */
class StudentEntity extends ContentEntityBase implements StudentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['student_name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Student Name'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -8,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -8,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['student_rollno'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Roll No'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 255)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -7,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -7,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->addConstraint('UniqueField');

      $fields['student_class'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Class Name'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler', 'default:taxonomy_term')
      ->setSetting('handler_settings',
          array(
        'target_bundles' => array(
         'class' => 'class'
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

      $fields['student_phone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Contact Number'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 10)
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
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
