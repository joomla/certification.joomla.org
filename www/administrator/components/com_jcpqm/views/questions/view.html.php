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
 * Jcpqm View class for the Questions
 */
class JcpqmViewQuestions extends JViewLegacy
{
	/**
	 * Questions view display method
	 * @return void
	 */
	function display($tpl = null)
	{
		if ($this->getLayout() !== 'modal')
		{
			// Include helper submenu
			JcpqmHelper::addSubmenu('questions');
		}

		// Assign data to the view
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');
		$this->user = JFactory::getUser();
		$this->listOrder = $this->escape($this->state->get('list.ordering'));
		$this->listDirn = $this->escape($this->state->get('list.direction'));
		$this->saveOrder = $this->listOrder == 'ordering';
		// set the return here value
		$this->return_here = urlencode(base64_encode((string) JUri::getInstance()));
		// get global action permissions
		$this->canDo = JcpqmHelper::getActions('question');
		$this->canEdit = $this->canDo->get('question.edit');
		$this->canState = $this->canDo->get('question.edit.state');
		$this->canCreate = $this->canDo->get('question.create');
		$this->canDelete = $this->canDo->get('question.delete');
		$this->canBatch = $this->canDo->get('core.batch');

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal')
		{
			$this->addToolbar();
			$this->sidebar = JHtmlSidebar::render();
			// load the batch html
			if ($this->canCreate && $this->canEdit && $this->canState)
			{
				$this->batchDisplay = JHtmlBatch_::render();
			}
		}
		
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
		JToolBarHelper::title(JText::_('COM_JCPQM_QUESTIONS'), 'joomla');
		JHtmlSidebar::setAction('index.php?option=com_jcpqm&view=questions');
		JFormHelper::addFieldPath(JPATH_COMPONENT . '/models/fields');

		if ($this->canCreate)
		{
			JToolBarHelper::addNew('question.add');
		}

		// Only load if there are items
		if (JcpqmHelper::checkArray($this->items))
		{
			if ($this->canEdit)
			{
				JToolBarHelper::editList('question.edit');
			}

			if ($this->canState)
			{
				JToolBarHelper::publishList('questions.publish');
				JToolBarHelper::unpublishList('questions.unpublish');
				JToolBarHelper::archiveList('questions.archive');

				if ($this->canDo->get('core.admin'))
				{
					JToolBarHelper::checkin('questions.checkin');
				}
			}

			// Add a batch button
			if ($this->canBatch && $this->canCreate && $this->canEdit && $this->canState)
			{
				// Get the toolbar object instance
				$bar = JToolBar::getInstance('toolbar');
				// set the batch button name
				$title = JText::_('JTOOLBAR_BATCH');
				// Instantiate a new JLayoutFile instance and render the batch button
				$layout = new JLayoutFile('joomla.toolbar.batch');
				// add the button to the page
				$dhtml = $layout->render(array('title' => $title));
				$bar->appendButton('Custom', $dhtml, 'batch');
			}

			if ($this->state->get('filter.published') == -2 && ($this->canState && $this->canDelete))
			{
				JToolbarHelper::deleteList('', 'questions.delete', 'JTOOLBAR_EMPTY_TRASH');
			}
			elseif ($this->canState && $this->canDelete)
			{
				JToolbarHelper::trash('questions.trash');
			}

			if ($this->canDo->get('core.export') && $this->canDo->get('question.export'))
			{
				JToolBarHelper::custom('questions.exportData', 'download', '', 'COM_JCPQM_EXPORT_DATA', true);
			}
		}

		if ($this->canDo->get('core.import') && $this->canDo->get('question.import'))
		{
			JToolBarHelper::custom('questions.importData', 'upload', '', 'COM_JCPQM_IMPORT_DATA', false);
		}

		// set help url for this view if found
		$help_url = JcpqmHelper::getHelpUrl('questions');
		if (JcpqmHelper::checkString($help_url))
		{
				JToolbarHelper::help('COM_JCPQM_HELP_MANAGER', false, $help_url);
		}

		// add the options comp button
		if ($this->canDo->get('core.admin') || $this->canDo->get('core.options'))
		{
			JToolBarHelper::preferences('com_jcpqm');
		}

		if ($this->canState)
		{
			JHtmlSidebar::addFilter(
				JText::_('JOPTION_SELECT_PUBLISHED'),
				'filter_published',
				JHtml::_('select.options', JHtml::_('jgrid.publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true)
			);
			// only load if batch allowed
			if ($this->canBatch)
			{
				JHtmlBatch_::addListSelection(
					JText::_('COM_JCPQM_KEEP_ORIGINAL_STATE'),
					'batch[published]',
					JHtml::_('select.options', JHtml::_('jgrid.publishedOptions', array('all' => false)), 'value', 'text', '', true)
				);
			}
		}

		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_ACCESS'),
			'filter_access',
			JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text', $this->state->get('filter.access'))
		);

		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			JHtmlBatch_::addListSelection(
				JText::_('COM_JCPQM_KEEP_ORIGINAL_ACCESS'),
				'batch[access]',
				JHtml::_('select.options', JHtml::_('access.assetgroups'), 'value', 'text')
			);
		}

		// Category Filter.
		JHtmlSidebar::addFilter(
			JText::_('JOPTION_SELECT_CATEGORY'),
			'filter_category_id',
			JHtml::_('select.options', JHtml::_('category.options', 'com_jcpqm.question'), 'value', 'text', $this->state->get('filter.category_id'))
		);

		if ($this->canBatch && $this->canCreate && $this->canEdit)
		{
			// Category Batch selection.
			JHtmlBatch_::addListSelection(
				JText::_('COM_JCPQM_KEEP_ORIGINAL_CATEGORY'),
				'batch[category]',
				JHtml::_('select.options', JHtml::_('category.options', 'com_jcpqm.question'), 'value', 'text')
			);
		}

		// Set Exam Name Selection
		$this->examNameOptions = JFormHelper::loadFieldType('Exam')->options;
		// We do some sanitation for Exam Name filter
		if (JcpqmHelper::checkArray($this->examNameOptions) &&
			isset($this->examNameOptions[0]->value) &&
			!JcpqmHelper::checkString($this->examNameOptions[0]->value))
		{
			unset($this->examNameOptions[0]);
		}
		// Only load Exam Name filter if it has values
		if (JcpqmHelper::checkArray($this->examNameOptions))
		{
			// Exam Name Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_JCPQM_QUESTION_EXAM_LABEL').' -',
				'filter_exam',
				JHtml::_('select.options', $this->examNameOptions, 'value', 'text', $this->state->get('filter.exam'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Exam Name Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_JCPQM_QUESTION_EXAM_LABEL').' -',
					'batch[exam]',
					JHtml::_('select.options', $this->examNameOptions, 'value', 'text')
				);
			}
		}

		// Set Questiontype Questiontype Selection
		$this->questiontypeQuestiontypeOptions = JFormHelper::loadFieldType('Questiontype')->options;
		// We do some sanitation for Questiontype Questiontype filter
		if (JcpqmHelper::checkArray($this->questiontypeQuestiontypeOptions) &&
			isset($this->questiontypeQuestiontypeOptions[0]->value) &&
			!JcpqmHelper::checkString($this->questiontypeQuestiontypeOptions[0]->value))
		{
			unset($this->questiontypeQuestiontypeOptions[0]);
		}
		// Only load Questiontype Questiontype filter if it has values
		if (JcpqmHelper::checkArray($this->questiontypeQuestiontypeOptions))
		{
			// Questiontype Questiontype Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_JCPQM_QUESTION_QUESTIONTYPE_LABEL').' -',
				'filter_questiontype',
				JHtml::_('select.options', $this->questiontypeQuestiontypeOptions, 'value', 'text', $this->state->get('filter.questiontype'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Questiontype Questiontype Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_JCPQM_QUESTION_QUESTIONTYPE_LABEL').' -',
					'batch[questiontype]',
					JHtml::_('select.options', $this->questiontypeQuestiontypeOptions, 'value', 'text')
				);
			}
		}

		// Set Level Selection
		$this->levelOptions = $this->getTheLevelSelections();
		// We do some sanitation for Level filter
		if (JcpqmHelper::checkArray($this->levelOptions) &&
			isset($this->levelOptions[0]->value) &&
			!JcpqmHelper::checkString($this->levelOptions[0]->value))
		{
			unset($this->levelOptions[0]);
		}
		// Only load Level filter if it has values
		if (JcpqmHelper::checkArray($this->levelOptions))
		{
			// Level Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_JCPQM_QUESTION_LEVEL_LABEL').' -',
				'filter_level',
				JHtml::_('select.options', $this->levelOptions, 'value', 'text', $this->state->get('filter.level'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Level Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_JCPQM_QUESTION_LEVEL_LABEL').' -',
					'batch[level]',
					JHtml::_('select.options', $this->levelOptions, 'value', 'text')
				);
			}
		}

		// Set Workstatus Selection
		$this->workstatusOptions = $this->getTheWorkstatusSelections();
		// We do some sanitation for Workstatus filter
		if (JcpqmHelper::checkArray($this->workstatusOptions) &&
			isset($this->workstatusOptions[0]->value) &&
			!JcpqmHelper::checkString($this->workstatusOptions[0]->value))
		{
			unset($this->workstatusOptions[0]);
		}
		// Only load Workstatus filter if it has values
		if (JcpqmHelper::checkArray($this->workstatusOptions))
		{
			// Workstatus Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_JCPQM_QUESTION_WORKSTATUS_LABEL').' -',
				'filter_workstatus',
				JHtml::_('select.options', $this->workstatusOptions, 'value', 'text', $this->state->get('filter.workstatus'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Workstatus Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_JCPQM_QUESTION_WORKSTATUS_LABEL').' -',
					'batch[workstatus]',
					JHtml::_('select.options', $this->workstatusOptions, 'value', 'text')
				);
			}
		}

		// Set Synced Selection
		$this->syncedOptions = $this->getTheSyncedSelections();
		// We do some sanitation for Synced filter
		if (JcpqmHelper::checkArray($this->syncedOptions) &&
			isset($this->syncedOptions[0]->value) &&
			!JcpqmHelper::checkString($this->syncedOptions[0]->value))
		{
			unset($this->syncedOptions[0]);
		}
		// Only load Synced filter if it has values
		if (JcpqmHelper::checkArray($this->syncedOptions))
		{
			// Synced Filter
			JHtmlSidebar::addFilter(
				'- Select '.JText::_('COM_JCPQM_QUESTION_SYNCED_LABEL').' -',
				'filter_synced',
				JHtml::_('select.options', $this->syncedOptions, 'value', 'text', $this->state->get('filter.synced'))
			);

			if ($this->canBatch && $this->canCreate && $this->canEdit)
			{
				// Synced Batch Selection
				JHtmlBatch_::addListSelection(
					'- Keep Original '.JText::_('COM_JCPQM_QUESTION_SYNCED_LABEL').' -',
					'batch[synced]',
					JHtml::_('select.options', $this->syncedOptions, 'value', 'text')
				);
			}
		}
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_('COM_JCPQM_QUESTIONS'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_jcpqm/assets/css/questions.css", (JcpqmHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
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
		if(strlen($var) > 50)
		{
			// use the helper htmlEscape method instead and shorten the string
			return JcpqmHelper::htmlEscape($var, $this->_charset, true);
		}
		// use the helper htmlEscape method instead.
		return JcpqmHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Returns an array of fields the table can be sorted by
	 *
	 * @return  array  Array containing the field name to sort by as the key and display text as value
	 */
	protected function getSortFields()
	{
		return array(
			'a.sorting' => JText::_('JGRID_HEADING_ORDERING'),
			'a.published' => JText::_('JSTATUS'),
			'a.question_title' => JText::_('COM_JCPQM_QUESTION_QUESTION_TITLE_LABEL'),
			'h.questiontype' => JText::_('COM_JCPQM_QUESTION_QUESTIONTYPE_LABEL'),
			'a.id' => JText::_('JGRID_HEADING_ID')
		);
	}

	protected function getTheLevelSelections()
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('level'));
		$query->from($db->quoteName('#__jcpqm_question'));
		$query->order($db->quoteName('level') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$results = $db->loadColumn();

		if ($results)
		{
			// get model
			$model = $this->getModel();
			$results = array_unique($results);
			$_filter = array();
			foreach ($results as $level)
			{
				// Translate the level selection
				$text = $model->selectionTranslation($level,'level');
				// Now add the level and its text to the options array
				$_filter[] = JHtml::_('select.option', $level, JText::_($text));
			}
			return $_filter;
		}
		return false;
	}

	protected function getTheWorkstatusSelections()
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('workstatus'));
		$query->from($db->quoteName('#__jcpqm_question'));
		$query->order($db->quoteName('workstatus') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$results = $db->loadColumn();

		if ($results)
		{
			// get model
			$model = $this->getModel();
			$results = array_unique($results);
			$_filter = array();
			foreach ($results as $workstatus)
			{
				// Translate the workstatus selection
				$text = $model->selectionTranslation($workstatus,'workstatus');
				// Now add the workstatus and its text to the options array
				$_filter[] = JHtml::_('select.option', $workstatus, JText::_($text));
			}
			return $_filter;
		}
		return false;
	}

	protected function getTheSyncedSelections()
	{
		// Get a db connection.
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);

		// Select the text.
		$query->select($db->quoteName('synced'));
		$query->from($db->quoteName('#__jcpqm_question'));
		$query->order($db->quoteName('synced') . ' ASC');

		// Reset the query using our newly populated query object.
		$db->setQuery($query);

		$results = $db->loadColumn();

		if ($results)
		{
			// get model
			$model = $this->getModel();
			$results = array_unique($results);
			$_filter = array();
			foreach ($results as $synced)
			{
				// Translate the synced selection
				$text = $model->selectionTranslation($synced,'synced');
				// Now add the synced and its text to the options array
				$_filter[] = JHtml::_('select.option', $synced, JText::_($text));
			}
			return $_filter;
		}
		return false;
	}
}
