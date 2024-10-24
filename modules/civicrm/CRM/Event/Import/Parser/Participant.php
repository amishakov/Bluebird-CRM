<?php
/*
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC. All rights reserved.                        |
 |                                                                    |
 | This work is published under the GNU AGPLv3 license with some      |
 | permitted exceptions and without any warranty. For full license    |
 | and copyright information, see https://civicrm.org/licensing       |
 +--------------------------------------------------------------------+
 */

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC https://civicrm.org/licensing
 */

/**
 * class to parse membership csv files
 */
class CRM_Event_Import_Parser_Participant extends CRM_Import_Parser {

  /**
   * Get information about the provided job.
   *
   *  - name
   *  - id (generally the same as name)
   *  - label
   *
   * @return array
   */
  public static function getUserJobInfo(): array {
    return [
      'participant_import' => [
        'id' => 'participant_import',
        'name' => 'participant_import',
        'label' => ts('Participant Import'),
        'entity' => 'Participant',
        'url' => 'civicrm/import/participant',
      ],
    ];
  }

  /**
   * The initializer code, called before the processing.
   */
  public function init() {
    unset($this->userJob);
    $this->setFieldMetadata();
  }

  /**
   * Handle the values in import mode.
   *
   * @param array $values
   *   The array of values belonging to this line.
   */
  public function import(array $values): void {
    $rowNumber = (int) ($values[array_key_last($values)]);
    try {
      $params = $this->getMappedRow($values);
      if (!empty($params['external_identifier'])) {
        $params['contact_id'] = $this->lookupExternalIdentifier($params['external_identifier'], $this->getContactType(), $params['contact_id'] ?? NULL);
      }

      $formatted = $params;
      // don't add to recent items, CRM-4399
      $formatted['skipRecentView'] = TRUE;

      $formatValues = [];
      foreach ($params as $key => $field) {
        if ($field == NULL || $field === '') {
          continue;
        }

        $formatValues[$key] = $field;
      }

      if (!empty($params['id'])) {
        $this->checkEntityExists('Participant', $params['id']);
        if (!$this->isUpdateExisting()) {
          throw new CRM_Core_Exception(ts('% record found and update not selected', [1 => 'Participant']));
        }
        //@todo calling api functions directly is not supported
        $this->deprecated_participant_check_params($formatted);
        $newParticipant = civicrm_api3('Participant', 'create', $formatted);
        $this->setImportStatus($rowNumber, 'IMPORTED', '', $newParticipant['id']);
        return;
      }

      if (empty($params['contact_id'])) {
        $error = $this->checkContactDuplicate($formatValues);

        if (CRM_Core_Error::isAPIError($error, CRM_Core_Error::DUPLICATE_CONTACT)) {
          $matchedIDs = (array) $error['error_message']['params'];
          if (count($matchedIDs) >= 1) {
            foreach ($matchedIDs as $contactId) {
              $formatted['contact_id'] = $contactId;
              $formatted['version'] = 3;
              $newParticipant = $this->deprecated_create_participant_formatted($formatted);
            }
          }
        }
        else {
          // Using new Dedupe rule.
          $ruleParams = [
            'contact_type' => $this->_contactType,
            'used' => 'Unsupervised',
          ];
          $fieldsArray = CRM_Dedupe_BAO_DedupeRule::dedupeRuleFields($ruleParams);

          $disp = '';
          foreach ($fieldsArray as $value) {
            if (array_key_exists(trim($value), $params)) {
              $paramValue = $params[trim($value)];
              if (is_array($paramValue)) {
                $disp .= $params[trim($value)][0][trim($value)] . " ";
              }
              else {
                $disp .= $params[trim($value)] . " ";
              }
            }
          }

          if (!empty($params['external_identifier'])) {
            if ($disp) {
              $disp .= "AND {$params['external_identifier']}";
            }
            else {
              $disp = $params['external_identifier'];
            }
          }
          throw new CRM_Core_Exception('No matching Contact found for (' . $disp . ')');
        }
      }
      else {
        $newParticipant = $this->deprecated_create_participant_formatted($formatted);
      }

      if (is_array($newParticipant) && civicrm_error($newParticipant)) {
        if ($this->isSkipDuplicates()) {

          $contactID = $newParticipant['contactID'] ?? NULL;
          $participantID = $newParticipant['participantID'] ?? NULL;
          $url = CRM_Utils_System::url('civicrm/contact/view/participant',
            "reset=1&id={$participantID}&cid={$contactID}&action=view", TRUE
          );
          if (is_array($newParticipant['error_message']) &&
            ($participantID == $newParticipant['error_message']['params'][0])
          ) {
            $this->setImportStatus($rowNumber, 'DUPLICATE', $url);
            return;
          }
          if ($newParticipant['error_message']) {
            throw new CRM_Core_Exception($newParticipant['error_message']);
          }
          throw new CRM_Core_Exception(ts('Unknown error'));
        }
      }
    }
    catch (CRM_Core_Exception $e) {
      $this->setImportStatus($rowNumber, 'ERROR', $e->getMessage());
      return;
    }
    $this->setImportStatus($rowNumber, 'IMPORTED', '', $newParticipant['id']);
  }

