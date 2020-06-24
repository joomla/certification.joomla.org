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

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('formbehavior.chosen', 'select');
JHtml::_('behavior.keepalive');
$componentParams = $this->params; // will be removed just use $this->params instead
?>
<script type="text/javascript">
	// waiting spinner
	var outerDiv = jQuery('body');
	jQuery('<div id="loading"></div>')
		.css("background", "rgba(255, 255, 255, .8) url('components/com_jcpqm/assets/images/import.gif') 50% 15% no-repeat")
		.css("top", outerDiv.position().top - jQuery(window).scrollTop())
		.css("left", outerDiv.position().left - jQuery(window).scrollLeft())
		.css("width", outerDiv.width())
		.css("height", outerDiv.height())
		.css("position", "fixed")
		.css("opacity", "0.80")
		.css("-ms-filter", "progid:DXImageTransform.Microsoft.Alpha(Opacity = 80)")
		.css("filter", "alpha(opacity = 80)")
		.css("display", "none")
		.appendTo(outerDiv);
	jQuery('#loading').show();
	// when page is ready remove and show
	jQuery(window).load(function() {
		jQuery('#jcpqm_loader').fadeIn('fast');
		jQuery('#loading').hide();
	});
</script>
<div id="jcpqm_loader" style="display: none;">
<form action="<?php echo JRoute::_('index.php?option=com_jcpqm&layout=edit&id='. (int) $this->item->id . $this->referral); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">

	<?php echo JLayoutHelper::render('question.details_above', $this); ?>
<div class="form-horizontal">

	<?php echo JHtml::_('bootstrap.startTabSet', 'questionTab', array('active' => 'details')); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'questionTab', 'details', JText::_('COM_JCPQM_QUESTION_DETAILS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<?php echo JLayoutHelper::render('question.details_left', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php echo JHtml::_('bootstrap.addTab', 'questionTab', 'answers', JText::_('COM_JCPQM_QUESTION_ANSWERS', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('question.answers_left', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('question.answers_right', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>

	<?php $this->ignore_fieldsets = array('details','metadata','vdmmetadata','accesscontrol'); ?>
	<?php $this->tab_name = 'questionTab'; ?>
	<?php echo JLayoutHelper::render('joomla.edit.params', $this); ?>

	<?php if ($this->canDo->get('question.delete') || $this->canDo->get('core.edit.created_by') || $this->canDo->get('question.edit.state') || $this->canDo->get('core.edit.created')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'questionTab', 'publishing', JText::_('COM_JCPQM_QUESTION_PUBLISHING', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span6">
				<?php echo JLayoutHelper::render('question.publishing', $this); ?>
			</div>
			<div class="span6">
				<?php echo JLayoutHelper::render('question.metadata', $this); ?>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php if ($this->canDo->get('core.admin')) : ?>
	<?php echo JHtml::_('bootstrap.addTab', 'questionTab', 'permissions', JText::_('COM_JCPQM_QUESTION_PERMISSION', true)); ?>
		<div class="row-fluid form-horizontal-desktop">
			<div class="span12">
				<fieldset class="adminform">
					<div class="adminformlist">
					<?php foreach ($this->form->getFieldset('accesscontrol') as $field): ?>
						<div>
							<?php echo $field->label; echo $field->input;?>
						</div>
						<div class="clearfix"></div>
					<?php endforeach; ?>
					</div>
				</fieldset>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>
	<?php endif; ?>

	<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<div>
		<input type="hidden" name="task" value="question.edit" />
		<?php echo JHtml::_('form.token'); ?>
	</div>
</div>

<div class="clearfix"></div>
<?php echo JLayoutHelper::render('question.details_under', $this); ?>
</form>
</div>

<script type="text/javascript">

// #jform_questiontype listeners for questiontype_vvvvvvv function
jQuery('#jform_questiontype').on('keyup',function()
{
	var questiontype_vvvvvvv = jQuery("#jform_questiontype").val();
	vvvvvvv(questiontype_vvvvvvv);

});
jQuery('#adminForm').on('change', '#jform_questiontype',function (e)
{
	e.preventDefault();
	var questiontype_vvvvvvv = jQuery("#jform_questiontype").val();
	vvvvvvv(questiontype_vvvvvvv);

});

// #jform_questiontype listeners for questiontype_vvvvvvw function
jQuery('#jform_questiontype').on('keyup',function()
{
	var questiontype_vvvvvvw = jQuery("#jform_questiontype").val();
	vvvvvvw(questiontype_vvvvvvw);

});
jQuery('#adminForm').on('change', '#jform_questiontype',function (e)
{
	e.preventDefault();
	var questiontype_vvvvvvw = jQuery("#jform_questiontype").val();
	vvvvvvw(questiontype_vvvvvvw);

});

// #jform_questiontype listeners for questiontype_vvvvvvx function
jQuery('#jform_questiontype').on('keyup',function()
{
	var questiontype_vvvvvvx = jQuery("#jform_questiontype").val();
	vvvvvvx(questiontype_vvvvvvx);

});
jQuery('#adminForm').on('change', '#jform_questiontype',function (e)
{
	e.preventDefault();
	var questiontype_vvvvvvx = jQuery("#jform_questiontype").val();
	vvvvvvx(questiontype_vvvvvvx);

});

// #jform_questiontype listeners for questiontype_vvvvvvy function
jQuery('#jform_questiontype').on('keyup',function()
{
	var questiontype_vvvvvvy = jQuery("#jform_questiontype").val();
	vvvvvvy(questiontype_vvvvvvy);

});
jQuery('#adminForm').on('change', '#jform_questiontype',function (e)
{
	e.preventDefault();
	var questiontype_vvvvvvy = jQuery("#jform_questiontype").val();
	vvvvvvy(questiontype_vvvvvvy);

});

// #jform_questiontype listeners for questiontype_vvvvvvz function
jQuery('#jform_questiontype').on('keyup',function()
{
	var questiontype_vvvvvvz = jQuery("#jform_questiontype").val();
	vvvvvvz(questiontype_vvvvvvz);

});
jQuery('#adminForm').on('change', '#jform_questiontype',function (e)
{
	e.preventDefault();
	var questiontype_vvvvvvz = jQuery("#jform_questiontype").val();
	vvvvvvz(questiontype_vvvvvvz);

});

// #jform_questiontype listeners for questiontype_vvvvvwa function
jQuery('#jform_questiontype').on('keyup',function()
{
	var questiontype_vvvvvwa = jQuery("#jform_questiontype").val();
	vvvvvwa(questiontype_vvvvvwa);

});
jQuery('#adminForm').on('change', '#jform_questiontype',function (e)
{
	e.preventDefault();
	var questiontype_vvvvvwa = jQuery("#jform_questiontype").val();
	vvvvvwa(questiontype_vvvvvwa);

});

</script>
