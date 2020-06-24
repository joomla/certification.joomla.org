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
 * Jcpqm Exam Model
 */
class JcpqmModelExam extends JModelAdmin
{
	/**
	 * The tab layout fields array.
	 *
	 * @var      array
	 */
	protected $tabLayoutFields = array(
		'details' => array(
			'left' => array(
				'key',
				'basecatidquestions',
				'crowdin_info',
				'jcp_info'
			),
			'above' => array(
				'name',
				'alias'
			)
		),
		'crowdin' => array(
			'fullwidth' => array(
				'note_crowdinprojectinfo'
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
	public $typeAlias = 'com_jcpqm.exam';

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
	public function getTable($type = 'exam', $prefix = 'JcpqmTable', $config = array())
	{
		// add table path for when model gets used from other component
		$this->addTablePath(JPATH_ADMINISTRATOR . '/components/com_jcpqm/tables');
		// get instance of the table
		return JTable::getInstance($type, $prefix, $config);
	}

/* BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN
*
*  TODO MDI model EXAM :: BEGIN 
*
*/
	static protected $categoryMap;

	public function getCategoryMap()
	{
		if (!isset(self::$categoryMap))
		{
			return null;
		}
		return self::$categoryMap;
	}

	public function categoryMapping()
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_categories/models');
		JModelLegacy::addTablePath(JPATH_ADMINISTRATOR . '/components/com_categories/tables');
		/* @var $category \CategoriesModelCategory  */
		$category   = JModelLegacy::getInstance('Category', 'CategoriesModel', array('ignore_request' => true));

		$app = JFactory::getApplication();

		/* @var $categoriesModel \CategoriesModelCategories  */
		$categoriesModel = JModelLegacy::getInstance('Categories', 'CategoriesModel', array('ignore_request'   => true));

		// filter.published
		// filter.search
		// filter.
		
		
		
		
		
		// filter.tag
		// list.ordering
		// filter.level     levels <
		define(JCPQM_QUESTION, "jcpqm.question");
		define(TMT_QUESTIONS,  "tmt.questions");
		foreach (array(JCPQM_QUESTION,TMT_QUESTIONS) as $extension)
		{
			$categoriesModel->setState( "filter.extension", "com_".$extension);
			$categoriesModel->setState( "filter.published", STATUS_PUBLISHED);

			$cats[$extension]= $categoriesModel->getItems();
			foreach ($cats[$extension] as $idx => $item2)
			{
				// `reverse` filter.level for only in path of (un)published and make path the key,
				// base categories published / unpublished are assumed to exist
				if ( ( (int)$item2->level >= 1 )
					&& ( ( strpos($item2->path, 'published' ) === STRPOS_ATBEGINNING )
						|| ( strpos($item2->path, 'unpublished') === STRPOS_ATBEGINNING ) ) )
				{
					$cats[$extension][$item2->path]=$item2;
				}
				unset($cats[$extension][$idx]);
			}
		}
		// create missing categories on tmt.questions
		foreach ($cats[JCPQM_QUESTION] as $path => $jcqpmQuestion)
		{
			$jcpqmCatId      = $jcqpmQuestion->id;
			/*
			 * in "published" base category
			 */
			if ( array_key_exists ( $path , $cats[TMT_QUESTIONS] ) )
			{
				$shikaCatId = $cats[TMT_QUESTIONS][$path]->id;
				$map['ToShika'  ][(int) $jcpqmCatId] = (int) $shikaCatId;
				$map['FromShika'][(int) $shikaCatId] = (int) $jcpqmCatId;
			}

			/*
			 * in "unpublished" base category,
			 * we will create the corresponding ones on shika,
			 * however on jcpqm we will use the normal "unpublish" mechanism
			 * Upon "unplushing" a question in jcpqm, it will be moved to the corresponding
			 * "unpublished" category in shika
			 */

			$keyUnPub='un'.$path;
			if ( array_key_exists ( $keyUnPub , $cats[TMT_QUESTIONS] ) )
			{
				$shikaUnPubCatId = $cats[TMT_QUESTIONS][$keyUnPub]->id;
				$map['ToShikaUnPub'][$jcpqmCatId] = (int)$shikaUnPubCatId;
			}
		}
		// create
		foreach ($cats[JCPQM_QUESTION] as $path => $jcqpmQuestion)
		{

			$topPaths=array($path);
			if ( strpos($path, 'unpublished') !== STRPOS_ATBEGINNING )
			{
				$topPaths[]='un'.$path;

			}
			foreach ( $topPaths as $toPath )
			{
				if (!array_key_exists($toPath, $cats[TMT_QUESTIONS]))
				{
					$parent_id=0;
					if ($jcqpmQuestion->level > 1)
					{
						$pathParent = substr($toPath, 0, strlen($toPath) - strlen($cats[JCPQM_QUESTION][$path]->alias) - 1);
						$parent_id = (int) $cats[TMT_QUESTIONS][$pathParent]->id;
					}

					$titleSuffix = $toPath == "published" ? "" : " (unPub)";
					$jcpqmCatId = $cats[JCPQM_QUESTION][$path]->id;
					$data = array(
						"id"            => ID_NEW
					, "hits"            => "0"
					, "parent_id"       => $parent_id
					, "extension"       => "com_tmt.questions"
					, "title"           => ucfirst($cats[JCPQM_QUESTION][$path]->alias) . $titleSuffix
					, "alias"           => $cats[JCPQM_QUESTION][$path]->alias
					, "path"            => $toPath
					, "version_note"    => ""
					, "note"            => ""
					, "description"     => ""
					, "published"       => STATUS_PUBLISHED
					, "access"          => "1"
					, "metadesc"        => ""
					, "metakey"         => ""
					, "created_user_id" => "0"
					, "created_time"    => ""
					, "modified_time"   => ""
					, "language"        => "*"
					);
					$category->save($data);
					$errors=$category->getErrors();
					if ( sizeof($errors)>0)
					{
						$this->setError(sprintf('%s :: <b>%s</b>', $errors[0] , $toPath));
						continue;
					}
					$shikaCatId                       = (int) $category->getState("category.id");
					$cats[TMT_QUESTIONS][$toPath]->id = $shikaCatId;

					if (strpos($toPath, 'un') !== STRPOS_ATBEGINNING )
					{
						$map['ToShika'][(int) $jcpqmCatId]   = (int) $shikaCatId;
						$map['FromShika'][(int) $shikaCatId] = (int) $jcpqmCatId;
					}
					else
					{
						$map['ToShikaUnPub'][$jcpqmCatId] = (int) $shikaCatId;
					}
				}
			}
		}

		$errors=$this->getErrors();
		if ( sizeof($errors) >0)
		{
			foreach( $errors as $error)
			{
				$app->enqueueMessage($errors, 'error');
			}
		}
		else{
			$app->enqueueMessage("categories synced up to shika successfullly !");
		}

		// for list selection, know which child items be made selectable
		foreach( $cats[JCPQM_QUESTION] as $key => $cat)
		{
			if ((int) $cat->level == 3) {
				$markfor=$key;
				$map["section"]["l3"][]=$markfor;
				continue;
			}
			if ($markfor.'/'.$cat->alias == $cat->path)
			{
				$map['section']['allow'][$markfor][]=(int)$cat->id;
			}
		}
		// for list selection, know which parent items to "hide"
		foreach($map["section"]["l3"] as $itemL3)
		{
			$pathParts = explode('/',$itemL3);
			$map["section"]['hide'][$itemL3][]=(int)$cats[JCPQM_QUESTION][$pathParts[0]]->id;
			$map["section"]['hide'][$itemL3][]=(int)$cats[JCPQM_QUESTION][$pathParts[0].'/'.$pathParts[1]]->id;
			$map["section"]['hide'][$itemL3][]=(int)$cats[JCPQM_QUESTION][$pathParts[0].'/'.$pathParts[1].'/'.$pathParts[2]]->id;
		}
		unset($map["section"]["l3"]);
		self::$categoryMap=$map;
	}

public function generateIni($examId)
{
	$exam               = $this->getItem($examId);
	$catBase            = (int) $exam->basecatidquestions;
	$questionCategories = new    \Joomla\CMS\Categories\Categories(array(
		'extension' => 'com_jcpqm.question',
		'table'     => 'categories'
	));
	$basecat            =$questionCategories->get($catBase);
	$application = JFactory::getApplication();

	$language  = 'en-GB';
	$component = 'com_jcpqm';
	$base      = JPATH_ADMINISTRATOR . "/components/" . $component . "/jcpqm_crowdin/language/" . $language . "/";
	foreach ( $basecat->getChildren(false) as $cat )
	{
		$section     = $cat->alias;
		$inifile     = $base . "/" . $language . "." . $component . "_" . $exam->alias . "_" . $section . ".ini";
		$questionStr = [];
		if (file_exists($inifile))
		{
			/* @var $questionsModel \JcpqmModelQuestions l */
			$questionsModel = \JcpqmHelper::getModel('Questions');
			$questionsModel->getState(); // require // $questionsModel->set('__state_set',true);
			$questionsModel->setState('list.limit', 0);
			$questionsModel->setState('list.start', 0);
			$questionsModel->setState('filter.published', 1);
			$questionsModel->setState('filter.state', 1); // published
			$questionsModel->setState('filter.exam', (int) $exam->id);
			$questionsModel->setState('filter.category_id', (int) $cat->id);
			// $this->__state_set
			$questions = $questionsModel->getItems();
			$infoMsg[] = $section . ":" . sizeof($questions);
			if (sizeof($questions))
			{
				foreach ($questions as $question)
				{
					$Jtext          = sprintf("%s_%s_%s", strtoupper($exam->alias), strtoupper($section), strtoupper($question->uuid));
					$questionStr[] = sprintf("%s%s=\"%s\"", $Jtext, 'Q', str_replace('"', '\"', $question->q_q));
					if ( $question->questiontype == 1 )
					{
						// booleans are different, the natively use JTRUE and JFALSE, no need to generate language strings
						continue;
					}
					for ($i = 1; $i <= QT_MAX_NUMANSWERS ; $i++)
					{
						$el = 'q_a' . $i;
						if (!property_exists($question, $el) || $question->$el == "")
						{
							break;
						}
						$questionStr[] = sprintf("%s%s=\"%s\"", $Jtext, $i, str_replace('"', '\"', $question->$el));
					}
				}
			}
			file_put_contents($inifile, join("\n", $questionStr));
			// join("\n",$questionStr) and save to file

		}
	}
	$application->enqueueMessage($base . "\n" . $exam->alias . " :: " . join(", ", $infoMsg));
}



public function pushToCrowdin()
{
	$msg         = "push to crowdin, not implemented, should only work for EN-gb";
	$application = JFactory::getApplication();
	$application->enqueueMessage($msg, 'info');
}


public function getTranslations()
{
	$msg         = "getting translations from crowdin, not implemented, should get all translated ini files";
	$application = JFactory::getApplication();
	$application->enqueueMessage($msg, 'info');
}

/**
 * loadJcpQmLanguages
 *
 * triggered from the system jcpqmlanguage plugin
 * this way we keep all the heavy lifting in the component
 *
 * // administrator/components/com_jcpqm/jcpqm_crowdin/language/en-GB/en-GB.com_jcpqm_j3-admin-2019_configuration.ini
 * // path    :: <JPATHADMINISTATOR>/compoments/<component_name>/jcpqm_crowdin/language/<languageTag>
 * // inifile :: <languageTag>.<component_name>_<exam>_categorie>.ini
 *
 * @since version
 */


public function loadJcpQmLanguages()
{
	$languageFactory = \Joomla\CMS\Factory::getLanguage();

	$language  = 'en-GB';
	$component = 'com_jcpqm';

	// TODO loop the available exams
	foreach( array('j3-admin-2019') as $examAlias )
	{
		// for starters we just use 1
		$exam               = $this->getItem(1);
		$catBase            = (int) $exam->basecatidquestions;
		$questionCategories = new    \Joomla\CMS\Categories\Categories(array(
			'extension' => 'com_jcpqm.question',
			'table'     => 'categories'
		));
		$basecat            =$questionCategories->get($catBase);
		foreach ( $basecat->getChildren(false) as $cat )
		{
			$section = $cat->alias;
			$languageFactory->load($component . '_' . $exam->alias . '_' . $section,
				JPATH_ADMINISTRATOR . '/components/com_jcpqm/jcpqm_crowdin',
				$language);
		}
	}
}
/* MDI model EXAM :: END
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
				$item->tags->getTagIds($item->id, 'com_jcpqm.exam');
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
		$form = $this->loadForm('com_jcpqm.exam', 'exam', $options);

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
		if ($id != 0 && (!$user->authorise('exam.edit.state', 'com_jcpqm.exam.' . (int) $id))
			|| ($id == 0 && !$user->authorise('exam.edit.state', 'com_jcpqm')))
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
*  MDI getform EXAM :: BEGIN
*
*/

	$document = JFactory::getDocument();
//	$document->addStyleSheet('https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css');
              
	$htmlBlock = $this->crowdinExamInfo( $form );
	return $form;
}

public function ajaxGetBaseCatId( $examId )
{
	$exam=$this->getItem($examId);
	$catBase = (int) $exam->basecatidquestions;
	// get the first child of the base
	$questionCategories=new	\Joomla\CMS\Categories\Categories(array(
		'extension'  => 'com_jcpqm.question',
		'table'      => 'categories'
	));
	$questionCategoryItem=$questionCategories->get($catBase);
	$firstChild=$questionCategoryItem->getChildren(false)[0];
	return array($catBase,(int)$firstChild->id);
}


public function crowdinExamInfo(\Jform $form = null)
{
	// get the catid for this examrecord
	$jinput = JFactory::getApplication()->input;
	if (is_null($form))
	{
		$formData = new JInput($jinput->get('jform', '', 'array'));
		$catid = $formData->getInt('basecatidquestions', 0);
		$ApiKeyCrowdin =$formData->getString('key', 0);
	}
	else
	{
		$catid = (int)$form->getValue('basecatidquestions');
		$ApiKeyCrowdin = $form->getValue('key');
	}


	// get to the category record information, and its alias
	$questionCategories=new	\Joomla\CMS\Categories\Categories(array(
		'extension'  => 'com_jcpqm.question',
		'table'      => 'categories'
	));
	$questionCategoryItem   = $questionCategories->get($catid);
	$pathArr=explode('/',(string)$questionCategoryItem->get('path'));
	unset($pathArr[0]);
	$projectAlias=join('-',$pathArr);
	// we MUST have a naming convention for projects, alas we have legacy
	$projectAlias = $projectAlias == 'j3-administrator' ? 'jcp-admin-j3' : 'jcp-' + $projectAlias;
	$TransportInterface = $this->TransportInterface();


	// twig autoloader
	require_once JPATH_ADMINISTRATOR . '/components/com_jcpqm/jcpqm_vendor/autoload.php';

	/* **************************************************************************
	 * Crowdin Info
	 * ************************************************************************** */

	// crowdin project identifier should follow the base category convention

	// jcp-admin-j3

	$crowdin_Info_postresponse = $this->crowdin_Info($projectAlias, $TransportInterface, $ApiKeyCrowdin);
	$Info_postresponse = json_decode($crowdin_Info_postresponse->body);
	if ($crowdin_Info_postresponse->code != HTTP_OK )
	{
		JFactory::getApplication()->enqueueMessage("API error crowdin_Info" . $crowdin_Info_postresponse->code, 'error');
	}
	// make parameters available to twig templating engine
	$twigParameters["availableLanguages"]=$this->languageMap($Info_postresponse);
	$twigParameters["fileset"]=$this->examFileset($Info_postresponse, $projectAlias);
	$twigParameters["details"]=$Info_postresponse->details;

$twigtmpl_CrowdinInfo  =<<<EOD
<h2>{{details.name}}/{{fileset.name}} [{{details.source_language.name}}]</h2>
<h3>Sourcefiles</h3>
<ul>
	{% for value in fileset.files %}
	<li>
	   <b>{{value.name}}</b></br>
         <span class="badge badge-secondary">Created: {{ value.created       | date('Y-m-d H:i:s') }}</span>
         <span class="badge badge-success"  >Updated: {{ value.last_updated  | date('Y-m-d H:i:s') }}</span>
	</li>
	{% endfor %}
</ul>
EOD;

	/* **************************************************************************
	 * Crowdin LanguageStatus
	 * ************************************************************************** */

	// TODO loop all the language
	foreach( $twigParameters["availableLanguages"] as $language => $value)
	{
		$crowdin_LanguageStatus_postresponse = $this->crowdin_LanguageStatus($projectAlias, $TransportInterface, $ApiKeyCrowdin, $language);
		$crowdin_LanguageStatus              = json_decode($crowdin_LanguageStatus_postresponse->body);
		if ($crowdin_LanguageStatus_postresponse->code != HTTP_OK )
		{
			JFactory::getApplication()->enqueueMessage("API error crowdin LanguageStatus" . $crowdin_LanguageStatus_postresponse->code, 'error');
		}
		$alias = $form->getValue('alias');
		foreach ($crowdin_LanguageStatus->files as $examFiles)
		{
			if ($examFiles->name == $alias)
			{
				break;
			}
		}
		// make parameters available to twig templating engine
		$twigParameters["examFiles"][$language] = $examFiles;
	}

$twigtmpl_CrowdinLanguageStatus1   =<<<EOD
<div>
<h2>Translation status</h2>
    {% for key, value in availableLanguages %}
         <span class="badge badge-info">
            <img class="tr_flag" src="/media/mod_languages/images/{{ availableLanguages[key] | lower | replace({'-':'_'}) }}.gif" alt="flag">
            [{{ key }}]=>"{{value}}"
         </span>
    {% endfor %}
</div>
EOD;
$twigtmpl_CrowdinLanguageStatus2   =<<<EOD
<style>
    .translated .progress {
        margin-bottom: 1px
    }
    .filestatus {
   	    padding-left: 100px
    }
    .tr_flag {
    	padding-right: 10px;
    }
	.progress-bar {
	    float: left;
	    width: 0%;
	    height: 100%;
	    font-size: 12px;
	    line-height: 20px;
	    color: #fff;
	    text-align: center;
	    background-color: #337ab7;
	    -webkit-box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);
	    box-shadow: inset 0 -1px 0 rgba(0,0,0,.15);
	    -webkit-transition: width .6s ease;
	    -o-transition: width .6s ease;
	    transition: width .6s ease;
	}
	 .progress-bar-info {
    	background-color: #5bc0de;
	}
	.progress-bar-success {
    	background-color: #5cb85c;
	}
</style>
EOD;
$twigtmpl_CrowdinLanguageStatus2   .=<<<EOD
{% for key, examFilesLanguage in examFiles %}
	<div class="setstatus">
	<h4><img class="tr_flag" src="/media/mod_languages/images/{{ availableLanguages[key] | lower | replace({'-':'_'}) }}.gif" alt="flag">{{ availableLanguages[key] }}</h4>
	{{ _self.progressbar('Translated', examFilesLanguage.translated, examFilesLanguage.phrases, "info" ) }}
	{{ _self.progressbar('Approved',   examFilesLanguage.approved,   examFilesLanguage.phrases, "success" ) }}
    </div>

	<div class="filestatus">    	
	{% for files in examFilesLanguage.files %}
	<h5>{{files.name}}</h5>
	    {{ _self.progressbar('Translated', files.translated, files.phrases, "info" ) }}
	    {{ _self.progressbar('Approved',   files.approved,   files.phrases, "success" ) }}
	{% endfor -%}
	</div>
{% endfor -%}
EOD;
$twigtmpl_CrowdinLanguageStatus2   .=<<<EOD
{% macro progressbar(name, part, total, style) -%}
<div class="{{ name | lower  }}">
    <div class="name" style="width:100px; float:left">
        {{name}}
    </div>
    <div class="progress">
        {% set percentage = (part/total)*100 %}
        {% if percentage < 20 %}{% set color = ";color:black"%}{% else %}{% set color =""%}{% endif %}
        <div class="progress-bar progress-bar-{{style}}" role="progressbar" aria-valuenow="{{part}}"
             aria-valuemin="0" aria-valuemax="{{total}}"
             style="width:{{percentage}}% {{color}}">
            #{{part}}/{{total}}&nbsp;=&nbsp;{{percentage|round(1, 'floor')}}%&nbsp;{{name}}
        </div>
    </div>
</div>
{% endmacro -%}
EOD;

//$twigParameters=json_decode('{"availableLanguages":{"fr":"fr-FR","es-ES":"es-ES","de":"de-DE","it":"it-IT","nl":"nl-NL"},"fileset":{"node_type":"directory","id":"2396","name":"j3-admin-2019","files":[{"node_type":"file","id":"2464","name":"j3-admin-2019_configuration.ini","created":"2019-06-16T21:44:53+0000","last_updated":"2019-06-17T08:20:21+0000","last_accessed":null,"last_revision":"1"},{"node_type":"file","id":"2466","name":"j3-admin-2019_content.ini","created":"2019-06-16T21:46:33+0000","last_updated":"2019-06-16T21:46:33+0000","last_accessed":null,"last_revision":"1"}]},"examFiles":{"fr":{"node_type":"directory","id":"2396","name":"j3-admin-2019","phrases":304,"translated":304,"approved":0,"words":3428,"words_translated":3428,"words_approved":0,"files":[{"node_type":"file","id":"2464","name":"j3-admin-2019_configuration.ini","phrases":"168","translated":"168","approved":"0","words":"1842","words_translated":"1842","words_approved":"0"},{"node_type":"file","id":"2466","name":"j3-admin-2019_content.ini","phrases":"136","translated":"136","approved":"0","words":"1586","words_translated":"1586","words_approved":"0"}]},"es-ES":{"node_type":"directory","id":"2396","name":"j3-admin-2019","phrases":304,"translated":239,"approved":0,"words":3428,"words_translated":2246,"words_approved":0,"files":[{"node_type":"file","id":"2464","name":"j3-admin-2019_configuration.ini","phrases":"168","translated":"121","approved":"0","words":"1842","words_translated":"1083","words_approved":"0"},{"node_type":"file","id":"2466","name":"j3-admin-2019_content.ini","phrases":"136","translated":"118","approved":"0","words":"1586","words_translated":"1163","words_approved":"0"}]},"de":{"node_type":"directory","id":"2396","name":"j3-admin-2019","phrases":304,"translated":169,"approved":0,"words":3428,"words_translated":1593,"words_approved":0,"files":[{"node_type":"file","id":"2464","name":"j3-admin-2019_configuration.ini","phrases":"168","translated":"51","approved":"0","words":"1842","words_translated":"430","words_approved":"0"},{"node_type":"file","id":"2466","name":"j3-admin-2019_content.ini","phrases":"136","translated":"118","approved":"0","words":"1586","words_translated":"1163","words_approved":"0"}]},"it":{"node_type":"directory","id":"2396","name":"j3-admin-2019","phrases":304,"translated":264,"approved":0,"words":3428,"words_translated":2453,"words_approved":0,"files":[{"node_type":"file","id":"2464","name":"j3-admin-2019_configuration.ini","phrases":"168","translated":"146","approved":"0","words":"1842","words_translated":"1290","words_approved":"0"},{"node_type":"file","id":"2466","name":"j3-admin-2019_content.ini","phrases":"136","translated":"118","approved":"0","words":"1586","words_translated":"1163","words_approved":"0"}]},"nl":{"node_type":"directory","id":"2396","name":"j3-admin-2019","phrases":304,"translated":304,"approved":0,"words":3428,"words_translated":3428,"words_approved":0,"files":[{"node_type":"file","id":"2464","name":"j3-admin-2019_configuration.ini","phrases":"168","translated":"168","approved":"0","words":"1842","words_translated":"1842","words_approved":"0"},{"node_type":"file","id":"2466","name":"j3-admin-2019_content.ini","phrases":"136","translated":"136","approved":"0","words":"1586","words_translated":"1586","words_approved":"0"}]}}}',true);

	/* **************************************************************************
	 * Template processing
	 * ************************************************************************** */
	$twig_loader = new Twig\Loader\ArrayLoader([
		"CrowdinInfo"            => $twigtmpl_CrowdinInfo,
		"CrowdinLanguageStatus1" => $twigtmpl_CrowdinLanguageStatus1,
		"CrowdinLanguageStatus2" => $twigtmpl_CrowdinLanguageStatus2
	]);
	$twig = new Twig\Environment($twig_loader);
	//
	$html  = $twig->render("CrowdinInfo",        $twigParameters);
	try
	{
		$html .= $twig->render("CrowdinLanguageStatus1", $twigParameters);
		$html .= $twig->render("CrowdinLanguageStatus2", $twigParameters);
	}

	catch (Twig\Error\Error $ex )
	{
		$msg = sprintf("%s : %s"
			, get_class( $ex )
			, $ex->getMessage()
		);
		JFactory::getApplication()->enqueueMessage($msg, 'error');
	};

	// modify the form
	$xpath = "//field[@name='note_crowdinprojectinfo']/@description";
	$form->getXml()->xpath($xpath)[0]['description'] = $html;
	return $html;
}
private function languageMapArray()
{
	return [
		'ar'    => 'ar-AA',
		'bg'    => 'bg-BG',
		'bn'    => 'bn-BD',
		'br-FR' => 'br-FR',
		'ca'    => 'ca-ES',
		'cs'    => 'cs-CZ',
		'da'    => 'da-DK',
		'de'    => 'de-DE',
		'el'    => 'el-GR',
		'en-GB' => 'en-GB',
		'en-US' => 'en-US',
		'es-CO' => 'es-CO',
		'es-ES' => 'es-ES',
		'et'    => 'et-EE',
		'fa'    => 'fa-IR',
		'fi'    => 'fi-FI',
		'fr'    => 'fr-FR',
		'fr-CA' => 'fr-CA',
		'ga-IE' => 'ga-IE',
		'he'    => 'he-IL',
		'hi'    => 'hi-IN',
		'hr'    => 'hr-HR',
		'hu'    => 'hu-HU',
		'id'    => 'id-ID',
		'is'    => 'is-IS',
		'it'    => 'it-IT',
		'ja'    => 'ja-JA',
		'ka'    => 'ka-GE',
		'lv'    => 'lv-LV',
		'mk'    => 'mk-MK',
		'ml-IN' => 'ml-IN',
		'mr'    => 'mr-IN',
		'ms'    => 'ms-MY',
		'nb'    => 'nb-NO',
		'nl'    => 'nl-NL',
		'nl-BE' => 'nl-BE',
		'pl'    => 'pl-PL',
		'pt-BR' => 'pt-BR',
		'pt-PT' => 'pt-PT',
		'ro'    => 'ro-RO',
		'ru'    => 'ru-RU',
		'si-LK' => 'si-LK',
		'sk'    => 'sk-SK',
		'sl'    => 'sl-SI',
		'sr'    => 'sr-RS',
		'sr-CS' => 'sr-CS',
		'sv-SE' => 'sv-SE',
		'ta'    => 'ta-IN',
		'th'    => 'th-TH',
		'tl'    => 'tl-PH',
		'tr'    => 'tr-TR',
		'ur-IN' => 'ur-IN',
		'zh-CN' => 'zh-CN',
		'zh-TW' => 'zh-TW',
	];
}

/**
 *
 * @return \Joomla\CMS\Http\TransportInterface
 *
 * @since version
 */
private function TransportInterface()
{
	// curloptions
	$curl_setopt_array = new Registry(array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING       => "",
		CURLOPT_MAXREDIRS      => 10,
		CURLOPT_TIMEOUT        => 30,
		CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
		CURLOPT_HTTPHEADER     => array(
			"Accept: */*",
			"Cache-Control: no-cache",
			"Connection: keep-alive",
			"Host: api.crowdin.com",
			"accept-encoding: gzip, deflate",
			"cache-control: no-cache",
			"content-length: "
		),
	));

