<?php
/**
 * @package		mod_qlslider
 * @copyright	Copyright (C) 2017 ql.de All rights reserved.
 * @author 		Mareike Riegel mareike.riegel@ql.de
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die;

/**
 * Script file of HelloWorld module
 */
class mod_qlsliderInstallerScript
{
    /**
     * Method to install the extension
     * $parent is the class calling this method
     *
     * @return void
     */
    function install($parent)
    {
        echo '<p>'.JText::_('MOD_QLSLIDER_INSTALL_INSTALL').'</p>';
    }

    /**
     * Method to uninstall the extension
     * $parent is the class calling this method
     *
     * @return void
     */
    function uninstall($parent)
    {
        echo '<p>'.JText::_('MOD_QLSLIDER_INSTALL_UNINSTALL').'</p>';
    }

    /**
     * Method to update the extension
     * $parent is the class calling this method
     *
     * @return void
     */
    function update($parent)
    {
        echo '<p>'.JText::sprintf('MOD_QLSLIDER_INSTALL_UPDATE',$parent->get('manifest')->version).'</p>';
	}

    /**
     * Method to run before an install/update/uninstall method
     * $parent is the class calling this method
     * $type is the type of change (install, update or discover_install)
     *
     * @return void
     */
    function preflight($type, $parent)
    {
        //echo '<p>'.JText::sprintf('MOD_QLSLIDER_INSTALL_PREFLIGHT'),'</p>';
    }

    /**
     * Method to run after an install/update/uninstall method
     * $parent is the class calling this method
     * $type is the type of change (install, update or discover_install)
     *
     * @return void
     */
    function postflight($type, $parent)
    {
        //echo '<p>'.JText::sprintf('MOD_QLSLIDER_INSTALL_POSTFLIGHT'),'</p>';
    }
}