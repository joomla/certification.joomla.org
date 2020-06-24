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
 * Jcpqm Ajax Model
 */
class JcpqmModelAjax extends JModelList
{
	protected $app_params;
	
	public function __construct() 
	{		
		parent::__construct();		
		// get params
		$this->app_params	= JComponentHelper::getParams('com_jcpqm');
		
	}

	// Used in question
/* BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN
	*
	*  TODO MDI Ajax Question :: BEGIN
	*
	*/

	/**
	 * @param $catId    int
	 * @param $view     string
	 *
	 *
	 * @since version
	 */
	Public function myGetQuestionCatBase($catId, $view)
	{
		// Do stuff here, the controller will take care of the json encoding
		/** @var $modelQuestion \JcpqmModelExam */
		$modelQuestion = \JcpqmHelper::getModel( $view );
		$return = $modelQuestion->ajaxGetBaseCatId( $catId );
		return $return;
	}


	/*
*  TODO MDI Ajax Question :: END
*
END END END END END END END END END END END END END END END END END END END END END END END END */
}