	return \Joomla\CMS\Http\HttpFactory::getAvailableDriver($curl_setopt_array, "Curl");
}

/**
 * @param                                     $examAlias
 * @param \Joomla\CMS\Http\TransportInterface $TransportInterface
 * @param                                     $ApiKeyCrowdin
 *
 * @return \Joomla\CMS\Http\Response
 *
 * @since version
 */
private function crowdin_LanguageStatus($examAlias, \Joomla\CMS\Http\TransportInterface $TransportInterface, $ApiKeyCrowdin, $language='nl')
{
	$cmd      = "language-status";
	if (isset($language))
	{
		$data["language"] = $language;
	};

	return $this->doApiRequest($examAlias, $TransportInterface, $ApiKeyCrowdin, $cmd, 'POST', $data);
}

	/**
	 * @param                                     $examAlias
	 * @param \Joomla\CMS\Http\TransportInterface $TransportInterface
	 * @param                                     $ApiKeyCrowdin
	 * @param                                     $cmd
	 * @param                                     $method
	 * @param                                     $data
	 *
	 * @return \Joomla\CMS\Http\Response
	 *
	 * @since version
	 */
	private function doApiRequest($examAlias, \Joomla\CMS\Http\TransportInterface $TransportInterface, $ApiKeyCrowdin, $cmd, $method, $data = null)
	{
		$uri          = new Joomla\CMS\Uri\Uri("https://api.crowdin.com/api/project/" . $examAlias . "/" . $cmd);
		$postresponse = $TransportInterface->request(
			$method                                     // CURLOPT_CUSTOMREQUEST
			, $uri                                      // CURLOPT_URL uri part
			, array_merge($data,
				array("key"            => $ApiKeyCrowdin
				, "project-identifier" => $examAlias
				, "json"               => ""
				))
			, array(                                    // CURLOPT_HTTPHEADER
				"Accept: */*",
				"Cache-Control: no-cache",
				"Connection: keep-alive",
				"Host: api.crowdin.com",
				"accept-encoding: gzip, deflate",
				"cache-control: no-cache",
				"content-length: "
			)
		);

		return $postresponse;
	}


