<?php
/**
 * @package    DPCalendar
 * @author     Digital Peak http://www.digital-peak.com
 * @copyright  Copyright (C) 2007 - 2019 Digital Peak. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 */
defined('_JEXEC') or die();

if (!$this->event->description && !$this->event->displayEvent->afterDisplayContent) {
	return;
}
?>
<div class="com-dpcalendar-event__description">
	<h3 class="dp-heading"><?php echo $this->translate('COM_DPCALENDAR_DESCRIPTION'); ?></h3>
	<div class="com-dpcalendar-event__description-content">
		<?php echo JHTML::_('content.prepare', $this->event->description); ?>
	</div>
          
<!-- Added Exam instructions and seat buy --> 
    <div class="exam-instructions">
          {module 177}
<?php echo $this->event->displayEvent->beforeDisplayContent; ?>
		<dl class="dp-description url">
			<dt class="dp-description__label hide">Buy your seat</dt>
			<dd class="dp-description__description">
				<?php $u = JUri::getInstance($this->event->url); ?>
				<a href="<?php echo $this->event->url; ?>" class="btn btn-large btn-success"
				   target="<?php echo $u->getHost() && JUri::getInstance()->getHost() != $u->getHost() ? '_blank' : ''; ?>">
					Buy your seat
				</a>
			</dd>
		</dl>
          </div>
	<div class="com-dpcalendar-event__event-text">
		<?php echo $this->event->displayEvent->afterDisplayContent; ?>
	</div>
</div>
