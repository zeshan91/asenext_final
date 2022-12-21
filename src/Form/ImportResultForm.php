<?php

namespace Drupal\asenext_final\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\asenext_final\CsvHelper;
use Drupal\asenext_final\Entity\ResultEntity;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides a AseNext Final form.
 */
class ImportResultForm extends FormBase {

  /**
   * @var \Drupal\asenext_final\CsvHelper
   */
  protected $csvHelper;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * {@inheritdoc}
   */
  public function __construct(CsvHelper $csv_helper, EntityTypeManagerInterface $entity_type_manager) {
    $this->csvHelper = $csv_helper;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    // Instantiates this form class.
    return new static(
      // Load the service required to construct this class.
      $container->get('asenext_final.csv_helper'),
      $container->get('entity_type.manager')
    );
  }


  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'asenext_final_import_result';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Create Entity.
    $form['import_file'] = [
      '#type' => 'managed_file',
      '#title' => 'Upload CSV File',
      '#field_name' => 'import_file',
      "#upload_validators"  => [
        "file_validate_extensions" => ["csv"],
      ],
      '#upload_location' => 'temporary://reports',
      '#description' => '
        Use below columns<br>
        Roll No,Subject,Score
      ',
      '#required' => 1,
    ];

    $form['actions'] = [
      '#type' => 'actions',
    ];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Send'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $postdata = $form_state->getValues();
    $fileId = is_array($postdata['import_file']) ? current($postdata['import_file']) : $postdata['import_file'];
    if (empty($fileId)) {
      return;
    }

    $this->csvHelper->read($fileId);
    $header = $this->csvHelper->getHeader();
    $requiredHeaders = ['Roll No', 'Subject', 'Score'];
    $diff = array_diff($requiredHeaders, $header);
    if (!empty($diff)) {
      $form_state->setErrorByName('import_file', implode(', ', $diff) . " columns are missing. Please check csv header");
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $header = $this->csvHelper->getHeader();
    $headerIndex = array_flip($header);
    $allRows = [];
    while ($row = $this->csvHelper->readRow()) {
      $subjectTermID = $this->getSubjectTermId($row[$headerIndex['Subject']]);
      $rollNo = $row[$headerIndex['Roll No']];
      $studenRefID = $this->getStudentRefID($rollNo);
      if (empty($studenRefID)) {
        $this->messenger()->addError($this->t("Roll No %roll_no does not exist.", ['%roll_no' => $rollNo]));
        continue;
      }
      $values = [];
      $values['result_subject'] = ['target_id' => $subjectTermID];
      $values['result_rollno'] = ['target_id' => $studenRefID];
      $values['result_score'] = $row[$headerIndex['Score']];
      $entity = ResultEntity::create($values);
      $violations = $entity->validate();
      if ($violations->count() == 0) {
        $entity->save();
      }
      else {
        foreach ($violations as $val) {
          $this->messenger()->addError($this->t($val->getMessage()->render()));
        }
      }
    }
    $this->messenger()->addStatus($this->t('Data imported successfully.'));
    $form_state->setRedirect('entity.result_entity.collection');
  }

  /**
   * Get Class ID.
   */
  private function getSubjectTermId($subjectName) {
    $terms = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadByProperties([
        'vid' => 'subjects',
        'name' =>$subjectName,
      ]);

    if (!empty($terms)) {
      $term = current($terms);
      return $term->id();
    }

    // Create the class term.
    $values = [];
    $values['name'] = $subjectName;
    $values['vid'] = 'subjects';
    $term = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->create($values);
    $term->save();
    return $term->id();

  }

  /**
   * Get Class ID.
   */
  private function getStudentRefID($rollNo) {
    $students = $this->entityTypeManager
      ->getStorage('student_entity')
      ->loadByProperties([
        'student_rollno' => $rollNo,
      ]);
    if (!empty($students)) {
      $student = current($students);
      return $student->id();
    }
    return FALSE;
  }

}
