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
 * Exam Controller
 */
class JcpqmControllerExam extends JControllerForm
{
	/**
	 * Current or most recently performed task.
	 *
	 * @var    string
	 * @since  12.2
	 * @note   Replaces _task.
	 */
	protected $task;

	/**
	 * Class constructor.
	 *
	 * @param   array  $config  A named array of configuration variables.
	 *
	 * @since   1.6
	 */
	public function __construct($config = array())
	{
		$this->view_list = 'Exams'; // safeguard for setting the return view listing to the main view.
		parent::__construct($config);
	}

/* BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN
*
*  TODO MDI controller EXAM :: BEGIN
*
*/

// Check if category with same alias is present
public function getini()
{
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	if (true == false)
	{
		/* @var $model \JcpqmModelExam */
		$model = $this->getModel();

		if (is_null($model->getCategoryMap()))
		{
			$model->categoryMapping();
		}
		$this->LoadIni();

		$errors = $this->getErrors();
		if (sizeof($errors) == 0)
		{
			\Joomla\CMS\Factory::getApplication()->enqueueMessage("Loaded .ini files");
		}
	}
	else
	{
		$msg="Disabled by, Marco loading of ini files was a one time thing ;)";
		\Joomla\CMS\Factory::getApplication()->enqueueMessage($msg,'error');
	}
	$id = $this->input->get('id');
	$this->setRedirect(JRoute::_(
		'index.php?option=' . $this->option
		. "&view=" . $this->view_item
		. self::getRedirectToItemAppend($id), false
	));
}

public function generateIni()
{
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	/* @var $modelExam \JcpqmModelExam  */
	$modelExam=$this->getModel();

	$modelExam->generateIni(	$this->input->get('id') );

	$errors=$this->getErrors();
	if ( sizeof($errors) == 0) {
		JFactory::getApplication()->enqueueMessage("Saved .ini files");
	}

	$id=$this->input->get('id');
	$this->setRedirect(JRoute::_(
		'index.php?option=' . $this->option
		. "&view=" . $this->view_item
		. self::getRedirectToItemAppend($id) , false
	));
}

public function LoadIni()
{
	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	/* @var $modelExam \JcpqmModelExam  */
	$modelExam=$this->getModel();
	$categoryMap=$modelExam->getCategoryMap();

	$idExam=(int)$this->input->get('id');
	$examModel = $modelExam->getItem($idExam);
	$exam = $examModel->alias;

	$language="en-GB";
	$base=JPATH_COMPONENT_ADMINISTRATOR . '/jcpqm_crowdin/language/' . $language;
    $component = $this->option;

	$sections=array();
	$sectionIds=array();
	/* @var $category \CategoriesModelCategory  */
	$category   = JModelLegacy::getInstance('Category', 'CategoriesModel', array('ignore_request' => true));
	foreach( $categoryMap["section"]["allow"]["published/j3/administrator"] as $catId)
	{
		$alias              = $category->getItem($catId)->alias;
		$sections[]         = $alias;
		$sectionIds[$alias] = (int) $category->getItem($catId)->id;
	}

	// Load inifile into array per section and question
	foreach ($sections as $section)
	{
		$inifile=$base . "/" . $language . "." . $component . "_" . $exam . "_" . $section . ".ini";
		if (file_exists($inifile))
		{
			$languageFile = parse_ini_file($inifile);
			foreach ($languageFile as $key => $value)
			{
				if (preg_match('/(?:.*?)_Q$/', $key))
				{
					$questionArr[$section][$key]['Q'] = $value;
					$QE                  = $key;
					continue;
				}
				$questionArr[$section][$QE][] = $value;
			}
		}
	}

	/** @var $modelQuestion \JcpqmModelQuestion l  */
	$modelQuestion = $this->getModel('Question');

	foreach($questionArr as $catName => $section)
	{
		foreach ($section as $item)
		{
			$data = array(
				"id"            => 0
			, "alias"           => uniqid("a")
			, "uuid"            => uniqid("q")
			, "version_note"    => ""
			, "note"            => "imported"
			, "published"       => "1"
			, "access"          => "1"
			, "version"         => null
			, "metadesc"        => ""
			, "metakey"         => ""
			, "created_user_id" => 0
			, "created_time"    => ""
			, "modified_time"   => ""
			, "language"        => "*"
			,"metadata" => '{"robots":"","author":"","rights":""}'
			);

			$numAnswers = sizeof($item);
			switch($numAnswers){
				case 1: $question_type=1; break;
				case 4: $question_type=2; break;
				case 5: $question_type=4; break;
			}
			$data ['exam']           = $idExam;
			// we cant use cat_id as that is overwritten in the form save from dedicated listboxes
			$data ['force_catid']    = (int)$sectionIds[$catName];
			$data ['questiontype']   = $question_type;
			$data ['question_title'] = $item['Q'];
			$data ['q_q']            = $item['Q'];

			for($i=0; $i< $numAnswers-1; $i++)
			{
				$data[ sprintf('q_a%d',$i+1) ] = $item[$i];
			}
			if ( !$modelQuestion->save($data) )
			{
				$this->setError('Save failed :'.$item['Q'] );
			};
		}
	}
}

public function pushToCrowdin()
{
    // have model method to return the USED category id's in the questions for an exam => UsedExamCategories
    // have a model method to return all the categories (id's and aliases as aliases are used in the "naming" => AllExamCategories
    // loop AllExamCategories
    // if not in UsedExamCategories -> delete from crowdin https://support.crowdin.com/api/delete-file/
    // else  try update https://support.crowdin.com/api/update-file/, if that fails https://support.crowdin.com/api/add-file/

	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	/* @var $model \JcpqmModelExam */
	$model=$this->getModel();
	$model->pushToCrowdin();
	$this->setRedirect(JRoute::_('index.php?option=' . $this->option . "&view=" . $this->view_item  . \JcpqmControllerExam::getRedirectToItemAppend(1) , false));
}

public function getTranslations()
{
   // loop UsedExamCategories
  	JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

	/* @var $model \JcpqmModelExam */
	$model=$this->getModel();
	$model->getTranslations();
	$this->setRedirect(JRoute::_('index.php?option=' . $this->option . "&view=" . $this->view_item  . \JcpqmControllerExam::getRedirectToItemAppend(1) , false));

}
/* MDI controller EXAM :: END
   END END END END END END END END END END END END END END END END END END END END END END END END */

