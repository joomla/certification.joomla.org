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

use \Joomla\CMS\MVC\Controller\BaseController;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_certified_users'))
{
	throw new Exception(Text::_('JERROR_ALERTNOAUTHOR'));
}

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Certified_users', JPATH_COMPONENT_ADMINISTRATOR);
JLoader::register('Certified_usersHelper', JPATH_COMPONENT_ADMINISTRATOR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'certified_users.php');

$controller = BaseController::getInstance('Certified_users');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
