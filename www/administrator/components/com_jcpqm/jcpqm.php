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
JHtml::_('behavior.tabstate');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jcpqm'))
{
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
};

// Add CSS file for all pages
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_jcpqm/assets/css/admin.css');
$document->addScript('components/com_jcpqm/assets/js/admin.js');

// require helper files
JLoader::register('JcpqmHelper', __DIR__ . '/helpers/jcpqm.php'); 
JLoader::register('JHtmlBatch_', __DIR__ . '/helpers/html/batch_.php'); 

/***[INSERTED$$$$]***//*4*/
include JPATH_ADMINISTRATOR . '/components/com_jcpqm/helpers/jcpqmconstants.php';
/***[/INSERTED$$$$]***/
// Get an instance of the controller prefixed by Jcpqm
$controller = JControllerLegacy::getInstance('Jcpqm');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
