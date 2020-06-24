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

JHTML::_('behavior.modal');

/**
 * Script File of Jcpqm Component
 */
class com_jcpqmInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent)
	{

	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent)
	{
		// Get Application object
		$app = JFactory::getApplication();

		// Get The Database object
		$db = JFactory::getDbo();

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Question alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.question') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$question_found = $db->getNumRows();
		// Now check if there were any rows
		if ($question_found)
		{
			// Since there are load the needed  question type ids
			$question_ids = $db->loadColumn();
			// Remove Question from the content type table
			$question_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.question') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($question_condition);
			$db->setQuery($query);
			// Execute the query to remove Question items
			$question_done = $db->execute();
			if ($question_done)
			{
				// If succesfully remove Question add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.question) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Question items from the contentitem tag map table
			$question_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.question') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($question_condition);
			$db->setQuery($query);
			// Execute the query to remove Question items
			$question_done = $db->execute();
			if ($question_done)
			{
				// If succesfully remove Question add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.question) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Question items from the ucm content table
			$question_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_jcpqm.question') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($question_condition);
			$db->setQuery($query);
			// Execute the query to remove Question items
			$question_done = $db->execute();
			if ($question_done)
			{
				// If succesfully remove Question add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.question) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Question items are cleared from DB
			foreach ($question_ids as $question_id)
			{
				// Remove Question items from the ucm base table
				$question_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $question_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($question_condition);
				$db->setQuery($query);
				// Execute the query to remove Question items
				$db->execute();

				// Remove Question items from the ucm history table
				$question_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $question_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($question_condition);
				$db->setQuery($query);
				// Execute the query to remove Question items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Question catid alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.questions.category') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$question_catid_found = $db->getNumRows();
		// Now check if there were any rows
		if ($question_catid_found)
		{
			// Since there are load the needed  question_catid type ids
			$question_catid_ids = $db->loadColumn();
			// Remove Question catid from the content type table
			$question_catid_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.questions.category') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($question_catid_condition);
			$db->setQuery($query);
			// Execute the query to remove Question catid items
			$question_catid_done = $db->execute();
			if ($question_catid_done)
			{
				// If succesfully remove Question catid add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.questions.category) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Question catid items from the contentitem tag map table
			$question_catid_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.questions.category') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($question_catid_condition);
			$db->setQuery($query);
			// Execute the query to remove Question catid items
			$question_catid_done = $db->execute();
			if ($question_catid_done)
			{
				// If succesfully remove Question catid add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.questions.category) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Question catid items from the ucm content table
			$question_catid_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_jcpqm.questions.category') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($question_catid_condition);
			$db->setQuery($query);
			// Execute the query to remove Question catid items
			$question_catid_done = $db->execute();
			if ($question_catid_done)
			{
				// If succesfully remove Question catid add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.questions.category) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Question catid items are cleared from DB
			foreach ($question_catid_ids as $question_catid_id)
			{
				// Remove Question catid items from the ucm base table
				$question_catid_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $question_catid_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($question_catid_condition);
				$db->setQuery($query);
				// Execute the query to remove Question catid items
				$db->execute();

				// Remove Question catid items from the ucm history table
				$question_catid_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $question_catid_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($question_catid_condition);
				$db->setQuery($query);
				// Execute the query to remove Question catid items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Exam alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.exam') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$exam_found = $db->getNumRows();
		// Now check if there were any rows
		if ($exam_found)
		{
			// Since there are load the needed  exam type ids
			$exam_ids = $db->loadColumn();
			// Remove Exam from the content type table
			$exam_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.exam') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($exam_condition);
			$db->setQuery($query);
			// Execute the query to remove Exam items
			$exam_done = $db->execute();
			if ($exam_done)
			{
				// If succesfully remove Exam add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.exam) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Exam items from the contentitem tag map table
			$exam_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.exam') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($exam_condition);
			$db->setQuery($query);
			// Execute the query to remove Exam items
			$exam_done = $db->execute();
			if ($exam_done)
			{
				// If succesfully remove Exam add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.exam) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Exam items from the ucm content table
			$exam_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_jcpqm.exam') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($exam_condition);
			$db->setQuery($query);
			// Execute the query to remove Exam items
			$exam_done = $db->execute();
			if ($exam_done)
			{
				// If succesfully remove Exam add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.exam) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Exam items are cleared from DB
			foreach ($exam_ids as $exam_id)
			{
				// Remove Exam items from the ucm base table
				$exam_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $exam_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($exam_condition);
				$db->setQuery($query);
				// Execute the query to remove Exam items
				$db->execute();

				// Remove Exam items from the ucm history table
				$exam_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $exam_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($exam_condition);
				$db->setQuery($query);
				// Execute the query to remove Exam items
				$db->execute();
			}
		}

		// Create a new query object.
		$query = $db->getQuery(true);
		// Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// Where Questiontype alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.questiontype') );
		$db->setQuery($query);
		// Execute query to see if alias is found
		$db->execute();
		$questiontype_found = $db->getNumRows();
		// Now check if there were any rows
		if ($questiontype_found)
		{
			// Since there are load the needed  questiontype type ids
			$questiontype_ids = $db->loadColumn();
			// Remove Questiontype from the content type table
			$questiontype_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.questiontype') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($questiontype_condition);
			$db->setQuery($query);
			// Execute the query to remove Questiontype items
			$questiontype_done = $db->execute();
			if ($questiontype_done)
			{
				// If succesfully remove Questiontype add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.questiontype) type alias was removed from the <b>#__content_type</b> table'));
			}

			// Remove Questiontype items from the contentitem tag map table
			$questiontype_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_jcpqm.questiontype') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($questiontype_condition);
			$db->setQuery($query);
			// Execute the query to remove Questiontype items
			$questiontype_done = $db->execute();
			if ($questiontype_done)
			{
				// If succesfully remove Questiontype add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.questiontype) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// Remove Questiontype items from the ucm content table
			$questiontype_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_jcpqm.questiontype') );
			// Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($questiontype_condition);
			$db->setQuery($query);
			// Execute the query to remove Questiontype items
			$questiontype_done = $db->execute();
			if ($questiontype_done)
			{
				// If succesfully remove Questiontype add queued success message.
				$app->enqueueMessage(JText::_('The (com_jcpqm.questiontype) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// Make sure that all the Questiontype items are cleared from DB
			foreach ($questiontype_ids as $questiontype_id)
			{
				// Remove Questiontype items from the ucm base table
				$questiontype_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $questiontype_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($questiontype_condition);
				$db->setQuery($query);
				// Execute the query to remove Questiontype items
				$db->execute();

				// Remove Questiontype items from the ucm history table
				$questiontype_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $questiontype_id);
				// Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($questiontype_condition);
				$db->setQuery($query);
				// Execute the query to remove Questiontype items
				$db->execute();
			}
		}

		// If All related items was removed queued success message.
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_base</b> table'));
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_history</b> table'));

		// Remove jcpqm assets from the assets table
		$jcpqm_condition = array( $db->quoteName('name') . ' LIKE ' . $db->quote('com_jcpqm%') );

		// Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($jcpqm_condition);
		$db->setQuery($query);
		$questiontype_done = $db->execute();
		if ($questiontype_done)
		{
			// If succesfully remove jcpqm add queued success message.
			$app->enqueueMessage(JText::_('All related items was removed from the <b>#__assets</b> table'));
		}

		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:marco.dings@community.joomla.org">marco.dings@community.joomla.org</a>.
		<br />We at Joomla! are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="http://certification.joomla.org" target="_blank">http://certification.joomla.org</a> today!</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent)
	{
		
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant ...hmmm
		if ($type == 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.6.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.6.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type == 'update')
		{
		}
		// do any install needed
		if ($type == 'install')
		{
		}
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// We check if we have dynamic folders to copy
		$this->setDynamicF0ld3rs($app, $parent);
		// set the default component settings
		if ($type == 'install')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the question content type object.
			$question = new stdClass();
			$question->type_title = 'Jcpqm Question';
			$question->type_alias = 'com_jcpqm.question';
			$question->table = '{"special": {"dbtable": "#__jcpqm_question","key": "id","type": "Question","prefix": "jcpqmTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$question->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "question_title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "q_a3","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "catid","core_xreference": "null","asset_id": "asset_id"},"special": {"question_title":"question_title","alias":"alias","exam":"exam","questiontype":"questiontype","level":"level","workstatus":"workstatus","synced":"synced","q_m3":"q_m3","q_a3c":"q_a3c","q_a3":"q_a3","q_m1":"q_m1","q_a1c":"q_a1c","q_a1":"q_a1","q_a4":"q_a4","shikaquestion":"shikaquestion","q_a2":"q_a2","catidplus":"catidplus","q_q":"q_q","note":"note","uuid":"uuid","q_a2c":"q_a2c","not_required":"not_required","q_m2":"q_m2","q_a4c":"q_a4c","q_atf":"q_atf","q_m4":"q_m4"}}';
			$question->router = 'JcpqmHelperRoute::getQuestionRoute';
			$question->content_history_options = '{"formFile": "administrator/components/com_jcpqm/models/forms/question.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","not_required"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","catid","questiontype","workstatus","synced","q_m3","q_a3c","q_m1","q_a1c","shikaquestion","q_a2c","q_m2","q_a4c","q_atf","q_m4"],"displayLookup": [{"sourceColumn": "catid","targetTable": "#__categories","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "exam","targetTable": "#__jcpqm_exam","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "questiontype","targetTable": "#__jcpqm_questiontype","targetColumn": "id","displayColumn": "questiontype"},{"sourceColumn": "shikaquestion","targetTable": "#__tmt_questions","targetColumn": "id","displayColumn": "title"}]}';

			// Set the object into the content types table.
			$question_Inserted = $db->insertObject('#__content_types', $question);

			// Create the question category content type object.
			$question_category = new stdClass();
			$question_category->type_title = 'Jcpqm Question Catid';
			$question_category->type_alias = 'com_jcpqm.questions.category';
			$question_category->table = '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
			$question_category->field_mappings = '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}';
			$question_category->router = 'JcpqmHelperRoute::getCategoryRoute';
			$question_category->content_history_options = '{"formFile":"administrator\/components\/com_categories\/models\/forms\/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],"convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}';

			// Set the object into the content types table.
			$question_category_Inserted = $db->insertObject('#__content_types', $question_category);

			// Create the exam content type object.
			$exam = new stdClass();
			$exam->type_title = 'Jcpqm Exam';
			$exam->type_alias = 'com_jcpqm.exam';
			$exam->table = '{"special": {"dbtable": "#__jcpqm_exam","key": "id","type": "Exam","prefix": "jcpqmTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$exam->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias","jcp_info":"jcp_info","crowdin_info":"crowdin_info","basecatidquestions":"basecatidquestions","key":"key"}}';
			$exam->router = 'JcpqmHelperRoute::getExamRoute';
			$exam->content_history_options = '{"formFile": "administrator/components/com_jcpqm/models/forms/exam.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","basecatidquestions"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$exam_Inserted = $db->insertObject('#__content_types', $exam);

			// Create the questiontype content type object.
			$questiontype = new stdClass();
			$questiontype->type_title = 'Jcpqm Questiontype';
			$questiontype->type_alias = 'com_jcpqm.questiontype';
			$questiontype->table = '{"special": {"dbtable": "#__jcpqm_questiontype","key": "id","type": "Questiontype","prefix": "jcpqmTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$questiontype->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "questiontype","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "elaboration","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"questiontype":"questiontype","elaboration":"elaboration"}}';
			$questiontype->router = 'JcpqmHelperRoute::getQuestiontypeRoute';
			$questiontype->content_history_options = '{"formFile": "administrator/components/com_jcpqm/models/forms/questiontype.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Set the object into the content types table.
			$questiontype_Inserted = $db->insertObject('#__content_types', $questiontype);


			// Install the global extenstion params.
			$query = $db->getQuery(true);
			// Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"marco dings","autorEmail":"marco.dings@community.joomla.org","check_in":"-1 day","save_history":"1","history_limit":"10"}'),
			);
			// Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_jcpqm')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();


/* BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN
*
*  TODO MDI Postflight (install) :: BEGIN
*
*/
/* MDI Postflight (install) :: END 
   END END END END END END END END END END END END END END END END END END END END END END END END */
			echo '<a target="_blank" href="http://certification.joomla.org" title="JCP Question Manager">
				<img src="components/com_jcpqm/assets/images/vdm-component.png"/>
				</a>';
		}
		// do any updates needed
		if ($type == 'update')
		{

			// Get The Database object
			$db = JFactory::getDbo();

			// Create the question content type object.
			$question = new stdClass();
			$question->type_title = 'Jcpqm Question';
			$question->type_alias = 'com_jcpqm.question';
			$question->table = '{"special": {"dbtable": "#__jcpqm_question","key": "id","type": "Question","prefix": "jcpqmTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$question->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "question_title","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "q_a3","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "catid","core_xreference": "null","asset_id": "asset_id"},"special": {"question_title":"question_title","alias":"alias","exam":"exam","questiontype":"questiontype","level":"level","workstatus":"workstatus","synced":"synced","q_m3":"q_m3","q_a3c":"q_a3c","q_a3":"q_a3","q_m1":"q_m1","q_a1c":"q_a1c","q_a1":"q_a1","q_a4":"q_a4","shikaquestion":"shikaquestion","q_a2":"q_a2","catidplus":"catidplus","q_q":"q_q","note":"note","uuid":"uuid","q_a2c":"q_a2c","not_required":"not_required","q_m2":"q_m2","q_a4c":"q_a4c","q_atf":"q_atf","q_m4":"q_m4"}}';
			$question->router = 'JcpqmHelperRoute::getQuestionRoute';
			$question->content_history_options = '{"formFile": "administrator/components/com_jcpqm/models/forms/question.xml","hideFields": ["asset_id","checked_out","checked_out_time","version","not_required"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","catid","questiontype","workstatus","synced","q_m3","q_a3c","q_m1","q_a1c","shikaquestion","q_a2c","q_m2","q_a4c","q_atf","q_m4"],"displayLookup": [{"sourceColumn": "catid","targetTable": "#__categories","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "exam","targetTable": "#__jcpqm_exam","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "questiontype","targetTable": "#__jcpqm_questiontype","targetColumn": "id","displayColumn": "questiontype"},{"sourceColumn": "shikaquestion","targetTable": "#__tmt_questions","targetColumn": "id","displayColumn": "title"}]}';

			// Check if question type is already in content_type DB.
			$question_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($question->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$question->type_id = $db->loadResult();
				$question_Updated = $db->updateObject('#__content_types', $question, 'type_id');
			}
			else
			{
				$question_Inserted = $db->insertObject('#__content_types', $question);
			}

			// Create the question category content type object.
			$question_category = new stdClass();
			$question_category->type_title = 'Jcpqm Question Catid';
			$question_category->type_alias = 'com_jcpqm.questions.category';
			$question_category->table = '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
			$question_category->field_mappings = '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}';
			$question_category->router = 'JcpqmHelperRoute::getCategoryRoute';
			$question_category->content_history_options = '{"formFile":"administrator\/components\/com_categories\/models\/forms\/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],"convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}';

			// Check if question category type is already in content_type DB.
			$question_category_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($question_category->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$question_category->type_id = $db->loadResult();
				$question_category_Updated = $db->updateObject('#__content_types', $question_category, 'type_id');
			}
			else
			{
				$question_category_Inserted = $db->insertObject('#__content_types', $question_category);
			}

			// Create the exam content type object.
			$exam = new stdClass();
			$exam->type_title = 'Jcpqm Exam';
			$exam->type_alias = 'com_jcpqm.exam';
			$exam->table = '{"special": {"dbtable": "#__jcpqm_exam","key": "id","type": "Exam","prefix": "jcpqmTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$exam->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias","jcp_info":"jcp_info","crowdin_info":"crowdin_info","basecatidquestions":"basecatidquestions","key":"key"}}';
			$exam->router = 'JcpqmHelperRoute::getExamRoute';
			$exam->content_history_options = '{"formFile": "administrator/components/com_jcpqm/models/forms/exam.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","basecatidquestions"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if exam type is already in content_type DB.
			$exam_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($exam->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$exam->type_id = $db->loadResult();
				$exam_Updated = $db->updateObject('#__content_types', $exam, 'type_id');
			}
			else
			{
				$exam_Inserted = $db->insertObject('#__content_types', $exam);
			}

			// Create the questiontype content type object.
			$questiontype = new stdClass();
			$questiontype->type_title = 'Jcpqm Questiontype';
			$questiontype->type_alias = 'com_jcpqm.questiontype';
			$questiontype->table = '{"special": {"dbtable": "#__jcpqm_questiontype","key": "id","type": "Questiontype","prefix": "jcpqmTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$questiontype->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "questiontype","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "elaboration","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "metadata","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "metakey","core_metadesc": "metadesc","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"questiontype":"questiontype","elaboration":"elaboration"}}';
			$questiontype->router = 'JcpqmHelperRoute::getQuestiontypeRoute';
			$questiontype->content_history_options = '{"formFile": "administrator/components/com_jcpqm/models/forms/questiontype.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// Check if questiontype type is already in content_type DB.
			$questiontype_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($questiontype->type_alias));
			$db->setQuery($query);
			$db->execute();

			// Set the object into the content types table.
			if ($db->getNumRows())
			{
				$questiontype->type_id = $db->loadResult();
				$questiontype_Updated = $db->updateObject('#__content_types', $questiontype, 'type_id');
			}
			else
			{
				$questiontype_Inserted = $db->insertObject('#__content_types', $questiontype);
			}



/* BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN BEGIN
*
*  TODO MDI Postflight (update) :: BEGIN
*
*/
/* MDI Postflight (update) :: END 
   END END END END END END END END END END END END END END END END END END END END END END END END */
			echo '<a target="_blank" href="http://certification.joomla.org" title="JCP Question Manager">
				<img src="components/com_jcpqm/assets/images/vdm-component.png"/>
				</a>
				<h3>Upgrade to Version 1.0.37 Was Successful! Let us know if anything is not working as expected.</h3>';
		}
	}

	/**
	 * Method to set/copy dynamic folders into place (use with caution)
	 *
	 * @return void
	 */
	protected function setDynamicF0ld3rs($app, $parent)
	{
		// get the instalation path
		$installer = $parent->getParent();
		$installPath = $installer->getPath('source');
		// get all the folders
		$folders = JFolder::folders($installPath);
		// check if we have folders we may want to copy
		$doNotCopy = array('media','admin','site'); // Joomla already deals with these
		if (count($folders) > 1)
		{
			foreach ($folders as $folder)
			{
				// Only copy if not a standard folders
				if (!in_array($folder, $doNotCopy))
				{
					// set the source path
					$src = $installPath.'/'.$folder;
					// set the destination path
					$dest = JPATH_ROOT.'/'.$folder;
					// now try to copy the folder
					if (!JFolder::copy($src, $dest, '', true))
					{
						$app->enqueueMessage('Could not copy '.$folder.' folder into place, please make sure destination is writable!', 'error');
					}
				}
			}
		}
	}
}
