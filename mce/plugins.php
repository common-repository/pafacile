<?php
# Since ver 1.5.8
class PAFacileTinyMCEPlugins{

	
	static function add($plugins){
		$plugins['PAFacile'] =  toSendItGenericMethods::pluginDirectory() .'/mce/editor_plugin.dev.js';
		return $plugins;
	}
	
	static function registerButton($buttons){
		array_push($buttons,'|','PAFacile'); 
		return $buttons;
	}
	
	static function init(){
		if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') ) {     
			return;   
		}
		if ( get_user_option('rich_editing') == 'true' ) {     
			add_filter( 'mce_external_plugins', array('PAFacileTinyMCEPlugins', 'add') );     
			add_filter( 'mce_buttons', array('PAFacileTinyMCEPlugins', 'registerButton') );   
		
		}
			
	}
	
}


add_action('init', array('PAFacileTinyMCEPlugins','init'));

?>