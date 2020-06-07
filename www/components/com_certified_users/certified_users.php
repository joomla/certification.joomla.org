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
use \Joomla\CMS\MVC\Controller\BaseController;

// Include dependancies
jimport('joomla.application.component.controller');

JLoader::registerPrefix('Certified_users', JPATH_COMPONENT);
JLoader::register('Certified_usersController', JPATH_COMPONENT . '/controller.php');


// Execute the task.
$controller = BaseController::getInstance('Certified_users');
$controller->execute(Factory::getApplication()->input->get('task'));
$controller->redirect();
