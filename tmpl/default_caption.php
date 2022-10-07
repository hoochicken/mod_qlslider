<?php
/**
 * @package		mod_qlslider
 * @copyright	Copyright (C) 2022 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ('Restricted access'); ?>
<div class="caption">
    <?php if($params->get('show_title')) : ?>
        <div class="title">
            <?php if($params->get('link_title') && $slide->link) { ?><a href="<?php echo $slide->link; ?>" target="<?php echo $slide->target; ?>"><?php } ?>
                <?php echo $slide->title; ?>
                <?php if($params->get('link_title') && $slide->link) { ?></a><?php } ?>
        </div>
    <?php endif; ?>

    <?php if($params->get('show_desc')) : ?>
        <div class="description">
            <?php if($params->get('link_desc') && $slide->link) : ?>
                <a href="<?php echo $slide->link; ?>" target="<?php echo $slide->target; ?>">
                    <?php echo strip_tags($slide->description,"<br><span><em><i><b><strong><small><big>"); ?>
                </a>
            <?php else : ?>
                <?php echo $slide->description; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>