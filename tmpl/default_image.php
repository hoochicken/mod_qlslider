<?php
/**
 * @package		mod_qlslider
 * @copyright	Copyright (C) 2017 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die ('Restricted access'); ?>
<?php
$action=$params->get('link_image',1);
$attr='';
$imagePath=$slide->image;
if(file_exists($slide->thumb))$imagePath=$slide->thumb;

if(1<$action AND file_exists($slide->resized))$href=$slide->resized;
elseif(1<$action)$href=$slide->image;
else $href=$slide->link;

//echo '<pre>';print_r($slide);die;
if(1<$action)
{
    if($obj_helper->jquery) $attr='class="image-link"';
    else $attr='rel="lightbox-slider'.$module->id.'"';
    if($params->get('show_desc')) $attr.= ' title="'.(!empty($slide->title) ? htmlspecialchars($slide->title.' ') : '').htmlspecialchars('<small>'.strip_tags($slide->description,"<p><a><b><strong><em><i><u>").'</small>').'"';
}
if (($slide->link && $action==1) || $action>1) : ?>
    <a <?php echo $attr; ?> href="<?php echo $href; ?>" target="<?php echo $slide->target; ?>">
<?php endif ?>
    <img class="qlslider" src="<?php echo $imagePath; ?>" alt="<?php echo $slide->alt; ?>"/>
<?php if (($slide->link && $action==1) || $action>1) : ?>
    </a>
<?php endif;