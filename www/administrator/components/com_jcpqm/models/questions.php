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
 * Questions Model
 */
class JcpqmModelQuestions extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
        {
			$config['filter_fields'] = array(
				'a.id','id',
				'a.published','published',
				'a.ordering','ordering',
				'a.created_by','created_by',
				'a.modified_by','modified_by',
				'a.question_title','question_title',
				'a.questiontype','questiontype',
				'a.exam','exam',
				'c.title','category_title',
				'c.id', 'category_id',
				'a.catid', 'catid',
				'a.level','level',
				'a.workstatus','workstatus',
				'a.synced','synced'
			);
		}

		parent::__construct($config);
	}

/* BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN
*
*  TODO MDI model(list view) QUESTION :: BEGIN
*
*/
public function loadCrowdinIni()
{
	// From global config, get 
  	// - path
  	// - wildcard for filename
  
	// scandir
	// parse_ini_file 
  
	// check 
  	// - against language
  	// - check against existing question entries
  
}
/* 
*  MDI model(list view) QUESTION :: END
*   
END END END END END END END END END END END END END END END END END END END END END END END END */
	
	/**
	 * Method to auto-populate the model state.
	 *
	 * @return  void
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}
		$question_title = $this->getUserStateFromRequest($this->context . '.filter.question_title', 'filter_question_title');
		$this->setState('filter.question_title', $question_title);

		$questiontype = $this->getUserStateFromRequest($this->context . '.filter.questiontype', 'filter_questiontype');
		$this->setState('filter.questiontype', $questiontype);

		$exam = $this->getUserStateFromRequest($this->context . '.filter.exam', 'filter_exam');
		$this->setState('filter.exam', $exam);

		$category = $app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category');
		$this->setState('filter.category', $category);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$catid = $app->getUserStateFromRequest($this->context . '.filter.catid', 'filter_catid');
		$this->setState('filter.catid', $catid);

		$level = $this->getUserStateFromRequest($this->context . '.filter.level', 'filter_level');
		$this->setState('filter.level', $level);

		$workstatus = $this->getUserStateFromRequest($this->context . '.filter.workstatus', 'filter_workstatus');
		$this->setState('filter.workstatus', $workstatus);

		$synced = $this->getUserStateFromRequest($this->context . '.filter.synced', 'filter_synced');
		$this->setState('filter.synced', $synced);
        
		$sorting = $this->getUserStateFromRequest($this->context . '.filter.sorting', 'filter_sorting', 0, 'int');
		$this->setState('filter.sorting', $sorting);
        
		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		$this->setState('filter.access', $access);
        
		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);
        
		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by', '');
		$this->setState('filter.created_by', $created_by);

		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created');
		$this->setState('filter.created', $created);

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		// check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();

		// set values to display correctly.
		if (JcpqmHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				$access = (JFactory::getUser()->authorise('question.access', 'com_jcpqm.question.' . (int) $item->id) && JFactory::getUser()->authorise('question.access', 'com_jcpqm'));
				if (!$access)
				{
					unset($items[$nr]);
					continue;
				}

			}
		}

		// set selection value to a translatable value
		if (JcpqmHelper::checkArray($items))
		{
			foreach ($items as $nr => &$item)
			{
				// convert level
				$item->level = $this->selectionTranslation($item->level, 'level');
				// convert workstatus
				$item->workstatus = $this->selectionTranslation($item->workstatus, 'workstatus');
				// convert synced
				$item->synced = $this->selectionTranslation($item->synced, 'synced');
			}
		}

        
		// return items
		return $items;
	}

	/**
	 * Method to convert selection values to translatable string.
	 *
	 * @return translatable string
	 */
	public function selectionTranslation($value,$name)
	{
		// Array of level language strings
		if ($name === 'level')
		{
			$levelArray = array(
				'easy' => 'COM_JCPQM_QUESTION_EASY',
				'medium' => 'COM_JCPQM_QUESTION_MEDIUM',
				'hard' => 'COM_JCPQM_QUESTION_HARD'
			);
			// Now check if value is found in this array
			if (isset($levelArray[$value]) && JcpqmHelper::checkString($levelArray[$value]))
			{
				return $levelArray[$value];
			}
		}
		// Array of workstatus language strings
		if ($name === 'workstatus')
		{
			$workstatusArray = array(
				0 => 'COM_JCPQM_QUESTION_NEW',
				1 => 'COM_JCPQM_QUESTION_REJECTED',
				2 => 'COM_JCPQM_QUESTION_DISCUSSING',
				3 => 'COM_JCPQM_QUESTION_ONEST_REVIEW',
				4 => 'COM_JCPQM_QUESTION_TWOND_REVIEW',
				5 => 'COM_JCPQM_QUESTION_APPROVED'
			);
			// Now check if value is found in this array
			if (isset($workstatusArray[$value]) && JcpqmHelper::checkString($workstatusArray[$value]))
			{
				return $workstatusArray[$value];
			}
		}
		// Array of synced language strings
		if ($name === 'synced')
		{
			$syncedArray = array(
				0 => 'COM_JCPQM_QUESTION_NOT_LINKED',
				1 => 'COM_JCPQM_QUESTION_SYNC_OK',
				2 => 'COM_JCPQM_QUESTION_SYNC_REQUIRED'
			);
			// Now check if value is found in this array
			if (isset($syncedArray[$value]) && JcpqmHelper::checkString($syncedArray[$value]))
			{
				return $syncedArray[$value];
			}
		}
		return $value;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// Select some fields
		$query->select('a.*');
		$query->select($db->quoteName('c.title','category_title'));

		// From the jcpqm_item table
		$query->from($db->quoteName('#__jcpqm_question', 'a'));
		$query->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON (' . $db->quoteName('a.catid') . ' = ' . $db->quoteName('c.id') . ')');

		// From the jcpqm_exam table.
		$query->select($db->quoteName('g.name','exam_name'));
		$query->join('LEFT', $db->quoteName('#__jcpqm_exam', 'g') . ' ON (' . $db->quoteName('a.exam') . ' = ' . $db->quoteName('g.id') . ')');

		// From the jcpqm_questiontype table.
		$query->select($db->quoteName('h.questiontype','questiontype_questiontype'));
		$query->join('LEFT', $db->quoteName('#__jcpqm_questiontype', 'h') . ' ON (' . $db->quoteName('a.questiontype') . ' = ' . $db->quoteName('h.id') . ')');

		// Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		// Filter by access level.
		if ($access = $this->getState('filter.access'))
		{
			$query->where('a.access = ' . (int) $access);
		}
		// Implement View Level Access
		if (!$user->authorise('core.options', 'com_jcpqm'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}
		// Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search) . '%');
				$query->where('(a.question_title LIKE '.$search.' OR a.alias LIKE '.$search.' OR a.uuid LIKE '.$search.')');
			}
		}

		// Filter by exam.
		if ($exam = $this->getState('filter.exam'))
		{
			$query->where('a.exam = ' . $db->quote($db->escape($exam)));
		}
		// Filter by questiontype.
		if ($questiontype = $this->getState('filter.questiontype'))
		{
			$query->where('a.questiontype = ' . $db->quote($db->escape($questiontype)));
		}
		// Filter by Level.
		if ($level = $this->getState('filter.level'))
		{
			$query->where('a.level = ' . $db->quote($db->escape($level)));
		}
		// Filter by Workstatus.
		if ($workstatus = $this->getState('filter.workstatus'))
		{
			$query->where('a.workstatus = ' . $db->quote($db->escape($workstatus)));
		}
		// Filter by Synced.
		if ($synced = $this->getState('filter.synced'))
		{
			$query->where('a.synced = ' . $db->quote($db->escape($synced)));
		}

		// Filter by a single or group of categories.
		$baselevel = 1;
		$categoryId = $this->getState('filter.category_id');

		if (is_numeric($categoryId))
		{
			$cat_tbl = JTable::getInstance('Category', 'JTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int) $cat_tbl->level;
			$query->where('c.lft >= ' . (int) $lft)
				->where('c.rgt <= ' . (int) $rgt);
		}
		elseif (is_array($categoryId))
		{
			JArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('a.category IN (' . $categoryId . ')');
		}


		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'asc');	
		if ($orderCol != '')
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get list export data.
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getExportData($pks)
	{
		// setup the query
		if (JcpqmHelper::checkArray($pks))
		{
			// Set a value to know this is exporting method.
			$_export = true;
			// Get the user object.
			$user = JFactory::getUser();
			// Create a new query object.
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// Select some fields
			$query->select('a.*');

			// From the jcpqm_question table
			$query->from($db->quoteName('#__jcpqm_question', 'a'));
			$query->where('a.id IN (' . implode(',',$pks) . ')');
			// Implement View Level Access
			if (!$user->authorise('core.options', 'com_jcpqm'))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN (' . $groups . ')');
			}

			// Order the results by ordering
			$query->order('a.ordering  ASC');

			// Load the items
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				$items = $db->loadObjectList();

				// set values to display correctly.
				if (JcpqmHelper::checkArray($items))
				{
					foreach ($items as $nr => &$item)
					{
						$access = (JFactory::getUser()->authorise('question.access', 'com_jcpqm.question.' . (int) $item->id) && JFactory::getUser()->authorise('question.access', 'com_jcpqm'));
						if (!$access)
						{
							unset($items[$nr]);
							continue;
						}

						// unset the values we don't want exported.
						unset($item->asset_id);
						unset($item->checked_out);
						unset($item->checked_out_time);
					}
				}
				// Add headers to items array.
				$headers = $this->getExImPortHeaders();
				if (JcpqmHelper::checkObject($headers))
				{
					array_unshift($items,$headers);
				}
				return $items;
			}
		}
		return false;
	}

	/**
	* Method to get header.
	*
	* @return mixed  An array of data items on success, false on failure.
	*/
	public function getExImPortHeaders()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// get the columns
		$columns = $db->getTableColumns("#__jcpqm_question");
		if (JcpqmHelper::checkArray($columns))
		{
			// remove the headers you don't import/export.
			unset($columns['asset_id']);
			unset($columns['checked_out']);
			unset($columns['checked_out_time']);
			$headers = new stdClass();
			foreach ($columns as $column => $type)
			{
				$headers->{$column} = $column;
			}
			return $headers;
		}
		return false;
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 *
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		$id .= ':' . $this->getState('filter.question_title');
		$id .= ':' . $this->getState('filter.questiontype');
		$id .= ':' . $this->getState('filter.exam');
		$id .= ':' . $this->getState('filter.category');
		$id .= ':' . $this->getState('filter.category_id');
		$id .= ':' . $this->getState('filter.catid');
		$id .= ':' . $this->getState('filter.level');
		$id .= ':' . $this->getState('filter.workstatus');
		$id .= ':' . $this->getState('filter.synced');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to checkin all items left checked out longer then a set time.
	 *
	 * @return  a bool
	 *
	 */
	protected function checkInNow()
	{
		// Get set check in time
		$time = JComponentHelper::getParams('com_jcpqm')->get('check_in');

		if ($time)
		{

			// Get a db connection.
			$db = JFactory::getDbo();
			// reset query
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__jcpqm_question'));
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				// Get Yesterdays date
				$date = JFactory::getDate()->modify($time)->toSql();
				// reset query
				$query = $db->getQuery(true);

				// Fields to update.
				$fields = array(
					$db->quoteName('checked_out_time') . '=\'0000-00-00 00:00:00\'',
					$db->quoteName('checked_out') . '=0'
				);

				// Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('checked_out') . '!=0', 
					$db->quoteName('checked_out_time') . '<\''.$date.'\''
				);

				// Check table
				$query->update($db->quoteName('#__jcpqm_question'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
