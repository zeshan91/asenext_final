<?php

namespace Drupal\asenext_final;

use Drupal\Core\StreamWrapper\StreamWrapperManagerInterface;
use Drupal\file\Entity\File;

/**
 * Service description.
 */
class CsvHelper {

  /**
   * The stream wrapper manager.
   *
   * @var \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface
   */
  protected $streamWrapperManager;

  /**
   * File Pointer
   *
   * @var $filePointer
   */
  protected $filePointer;

  /**
   * Header Row
   *
   * @var $headerRow
   */
  protected $headerRow;

  /**
   * Constructs a CsvHelper object.
   *
   * @param \Drupal\Core\StreamWrapper\StreamWrapperManagerInterface $stream_wrapper_manager
   *   The stream wrapper manager.
   */
  public function __construct(StreamWrapperManagerInterface $stream_wrapper_manager) {
    $this->streamWrapperManager = $stream_wrapper_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getCsvPath($fileId) {
    // @DCG place your code here.
    $file = File::load($fileId);
    if ($file) {
      $uri = $file->getFileUri();
      return $this->streamWrapperManager->getViaUri($uri)->realpath();
    }
    throw new \Exception('Invalid File ID');
  }

  /**
   * {@inheritdoc}
   */
  public function read($fileId) {
    $csvPath = $this->getCsvPath($fileId);
    $this->filePointer = fopen($csvPath, "r");
    $this->headerRow = fgetcsv($this->filePointer);
  }

  /**
   * {@inheritdoc}
   */
  public function getHeader() {
    return $this->headerRow;
  }


  /**
   * {@inheritdoc}
   */
  public function readRow() {
    if(!feof($this->filePointer)) {
      return fgetcsv($this->filePointer);
    }
    fclose($this->filePointer);
    return FALSE;
  }


}