        /**
	 * Method override to check if you can add a new record.
	 *
	 * @param   array  $data  An array of input data.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowAdd($data = array())
	{
		// Get user object.
		$user = JFactory::getUser();
		// Access check.
		$access = $user->authorise('exam.access', 'com_jcpqm');
		if (!$access)
		{
			return false;
		}

		// In the absense of better information, revert to the component permissions.
		return $user->authorise('exam.create', $this->option);
	}

	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param   array   $data  An array of input data.
	 * @param   string  $key   The name of the key for the primary key.
	 *
	 * @return  boolean
	 *
	 * @since   1.6
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// get user object.
		$user = JFactory::getUser();
		// get record id.
		$recordId = (int) isset($data[$key]) ? $data[$key] : 0;


		// Access check.
		$access = ($user->authorise('exam.access', 'com_jcpqm.exam.' . (int) $recordId) &&  $user->authorise('exam.access', 'com_jcpqm'));
		if (!$access)
		{
			return false;
		}

		if ($recordId)
		{
			// The record has been set. Check the record permissions.
			$permission = $user->authorise('exam.edit', 'com_jcpqm.exam.' . (int) $recordId);
			if (!$permission)
			{
				if ($user->authorise('exam.edit.own', 'com_jcpqm.exam.' . $recordId))
				{
					// Now test the owner is the user.
					$ownerId = (int) isset($data['created_by']) ? $data['created_by'] : 0;
					if (empty($ownerId))
					{
						// Need to do a lookup from the model.
						$record = $this->getModel()->getItem($recordId);

						if (empty($record))
						{
							return false;
						}
						$ownerId = $record->created_by;
					}

					// If the owner matches 'me' then allow.
					if ($ownerId == $user->id)
					{
						if ($user->authorise('exam.edit.own', 'com_jcpqm'))
						{
							return true;
						}
					}
				}
				return false;
			}
		}
		// Since there is no permission, revert to the component permissions.
		return $user->authorise('exam.edit', $this->option);
	}

	/**
	 * Gets the URL arguments to append to an item redirect.
	 *
	 * @param   integer  $recordId  The primary key id for the item.
	 * @param   string   $urlVar    The name of the URL variable for the id.
	 *
	 * @return  string  The arguments to append to the redirect URL.
	 *
	 * @since   1.6
	 */
	protected function getRedirectToItemAppend($recordId = null, $urlVar = 'id')
	{
		// get the referral options (old method use return instead see parent)
		$ref = $this->input->get('ref', 0, 'string');
		$refid = $this->input->get('refid', 0, 'int');

		// get redirect info.
		$append = parent::getRedirectToItemAppend($recordId, $urlVar);

		// set the referral options
		if ($refid && $ref)
                {
			$append = '&ref=' . (string)$ref . '&refid='. (int)$refid . $append;
		}
		elseif ($ref)
		{
			$append = '&ref='. (string)$ref . $append;
		}

		return $append;
	}

