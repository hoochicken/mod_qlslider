<?php
/**
 * @package		mod_qlslider
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ('Restricted access'); ?>


<div class="qlsliderNavigation pos<?php echo $params->get('navigationPosition',1);?>">
    <a href="#" class="prev"><span class="icon-leftarrow">&nbsp;</span></a>
    <a href="#" class="next"><span class="icon-rightarrow">&nbsp;</span></a>
    <div class="clear"></div>
</div>
<?php /*
<div id="navigation<?php echo $module->id; ?>" class="navigation-container" style="<?php echo $style['navi'] ?>">
    <?php if($show->arr) : ?>
        <img id="prev<?php echo $module->id; ?>" class="prev-button <?php echo $show->arr==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->prev; ?>" alt="<?php echo $direction == 'rtl' ? JText::_('MOD_QLSLIDER_NEXT') : JText::_('MOD_QLSLIDER_PREVIOUS'); ?>" />
        <img id="next<?php echo $module->id; ?>" class="next-button <?php echo $show->arr==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->next; ?>" alt="<?php echo $direction == 'rtl' ? JText::_('MOD_QLSLIDER_PREVIOUS') : JText::_('MOD_QLSLIDER_NEXT'); ?>" />
    <?php endif; ?>
    <?php if($show->btn) : ?>
        <img id="play<?php echo $module->id; ?>" class="play-button <?php echo $show->btn==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->play; ?>" alt="<?php echo JText::_('MOD_QLSLIDER_PLAY'); ?>" />
        <img id="pause<?php echo $module->id; ?>" class="pause-button <?php echo $show->btn==1 ? 'showOnHover':'' ?>" src="<?php echo $navigation->pause; ?>" alt="<?php echo JText::_('MOD_QLSLIDER_PAUSE'); ?>" />
    <?php endif; ?>
</div>*/