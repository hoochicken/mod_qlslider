<?php
/**
 * @package		mod_qlslider
 * @copyright	Copyright (C) 2017 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
    jQuery(document).ready(
        function()
        {
            jQuery("#qlslider<?php echo $module->id; ?>").qlslider({activate: function(){}, timerAnimSlide:400, infinite:true, resizeItem:{width:50}, responsive:{minWidth:<?php echo $params->get('image_width',240);?>}});
        });
</script>
<div id="qlslider<?php echo $module->id; ?>" class="qlslider">
    <div class="qlsliderContent">
        <div class="content centered<?php echo (string)$params->get('image_centering',0);?>">
            <div class="items">
                <?php foreach ($slides as $slide) : ?>
                    <div class="item">
                        <?php if($slide->image) require JModuleHelper::getLayoutPath('mod_qlslider', $params->get('layout','default').'_image'); ?>
                        <?php //if(($params->get('show_title') || ($params->get('show_desc') && !empty($slide->description) || ($params->get('show_readmore') && $slide->link)))) require JModuleHelper::getLayoutPath('mod_qlslider', $params->get('layout','default').'_description'); ?>
                        <?php if(true==$slide->caption) require JModuleHelper::getLayoutPath('mod_qlslider', $params->get('layout','default').'_caption'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php if(1==$params->get('navigationShow',1)) require JModuleHelper::getLayoutPath('mod_qlslider', $params->get('layout','default').'_navigation'); ?>
</div>