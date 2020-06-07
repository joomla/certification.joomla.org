<?php
/**
 * @version    1.0.0
 * @package    Com_Certified_users
 * @author     The Joomla Project <secretary@opensourcematters.org>
 * @copyright  2020 The Joomla Project
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access
use Joomla\CMS\Factory;
use \Joomla\CMS\Uri\Uri;

defined('_JEXEC') or die;

$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_certified_users/css/styles.css');
?>
<div class="certification_back_link">
    <a href="<?php echo JRoute::_('index.php?option=com_certified_users&view=certified_users'); ?>">
		<?php echo JText::_('COM_CERTIFIED_USERS_GO_BACK'); ?>
    </a>
</div>

<div class="certification-profile-top">
    <div class="certification-profile-image">
        <img src="<?php echo $this->item->user_image; ?>"/>
    </div>
    <div class="certification-profile-data">
        <div class="certification-profile-name"><?php echo $this->item->user_name; ?></div>
		<?php if (!empty($this->item->user_email) && filter_var($this->item->user_email, FILTER_VALIDATE_EMAIL)) { ?>
            <div class="certification-profile-personal">
				<?php echo '<div class="certification-profile-email">' . JHtml::_('email.cloak', $this->item->user_email) . '</div>'; ?>
            </div>
		<?php } ?>
		<?php if (!empty($this->item->user_website)) { ?>
            <div class="certification-profile-url">
				<?php echo $this->item->user_website; ?>
            </div>
		<?php } ?>
        <div class="certfication-profile-location">
			<?php echo $this->item->user_location; ?>
        </div>
    </div>
</div>

<br/>

<div class="certification-profile-badges">
    <table class="certification-listing-table certification-profile-table">
        <thead>
        <tr>
            <th>
                <strong></strong>
            </th>
            <th>
                <strong><?php echo JText::_('COM_CERTIFIED_USERS_LBL_CERTIFICATION'); ?></strong>
            </th>
            <th>
                <strong><?php echo JText::_('COM_CERTIFIED_USERS_LBL_CERTIFIED_ON'); ?></strong>
            </th>
        </tr>
        </thead>
        <tbody>
		<?php foreach ($this->item->certifications as $certification) { ?>
            <tr>
                <td>
                    <img src="<?php echo $certification->badge; ?>" />
                </td>
                <td><?php echo $certification->name; ?></td>
                <td><?php echo $certification->certified_on; ?></td>
            </tr>
		<?php } ?>
        </tbody>
        <tfoot>
        <tr>
            <th colspan="3">&nbsp;</th>
        </tr>
        </tfoot>
    </table>

</div>


