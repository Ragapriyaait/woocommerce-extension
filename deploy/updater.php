<?php
/**
 * Get Github Latest Version
 */

function wpme_woo_com_forms_get_github_version(){
  static $checked_version = null;
  if($checked_version !== null){
    return $checked_version;
  }

  $response = json_decode(wp_remote_retrieve_body(
    wp_remote_get(
      'https://raw.githubusercontent.com/Ragapriyaait/woocommerce-extension/main/version.json'
    )
  ), true);
  if(!is_array($response) || !array_key_exists('version', $response)){
    return null;
  }
  $checked_version = $response['version'];
  return $response['version'];
}

/**
 * Updater init
 */

function wpme_woo_com_forms_updater_init($file){
 $GLOBALS['wpme_woo_com_downloadLink'] = 'https://github.com/Ragapriyaait/woocommerce-extension/archive/main.zip';
  $GLOBALS['wpme_woo_com_plugin'] = null;
  $GLOBALS['wpme_woo_com_basename'] = null;
  $GLOBALS['wpme_woo_com_active'] = null;
  static $version = null;

  /**
   * Updater
   */
  add_action('admin_init', function() use ($file) {
    //  Get the basics
    $GLOBALS['wpme_woo_com_plugin'] = get_plugin_data($file);
    $GLOBALS['wpme_woo_com_basename'] = plugin_basename($file);
    $GLOBALS['wpme_woo_com_active'] = is_plugin_active($GLOBALS['wpme_woo_com_basename']);
  });

  // Add update filter
  add_filter('site_transient_update_plugins', function($transient) use ($file, $version) {
    if($transient && property_exists( $transient, 'checked') ) {
      if( $checked = $transient->checked && isset($GLOBALS['wpme_woo_com_plugin'])) { 
        $version = $version === null ? wpme_woo_com_forms_get_github_version() : $version;
        $out_of_date = version_compare($version, $GLOBALS['wpme_woo_com_plugin']['Version'], 'gt');
        if($out_of_date){
          $slug = current(explode('/', $GLOBALS['wpme_woo_com_basename']));
          $plugin = array(
            'url' => isset($GLOBALS['wpme_woo_com_plugin']['PluginURI']) ? $GLOBALS['wpme_woo_com_plugin']['PluginURI'] : '',
            'slug' => $slug,
            'package' => $GLOBALS['wpme_woo_com_downloadLink'],
            'new_version' => $version,
          );
          $transient->response[$GLOBALS['wpme_woo_com_basename']] = (object)$plugin; 
        }
      }
    }
    return $transient;
  }, 10, 1 );

  // Add pop up filter
  add_filter('plugins_api', function($result, $action, $args) use ($file, $version){
		if( ! empty( $args->slug ) ) { // If there is a slug
			if( $args->slug == current( explode( '/' , $GLOBALS['wpme_woo_com_basename']))) { // And it's our slug
        $version = $version === null ? wpme_woo_com_forms_get_github_version() : $version;
        // Set it to an array
				$plugin = array(
					'name'				=> $GLOBALS['wpme_woo_com_plugin']["Name"],
					'slug'				=> $GLOBALS['wpme_woo_com_basename'],
					'requires'	  => '',
					'tested'			=> '',
					'rating'			=> '100.0',
					'num_ratings'	=> '10',
					'downloaded'	=> '134',
					'added'				=> '2016-01-05',
					'version'			=> $version,
					'author'			=> $GLOBALS['wpme_woo_com_plugin']["AuthorName"],
					'author_profile'	=> $GLOBALS['wpme_woo_com_plugin']["AuthorURI"],
					'last_updated'		=> '',
					'homepage'			=> $GLOBALS['wpme_woo_com_plugin']["PluginURI"],
					'short_description' => $GLOBALS['wpme_woo_com_plugin']["Description"],
					'sections'			=> array(
						'Description'	=> $GLOBALS['wpme_woo_com_plugin']["Description"],
						'Updates'		=> $version,
					),
					'download_link'		=> $GLOBALS['wpme_woo_com_downloadLink'],
				);
				return (object)$plugin;
			}
		}
		return $result;
  }, 10, 3);

  // Add install filter
  add_filter('upgrader_post_install', function($response, $hook_extra, $result) use($file) {
    global $wp_filesystem;
    $install_directory = plugin_dir_path($file);
    $wp_filesystem->move( $result['destination'], $install_directory);
    $result['destination'] = $install_directory;
    if ($GLOBALS['wpme_woo_com_active']) { // If it was active
			activate_plugin($GLOBALS['wpme_woo_com_basename']); // Reactivate
		}
  }, 10, 3 );
}

