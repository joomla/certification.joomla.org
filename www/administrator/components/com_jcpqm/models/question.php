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

use Joomla\Registry\Registry;

/**
 * Jcpqm Question Model
 */
class JcpqmModelQuestion extends JModelAdmin
{
	/**
	 * The tab layout fields array.
	 *
	 * @var      array
	 */
	protected $tabLayoutFields = array(
		'details' => array(
			'left' => array(
				'level',
				'exam',
				'catid',
				'shikaquestion',
				'catidplus',
				'q_q',
				'workstatus',
				'note',
				'uuid'
			),
			'above' => array(
				'question_title',
				'alias',
				'questiontype',
				'synced'
			),
			'under' => array(
				'not_required'
			)
		),
		'answers' => array(
			'left' => array(
				'q_htf',
				'q_atf',
				'q_h1',
				'q_a1c',
				'q_a1',
				'q_m1',
				'q_h3',
				'q_a3c',
				'q_a3',
				'q_m3'
			),
			'right' => array(
				'q_h2',
				'q_a2c',
				'q_a2',
				'q_m2',
				'q_h4',
				'q_a4c',
				'q_a4',
				'q_m4'
			)
		)
	);

	/**
	 * @var        string    The prefix to use with controller messages.
	 * @since   1.6
	 */
	protected $text_prefix = 'COM_JCPQM';

	/**
	 * The type alias for this content type.
	 *
	 * @var      string
	 * @since    3.2
	 */
	public $typeAlias = 'com_jcpqm.question';