	/**
	 * Method to run batch operations.
	 *
	 * @param   object  $model  The model.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   2.5
	 */
	public function batch($model = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Set the model
		$model = $this->getModel('Exam', '', array());

		// Preset the redirect
		$this->setRedirect(JRoute::_('index.php?option=com_jcpqm&view=exams' . $this->getRedirectToListAppend(), false));

		return parent::batch($model);
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   12.2
	 */
	public function cancel($key = null)
	{
		// get the referral options
		$this->ref = $this->input->get('ref', 0, 'word');
		$this->refid = $this->input->get('refid', 0, 'int');

		// Check if there is a return value
		$return = $this->input->get('return', null, 'base64');

		$cancel = parent::cancel($key);

		if (!is_null($return) && JUri::isInternal(base64_decode($return)))
		{
			$redirect = base64_decode($return);

			// Redirect to the return value.
			$this->setRedirect(
				JRoute::_(
					$redirect, false
				)
			);
		}
		elseif ($this->refid && $this->ref)
		{
			$redirect = '&view=' . (string)$this->ref . '&layout=edit&id=' . (int)$this->refid;

			// Redirect to the item screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . $redirect, false
				)
			);
		}
		elseif ($this->ref)
		{
			$redirect = '&view='.(string)$this->ref;

			// Redirect to the list screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . $redirect, false
				)
			);
		}
		return $cancel;
	}

	/**
	 * Method to save a record.
	 *
	 * @param   string  $key     The name of the primary key of the URL variable.
	 * @param   string  $urlVar  The name of the URL variable if different from the primary key (sometimes required to avoid router collisions).
	 *
	 * @return  boolean  True if successful, false otherwise.
	 *
	 * @since   12.2
	 */
	public function save($key = null, $urlVar = null)
	{
		// get the referral options
		$this->ref = $this->input->get('ref', 0, 'word');
		$this->refid = $this->input->get('refid', 0, 'int');

		// Check if there is a return value
		$return = $this->input->get('return', null, 'base64');
		$canReturn = (!is_null($return) && JUri::isInternal(base64_decode($return)));

		if ($this->ref || $this->refid || $canReturn)
		{
			// to make sure the item is checkedin on redirect
			$this->task = 'save';
		}

		$saved = parent::save($key, $urlVar);

		// This is not needed since parent save already does this
		// Due to the ref and refid implementation we need to add this
		if ($canReturn)
		{
			$redirect = base64_decode($return);

			// Redirect to the return value.
			$this->setRedirect(
				JRoute::_(
					$redirect, false
				)
			);
		}
		elseif ($this->refid && $this->ref)
		{
			$redirect = '&view=' . (string)$this->ref . '&layout=edit&id=' . (int)$this->refid;

			// Redirect to the item screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . $redirect, false
				)
			);
		}
		elseif ($this->ref)
		{
			$redirect = '&view=' . (string)$this->ref;

			// Redirect to the list screen.
			$this->setRedirect(
				JRoute::_(
					'index.php?option=' . $this->option . $redirect, false
				)
			);
		}
		return $saved;
	}

	/**
	 * Function that allows child controller access to model data
	 * after the data has been saved.
	 *
	 * @param   JModel  &$model     The data model object.
	 * @param   array   $validData  The validated data.
	 *
	 * @return  void
	 *
	 * @since   11.1
	 */
	protected function postSaveHook(JModelLegacy $model, $validData = array())
	{
		return;
	}

}
