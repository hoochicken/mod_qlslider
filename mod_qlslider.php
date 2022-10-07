<?php
/**
 * @package        mod_qlslider
 * @copyright    Copyright (C) 2022 ql.de All rights reserved.
 * @author        Mareike Riegel mareike.riegel@ql.de
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');

// Include the syndicate functions only once
require_once(dirname(__FILE__) . '/helper.php');
$app = JFactory::getApplication();
$document = JFactory::getDocument();
$obj_helper = new modQlsliderHelper($params, $module);

// taking the slides from the source
$slides = $obj_helper->getSlides();
if (false == $slides) return;
//if(1==$params->get('resize',0) AND !function_exists('imagescale'))JFactory::getApplication()->enqueueMessage(JText::_('MOD_QLSLIDER_MSG_FUNCTIONIMAGESCALENOTFOUND'));
//echo '<pre>';print_r($slides);die;
$obj_helper->addJquery();
$obj_helper->addScripts();
$obj_helper->addStyles();

if (!is_numeric($count = $params->get('visible_images'))) $count = 3;
if (!is_numeric($width = $params->get('image_width'))) $width = 240;
if (!is_numeric($height = $params->get('image_height'))) $height = 180;
if (!is_numeric($max = $params->get('max_images'))) $max = 20;
if (!is_numeric($spacing = $params->get('space_between_images'))) $spacing = 10;
if (!is_numeric($preload = $params->get('preload'))) $preload = 800;
if ($count > count($slides)) $count = count($slides);
if ($count < 1) $count = 1;
if ($count > $max) $count = $max;
$theme = $params->get('theme', 'default');
$slider_type = $params->get('slider_type', 0);
switch ($slider_type) {
    case 2:
        $slide_size = $width;
        $count = 1;
        break;
    case 1:
        $slide_size = $height + $spacing;
        break;
    case 0:
    default:
        $slide_size = $width + $spacing;
        break;
}

require JModuleHelper::getLayoutPath('mod_qlslider', $params->get('layout', 'default'));