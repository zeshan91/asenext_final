<?php

namespace Drupal\asenext_final\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the student entity entity edit forms.
 */
class StudentEntityForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $result = parent::save($form, $form_state);

    $entity = $this->getEntity();

    $message_arguments = ['%label' => $entity->toLink()->toString()];
    $logger_arguments = [
      '%label' => $entity->label(),
      'link' => $entity->toLink($this->t('View'))->toString(),
    ];

    switch ($result) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('New student entity %label has been created.', $message_arguments));
        $this->logger('asenext_final')->notice('Created new student entity %label', $logger_arguments);
        break;

      case SAVED_UPDATED:
        $this->messenger()->addStatus($this->t('The student entity %label has been updated.', $message_arguments));
        $this->logger('asenext_final')->notice('Updated student entity %label.', $logger_arguments);
        break;
    }

    $form_state->setRedirect('entity.student_entity.collection');

    return $result;
  }

}