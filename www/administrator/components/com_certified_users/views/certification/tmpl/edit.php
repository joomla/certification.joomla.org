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
HTMLHelper::_('behavior.tooltip');
HTMLHelper::_('behavior.formvalidation');
HTMLHelper::_('formbehavior.chosen', 'select');
HTMLHelper::_('behavior.keepalive');

// Import CSS
$document = Factory::getDocument();
$document->addStyleSheet(Uri::root() . 'media/com_certified_users/css/form.css');
?>
<script type="text/javascript">
    js = jQuery.noConflict();
    js(document).ready(function () {

    });

    Joomla.submitbutton = function (task) {
        if (task == 'certification.cancel') {
            Joomla.submitform(task, document.getElementById('certification-form'));
        } else {

            if (task != 'certification.cancel' && document.formvalidator.isValid(document.id('certification-form'))) {

                Joomla.submitform(task, document.getElementById('certification-form'));
            } else {
                alert('<?php echo $this->escape(Text::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
            }
        }
    }
</script>

<form
        action="<?php echo JRoute::_('index.php?option=com_certified_users&layout=edit&id=' . (int) $this->item->id); ?>"
        method="post" enctype="multipart/form-data" name="adminForm" id="certification-form"
        class="form-validate form-horizontal">


    <input type="hidden" name="jform[id]" value="<?php echo $this->item->id; ?>"/>
    <input type="hidden" name="jform[ordering]" value="<?php echo $this->item->ordering; ?>"/>
    <input type="hidden" name="jform[state]" value="<?php echo $this->item->state; ?>"/>
    <input type="hidden" name="jform[checked_out]" value="<?php echo $this->item->checked_out; ?>"/>
    <input type="hidden" name="jform[checked_out_time]" value="<?php echo $this->item->checked_out_time; ?>"/>
	<?php echo $this->form->renderField('created_by'); ?>
	<?php echo $this->form->renderField('modified_by'); ?>
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'exam')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'exam', JText::_('COM_CERTIFIED_USERS_TAB_EXAM', true)); ?>
    <div class="row-fluid">
        <div class="span10 form-horizontal">
            <fieldset class="adminform">
				<?php echo $this->form->renderField('name'); ?>
				<?php echo $this->form->renderField('badge'); ?>
				<?php if ($this->state->params->get('save_history', 1)) : ?>
                    <div class="control-group">
                        <div class="control-label"><?php echo $this->form->getLabel('version_note'); ?></div>
                        <div class="controls"><?php echo $this->form->getInput('version_note'); ?></div>
                    </div>
				<?php endif; ?>
            </fieldset>
        </div>
    </div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>


	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

    <input type="hidden" name="task" value=""/>
	<?php echo JHtml::_('form.token'); ?>

</form>
