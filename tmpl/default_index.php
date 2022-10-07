<?php
/**
 * @package		mod_qlslider
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ('Restricted access'); ?>
<div id="cust-navigation<?php echo $module->id; ?>" class="<?php echo $params->get('idx_style', 0) ? 'navigation-numbers' : 'navigation-container-custom' ?> <?php echo $show->idx==2 ? 'showOnHover':'' ?>">
    <?php $i = 0;
    foreach ($slides as $slide) :?>
        <span class="load-button<?php if ($i == 0) echo ' load-button-active'; ?>"><?php if($params->get('idx_style')) echo ($i+1) ?></span>
    <?php $i++;
    endforeach; ?>
</div>