<?php

/**
 * @version    1.0.0
 * @package    Com_Certified_users
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2020 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modelitem');
jimport('joomla.event.dispatcher');

JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');

JLoader::import('joomla.application.component.model');
JLoader::import( 'certifications', JPATH_ADMINISTRATOR . '/components/com_certified_users/models');

use \Joomla\CMS\Factory;
use \Joomla\Utilities\ArrayHelper;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Table\Table;

/**
 * Certified_users model.
 *
 * @since  1.6
 */
class Certified_usersModelCertified_user extends \Joomla\CMS\MVC\Model\ItemModel
{
	public $_item;


	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since    1.6
	 *
	 */
	protected function populateState()
	{
		$app  = Factory::getApplication('com_certified_users');
		$user = Factory::getUser();

		// Check published state
		if ((!$user->authorise('core.edit.state', 'com_certified_users')) && (!$user->authorise('core.edit', 'com_certified_users')))
		{
			$this->setState('filter.published', 1);
			$this->setState('filter.archived', 2);
		}

		// Load state from the request userState on edit or from the passed variable on default
		if (Factory::getApplication()->input->get('layout') == 'edit')
		{
			$id = Factory::getApplication()->getUserState('com_certified_users.edit.certified_user.id');
		}
		else
		{
			$id = Factory::getApplication()->input->get('id');
			Factory::getApplication()->setUserState('com_certified_users.edit.certified_user.id', $id);
		}

		$this->setState('certified_user.id', $id);

		// Load the parameters.
		$params       = $app->getParams();
		$params_array = $params->toArray();

		if (isset($params_array['item_id']))
		{
			$this->setState('certified_user.id', $params_array['item_id']);
		}

		$this->setState('params', $params);
	}

	/**
	 * Method to get an object.
	 *
	 * @param   integer  $id  The id of the object to get.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @throws Exception
	 */
	public function getItem($id = null)
	{
		$app  = Factory::getApplication('com_certified_users');
		$params = $app->getParams();
		$params_array = $params->toArray();

		if ($this->_item === null)
		{
			$this->_item = false;

			if (empty($id))
			{
				$id = $this->getState('certified_user.id');
			}

			// Get a level row instance.
			$table = $this->getTable();

			// Attempt to load the row.
			if ($table->load($id))
			{

				// Check published state.
				if ($published = $this->getState('filter.published'))
				{
					if (isset($table->state) && $table->state != $published)
					{
						throw new Exception(Text::_('COM_CERTIFIED_USERS_ITEM_NOT_LOADED'), 403);
					}
				}

				// Convert the JTable to a clean JObject.
				$properties  = $table->getProperties(1);
				$this->_item = ArrayHelper::toObject($properties, 'JObject');
			}

                if (empty($this->_item))
				{
					throw new Exception(Text::_('COM_CERTIFIED_USERS_ITEM_NOT_LOADED'), 404);
		}
            }



		if (isset($this->_item->created_by))
		{
			$this->_item->created_by_name = JFactory::getUser($this->_item->created_by)->name;
		}

		if (isset($this->_item->modified_by))
		{
			$this->_item->modified_by_name = JFactory::getUser($this->_item->modified_by)->name;
		}

		if (isset($this->_item->user))
		{
			$user = JFactory::getUser($this->_item->user);
			$this->_item->user_name = $user->name;
			$this->_item->user_id = $user->id;

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
				$this->_item->user_image = $userfieldvalues[$params_array['user_field_image']];
			} else {
				$this->_item->user_image = $params_array['default_user_image'];
			}

			$this->_item->user_location = $userfieldvalues[$params_array['user_field_country']];
			if ($userfieldvalues[$params_array['user_field_city']] != ''){
				$this->_item->user_location = $userfieldvalues[$params_array['user_field_city']] . ', ' .$this->_item->user_location;
			}

			$this->_item->user_website = $userfieldvalues[$params_array['user_field_website']];
			$this->_item->user_email = $userfieldvalues[$params_array['user_field_email']];

		}

