<?php

/**
 * Class CRM_Civicase_Page_CaseAngular
 *
 * Define an Angular base-page for CiviCase.
 */
class CRM_Civicase_Page_CaseAngular extends \CRM_Core_Page {

  /**
   * This function takes care of all the things common to all
   * pages. This typically involves assigning the appropriate
   * smarty variable :)
   *
   * @return string
   *   The content generated by running this page
   */
  public function run() {
    $loader = new \Civi\Angular\AngularLoader();
    $loader->setPageName('civicrm/case/a');
    $loader->setModules(array('crmApp', 'civicase'));
    $loader->load();
    \Civi::resources()->addSetting(array(
      'crmApp' => array(
        'defaultRoute' => '/case/list',
      ),
    ));
    return parent::run();
  }

  /**
   * @inheritdoc
   */
  public function getTemplateFileName() {
    return 'Civi/Angular/Page/Main.tpl';
  }

}