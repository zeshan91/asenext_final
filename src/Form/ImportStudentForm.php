<?php

namespace Drupal\asenext_final\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\asenext_final\CsvHelper;
use Drupal\asenext_final\Entity\StudentEntity;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Provides a AseNext Final form.
 */
class ImportStudentForm extends FormBase {

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
    return 'asenext_final_import_student';
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
        Student Name,Roll No,Class,Contact Number
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
    $requiredHeaders = ['Student Name', 'Roll No', 'Class', 'Contact Number'];
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
      $classTermID = $this->getClassTermId($row[$headerIndex['Class']]);
      $values = [];
      $values['student_name'] = $row[$headerIndex['Student Name']];
      $values['student_rollno'] = $row[$headerIndex['Roll No']];
      $values['student_class'] = ['target_id' => $classTermID];
      $values['student_phone'] = $row[$headerIndex['Contact Number']];
      $entity = StudentEntity::create($values);
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
    $form_state->setRedirect('entity.student_entity.collection');
  }


  /**
   * Get Class ID.
   */
  private function getClassTermId($className) {
    $terms = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->loadByProperties([
        'vid' => 'class',
        'name' =>$className,
      ]);

    if (!empty($terms)) {
      $term = current($terms);
      return $term->id();
    }

    // Create the class term.
    $values = [];
    $values['name'] = $className;
    $values['vid'] = 'class';
    $term = $this->entityTypeManager
      ->getStorage('taxonomy_term')
      ->create($values);
    $term->save();
    return $term->id();

  }

}