		$this->_item->certifications = json_decode($this->_item->certifications);

		$certifications_model = JModelLegacy::getInstance('certifications','Certified_usersModel');
		$certification_details = [];
		foreach ($certifications_model->getItems() as $cert){
			$certification_details[$cert->id] = [
				'name' => $cert->name,
				'badge' => $cert->badge
			];
		}

		foreach ($this->_item->certifications as $cert){
			$cert->certified_on = date('d F Y', strtotime($cert->certified_on));
			$cert->badge = $certification_details[$cert->certification]['badge'];
			$cert->name = $certification_details[$cert->certification]['name'];
		}

		$certifications = (array) $this->_item->certifications;
		uasort($certifications, function($a, $b) {
			return strtotime($b->certified_on) - strtotime($a->certified_on);
		});
		$this->_item->certifications = (object) $certifications;

		return $this->_item;
	}

	/**
	 * Get an instance of JTable class
	 *
	 * @param   string  $type    Name of the JTable class to get an instance of.
	 * @param   string  $prefix  Prefix for the table class name. Optional.
	 * @param   array   $config  Array of configuration values for the JTable object. Optional.
	 *
	 * @return  JTable|bool JTable if success, false on failure.
	 */
	public function getTable($type = 'Certified_user', $prefix = 'Certified_usersTable', $config = array())
	{
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_certified_users/tables');

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Get the id of an item by alias
	 *
	 * @param   string  $alias  Item alias
	 *
	 * @return  mixed
	 */
	public function getItemIdByAlias($alias)
	{
		$table      = $this->getTable();
		$properties = $table->getProperties();
		$result     = null;
		$aliasKey   = $this->getAliasFieldNameByView('certified_user');

		if (key_exists('alias', $properties))
		{
			$table->load(array('alias' => $alias));
			$result = $table->id;
		}
		elseif (isset($aliasKey) && key_exists($aliasKey, $properties))
		{
			$table->load(array($aliasKey => $alias));
			$result = $table->id;
		}

		return $result;

	}

	/**
	 * Method to check in an item.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkin($id = null)
	{
		// Get the id.
		$id = (!empty($id)) ? $id : (int) $this->getState('certified_user.id');

		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Attempt to check the row in.
			if (method_exists($table, 'checkin'))
			{
				if (!$table->checkin($id))
				{
					return false;
				}
			}
		}

		return true;

	}

	/**
	 * Method to check out an item for editing.
	 *
	 * @param   integer  $id  The id of the row to check out.
	 *
	 * @return  boolean True on success, false on failure.
	 *
	 * @since    1.6
	 */
	public function checkout($id = null)
	{
		// Get the user id.
		$id = (!empty($id)) ? $id : (int) $this->getState('certified_user.id');


		if ($id)
		{
			// Initialise the table
			$table = $this->getTable();

			// Get the current user object.
			$user = Factory::getUser();

			// Attempt to check the row out.
			if (method_exists($table, 'checkout'))
			{
				if (!$table->checkout($user->get('id'), $id))
				{
					return false;
				}
			}
		}

		return true;

	}

	/**
	 * Publish the element
	 *
	 * @param   int  $id     Item id
	 * @param   int  $state  Publish state
	 *
	 * @return  boolean
	 */
	public function publish($id, $state)
	{
		$table = $this->getTable();

		$table->load($id);
		$table->state = $state;

		return $table->store();

	}

	/**
	 * Method to delete an item
	 *
	 * @param   int  $id  Element id
	 *
	 * @return  bool
	 */
	public function delete($id)
	{
		$table = $this->getTable();


		return $table->delete($id);

	}

	public function getAliasFieldNameByView($view)
	{
		switch ($view)
		{
			case 'certified_user':
			case 'certified_userform':
				return 'alias';
			break;
		}
	}
}
