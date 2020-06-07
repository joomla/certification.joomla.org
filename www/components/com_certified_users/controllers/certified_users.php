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

/**
 * Certified_users list controller class.
 *
 * @since  1.6
 */
class Certified_usersControllerCertified_users extends Certified_usersController
{
	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional
	 * @param   array   $config  Configuration array for model. Optional
	 *
	 * @return object    The model
	 *
	 * @since    1.6
	 */
	public function &getModel($name = 'Certified_users', $prefix = 'Certified_usersModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}
}
