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
 * Jcpqm Import View
 */
class JcpqmViewImport extends JViewLegacy
{
	protected $headerList;
	protected $hasPackage = false;
	protected $headers;
	protected $hasHeader = 0;
	protected $dataType;

	public function display($tpl = null)
	{		
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			JcpqmHelper::addSubmenu('import');
		}

		$paths = new stdClass;
		$paths->first = '';
		$state = $this->get('state');

		$this->paths = &$paths;
		$this->state = &$state;
                // get global action permissions
		$this->canDo = JcpqmHelper::getActions('import');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
		}

		// get the session object
		$session = JFactory::getSession();
		// check if it has package
		$this->hasPackage 	= $session->get('hasPackage', false);
		$this->dataType 	= $session->get('dataType', false);
		if($this->hasPackage && $this->dataType)
		{
			$this->headerList 	= json_decode($session->get($this->dataType.'_VDM_IMPORTHEADERS', false),true);
			$this->headers 		= JcpqmHelper::getFileHeaders($this->dataType);
			// clear the data type
			$session->clear('dataType');
		}
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JToolBarHelper::title(JText::_('COM_JCPQM_IMPORT_TITLE'), 'upload');
		JHtmlSidebar::setAction('index.php?option=com_jcpqm&view=import');

		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_jcpqm');
		}

		// set help url for this view if found
		$help_url = JcpqmHelper::getHelpUrl('import');
		if (JcpqmHelper::checkString($help_url))
		{
			   JToolbarHelper::help('COM_JCPQM_HELP_MANAGER', false, $help_url);
		}
	}
}
