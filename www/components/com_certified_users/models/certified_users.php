<?php

/**
 * @version    1.0.0
 * @package    Com_Certified_users
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2020 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use \Joomla\CMS\Factory;
use \Joomla\CMS\Filter\OutputFilter;
use \Joomla\CMS\Language\Text;

jimport('joomla.application.component.modellist');

JLoader::import('joomla.application.component.model');
JLoader::import( 'certifications', JPATH_ADMINISTRATOR . '/components/com_certified_users/models');

/**
 * Methods supporting a list of Certified_users records.
 *
 * @since  1.6
 */
class Certified_usersModelCertified_users extends \Joomla\CMS\MVC\Model\ListModel
{
	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see        JController
	 * @since      1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'ordering', 'a.ordering',
				'state', 'a.state',
				'created_by', 'a.created_by',
				'modified_by', 'a.modified_by',
				'user', 'a.user',
				'certifications', 'a.certifications',
			);
		}

		parent::__construct($config);
	}


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   Elements order
	 * @param   string  $direction  Order direction
	 *
	 * @return void
	 *
	 * @throws Exception
	 *
	 * @since    1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = Factory::getApplication();

		$list = $app->getUserState($this->context . '.list');

		$ordering  = isset($list['filter_order']) ? $list['filter_order'] : null;
		$direction = isset($list['filter_order_Dir']) ? $list['filter_order_Dir'] : null;
		if (empty($ordering))
		{
			$ordering = $app->getUserStateFromRequest($this->context . '.filter_order', 'filter_order', $app->get('filter_order'));
			if (!in_array($ordering, $this->filter_fields))
			{
				$ordering = 'id';
			}
			$this->setState('list.ordering', $ordering);
		}
		if (empty($direction))
		{
			$direction = $app->getUserStateFromRequest($this->context . '.filter_order_Dir', 'filter_order_Dir', $app->get('filter_order_Dir'));
			if (!in_array(strtoupper($direction), array('ASC', 'DESC', '')))
			{
				$direction = 'DESC';
			}
			$this->setState('list.direction', $direction);
		}

		$list['limit']     = $app->getUserStateFromRequest($this->context . '.list.limit', 'limit', $app->get('list_limit'), 'uint');
		$list['start']     = $app->input->getInt('start', 0);
		$list['ordering']  = $ordering;
		$list['direction'] = $direction;

		$app->setUserState($this->context . '.list', $list);
		$app->input->set('list', null);


		// List state information.

		parent::populateState($ordering, $direction);

		$context = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $context);


		// Split context into component and optional section
		$parts = FieldsHelper::extract($context);

		if ($parts)
		{
			$this->setState('filter.component', $parts[0]);
			$this->setState('filter.section', $parts[1]);
		}
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return   JDatabaseQuery
	 *
	 * @since    1.6
	 */
	protected function getListQuery()
	{
		// Create a new query object.
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select', 'DISTINCT a.*'
			)
		);

		$query->from('`#__cud_users` AS a');

		// Join over the users for the checked out user.
		$query->select('uc.name AS uEditor');
		$query->join('LEFT', '#__users AS uc ON uc.id=a.checked_out');

		// Join over the created by field 'created_by'
		$query->join('LEFT', '#__users AS created_by ON created_by.id = a.created_by');

		// Join over the created by field 'modified_by'
		$query->join('LEFT', '#__users AS modified_by ON modified_by.id = a.modified_by');

		if (!Factory::getUser()->authorise('core.edit', 'com_certified_users'))
		{
			$query->where('a.state = 1');
		}
		else
		{
			$query->where('(a.state IN (0, 1))');
		}

		// Filter by search in title
		$search = $this->getState('filter.search');

		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->Quote('%' . $db->escape($search, true) . '%');
			}
		}


		// Add the list ordering clause.
		$orderCol  = $this->state->get('list.ordering', 'id');
		$orderDirn = $this->state->get('list.direction', 'DESC');

		if ($orderCol && $orderDirn)
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get an array of data items
	 *
	 * @return  mixed An array of data on success, false on failure.
	 */
	public function getItems()
	{
		$items = parent::getItems();

		$app  = Factory::getApplication('com_certified_users');
		$params = $app->getParams();
		$params_array = $params->toArray();

		$certifications_model = JModelLegacy::getInstance('certifications','Certified_usersModel');
		$certification_details = [];
		foreach ($certifications_model->getItems() as $cert){
			$certification_details[$cert->id] = [
				'name' => $cert->name,
				'badge' => $cert->badge
			];
		}

		foreach ($items as $item){
			if (isset($item->user))
			{
				$user = JFactory::getUser($item->user);
				$item->user_name = $user->name;
				$item->user_id = $user->id;

				$userfields = FieldsHelper::getFields('com_users.user', $user, true);
				$userfieldvalues = [];
				foreach ($userfields as $field) {
					if ($field->id == $params_array['user_field_image']){
						$userfieldvalues[$field->id] = $field->rawvalue;
					} else {
						$userfieldvalues[$field->id] = $field->value;
					}
				}

				if ($userfieldvalues[$params_array['user_field_image']] != ''){
					$item->user_image = $userfieldvalues[$params_array['user_field_image']];
				} else {
					$item->user_image = $params_array['default_user_image'];
				}

				$item->user_location = $userfieldvalues[$params_array['user_field_country']];
				if ($userfieldvalues[$params_array['user_field_city']] != ''){
					$item->user_location = $userfieldvalues[$params_array['user_field_city']] . ', ' .$item->user_location;
				}

				$item->user_website = $userfieldvalues[$params_array['user_field_website']];
				$item->user_email = $userfieldvalues[$params_array['user_field_email']];

				$item->alias = OutputFilter::stringURLSafe($item->user_name);

			}

			$item->certifications = json_decode($item->certifications);
			$ordering_date = false;

			foreach ($item->certifications as $cert){
				$cert->certified_on = date('d F Y', strtotime($cert->certified_on));
				$cert->badge = $certification_details[$cert->certification]['badge'];
				$cert->name = $certification_details[$cert->certification]['name'];

				if (!$ordering_date || strtotime($ordering_date) < strtotime($cert->certified_on)){
					$ordering_date = $cert->certified_on;
				}
			}

			$item->ordering_date = $ordering_date;

			$certifications = (array) $item->certifications;
			uasort($certifications, function($a, $b) {
				return strtotime($b->certified_on) - strtotime($a->certified_on);
			});
			$item->certifications = (object) $certifications;
		}

		uasort($items, function($a, $b) {
			return strtotime($b->ordering_date) - strtotime($a->ordering_date);
		});

		return $items;
	}

	/**
	 * Overrides the default function to check Date fields format, identified by
	 * "_dateformat" suffix, and erases the field if it's not correct.
	 *
	 * @return void
	 */
	protected function loadFormData()
	{
		$app              = Factory::getApplication();
		$filters          = $app->getUserState($this->context . '.filter', array());
		$error_dateformat = false;

		foreach ($filters as $key => $value)
		{
			if (strpos($key, '_dateformat') && !empty($value) && $this->isValidDate($value) == null)
			{
				$filters[$key]    = '';
				$error_dateformat = true;
			}
		}

		if ($error_dateformat)
		{
			$app->enqueueMessage(Text::_("COM_CERTIFIED_USERS_SEARCH_FILTER_DATE_FORMAT"), "warning");
			$app->setUserState($this->context . '.filter', $filters);
		}

		return parent::loadFormData();
	}

	/**
	 * Checks if a given date is valid and in a specified format (YYYY-MM-DD)
	 *
	 * @param   string  $date  Date to be checked
	 *
	 * @return bool
	 */
	private function isValidDate($date)
	{
		$date = str_replace('/', '-', $date);

		return (date_create($date)) ? Factory::getDate($date)->format("Y-m-d") : null;
	}
}
