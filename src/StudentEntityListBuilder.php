<?php

namespace Drupal\asenext_final;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the student entity entity type.
 */
class StudentEntityListBuilder extends EntityListBuilder {

  /**
   * {@inheritdoc}
   */
  public function render() {
    $build['table'] = parent::render();

    $total = $this->getStorage()
      ->getQuery()
      ->accessCheck(FALSE)
      ->count()
      ->execute();

    $build['summary']['#markup'] = $this->t('Total student entities: @total', ['@total' => $total]);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['student_name'] = $this->t('Student Name');
    $header['student_rollno'] = $this->t('Roll No');
    $header['student_class'] = $this->t('Class');
    $header['student_phone'] = $this->t('Contact Number');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\asenext_final\StudentEntityInterface $entity */
    $row['id'] = $entity->id();
    $row['student_name'] = $entity->get('student_name')->getString();
    $row['student_rollno'] = $entity->get('student_rollno')->getString();
    $row['student_class'] = $entity->student_class?->entity?->label();
    $row['student_phone'] = $entity->get('student_phone')->getString();
    return $row + parent::buildRow($entity);
  }

}