	/**
	 * Returns a Table object, always creating it
	 *
	 * @param   type    $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JTable  A database object
	 *
	 * @since   1.6
	 */
	public function getTable($type = 'question', $prefix = 'JcpqmTable', $config = array())
	{
		// add table path for when model gets used from other component
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_jcpqm/tables');
		// get instance of the table
		return JTable::getInstance($type, $prefix, $config);
	}

/* BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN
*
*  TODO MDI model QUESTION :: BEGIN
*
*/
public function publish_shika(&$pks, $value = 1)
{
	/** @var $modelExam \JcpqmModelExam */
	$modelExam = JcpqmHelper::getModel('Exam');
	if (is_null($modelExam->getCategoryMap()))
	{
		$modelExam->categoryMapping();
		$categoryMapping=$modelExam->getCategoryMap();
	}
	// get category
	// use $categoryMapping["ToShikaUnPub"][catid] to get new shika category
	// get shika question
	// change its category

	/** @var $tmtQuestion \TmtModelQuestion */
	$tmtQuestion = JcpqmHelper::getModel('question', JPATH_ADMINISTRATOR . '/components/com_tmt','tmt');
	JModelLegacy::addTablePath(JPATH_ADMINISTRATOR . '/components/com_tmt/tables');

	foreach( $pks as $pk )
	{
		$question_jcpqm = $this->getItem($pk);
		$question_shika = $tmtQuestion->getItem((int)$question_jcpqm->shikaquestion);
		$row = $value == STATUS_PUBLISHED ? '"ToShika' : "ToShikaUnPub";
		$question_shika->category_id=(int)$categoryMapping[$row][(int)$question_jcpqm->catid];
		$data=(array)$question_shika;
		$tmtQuestion->save($data);
//		$tmtQuestion->save(array('id'=>(int)$question_jcpqm->shikaquestion,'category_id'=>(int)$categoryMapping[$row][(int)$question_jcpqm->catid]));
	}
}

public function tmtAliasFromId($id)
{
	// JModelLegacy::addTablePath(JPATH_ADMINISTRATOR . '/components/com_tmt/tables');
	$table = JTable::getInstance('Question', 'TmtTable', array('dbo',$this->_db));
	if ( $table->load(array('id' => $id))  )
	{
		return $table->alias;
	}
	return false;
}

public function tmtIdFromAlias($alias)
{
	// JModelLegacy::addTablePath(JPATH_ADMINISTRATOR . '/components/com_tmt/tables');
	$table = JTable::getInstance('Question', 'TmtTable', array('dbo',$this->_db));
	if ( $table->load(array('alias' => $alias))  )
	{
		return $table->id;
	}
	return false;
}

public function pushToShika( $questionId )
{
	// load Shika models
	JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_tmt/models');
	/* @var $tmtQuestion \TmtModelQuestion l */
	$tmtQuestion = JModelLegacy::getInstance('question', 'TmtModel', array('ignore_request' => true));
	// help the model find the table getItem
	JModelLegacy::addTablePath(JPATH_ADMINISTRATOR . '/components/com_tmt/tables');

	$jcpqmQuestion = $this->getItem($questionId);
	$uuid          = $jcpqmQuestion->get('uuid');


	// mapping of categories between components
	/** @var $modelExam \JcpqmModelExam */
	$modelExam = JcpqmHelper::getModel('Exam');
//	$modelExam = $this->getModel('Exam');
	if (is_null($modelExam->getCategoryMap()))
	{
		$modelExam->categoryMapping();
		$categoryMapping=$modelExam->getCategoryMap();
	}

	$tmtQuestionIdFromForm = (int)$jcpqmQuestion->get("shikaquestion");
	$alias = $this->tmtAliasFromId($tmtQuestionIdFromForm);

	if ( $tmtQuestionIdFromForm==ID_NEW || $alias == false )
	{
		// creating a question
		$tmtQuestionId = ID_NEW;
	}
	else
	{
		// lookin the question id in the tmt table
		$tmtQuestionId = $this->tmtIdFromAlias($uuid);

		if ($tmtQuestionId===false){
			return sprintf("mismatch matching shika question found for UUID=%s, formid=%d aliasid=%d",$uuid, (int)$tmtQuestionId, $tmtQuestionIdFromForm);
		}

		$tmtQuestion->getItem( (int)$tmtQuestionId );

		if ( $tmtQuestion->isQuestionAttempted($tmtQuestionId) )
		{
			return JText::_('COM_JCPQM_QUESTION_ALREADY_USED_IN_EXAM');
		}
	}

	$data["gradingtype"]="quiz";

	$data["type"]="checkbox";
	if ( in_array( (int)$jcpqmQuestion->questiontype,array(QT_TRUE_FALSE,QT_MULTIPLE_CHOICE_3,QT_MULTIPLE_CHOICE_4)))
	{
		$data["type"]="radio";
	}

	$JtextBase=sprintf("%s_%s_%s",'J3-ADMIN-2019',"CONFIGURATION",strtoupper($jcpqmQuestion->uuid));
	$data["title"]       = $JtextBase . "Q";
	$data["description"] = $jcpqmQuestion->q_q;
	$data["alias"]       = $jcpqmQuestion->uuid;

	$data["ideal_time"]  = "";
    $row = (int)$jcpqmQuestion->published == STATUS_PUBLISHED ? "ToShika" : "ToShikaUnPub";
	$data["category_id"] = $categoryMapping[$row][(int)$jcpqmQuestion->catid];
	$data["level"]       = $jcpqmQuestion->level;
//created_by
//media_type
//media_url
	if (!isset($jcpqmQuestion->shikaquestion))
	{
		$jcpqmQuestion->shikaquestion = ID_NEW;
	}
//	$data["id"] = $jcpqmQuestion->shikaquestion;
	$data["id"] = $tmtQuestionId;
//unique
//ordering
	$data["checked_out"]      = $jcpqmQuestion->checked_out;
	$data["checked_out_time"] = $jcpqmQuestion->checked_out_time;
	$data["created_on"]       = $jcpqmQuestion->created;
	$data["marks"]            = MAX_MARKS;

	$data["created_by"]       = JFactory::getUser()->get('id');

	$numAnswers       = [
		QT_TRUE_FALSE         => QT_TRUE_FALSE_NUMANSWERS,
		QT_MULTIPLE_CHOICE_3  => QT_MULTIPLE_CHOICE_3_NUMANSWERS,
		QT_MULTIPLE_REPONSE_3 => QT_MULTIPLE_REPONSE_3_NUMANSWERS,
		QT_MULTIPLE_CHOICE_4  => QT_MULTIPLE_CHOICE_4_NUMANSWERS,
		QT_MULTIPLE_REPONSE_4 => QT_MULTIPLE_REPONSE_4_NUMANSWERS,
	];
	$mediaPlaceholder = [
		"name"     => "",
		"type"     => "",
		"tmp_name" => "",
		"error"    => 4,
		"size"     => 0
	];

	for ($q = 1; $q <= $numAnswers[(int) $jcpqmQuestion->questiontype]; $q++)
	{
		$data["answers_text"][]             = sprintf("%s%d", $JtextBase, $q);
		$data["answers_comments"][]         = $jcpqmQuestion->{'q_a' . $q};
		$data["answers_iscorrect_hidden"][] = $jcpqmQuestion->{'q_a' . $q . "c"};
		$data["answers_iscorrect"][]        = ANSWER_INCORRECT;
		$data["answers_marks"][]            = $jcpqmQuestion->{'q_m' . $q};
		$data["answer_id_hidden"][]         = "0";
		$data["answer_media_type"][]        = "0";
		$data["answer_media_video"][]       = "0";
		$data["answer_media_image"][]       = $mediaPlaceholder;
		$data["answer_media_audio"][]       = $mediaPlaceholder;
		$data["answer_media_file"][]        = $mediaPlaceholder;
	}
	if ( (int)$jcpqmQuestion->questiontype == QT_TRUE_FALSE)
	{
		$tf = (int)$jcpqmQuestion->q_atf;
		if ( $tf == 1 )
		{
			$data["answers_marks"][0]            = MAX_MARKS;
			$data["answers_marks"][1]            = NO_MARKS;
			$data["answers_iscorrect_hidden"][0] = ANSWER_SELECTED;
			$data["answers_iscorrect_hidden"][1] = ANSWER_NOT_SELECTED;
		}
		else
		{
			$data["answers_marks"][0]            = NO_MARKS;
			$data["answers_marks"][1]            = MAX_MARKS;
			$data["answers_iscorrect_hidden"][0] = ANSWER_NOT_SELECTED;
			$data["answers_iscorrect_hidden"][1] = ANSWER_SELECTED;
		}
		$data["answers_text"][0]             = 'JTRUE';
		$data["answers_text"][1]             = 'JFALSE';
		$data["answers_comments"][0]         = $data["answers_text"][0];
		$data["answers_comments"][1]         = $data["answers_text"][1];

	}
//	$data["mediaFiles"]["media_file"][]  = $mediaPlaceholder;
//	$data["mediaFiles"]["media_image"][] = $mediaPlaceholder;
//	$data["mediaFiles"]["media_audio"][] = $mediaPlaceholder;
	$data["state"]=1;
	$id_or_error = $tmtQuestion->save($data);

	if ( $id_or_error != false )
	{
		if ($id_or_error==$tmtQuestionId){
			// saved existing
			$id_or_error = (int)$id_or_error; // id that was saved
		}
		else {
			// saved new, now update "parent" with
			$table=$this->getTable();
			$table->load(['id'=>$questionId]);
			$table->set('shikaquestion',(int)$id_or_error);
			$table->store();
		}
	}
	return $id_or_error;

}
/* 
*  MDI model QUESTION :: END
*   
END END END END END END END END END END END END END END END END END END END END END END END END */
    
	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed  Object on success, false on failure.
	 *
	 * @since   1.6
	 */
	public function getItem($pk = null)
	{
		if ($item = parent::getItem($pk))
		{
			if (!empty($item->params) && !is_array($item->params))
			{
				// Convert the params field to an array.
				$registry = new Registry;
				$registry->loadString($item->params);
				$item->params = $registry->toArray();
			}

			if (!empty($item->metadata))
			{
				// Convert the metadata field to an array.
				$registry = new Registry;
				$registry->loadString($item->metadata);
				$item->metadata = $registry->toArray();
			}
			
			if (!empty($item->id))
			{
				$item->tags = new JHelperTags;
				$item->tags->getTagIds($item->id, 'com_jcpqm.question');
			}
		}

		return $item;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 * @param   array    $options   Optional array of options for the form creation.
	 *
	 * @return  mixed  A JForm object on success, false on failure
	 *
	 * @since   1.6
	 */
	public function getForm($data = array(), $loadData = true, $options = array('control' => 'jform'))
	{
		// set load data option
		$options['load_data'] = $loadData;
		// Get the form.
		$form = $this->loadForm('com_jcpqm.question', 'question', $options);

		if (empty($form))
		{
			return false;
		}

		$jinput = JFactory::getApplication()->input;

		// The front end calls this model and uses a_id to avoid id clashes so we need to check for that first.
		if ($jinput->get('a_id'))
		{
			$id = $jinput->get('a_id', 0, 'INT');
		}
		// The back end uses id so we use that the rest of the time and set it to 0 by default.
		else
		{
			$id = $jinput->get('id', 0, 'INT');
		}

		$user = JFactory::getUser();

		// Check for existing item.
		// Modify the form based on Edit State access controls.
		if ($id != 0 && (!$user->authorise('question.edit.state', 'com_jcpqm.question.' . (int) $id))
			|| ($id == 0 && !$user->authorise('question.edit.state', 'com_jcpqm')))
		{
			// Disable fields for display.
			$form->setFieldAttribute('ordering', 'disabled', 'true');
			$form->setFieldAttribute('published', 'disabled', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('ordering', 'filter', 'unset');
			$form->setFieldAttribute('published', 'filter', 'unset');
		}
		// If this is a new item insure the greated by is set.
		if (0 == $id)
		{
			// Set the created_by to this user
			$form->setValue('created_by', null, $user->id);
		}
		// Modify the form based on Edit Creaded By access controls.
		if (!$user->authorise('core.edit.created_by', 'com_jcpqm'))
		{
			// Disable fields for display.
			$form->setFieldAttribute('created_by', 'disabled', 'true');
			// Disable fields for display.
			$form->setFieldAttribute('created_by', 'readonly', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('created_by', 'filter', 'unset');
		}
		// Modify the form based on Edit Creaded Date access controls.
		if (!$user->authorise('core.edit.created', 'com_jcpqm'))
		{
			// Disable fields for display.
			$form->setFieldAttribute('created', 'disabled', 'true');
			// Disable fields while saving.
			$form->setFieldAttribute('created', 'filter', 'unset');
		}
		// Only load these values if no id is found
		if (0 == $id)
		{
			// Set redirected view name
			$redirectedView = $jinput->get('ref', null, 'STRING');
			// Set field name (or fall back to view name)
			$redirectedField = $jinput->get('field', $redirectedView, 'STRING');
			// Set redirected view id
			$redirectedId = $jinput->get('refid', 0, 'INT');
			// Set field id (or fall back to redirected view id)
			$redirectedValue = $jinput->get('field_id', $redirectedId, 'INT');
			if (0 != $redirectedValue && $redirectedField)
			{
				// Now set the local-redirected field default value
				$form->setValue($redirectedField, null, $redirectedValue);
			}
		}
/* BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN
*
*  TODO MDI getform Question :: BEGIN
*
*/
		$app        = JFactory::getApplication();
		$isSite     = JFactory::getApplication()->isClient('site');
		$isAdmin    = JFactory::getApplication()->isClient('administrator');
		$formCreate = ($id == ID_NEW);

		// introduce b42 in $this to introduce "caluclated" values to postSaveHook in controller
		$this->set("b42",new stdClass());

		if ( $formCreate )
		{
			$fieldname    = 'uuid';
			$form->setValue( $fieldname, null, uniqid("q") );
			$form->setFieldAttribute($fieldname, 'readonly', 'true');

			$fieldname    = 'alias';
			$form->setValue( $fieldname, null, uniqid("a") );
			$form->setFieldAttribute($fieldname, 'readonly', 'true');

			$fieldname    = 'questiontype';
			$form->setValue( $fieldname, null, QT_MULTIPLE_CHOICE_4 );

			$fieldname    = 'shikaquestion';
			$form->setFieldAttribute($fieldname, 'readonly', 'true');
			$form->setFieldAttribute($fieldname, 'button', 'false');

			$fieldname    = 'shikaquestion';
			$form->setFieldAttribute($fieldname, 'readonly', 'true');
			$form->setFieldAttribute($fieldname, 'button', 'false');
		}

		$fieldname    = 'synced';
		$form->setFieldAttribute($fieldname, 'read-only', true);

		$examId = (int)$form->getValue('exam');

		$path="";
		$examQuestionCategory = CATEGORY_ROOT;

		// get to the category record information, and its alias
		$questionCategories=new	\Joomla\CMS\Categories\Categories(array(
			'extension'  => 'com_jcpqm.question',
			'table'      => 'categories'
		));

		if ($examId > ID_NEW)
		{
			/** @var  $examModel \JcpqmModelExam */
			$examModel = \JcpqmHelper::getModel('exam');
			$examItem = $examModel->getItem($examId);
			$examQuestionCategory = (int)$examItem->basecatidquestions;
		};

		// Find the published category
		$questionCategoryItem=$questionCategories->get(CATEGORY_ROOT );
		foreach( $questionCategoryItem->getChildren(false) as $catlevel1 )
		{
			if ( $catlevel1->get('path') == "published")
			{
				$catPublished = $catlevel1;
				break;
			}
		}

		// find the "name="catidplus"" element, it is used as a template
		$fieldname="catidplus";
		$xpath  = "//field[@name=\"".$fieldname."\"]";
		$xmlBasecatidquestions = $form->getXml()->xpath($xpath);

		$cssStyle="";
		$catid = $form->getValue('catid');
		// get all level 3, for the categories in published, now generate and append list selectors
		foreach( $catPublished->getChildren(false) as $catlevel2 )
		{
			foreach ($catlevel2->getChildren(false) as $item)
			{
				$id                = (int) $item->get('id');
				$newName           = 'catidplus' . $id;
				$newEl             = new SimpleXMLElement($xmlBasecatidquestions[0]->asXML());
				// we must save it as a different unique name
				$newEl['name']     = $newName;
			    // "save as" after current element
			    $this->simplexml_insert_after($newEl,$xmlBasecatidquestions[0]);
			    // set the field up to be rendered in the layout
				$this->inserFieldAfter($newName, 'catidplus', 'details', 'left');

				// change attributes to make them different catidplus lists
				$newClass=$this->newClass(
					$form->getFieldAttribute($newName, 'class'),
			        array('catidplus',"id" . $id),
			        $form->getFieldAttribute($newName,'required'),
			        ['id'=>$id]
				);
				//$form->setFieldAttribute($newName, 'label', $newName);

				$form->setFieldAttribute($newName, 'default', $catid);
				$form->setFieldAttribute($newName, 'class', $newClass);
				$form->setFieldAttribute($newName, 'rootpath', $item->get("path"));

				$cssStyle.=".control-wrapper-".$newName."{display:none}\n";
			}
		}
		$cssStyle.=".control-wrapper-catid{display:none}\n";
		$document=JFactory::getDocument();
		$document->addStyleDeclaration( $cssStyle );
		if ($examQuestionCategory>0)
		{
			// querySelector get the first .class as where getElementsByClassName gets "all"
			$document->addScriptDeclaration(<<< EOD
document.addEventListener("DOMContentLoaded", function(event) {
    var el = document.querySelector('.control-wrapper-catidplus$examQuestionCategory' );
    el.style.cssText = 'display:block'
    
	document.getElementById("jform_catidplus66").onchange = function (el) {
	   console.log( "onchange jform_catidplus66")
	};
    
});
EOD
//var el = document.getElementsByClassName('control-wrapper-catidplus$examQuestionCategory' );

			);
		};


		$form->removeField('catidplus');
		// setting to hidden does not work for "lists", it would still show the input
		// $form->setFieldAttribute('catid',"hidden",true);

		return $form;
		// document.getElementById("jformEaxam").addEventListener('click', makeRequest);

	}

	private function newClass($oldClass, $class2add, $required=false, $dataAttributes = array(), $styles = array())
	{
		$return = $oldClass;
		$return .= $required ? " required" : ""; // as we are tricking the fieldrenderer we need to take care of this
		$return .= " ". implode(" ", $class2add);
		$hasStyles         = is_array($styles) && sizeof($styles) > 0;
		$hasDataAttributes = is_array($dataAttributes) && sizeof($dataAttributes) > 0;
		if ( !$hasStyles && !$hasDataAttributes) {
			return $return;
		}
		// we are "hacking", escape the class
		$return .= "\" ";
		if ($hasDataAttributes){
			foreach( $dataAttributes as $key => $value )
			{
				$return .= " data-" . $key ."=\"" . $value ."\"";
			}
		}
		if ($hasStyles){
			$return.=" style=\"";
			foreach( $styles as $key => $value )
			{
				$return .= $key .":" . $value . ";";
			}
		    $return .= "\"";
		}
		if ( $required )
		{
			$return .= " dummy=\" ";
		}
		return $return;
	}

	public function removeField($value, $tab, $section)
	{
		$section=strtolower($section);
		// when field not in $fieldsXXXX it is never redered, basically it is not in the form
		$key = array_search ($value, $this->tabLayoutFields[$tab][$section]);
		if ( $key !== false && is_int($key)) {
			unset( $this->tabLayoutFields[$tab][$section][$key]);
		}
	}

	private function insertField($insert, $tab, $section, $key)
	{
		$this->tabLayoutFields[$tab][$section] = array_merge(
			array_slice($this->tabLayoutFields[$tab][$section], 0, $key),
			array($insert),
			array_slice($this->tabLayoutFields[$tab][$section], $key)
		);
	}

	public function inserFieldBefore($insert, $tab, $anchor, $section)
	{
		$this->insertField($insert,
			$tab,
			strtolower($section),
			array_search ($anchor, $this->tabLayoutFields[$tab][strtolower($section)])
		);
	}

	public function inserFieldAfter($insert, $anchor, $tab, $section)
	{
		$this->insertField($insert,
			$tab,
			strtolower($section),
			1 + array_search ($anchor, $this->tabLayoutFields[$tab][strtolower($section)])
		);
	}

	public function getFieldsModel($tab, $section)
	{
		return $this->tabLayoutFields[$tab][strtolower($section)];
	}

	public function getFields_details_left()
	{
		// we need this to be called from the layout! as the the get call cannot be passed parameters
		return $this->getFieldsModel( 'details', 'Left');
	}

	private function simplexml_insert_after(SimpleXMLElement $insert, SimpleXMLElement $target)
	{
		$target_dom = dom_import_simplexml($target);
		$insert_dom = $target_dom->ownerDocument->importNode(dom_import_simplexml($insert), true);
		if ($target_dom->nextSibling)
		{
			return $target_dom->parentNode->insertBefore($insert_dom, $target_dom->nextSibling);
		}
		else
		{
			return $target_dom->parentNode->appendChild($insert_dom);
		}
	}

	private function dummy($form)
	{
/*
*  MDI MDI getform Question :: END
*
END END END END END END END END END END END END END END END END END END END END END END END END */
		return $form;
	}

	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	script files
	 */
	public function getScript()
	{
		return 'administrator/components/com_jcpqm/models/forms/question.js';
	}
    
	/**
	 * Method to test whether a record can be deleted.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to delete the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canDelete($record)
	{
		if (!empty($record->id))
		{
			if ($record->published != -2)
			{
				return;
			}

			$user = JFactory::getUser();
			// The record has been set. Check the record permissions.
			return $user->authorise('question.delete', 'com_jcpqm.question.' . (int) $record->id);
		}
		return false;
	}

	/**
	 * Method to test whether a record can have its state edited.
	 *
	 * @param   object  $record  A record object.
	 *
	 * @return  boolean  True if allowed to change the state of the record. Defaults to the permission set in the component.
	 *
	 * @since   1.6
	 */
	protected function canEditState($record)
	{
		$user = JFactory::getUser();
		$recordId = (!empty($record->id)) ? $record->id : 0;

		if ($recordId)
		{
			// The record has been set. Check the record permissions.
			$permission = $user->authorise('question.edit.state', 'com_jcpqm.question.' . (int) $recordId);
			if (!$permission && !is_null($permission))
			{
				return false;
			}
		}
		// In the absense of better information, revert to the component permissions.
		return $user->authorise('question.edit.state', 'com_jcpqm');
	}
    
	/**
	 * Method override to check if you can edit an existing record.
	 *
	 * @param	array	$data	An array of input data.
	 * @param	string	$key	The name of the key for the primary key.
	 *
	 * @return	boolean
	 * @since	2.5
	 */
	protected function allowEdit($data = array(), $key = 'id')
	{
		// Check specific edit permission then general edit permission.
		$user = JFactory::getUser();

		return $user->authorise('question.edit', 'com_jcpqm.question.'. ((int) isset($data[$key]) ? $data[$key] : 0)) or $user->authorise('question.edit',  'com_jcpqm');
	}
    
	/**
	 * Prepare and sanitise the table data prior to saving.
	 *
	 * @param   JTable  $table  A JTable object.
	 *
	 * @return  void
	 *
	 * @since   1.6
	 */
	protected function prepareTable($table)
	{
		$date = JFactory::getDate();
		$user = JFactory::getUser();
		
		if (isset($table->name))
		{
			$table->name = htmlspecialchars_decode($table->name, ENT_QUOTES);
		}
		
		if (isset($table->alias) && empty($table->alias))
		{
			$table->generateAlias();
		}
		
		if (empty($table->id))
		{
			$table->created = $date->toSql();
			// set the user
			if ($table->created_by == 0 || empty($table->created_by))
			{
				$table->created_by = $user->id;
			}
			// Set ordering to the last item if not set
			if (empty($table->ordering))
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true)
					->select('MAX(ordering)')
					->from($db->quoteName('#__jcpqm_question'));
				$db->setQuery($query);
				$max = $db->loadResult();

				$table->ordering = $max + 1;
			}
		}
		else
		{
			$table->modified = $date->toSql();
			$table->modified_by = $user->id;
		}
        
		if (!empty($table->id))
		{
			// Increment the items version number.
			$table->version++;
		}
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 *
	 * @since   1.6
	 */
	protected function loadFormData() 
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jcpqm.edit.question.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to validate the form data.
	 *
	 * @param   JForm   $form   The form to validate against.
	 * @param   array   $data   The data to validate.
	 * @param   string  $group  The name of the field group to validate.
	 *
	 * @return  mixed  Array of filtered data if valid, false otherwise.
	 *
	 * @see     JFormRule
	 * @see     JFilterInput
	 * @since   12.2
	 */
	public function validate($form, $data, $group = null)
	{
		// check if the not_required field is set
		if (JcpqmHelper::checkString($data['not_required']))
		{
			$requiredFields = (array) explode(',',(string) $data['not_required']);
			$requiredFields = array_unique($requiredFields);
			// now change the required field attributes value
			foreach ($requiredFields as $requiredField)
			{
				// make sure there is a string value
				if (JcpqmHelper::checkString($requiredField))
				{
					// change to false
					$form->setFieldAttribute($requiredField, 'required', 'false');
					// also clear the data set
					$data[$requiredField] = '';
				}
			}
		}
		return parent::validate($form, $data, $group);
	}

	/**
	 * Method to get the unique fields of this table.
	 *
	 * @return  mixed  An array of field names, boolean false if none is set.
	 *
	 * @since   3.0
	 */
	protected function getUniqeFields()
	{
		return array('uuid');
	}
	
	/**
	 * Method to delete one or more records.
	 *
	 * @param   array  &$pks  An array of record primary keys.
	 *
	 * @return  boolean  True if successful, false if an error occurs.
	 *
	 * @since   12.2
	 */
	public function delete(&$pks)
	{
		if (!parent::delete($pks))
		{
			return false;
		}
		
		return true;
	}

	/**
	 * Method to change the published state of one or more records.
	 *
	 * @param   array    &$pks   A list of the primary keys to change.
	 * @param   integer  $value  The value of the published state.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   12.2
	 */
	public function publish(&$pks, $value = 1)
	{
		if (!parent::publish($pks, $value))
		{
			return false;
		}
/***[INSERTED$$$$]***//*5*/
		$this->publish_shika($pks, $value);
/***[/INSERTED$$$$]***/
		
		return true;
        }
    
	/**
	 * Method to perform batch operations on an item or a set of items.
	 *
	 * @param   array  $commands  An array of commands to perform.
	 * @param   array  $pks       An array of item ids.
	 * @param   array  $contexts  An array of item contexts.
	 *
	 * @return  boolean  Returns true on success, false on failure.
	 *
	 * @since   12.2
	 */
	public function batch($commands, $pks, $contexts)
	{
		// Sanitize ids.
		$pks = array_unique($pks);
		JArrayHelper::toInteger($pks);

		// Remove any values of zero.
		if (array_search(0, $pks, true))
		{
			unset($pks[array_search(0, $pks, true)]);
		}

		if (empty($pks))
		{
			$this->setError(JText::_('JGLOBAL_NO_ITEM_SELECTED'));
			return false;
		}

		$done = false;

		// Set some needed variables.
		$this->user			= JFactory::getUser();
		$this->table			= $this->getTable();
		$this->tableClassName		= get_class($this->table);
		$this->contentType		= new JUcmType;
		$this->type			= $this->contentType->getTypeByTable($this->tableClassName);
		$this->canDo			= JcpqmHelper::getActions('question');
		$this->batchSet			= true;

		if (!$this->canDo->get('core.batch'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));
			return false;
		}
        
		if ($this->type == false)
		{
			$type = new JUcmType;
			$this->type = $type->getTypeByAlias($this->typeAlias);
		}

		$this->tagsObserver = $this->table->getObserverOfClass('JTableObserverTags');

		if (!empty($commands['move_copy']))
		{
			$cmd = JArrayHelper::getValue($commands, 'move_copy', 'c');

			if ($cmd == 'c')
			{
				$result = $this->batchCopy($commands, $pks, $contexts);

				if (is_array($result))
				{
					foreach ($result as $old => $new)
					{
						$contexts[$new] = $contexts[$old];
					}
					$pks = array_values($result);
				}
				else
				{
					return false;
				}
			}
			elseif ($cmd == 'm' && !$this->batchMove($commands, $pks, $contexts))
			{
				return false;
			}

			$done = true;
		}

		if (!$done)
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_INSUFFICIENT_BATCH_INFORMATION'));

			return false;
		}

		// Clear the cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Batch copy items to a new category or current.
	 *
	 * @param   integer  $values    The new values.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  mixed  An array of new IDs on success, boolean false on failure.
	 *
	 * @since 12.2
	 */
	protected function batchCopy($values, $pks, $contexts)
	{
		if (empty($this->batchSet))
		{
			// Set some needed variables.
			$this->user 		= JFactory::getUser();
			$this->table 		= $this->getTable();
			$this->tableClassName	= get_class($this->table);
			$this->canDo		= JcpqmHelper::getActions('question');
		}

		if (!$this->canDo->get('question.create') && !$this->canDo->get('question.batch'))
		{
			return false;
		}

		// get list of uniqe fields
		$uniqeFields = $this->getUniqeFields();
		// remove move_copy from array
		unset($values['move_copy']);

		// make sure published is set
		if (!isset($values['published']))
		{
			$values['published'] = 0;
		}
		elseif (isset($values['published']) && !$this->canDo->get('question.edit.state'))
		{
				$values['published'] = 0;
		}

		if (isset($values['category']) && (int) $values['category'] > 0 && !static::checkCategoryId($values['category']))
		{
			return false;
		}
		elseif (isset($values['category']) && (int) $values['category'] > 0)
		{
			// move the category value to correct field name
			$values['catid'] = $values['category'];
			unset($values['category']);
		}
		elseif (isset($values['category']))
		{
			unset($values['category']);
		}

		$newIds = array();
		// Parent exists so let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$this->table->reset();

			// only allow copy if user may edit this item.
			if (!$this->user->authorise('question.edit', $contexts[$pk]))
			{
				// Not fatal error
				$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
				continue;
			}

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			if (isset($values['catid']))
			{
				list($this->table->question_title, $this->table->alias) = $this->generateNewTitle($values['catid'], $this->table->alias, $this->table->question_title);
			}
			else
			{
				list($this->table->question_title, $this->table->alias) = $this->generateNewTitle($this->table->catid, $this->table->alias, $this->table->question_title);
			}

			// insert all set values
			if (JcpqmHelper::checkArray($values))
			{
				foreach ($values as $key => $value)
				{
					if (strlen($value) > 0 && isset($this->table->$key))
					{
						$this->table->$key = $value;
					}
				}
			}

			// update all uniqe fields
			if (JcpqmHelper::checkArray($uniqeFields))
			{
				foreach ($uniqeFields as $uniqeField)
				{
					$this->table->$uniqeField = $this->generateUniqe($uniqeField,$this->table->$uniqeField);
				}
			}

			// Reset the ID because we are making a copy
			$this->table->id = 0;

			// TODO: Deal with ordering?
			// $this->table->ordering = 1;

			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());

				return false;
			}

