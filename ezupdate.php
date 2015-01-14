<?php

/*
  Copyright (C) 2008 www.ads-ez.com

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 3 of the License, or (at
  your option) any later version.

  This program is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
  General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

$oldPath = get_include_path();
$path = $oldPath . PATH_SEPARATOR .
  ABSPATH. "wp-admin/includes/" . PATH_SEPARATOR .
  ABSPATH. "wp-includes";
set_include_path($path);
include_once("class-wp-upgrader.php") ;
set_include_path($oldPath);
if (!class_exists('Ez_Plugin_Upgrader')) {

  class Ez_Plugin_Upgrader extends Plugin_Upgrader {

    function install($package, $args = array()) {

      $defaults = array(
          'clear_update_cache' => true,
      );
      $parsed_args = wp_parse_args($args, $defaults);

      $this->init();
      $this->install_strings();
      $this->upgrade_strings(); // First diff: need these strings

      add_filter('upgrader_source_selection', array($this, 'check_package'));

      $this->run(array(
          'package' => $package,
          'destination' => WP_PLUGIN_DIR,
          'clear_destination' => true, // Second diff: overwrite files.
          'clear_working' => true,
          'hook_extra' => array(
              'type' => 'plugin',
              'action' => 'install',
          )
      ));

      remove_filter('upgrader_source_selection', array($this, 'check_package'));

      if (!$this->result || is_wp_error($this->result))
        return $this->result;

      // Force refresh of plugin update information
      wp_clean_plugins_cache($parsed_args['clear_update_cache']);

      return true;
    }

  }

  function ezUpdate(){
    if ( ! current_user_can('install_plugins') )
      wp_die(__('You do not have sufficient permissions to install plugins for this site.'));

    $file_upload = new File_Upload_Upgrader('pluginzip', 'package');

    $title = __('Upload Plugin');
    $parent_file = 'plugins.php';
    $submenu_file = 'plugin-install.php';
    require_once(ABSPATH . 'wp-admin/admin-header.php');

    $title = sprintf( __('Installing Plugin from uploaded file: %s'), basename( $file_upload->filename ) );
    $nonce = 'plugin-upload';
    $url = add_query_arg(array('package' => $file_upload->id), 'update.php?action=ezupdate-plugin');
    $type = 'upload';

    $upgrader = new Ez_Plugin_Upgrader( new Plugin_Installer_Skin( compact('type', 'title', 'nonce', 'url') ) );
    $result = $upgrader->install( $file_upload->package );

    if ( $result || is_wp_error($result) )
      $file_upload->cleanup();

    include(ABSPATH . 'wp-admin/admin-footer.php');
  }
  add_action("update-custom_ezupdate-plugin", 'ezUpdate') ;
}
