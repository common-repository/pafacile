<?php

class PAFacileFrontend{
	public static $metaInfo = null;
		
	
	
	static function manageShortcode($params, $shortCode){
		
		if($params[0] == 'statistiche'){
			require_once PAFACILE_PLUING_DIRECTORY .'/google-analytics/index.php';
			$ga = new PAFacileGoogleAnalytics();
			return $ga->getCount($params[1]);
		}else{
			
			if($params[1] == 'opendata'){
				
				return PAFacileOpendata::makeLinkList($params[0]);
				
			}else{
				return toSendItPAFacile::replaceContents($params);
			}
		}
	}
	
	static function isPublicPage(){
		$permalink = get_permalink();
		$s = get_option('PAFacile_permalinks', array());
		foreach($s as $key => $value){
			if($value!=0 && substr($key, -3)== '_id'){
				
				if(get_permalink($value) == $permalink){
					
					return $key;
				}
			}
		}
		return false;
		
	}

	
	static function setTemplateHeader(){
		?>
		<!-- PAFacile Header Template -->
		<?php 
		$generalSettings = get_option('PAFacile_settings');
			
		if($generalSettings['addDoublinCoreHeaders']!='n'){
			?>
			<link rel="schema.DC" href="http://purl.org/dc/elements/1.1/" />
			<link rel="schema.DCTERMS" href="http://purl.org/dc/terms/" />
			<?php 
		}
		?>
		<link rel="stylesheet" type="text/css" href="<?php echo toSendItGenericMethods::pluginDirectory(); ?>/pafacile.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo toSendItGenericMethods::pluginDirectory(); ?>/filetypes.css" />
		<?php 
		if(file_exists( WP_PLUGIN_DIR . '/pafacile-custom.css')){
			?>
			<link rel="stylesheet" type="text/css" href="<?php echo WP_PLUGIN_URL ?>/pafacile-custom.css" />
			<?php
		}
		$publicPageKey = self::isPublicPage();
		if($publicPageKey!==false){
			
			/*
			 * Since Ver 2.6.0
			 */
			
			$formati = PAFacileOpendata::getAllowedFormats();
			$formatiMime = PAFacileOpendata::getFormatTypes();
			if(count($formati)>0){
				$formati = array_keys($formati);
				
				$plink = get_permalink();
				if(preg_match('#\?#', $plink)){
					$plink .='&opendata=';
				}else{
					$plink .='?opendata=';
				}
				foreach($formati as $formato){
					$formatoMime = $formatiMime[$formato];
					echo("<link rel=\"alternative\" type=\"$formatoMime\" content=\"$plink$formato\" />\n");
				}
			}
			// Devo ottenere le informazioni sui metadati da pubblicare
			$metaKey = preg_replace('/^[^_]+_[^_]+_/','', $publicPageKey);
			$metaKey = preg_replace('/_.*$/','', $metaKey);
			
			
			if(isset($_GET['itemId'])) 	$metaSuffix = '_ddc'; else
										$metaSuffix = '_ldc'; 
			
			self::prepareMetaInfo($metaKey);
			if(isset($generalSettings["$metaKey$metaSuffix"])){
				$metaData = $generalSettings["$metaKey$metaSuffix"];
				$metaKeys = split("\r?\n", $metaData);
				for($i = 0; $i<count($metaKeys); $i++){
					if($metaKeys[$i]!=''){
						/*
						 * Since ver. 2.6.0
						 * Se il metadato non è strutturato correttamente non lo presento nell'header.
						 */
						
						if(preg_match('#=#', $metaKeys[$i])){
							list($name,$value) = split('=',$metaKeys[$i]);
							
							$value = self::parseMetaInfo($value);
							echo("<meta name=\"$name\" content=\"$value\" />\n");
						}
					}

				}
				
			}else{
				echo("<!-- PAFacile: $metaKey$metaSuffix not found -->");
			}
			
		}
	}

	static function parseContents($content, $fromWidget = false){
		/*
		 * Bugfix: invocato il metodo errato "strstr" al posto di "strtr"
		 */
		$content = strtr( $content , array(
			'<p>[' 		=> '[',
			']</p>' 	=> ']',
			']<br />' 	=> ']' 
		));
		if(!$fromWidget){
			$permalink = get_permalink();
			$s = get_option('PAFacile_permalinks');
			switch(true){
				case ($s['delibere_id']!=0 && get_permalink($s['delibere_id']) == $permalink):
					$content = toSendItPAFacileContents::mostraDelibere($content);
					break;
				case ($s['determine_id']!=0 && get_permalink($s['determine_id']) == $permalink):
					$content = toSendItPAFacileContents::mostraDetermine($content);
					break;
				case ($s['bandi_id']!=0 && get_permalink($s['bandi_id']) == $permalink):
					$content = BandiGare::mostra($content);
					break;
				case ($s['ordinanze_id']!=0 && get_permalink($s['ordinanze_id']) == $permalink):
					$content = toSendItPAFacileContents::mostraOrdinanze($content);
					break;
				case ($s['organigramma_id']!=0 && get_permalink($s['organigramma_id']) == $permalink):
					$content = toSendItPAFacileContents::mostraOrganigramma();
					break;
				case ($s['organi_id']!=0 && get_permalink($s['organi_id']) == $permalink):
					$content = toSendItPAFacileContents::mostraOrgani($content);
					break;
				case ($s['albopretorio_id']!=0 && get_permalink($s['albopretorio_id'])== $permalink):
					$content = AlboPretorio::mostra($content);
					break;
				case ($s['incarichi_id']!=0 && get_permalink($s['incarichi_id'])== $permalink):
					$content = toSendItPAFacileContents::mostraIncarichi($content);
					break;
				case ($s['sovvenzioni_id']!=0 && get_permalink($s['sovvenzioni_id'])== $permalink):
					$content = Sovvenzioni::mostra($content);
					break;
							
			}
			
		}else{
			self::$fromWidget = $fromWidget;
		}
		
		return $content;
	}
	
	
		static function prepareMetaInfo($table){
			global $wpdb;
			$tableName = $wpdb->prefix .'pa_'. $table;
			
			if(isset($_GET['itemId']) && is_numeric($_GET['itemId'])){ 
				$sql = "select * from $tableName where id='" . $_GET['itemId'] ."'";
				
				$rs = $wpdb->get_row($sql);
			}else{
				$rs = null;
			}
			self::$metaInfo = $rs;
		}
		
		static function parseMetaInfo($value){
			$rs = self::$metaInfo;
			if($rs!=null){
				foreach($rs as $key => $rsValue){
					$value = str_replace('@' . $key .';' , $rsValue, $value);
				}
			}
			return $value;
		}
		
	
}
?>