/**
 * @param                                     $examAlias
 * @param \Joomla\CMS\Http\TransportInterface $TransportInterface
 * @param                                     $ApiKeyCrowdin
 *
 * @return \Joomla\CMS\Http\Response
 *
 * @since version
 */
private function crowdin_Info($examAlias, \Joomla\CMS\Http\TransportInterface $TransportInterface, $ApiKeyCrowdin)
{
	$cmd = "info";
	$uri          = new Joomla\CMS\Uri\Uri("https://api.crowdin.com/api/project/" . $examAlias . "/" . $cmd);
	$postresponse = $TransportInterface->request('POST'
		, $uri
		, array(
			"key"              => $ApiKeyCrowdin
		, "project-identifier" => $examAlias
		, "json"               => ""
		)
		, array(
			"Accept: */*",
			"Cache-Control: no-cache",
			"Connection: keep-alive",
			"Host: api.crowdin.com",
			"accept-encoding: gzip, deflate",
			"cache-control: no-cache",
			"content-length: "
		)
	);

	return $postresponse;
}

/**
 * @param $postBody
 *
 * @return array
 *
 * @since version
 */
private function languageMap($postBody)
{
	$languageMap = $this->languageMapArray();
	foreach ($postBody->languages as $locale)
	{
		$availableLanguages[$locale->code] = $languageMap[$locale->code];
	}

	return $availableLanguages;
}

