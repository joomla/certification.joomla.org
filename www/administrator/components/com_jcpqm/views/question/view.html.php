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
 * Question View class
 */
class JcpqmViewQuestion extends JViewLegacy
{
	/**
	 * display method of View
	 * @return void
	 */
	public function display($tpl = null)
	{
		// set params
		$this->params = JComponentHelper::getParams('com_jcpqm');
		// Assign the variables
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->script = $this->get('Script');
		$this->state = $this->get('State');
		// get action permissions
		$this->canDo = JcpqmHelper::getActions('question', $this->item);
		// get input
		$jinput = JFactory::getApplication()->input;
		$this->ref = $jinput->get('ref', 0, 'word');
		$this->refid = $jinput->get('refid', 0, 'int');
		$return = $jinput->get('return', null, 'base64');
		// set the referral string
		$this->referral = '';
		if ($this->refid && $this->ref)
		{
			// return to the item that referred to this item
			$this->referral = '&ref=' . (string)$this->ref . '&refid=' . (int)$this->refid;
		}
		elseif($this->ref)
		{
			// return to the list view that referred to this item
			$this->referral = '&ref=' . (string)$this->ref;
		}
		// check return value
		if (!is_null($return))
		{
			// add the return value
			$this->referral .= '&return=' . (string)$return;
		}

		// Set the toolbar
		$this->addToolBar();
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}


	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId	= $user->id;
		$isNew = $this->item->id == 0;

		JToolbarHelper::title( JText::_($isNew ? 'COM_JCPQM_QUESTION_NEW' : 'COM_JCPQM_QUESTION_EDIT'), 'pencil-2 article-add');
		// Built the actions for new and existing records.
		if (JcpqmHelper::checkString($this->referral))
		{
			if ($this->canDo->get('question.create') && $isNew)
			{
				// We can create the record.
				JToolBarHelper::save('question.save', 'JTOOLBAR_SAVE');
			}
			elseif ($this->canDo->get('question.edit'))
			{
				// We can save the record.
				JToolBarHelper::save('question.save', 'JTOOLBAR_SAVE');
			}
			if ($isNew)
			{
				// Do not creat but cancel.
				JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
			}
			else
			{
				// We can close it.
				JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		else
		{
			if ($isNew)
			{
				// For new records, check the create permission.
				if ($this->canDo->get('question.create'))
				{
					JToolBarHelper::apply('question.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('question.save', 'JTOOLBAR_SAVE');
					JToolBarHelper::custom('question.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				};
				JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CANCEL');
			}
			else
			{
				if ($this->canDo->get('question.edit'))
				{
					// We can save the new record
					JToolBarHelper::apply('question.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('question.save', 'JTOOLBAR_SAVE');
					// We can save this record, but check the create permission to see
					// if we can return to make a new one.
					if ($this->canDo->get('question.create'))
					{
						JToolBarHelper::custom('question.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
					}
				}
				$canVersion = ($this->canDo->get('core.version') && $this->canDo->get('question.version'));
				if ($this->state->params->get('save_history', 1) && $this->canDo->get('question.edit') && $canVersion)
				{
					JToolbarHelper::versions('com_jcpqm.question', $this->item->id);
				}
				if ($this->canDo->get('question.create'))
				{
					JToolBarHelper::custom('question.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
				}
				if ($this->canDo->get('question.push_to_shika'))
				{
					// add Push to Shika button.
					JToolBarHelper::custom('question.pushToShika', 'upload', '', 'COM_JCPQM_PUSH_TO_SHIKA', false);
				}
				JToolBarHelper::cancel('question.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		JToolbarHelper::divider();
		// set help url for this view if found
		$help_url = JcpqmHelper::getHelpUrl('question');
		if (JcpqmHelper::checkString($help_url))
		{
			JToolbarHelper::help('COM_JCPQM_HELP_MANAGER', false, $help_url);
		}
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if(strlen($var) > 30)
		{
    		// use the helper htmlEscape method instead and shorten the string
			return JcpqmHelper::htmlEscape($var, $this->_charset, true, 30);
		}
		// use the helper htmlEscape method instead.
		return JcpqmHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = ($this->item->id < 1);
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_($isNew ? 'COM_JCPQM_QUESTION_NEW' : 'COM_JCPQM_QUESTION_EDIT'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_jcpqm/assets/css/question.css", (JcpqmHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
		// Add Ajax Token
		$this->document->addScriptDeclaration("var token = '".JSession::getFormToken()."';");
		$this->document->addScript(JURI::root() . $this->script, (JcpqmHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/javascript');
		$this->document->addScript(JURI::root() . "administrator/components/com_jcpqm/views/question/submitbutton.js", (JcpqmHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/javascript'); 
		JText::script('view not acceptable. Error');
	}
}
