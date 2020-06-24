<?php
/**
 * @package     Joomla.Legacy
 * @subpackage  Form
 *
 * @copyright   Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Platform.
 * Supports an HTML select list of categories
 *
 * @since  1.6
 */
class JFormFieldCategoryplus extends \JFormFieldCategory
{
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6
	 */
	public $type = 'Categoryplus';

	/**
	 * Method to get the field options for category
	 * Use the extension attribute in a form to specify the.specific extension for
	 * which categories should be displayed.
	 * Use the show_root attribute to specify whether to show the global category root in the list.
	 *
	 * @return  array    The field option objects.
	 *
	 * @since   1.6
	 */
	protected function getOptions()
	{
		// let the parent to the standard category filtering and shit ;)
		$options = parent::getOptions();

		JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_categories/models');
		JModelLegacy::addTablePath(JPATH_ADMINISTRATOR . '/components/com_categories/tables');
		/* @var $categoriesModel \CategoriesModelCategories  */
		$categoriesModel = JModelLegacy::getInstance('Categories', 'CategoriesModel', array('ignore_request'   => true));
		$extension = (string) $this->element['extension'];
		$categoriesModel->setState( "filter.extension", $extension);

		$rootPath			= (string) $this->element['rootpath'];
		$matchRootpath      = !( $rootPath == "" );
		$level              = (int)    $this->element['level'];
		$levelselector      = (string) $this->element['levelselector'];
		$showhiddenparrents = ( (string ) $this->element['showhiddenparrents'] == "true" );

		$cats = $categoriesModel->getItems();
		foreach($options as $key1 => $option )
		{
			foreach($cats as $key2 => $cat )
			{

				if ( $cat->id == $option->value)
				{
					$path_in_rootpath = !(strpos($rootPath, $cat->path) !== 0);
					$rootpath_in_path = !(strpos($cat->path, $rootPath) !== 0);

					if ( $path_in_rootpath )
					{
						// path=a     rootpath=a/b/c
						// path=a/b/c rootpath=a/b/c
						// optionally hide and continue
						$option->disable=$showhiddenparrents;
						unset($cats[$key2]); // make next loop smaller
						break;
					}
					if ( ! $rootpath_in_path )
					{
						// path=x       rootpath=a/b/c
						// remove we don't want them displayed
						unset($options[$key1]);
						unset($cats[$key2]); // make next loop smaller
						break;
					}
					// path=a/b/c/d rootpath=a/b/c
					$levelmatched=false;
					$catLevel = (int) $cat->level;
					$parentmatched=false;
					switch ($levelselector)
					{
						case '<':
							$levelmatched  = ($catLevel < $level);
							$parentmatched = ($catLevel == $level - 1);
							break;
						case '=':
							$levelmatched  = ($catLevel == $level);
							$parentmatched = ($catLevel < $level);
							break;
						case '>':
							$levelmatched = ($catLevel > $level);
							$parentmatched = ($catLevel <= $level);
							break;
					}
					if ( $parentmatched) {
						$option->disable=true;
					}
					if ( $levelmatched || $parentmatched ) {
						break;
					}

					unset($options[$key1]);
					unset($cats[$key2]); // make next loop smaller
					break;
				}
			}
		}
		return $options;
	}
}