/**
 * @param $responseBody
 * @param $examAlias
 *
 * @return string
 *
 * @since version
 */
private function examFileset($responseBody, $examAlias)
{
// limit info to current set
	$fileset = "";
	foreach ($responseBody->files as $fileset)
	{
		if ($fileset != $examAlias)
		{
			continue;
		}
		break;
	}

	return $fileset;
}

protected function dummy_ignore($form) {
/* MDI getform EXAM :: END
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
		return 'administrator/components/com_jcpqm/models/forms/exam.js';
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
			return $user->authorise('exam.delete', 'com_jcpqm.exam.' . (int) $record->id);
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
			$permission = $user->authorise('exam.edit.state', 'com_jcpqm.exam.' . (int) $recordId);
			if (!$permission && !is_null($permission))
			{
				return false;
			}
		}
		// In the absense of better information, revert to the component permissions.
		return $user->authorise('exam.edit.state', 'com_jcpqm');
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

		return $user->authorise('exam.edit', 'com_jcpqm.exam.'. ((int) isset($data[$key]) ? $data[$key] : 0)) or $user->authorise('exam.edit',  'com_jcpqm');
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
					->from($db->quoteName('#__jcpqm_exam'));
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
		$data = JFactory::getApplication()->getUserState('com_jcpqm.edit.exam.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
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
		return false;
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
		$this->canDo			= JcpqmHelper::getActions('exam');
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
			$this->canDo		= JcpqmHelper::getActions('exam');
		}

		if (!$this->canDo->get('exam.create') && !$this->canDo->get('exam.batch'))
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
		elseif (isset($values['published']) && !$this->canDo->get('exam.edit.state'))
		{
				$values['published'] = 0;
		}

		$newIds = array();
		// Parent exists so let's proceed
		while (!empty($pks))
		{
			// Pop the first ID off the stack
			$pk = array_shift($pks);

			$this->table->reset();

			// only allow copy if user may edit this item.
			if (!$this->user->authorise('exam.edit', $contexts[$pk]))
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
			list($this->table->name, $this->table->alias) = $this->_generateNewTitle($this->table->alias, $this->table->name);

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
			$this->canDo		= JcpqmHelper::getActions('exam');
		}

		if (!$this->canDo->get('exam.edit') && !$this->canDo->get('exam.batch'))
		{
			$this->setError(JText::_('JLIB_APPLICATION_ERROR_BATCH_CANNOT_EDIT'));
			return false;
		}

		// make sure published only updates if user has the permission.
		if (isset($values['published']) && !$this->canDo->get('exam.edit.state'))
		{
			unset($values['published']);
		}
		// remove move_copy from array
		unset($values['move_copy']);

		// Parent exists so we proceed
		foreach ($pks as $pk)
		{
			if (!$this->user->authorise('exam.edit', $contexts[$pk]))
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
        
		// Set the Params Items to data
		if (isset($data['params']) && is_array($data['params']))
		{
			$params = new JRegistry;
			$params->loadArray($data['params']);
			$data['params'] = (string) $params;
		}

		// Alter the name for save as copy
		if ($input->get('task') === 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($input->getInt('id'));

			if ($data['name'] == $origTable->name)
			{
				list($name, $alias) = $this->_generateNewTitle($data['alias'], $data['name']);
				$data['name'] = $name;
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
					$data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['name']);
				}
				else
				{
					$data['alias'] = JFilterOutput::stringURLSafe($data['name']);
				}

				$table = JTable::getInstance('exam', 'jcpqmTable');

				if ($table->load(array('alias' => $data['alias'])) && ($table->id != $data['id'] || $data['id'] == 0))
				{
					$msg = JText::_('COM_JCPQM_EXAM_SAVE_WARNING');
				}

				$data['alias'] = $this->_generateNewTitle($data['alias']);

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