  /**
   * @param array $params
   *
   * @return array|bool
   *   <type>
   * @throws \CRM_Core_Exception
   * @deprecated - this is part of the import parser not the API & needs to be
   *   moved on out
   *
   */
  protected function deprecated_create_participant_formatted($params) {
    if ($this->isIgnoreDuplicates()) {
      CRM_Core_Error::reset();
      $error = $this->deprecated_participant_check_params($params, TRUE);
      if (civicrm_error($error)) {
        return $error;
      }
    }
    return civicrm_api3('Participant', 'create', $params);
  }

  /**
   * Formatting that was written a long time ago and may not make sense now.
   *
   * @param array $params
   *
   * @param bool $checkDuplicate
   *
   * @return array|bool
   */
  protected function deprecated_participant_check_params($params, $checkDuplicate = FALSE) {

    // check if contact id is valid or not
    if (!empty($params['contact_id'])) {
      $contact = new CRM_Contact_BAO_Contact();
      $contact->id = $params['contact_id'];
      if (!$contact->find(TRUE)) {
        throw new CRM_Core_Exception(ts('Contact id is not valid'));
      }
    }

    $result = [];
    if ($checkDuplicate) {
      if (CRM_Event_BAO_Participant::checkDuplicate($params, $result)) {
        $participantID = array_pop($result);

        $error = CRM_Core_Error::createError("Found matching participant record.",
          CRM_Core_Error::DUPLICATE_PARTICIPANT,
          'Fatal', $participantID
        );

        return civicrm_api3_create_error($error->pop(),
          [
            'contactID' => $params['contact_id'],
            'participantID' => $participantID,
          ]
        );
      }
    }
  }

  /**
   * Set up field metadata.
   *
   * @return void
   * @throws \CRM_Core_Exception
   */
  protected function setFieldMetadata(): void {
    if (empty($this->importableFieldsMetadata)) {
      $fields = array_merge(
        [
          '' => ['title' => ts('- do not import -')],
          'participant_note' => [
            'title' => ts('Participant Note'),
            'name' => 'participant_note',
            'headerPattern' => '/(participant.)?note$/i',
            'data_type' => CRM_Utils_Type::T_TEXT,
            'options' => FALSE,
          ],
        ],
        $this->getImportFieldsForEntity('Participant'),
        CRM_Core_BAO_CustomField::getFieldsForImport('Participant'),
        $this->getContactMatchingFields()
      );

      $fields['contact_id']['title'] .= ' (match to contact)';
      $fields['contact_id']['html']['label'] = $fields['contact_id']['title'];
      $this->importableFieldsMetadata = $fields;
    }
  }

  /**
   * @return array
   */
  protected function getRequiredFields(): array {
    return [['event_id', 'status_id']];
  }

}
