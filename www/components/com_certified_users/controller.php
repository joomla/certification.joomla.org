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

jimport('joomla.application.component.controller');

use \Joomla\CMS\Factory;

/**
 * Class Certified_usersController
 *
 * @since  1.6
 */
class Certified_usersController extends \Joomla\CMS\MVC\Controller\BaseController
{
	/**
	 * Method to display a view.
	 *
	 * @param   boolean  $cachable   If true, the view output will be cached
	 * @param   mixed    $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
	 *
	 * @return  JController   This object to support chaining.
	 *
	 * @throws Exception
	 * @since    1.5
	 */
	public function display($cachable = false, $urlparams = false)
	{
		$app  = Factory::getApplication();
		$view = $app->input->getCmd('view', 'certified_users');
		$app->input->set('view', $view);

		parent::display($cachable, $urlparams);

		return $this;
	}
}