			if (!empty($this->type))
			{
				$this->createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);
			}

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());

				return false;
			}

			// Get the new item ID
			$newId = $this->table->get('id');

			// Add the new ID to the array
			$newIds[$pk] = $newId;
		}

		// Clean the cache
		$this->cleanCache();

		return $newIds;
	}

	/**
	 * Batch move items to a new category
	 *
	 * @param   integer  $value     The new category ID.
	 * @param   array    $pks       An array of row IDs.
	 * @param   array    $contexts  An array of item contexts.
	 *
	 * @return  boolean  True if successful, false otherwise and internal error is set.
	 *
	 * @since 12.2
	 */
	protected function batchMove($values, $pks, $contexts)
	{
		if (empty($this->batchSet))
		{
			// Set some needed variables.
			$this->user		= JFactory::getUser();
			$this->table		= $this->getTable();
			$this->tableClassName	= get_class($this->table);
			$this->canDo		= JcpqmHelper::getActions('question');
		}

		if (!$this->canDo->get('question.edit') && !$this->canDo->get('question.batch'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// make sure published only updates if user has the permission.
		if (isset($values['published']) && !$this->canDo->get('question.edit.state'))
		{
			unset($values['published']);
		}
		// remove move_copy from array
		unset($values['move_copy']);

		if (isset($values['category']) && (int) $values['category'] > 0 && !static::checkCategoryId($values['category']))
		{
			return false;
		}
		elseif (isset($values['category']) && (int) $values['category'] > 0)
		{
			// move the category value to correct field name
			$values['catid'] = $values['category'];
			unset($values['category']);
		}
		elseif (isset($values['category']))
		{
			unset($values['category']);
		}


		// Parent exists so we proceed
		foreach ($pks as $pk)
		{
			if (!$this->user->authorise('question.edit', $contexts[$pk]))
			{
				$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
				return false;
			}

			// Check that the row actually exists
			if (!$this->table->load($pk))
			{
				if ($error = $this->table->getError())
				{
					// Fatal error
					$this->setError($error);
					return false;
				}
				else
				{
					// Not fatal error
					$this->setError(JText::sprintf('JLIB_APPLICATION_ERROR_BATCH_MOVE_ROW_NOT_FOUND', $pk));
					continue;
				}
			}

			// insert all set values.
			if (JcpqmHelper::checkArray($values))
			{
				foreach ($values as $key => $value)
				{
					// Do special action for access.
					if ('access' === $key && strlen($value) > 0)
					{
						$this->table->$key = $value;
					}
					elseif (strlen($value) > 0 && isset($this->table->$key))
					{
						$this->table->$key = $value;
					}
				}
			}


			// Check the row.
			if (!$this->table->check())
			{
				$this->setError($this->table->getError());

				return false;
			}

			if (!empty($this->type))
			{
				$this->createTagsHelper($this->tagsObserver, $this->type, $pk, $this->typeAlias, $this->table);
			}

			// Store the row.
			if (!$this->table->store())
			{
				$this->setError($this->table->getError());

				return false;
			}
		}

		// Clean the cache
		$this->cleanCache();

		return true;
	}
	
	/**
	 * Method to save the form data.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success.
	 *
	 * @since   1.6
	 */
	public function save($data)
	{
		$input	= JFactory::getApplication()->input;
		$filter	= JFilterInput::getInstance();
        
		// set the metadata to the Item Data
		if (isset($data['metadata']) && isset($data['metadata']['author']))
		{
			$data['metadata']['author'] = $filter->clean($data['metadata']['author'], 'TRIM');
            
			$metadata = new JRegistry;
			$metadata->loadArray($data['metadata']);
			$data['metadata'] = (string) $metadata;
		}

/* BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN
*
*  TODO MDI BLOCK save before data modelling Question :: BEGIN
*
*/

		if ( ! function_exists('cvalidateAnswers' ) )
		{
			/**
			 * @param      $data
			 * @param int  $numberOfAnswers
			 * @param bool $multipleChoice
			 *
			 * @return string
			 *
			 * @since version
			 */
		function cvalidateAnswers(&$data, $numberOfAnswers = QT_MAX_NUMANSWERS, $multipleChoice = true) {
			$totalmarks   = NO_MARKS;
			$totalchecked = NO_MARKS;
			for ($i = 1; $i <= $numberOfAnswers; $i++)
			{
				if ((int) $data["q_a" . $i . "c"] != ANSWER_SELECTED )
				{
					$data["q_m" . $i] = NO_MARKS;
					continue;
				}
				$data["q_m" . $i]  = MAX_MARKS;
				$totalmarks       += MAX_MARKS;
				$totalchecked     += 1;
			}
			for ($i = $numberOfAnswers + 1; $i <= MAX_ANSWERS; $i++)
			{
				$data["q_m" . $i]       = NO_MARKS;
				$data["q_a" . $i . "c"] = ANSWER_NOT_SELECTED;
			}

			if (($totalmarks == NO_MARKS ))
			{
				return "</br>At least one answer needs to be selected";
			}
			if ($multipleChoice)
			{
				if ($totalchecked != 1)
				{
					return "</br>MultipleChoice: Only one answer may be selected";
				}
			}
			elseif ($totalchecked > 1) // multiple answer
			{
				for ($i = 1; $i <= $numberOfAnswers; $i++)
				{
					if ((int) $data["q_a" . $i . "c"] === ANSWER_SELECTED )
					{
						$data["q_m" . $i] = (int) (MAX_MARKS / $totalchecked);
					}
					else
					{
						$data["q_m" . $i] = NO_MARKS;
					}
				}
			}

			return true;
		}
		};

		if ( array_key_exists('force_catid', $data ) )
		{
			// this when we import the "absolute" catid
			$data['catid']=$data['force_catid'];
			unset($data['force_catid']);
		}
		else
		{
			/** @var $modelExam \JcpqmModelExam */
			$modelExam = JcpqmHelper::getModel('Exam');
			$item=$modelExam->getItem( (int) $data["exam"] );
			$catBase = (int) $item->basecatidquestions;
			$data['catid']=$data['catidplus'.$catBase];
		}

		if (in_array($input->get('task'), array('apply', 'save', 'save2new')))
		{
			$return=false;
			switch ( (int)$data["questiontype"] )
			{
				case QT_TRUE_FALSE: // true false
					if ( $data["q_atf"] )
					{
						$data["q_a1"] = "JTRUE";
						$data["q_m1"] = MAX_MARKS;
						$data["q_a2"] = "JFALSE";
						$data["q_m2"] = NO_MARKS;
					}
					else {
						$data["q_a1"] = "JFALSE";
						$data["q_m1"] = NO_MARKS;
						$data["q_a2"] = "JTRUE";
						$data["q_m2"] = MAX_MARKS;
					}
					$return=true;
					break;
				case QT_MULTIPLE_CHOICE_3; // multiple choice 3
					$return=cvalidateAnswers($data,QT_MULTIPLE_CHOICE_3_NUMANSWERS,true);
					break;
				case QT_MULTIPLE_REPONSE_3; // multiple reponse 3
					$return=cvalidateAnswers($data,QT_MULTIPLE_REPONSE_3_NUMANSWERS,false);
					break;
				case QT_MULTIPLE_CHOICE_4; // multiple choice 4
					$return=cvalidateAnswers($data,QT_MULTIPLE_CHOICE_4_NUMANSWERS,true);
					break;
				case QT_MULTIPLE_REPONSE_4; // multiple reponse 4
					$return=cvalidateAnswers($data,QT_MULTIPLE_REPONSE_4_NUMANSWERS,false);
					break;
			}
			if ( $return !== true ) {
				$this->setError("Answer not compliant with question type." . $return);
				return false;
			}
		}

		if (in_array($input->get('task'), array('apply', 'save')))
		{
			if ( $data['id']==ID_NEW)
			{
				$data['synced'] = SYNC_NOT_LINKED;
			}
			else
			{
				// check if somethings actually changed
				/** @var  $checkModel \JcpqmModelQuestion */
				$checkModel = \JcpqmHelper::getModel('question');
				$dataInDB   = (array) $checkModel->getItem($data["id"]);

				foreach (array("\0*\0_errors", 'params', 'metadata', 'tags') as $field)
				{
					unset($dataInDB[$field]);
				}
				$xx = array_diff_assoc($data, $dataInDB);
				foreach ($xx as $key => $value)
				{
					if (in_array($key, array('version', 'not_required', 'metadata', 'rules', 'tags', 'asset_id', 'synced'))
						|| (preg_match('/(catidplus\d+)|(q_m\d)/', $key) == 1)
					)
					{
						unset($xx[$key]);
						continue;
					}
				};
				if ( sizeof(($xx)) > 0)
				{
					$data['synced'] = SYNC_LINKED_WITH_LOCAL_CHANGES;
				}
				else
				{
					$data['synced'] = SYNC_LINKED;
				}
			}
		}

/* 
*  TODO MDI BLOCK save before data modelling Question :: END
*   
END END END END END END END END END END END END END END END END END END END END END END END END */
        
		// Set the Params Items to data
		if (isset($data['params']) && is_array($data['params']))
		{
			$params = new JRegistry;
			$params->loadArray($data['params']);
			$data['params'] = (string) $params;
		}

		// Alter the question_title for save as copy
		if ($input->get('task') === 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

			if ($data['question_title'] == $origTable->question_title)
			{
				list($question_title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['question_title']);
				$data['question_title'] = $question_title;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->alias)
				{
					$data['alias'] = '';
				}
			}

			$data['published'] = 0;
		}

		// Automatic handling of alias for empty fields
		if (in_array($input->get('task'), array('apply', 'save', 'save2new')) && (int) $input->get('id') == 0)
		{
			if ($data['alias'] == null || empty($data['alias']))
			{
				if (JFactory::getConfig()->get('unicodeslugs') == 1)
				{
					$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['question_title']);
				}
				else
				{
					$data['alias'] = JFilterOutput::stringURLSafe($data['question_title']);
				}

				$table = JTable::getInstance('question', 'jcpqmTable');

				if ($table->load(array('alias' => $data['alias'], 'catid' => $data['catid'])) && ($table->id != $data['id'] || $data['id'] == 0))
				{
					$msg = JText::_('COM_JCPQM_QUESTION_SAVE_WARNING');
				}

				list($question_title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['question_title']);
				$data['alias'] = $alias;

				if (isset($msg))
				{
					JFactory::getApplication()->enqueueMessage($msg, 'warning');
				}
			}
		}

		// Alter the uniqe field for save as copy
		if ($input->get('task') === 'save2copy')
		{
			// Automatic handling of other uniqe fields
			$uniqeFields = $this->getUniqeFields();
			if (JcpqmHelper::checkArray($uniqeFields))
			{
				foreach ($uniqeFields as $uniqeField)
				{
					$data[$uniqeField] = $this->generateUniqe($uniqeField,$data[$uniqeField]);
				}
			}
		}
		
		if (parent::save($data))
		{
			return true;
		}
		return false;
	}
	
	/**
	 * Method to generate a uniqe value.
	 *
	 * @param   string  $field name.
	 * @param   string  $value data.
	 *
	 * @return  string  New value.
	 *
	 * @since   3.0
	 */
	protected function generateUniqe($field,$value)
	{

		// set field value uniqe 
		$table = $this->getTable();

		while ($table->load(array($field => $value)))
		{
			$value = JString::increment($value);
		}

		return $value;
	}

	/**
	 * Method to change the title/s & alias.
	 *
	 * @param   string         $alias        The alias.
	 * @param   string/array   $title        The title.
	 *
	 * @return	array/string  Contains the modified title/s and/or alias.
	 *
	 */
	protected function _generateNewTitle($alias, $title = null)
	{

		// Alter the title/s & alias
		$table = $this->getTable();

		while ($table->load(array('alias' => $alias)))
		{
			// Check if this is an array of titles
			if (JcpqmHelper::checkArray($title))
			{
				foreach($title as $nr => &$_title)
				{
					$_title = JString::increment($_title);
				}
			}
			// Make sure we have a title
			elseif ($title)
			{
				$title = JString::increment($title);
			}
			$alias = JString::increment($alias, 'dash');
		}
		// Check if this is an array of titles
		if (JcpqmHelper::checkArray($title))
		{
			$title[] = $alias;
			return $title;
		}
		// Make sure we have a title
		elseif ($title)
		{
			return array($title, $alias);
		}
		// We only had an alias
		return $alias;
	}
}
