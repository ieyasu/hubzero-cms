<?php
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

jimport( 'joomla.application.component.view');

require_once 'api/org/nees/static/Files.php';
require_once 'lib/data/MaterialTypePeer.php';
require_once 'lib/data/LocationPlanPeer.php';
require_once 'lib/data/LocationPeer.php';
require_once 'lib/data/Experiment.php';
require_once 'lib/security/Authorizer.php';
require_once 'api/org/nees/html/joomla/ComponentHtml.php';

class ProjectEditorViewSensorTypes extends JView{
	
  function display($tpl = null){
    /* @var $oModel ProjectEditorModelSensorTypes */
    $oModel =& $this->getModel();

    $oSensorTypeArray = $oModel->getSensorTypes();
    $_REQUEST[SensorTypePeer::TABLE_NAME] = serialize($oSensorTypeArray);

    JFactory::getApplication()->getPathway()->addItem("Sensor Types","javascript:void(0)");
    
    parent::display($tpl);
  }
  
}
?>
