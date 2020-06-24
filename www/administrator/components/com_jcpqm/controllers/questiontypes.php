<?php
/**
 * @package    Joomla Certification Program, question manager
 *
 * @author     marco dings <http://certification.joomla.org>
 * @copyright  Copyright (C) 2015. All Rights Reserved
 * @license    GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

/**
 * Questiontypes Controller
 */
class JcpqmControllerQuestiontypes extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_JCPQM_QUESTIONTYPES';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Questiontype', $prefix = 'JcpqmModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('questiontype.export', 'com_jcpqm') && $user->authorise('core.export', 'com_jcpqm'))
		{
			// Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// Sanitize the input
			JArrayHelper::toInteger($pks);
			// Get the model
			$model = $this->getModel('Questiontypes');
			// get the data to export
			$data = $model->getExportData($pks);
			if (JcpqmHelper::checkArray($data))
			{
				// now set the data to the spreadsheet
				$date = JFactory::getDate();
				JcpqmHelper::xls($data,'Questiontypes_'.$date->format('jS_F_Y'),'Questiontypes exported ('.$date->format('jS F, Y').')','questiontypes');
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_JCPQM_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_jcpqm&view=questiontypes', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('questiontype.import', 'com_jcpqm') && $user->authorise('core.import', 'com_jcpqm'))
		{
			// Get the import model
			$model = $this->getModel('Questiontypes');
			// get the headers to import
			$headers = $model->getExImPortHeaders();
			if (JcpqmHelper::checkObject($headers))
			{
				// Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('questiontype_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'questiontypes');
				$session->set('dataType_VDM_IMPORTINTO', 'questiontype');
				// Redirect to import view.
				$message = JText::_('COM_JCPQM_IMPORT_SELECT_FILE_FOR_QUESTIONTYPES');
				$this->setRedirect(JRoute::_('index.php?option=com_jcpqm&view=import', false), $message);
				return;
			}
		}
		// Redirect to the list screen with error.
		$message = JText::_('COM_JCPQM_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_jcpqm&view=questiontypes', false), $message, 'error');
		return;
	}
}
