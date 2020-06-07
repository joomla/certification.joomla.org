<?php

/**
 * @version    1.0.0
 * @package    Com_Certified_users
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2020 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
defined('_JEXEC') or die;

use Joomla\CMS\Component\Router\RouterViewConfiguration;
use Joomla\CMS\Component\Router\RouterView;
use Joomla\CMS\Component\Router\Rules\StandardRules;
use Joomla\CMS\Component\Router\Rules\NomenuRules;
use Joomla\CMS\Component\Router\Rules\MenuRules;
use \Joomla\CMS\Factory;

/**
 * Class Certified_usersRouter
 *
 */
class Certified_usersRouter extends RouterView
{
	private $noIDs;

	public function __construct($app = null, $menu = null)
	{
		$params      = Factory::getApplication()->getParams('com_certified_users');
		$this->noIDs = (bool) $params->get('sef_ids');


		$certified_users = new RouterViewConfiguration('certified_users');
		$this->registerView($certified_users);

		$certified_user = new RouterViewConfiguration('certified_user');
		$certified_user->setKey('id')->setParent($certified_users);
		$this->registerView($certified_user);

		parent::__construct($app, $menu);

		$this->attachRule(new MenuRules($this));

		if ($params->get('sef_advanced', 0))
		{
			$this->attachRule(new StandardRules($this));
			$this->attachRule(new NomenuRules($this));
		}
		else
		{
			JLoader::register('Certified_usersRulesLegacy', __DIR__ . '/helpers/legacyrouter.php');
			JLoader::register('Certified_usersHelpersCertified_users', __DIR__ . '/helpers/certified_users.php');
			$this->attachRule(new Certified_usersRulesLegacy($this));
		}
	}


	/**
	 * Method to get the segment(s) for an certified_user
	 *
	 * @param   string  $id     ID of the certified_user to retrieve the segments for
	 * @param   array   $query  The request that is built right now
	 *
	 * @return  array|string  The segments of this item
	 */
	public function getCertified_userSegment($id, $query)
	{
		return array((int) $id => $id);
	}


	/**
	 * Method to get the segment(s) for an certified_user
	 *
	 * @param   string  $segment  Segment of the certified_user to retrieve the ID for
	 * @param   array   $query    The request that is parsed right now
	 *
	 * @return  mixed   The id of this item or false
	 */
	public function getCertified_userId($segment, $query)
	{
		return (int) $segment;
	}
}
