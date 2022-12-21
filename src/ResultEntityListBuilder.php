<?php

namespace Drupal\asenext_final;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;

/**
 * Provides a list controller for the result entity entity type.
 */
class ResultEntityListBuilder extends EntityListBuilder {

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

    $build['summary']['#markup'] = $this->t('Total result entities: @total', ['@total' => $total]);
    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['id'] = $this->t('ID');
    $header['result_subject'] = $this->t('Subject');
    $header['result_rollno'] = $this->t('Roll No');
    $header['result_score'] = $this->t('Score');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    /** @var \Drupal\asenext_final\ResultEntityInterface $entity */
    $row['id'] = $entity->id();
    $row['result_subject'] = $entity->result_subject->entity->label();
    $row['result_rollno'] = $entity->result_rollno->entity->get('student_rollno')->getString();
    $row['result_score'] = $entity->get('result_score')->getString();
    return $row + parent::buildRow($entity);
  }

}
