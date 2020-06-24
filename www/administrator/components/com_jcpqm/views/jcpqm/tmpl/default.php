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

JHtml::_('behavior.tooltip');

?>
<div id="j-main-container">
	<div class="span9">
<!--[INSERTED$$$$]--><!--9-->
        <div class="span12">
<!--[/INSERTED$$$$]-->
		<?php echo JHtml::_('bootstrap.startAccordion', 'dashboard_left', array('active' => 'main')); ?>
			<?php echo JHtml::_('bootstrap.addSlide', 'dashboard_left', 'cPanel', 'main'); ?>
				<?php echo $this->loadTemplate('main');?>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
<!--[INSERTED$$$$]--><!--11-->
        </div>
        <div class="jcpqm modules span12">
            <?php echo JHtml::_('content.prepare', '{loadposition jcpqm}');?>
        </div>
<!--[/INSERTED$$$$]-->
	</div>
	<div class="span3">
		<?php echo JHtml::_('bootstrap.startAccordion', 'dashboard_right', array('active' => 'vdm')); ?>
			<?php echo JHtml::_('bootstrap.addSlide', 'dashboard_right', 'Joomla!', 'vdm'); ?>
				<?php echo $this->loadTemplate('vdm');?>
			<?php echo JHtml::_('bootstrap.endSlide'); ?>
		<?php echo JHtml::_('bootstrap.endAccordion'); ?>
	</div>
</div>