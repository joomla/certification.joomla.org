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

use \Joomla\CMS\HTML\HTMLHelper;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;
use \Joomla\CMS\Router\Route;
use \Joomla\CMS\Language\Text;

HTMLHelper::addIncludePath(JPATH_COMPONENT . '/helpers/html');
HTMLHelper::_('bootstrap.tooltip');
HTMLHelper::_('behavior.multiselect');
HTMLHelper::_('formbehavior.chosen', 'select');

$user      = Factory::getUser();
$userId    = $user->get('id');
$listOrder = $this->state->get('list.ordering');
$listDirn  = $this->state->get('list.direction');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_certified_users/css/list.css');
$document->addStyleSheet(Uri::root() . 'media/com_certified_users/css/styles.css');

?>
<div class="certification-listing">
    <div class="certification-listing-header">
        <div class="certification-listing-intro">
            <h2><?php echo $this->params->get('directory_title'); ?></h2>
            <p><?php echo $this->params->get('directory_intro'); ?></p>
            <div class="certification-listing-header-images">
                <img src="<?php echo $this->params->get('directory_badge'); ?>">
                <img src="<?php echo $this->params->get('directory_logo'); ?>">
            </div>
        </div>
        <div class="certification-listing-cert-image">
            <img src="<?php echo $this->params->get('directory_image'); ?>">
        </div>
    </div>

    <form action="<?php echo htmlspecialchars(Uri::getInstance()->toString()); ?>" method="post"
          name="adminForm" id="adminForm">
        <div class="table-responsive">
            <table class="certification-listing-table table table-striped" id="certified_userList">
                <thead>
                <tr>
                    <th>

                    </th>
                    <th>
                        <strong><?php echo JText::_('COM_CERTIFIED_USERS_LBL_USER_NAME'); ?></strong>
                    </th>
                    <th>
                        <strong><?php echo JText::_('COM_CERTIFIED_USERS_LBL_USER_LOCATION'); ?></strong>
                    </th>
                    <th>
                        <strong><?php echo JText::_('COM_CERTIFIED_USERS_LBL_CERTIFIED_ON'); ?></strong>
                    </th>
                    <th>
                        <strong><?php echo JText::_('COM_CERTIFIED_USERS_LBL_CERTIFICATIONS'); ?></strong>
                    </th>

                </tr>
                </thead>
                <tbody>
				<?php foreach ($this->items as $i => $item) : ?>

                    <tr class="row<?php echo $i % 2; ?>">


                        <td class="cud-image" rowspan="<?php echo count($item->certifications); ?>">
                            <a href="<?php echo JRoute::_('index.php?option=com_certified_users&view=certified_user&id='.(int) $item->id); ?>">
                                <img src="<?php echo $item->user_image; ?>" width="106" height="106"/>
                            </a>
                        </td>
                        <td class="cud-name" rowspan="<?php echo count($item->certifications); ?>">
                            <a href="<?php echo JRoute::_('index.php?option=com_certified_users&view=certified_user&id='.(int) $item->id); ?>">
								<?php echo $item->user_name; ?>
                            </a>
                        </td>
                        <td class="cud-location" rowspan="<?php echo count($item->certifications); ?>">
							<?php echo $item->user_location; ?>
                        </td>
                        <td class="cud-date">
							<?php foreach ($item->certifications as $certification){
								echo $certification->certified_on . '<br />';
							} ?>
                        </td>
                        <td class="cud-exam">
							<?php foreach ($item->certifications as $certification){
								echo $certification->name . '<br />';
							} ?>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
                <tfoot>
                <tr>
                    <td colspan="<?php echo isset($this->items[0]) ? count(get_object_vars($this->items[0])) : 10; ?>">
						<?php echo $this->pagination->getListFooter(); ?>
						<?php echo $this->pagination->getResultsCounter(); ?>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>

        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="boxchecked" value="0"/>
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>"/>
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>"/>
		<?php echo HTMLHelper::_('form.token'); ?>
    </form>
</div>