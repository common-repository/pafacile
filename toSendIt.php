<?php

if(!class_exists('toSendItGenericMethods')){
	
	class toSendItGenericMethods{
		
		public static function checkMinimalMenuRole($userRoles, $menuRoles){
			/*
			 * Bugfix: Accidentale disponibilità di tutti i menu a un qualsiasi utente autenticato (passato da "read" a "administrator")
			 * Solo l'amministratore può far tutto.
			 */
			if(current_user_can('administrator')) return true;
			
			if(!is_array($menuRoles)) $menuRoles = array($menuRoles);
			if(isset($menuRoles) && count($menuRoles)>0){
				$hasMinimalRole = false;
				foreach($menuRoles as $singleRole){
					if(array_search($singleRole,$userRoles)!==false){
						$hasMinimalRole = true;
						break;
					}
				}
			}else{
				$hasMinimalRole = true;
			}
			return $hasMinimalRole;
		}
		
		static public function createMenuStructure($params, $subItems, $userRoles){
			extract($params);
			 /* $menuSlug, $minLevel, $pageTitle, $menuTitle, $imageUrl */
			
			/*
			 * Esempio d'uso:
			 * 
			 * createMenuStructure(
			 * 		array(
			 * 			'pageTitle' 	=> 'Albo on-line',
			 * 			'menuTitle' 	=> 'Albo on-line',
			 * 			'minLevel'  	=> '6',
			 * 			'menuSlug'		=> 	TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER,
			 * 			'imageUrl'		=>	TOSENDIT_PAFACILE_PLUGIN_URL .'/images/albo.png'
			 * 			'defaultAction'	=> 	array('PAFacilePages','pagePAAlboPretorio')
			 * 		), 
			 * 		array(	
			 * 			array(
			 * 				'pageTitle' => 	'PAFacile - Modifica avviso all\'albo',
			 * 				'menuTitle'	=>	'Modifica',
			 * 				'action'	=> 	array('PAFacilePages','pagePAAlboPretorio')
			 * 				
			 * 			),
			 * 			array(
			 * 				'pageTitle' => 	'PAFacile - Nuovo avviso all\'albo',
			 * 				'menuTitle'	=>	'Nuovo',
			 * 				'handler'	=>	TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER,
			 * 				'action'	=> 	array('PAFacilePages','pagePAAlboPretorio')
			 * 			)
			 * 		)
			 * );
			 * 
			*/
			
			$allowedRoles = isset($allowedRoles)?$allowedRoles:array();
			
			if(self::checkMinimalMenuRole($userRoles, $allowedRoles)){
				add_menu_page($pageTitle, $menuTitle, $minLevel, $menuSlug, $defaultAction, $imageUrl);
				foreach($subItems as $subItem){
					$minLevel 		= isset($subItem['minLevel'])?$subItem['minLevel']:$params['minLevel'];
					$handler		= isset($subItem['handler'])?$subItem['handler']:$params['menuSlug'];
					$action			= isset($subItem['action'])?$subItem['action']:$params['defaultAction'];
					$allowedRoles 	= isset($subItem['allowedRoles'])?$subItem['allowedRoles']:$allowedRoles;
					if(self::checkMinimalMenuRole($userRoles, $allowedRoles))
						add_submenu_page($menuSlug, $subItem['pageTitle'], $subItem['menuTitle'], $minLevel, $handler, $action);
				}
			}
			
		}
		
		static public function dateDiff($from, $to){
			preg_match_all('/(\d{4})\-(\d{2})\-(\d{2})/', $from, $fromYMD);
			preg_match_all('/(\d{4})\-(\d{2})\-(\d{2})/', $to, $toYMD);
			
			$fromSer = mktime(0,0,0,$fromYMD[2][0], $fromYMD[3][0], $fromYMD[1][0]);
			$toSer = mktime(0,0,0,$toYMD[2][0], $toYMD[3][0], $toYMD[1][0]);
			$days = date('z', $toSer - $fromSer);
			
			return $days; 
		}
		
		static public function getUserGroups($groupMetaVar, $user = null){
			/*
			 * Rimossa dipendenza da Role Scoper
			global $wpdb, $current_user;
			$gruppi = $current_user->groups;
			$tabellaGruppi = $wpdb->prefix .'groups_rs';
			if(!is_array($gruppi)) $gruppi = array('0'=>0);
			$sql = "select group_name from $tabellaGruppi where id in (" . implode(array_keys($gruppi),',') . ")";
			$ogruppi = $wpdb->get_results($sql);
			
			$gruppi = array();
			foreach($ogruppi as $key => $object){ $gruppi[] = $object->group_name; }
			$gruppi = array_values( $gruppi);
			*/
			
			global $current_user;
			$gruppi = get_user_option($groupMetaVar, is_null($user)?$current_user->ID:$user->ID);
			if(!is_array($gruppi)) $gruppi = array();
			return $gruppi;
		}
		
		function mergeSearchFilter($searchFilterVar){
			global $current_user;
			$filter = get_option($searchFilterVar.'@'.$current_user->ID,'');
			if($filter!=''){
				
				$filter = apply_filters('tosendit_'.$searchFilterVar,unserialize($filter));
				
				foreach($filter as $key => $value){
					if(!isset($_GET[$key])) $_GET[$key] = $value;
				}
			}
			if(isset($_GET['pg'])) $thePage = $_GET['pg'];
			unset($_GET['pg']);
			update_option($searchFilterVar.'@'.$current_user->ID, serialize($_GET));
			if(isset($thePage)) $_GET['pg'] = $thePage;
		}
				
		
		static function fileSize($size){
										 	
		 	$fileSizeName = Array(' Bytes', 
		 		'<abbr title="Kilobytes">KB</abbr>',
		 		'<abbr title="Megabytes">MB</abbr>'
		 	);
		 	return 
		 		$size?round($size/pow(1024, ($i = floor(log($size,1024)) )),2).$fileSizeName[$i]:'0 Bytes'; 
		}	
		static function getNextAvailableValue($table, $field, $date_field, $increment=1){
			global $wpdb;
			
			$max = $wpdb->get_col("select max($field)+$increment from $table where year($date_field) = year(now())",0);
			$max = $max[0];
			if($max==null) $max = $increment;
			return($max);
			
		}
		
		static function applyPaginationLimit($sql){
			if(isset($_GET['pg'])) $pg = $_GET['pg'];
			if(!isset($pg) || !is_numeric($pg)) $pg = 0;
			$limit = 'limit ' . $pg*TOSENDIT_PAGING_RECORDS . ', ' . TOSENDIT_PAGING_RECORDS;
			return "$sql $limit";
		}
		
		static function rebuildQueryString($excludeKeys){
			$qs = '';
			foreach($_GET as $key => $value){
				if(array_search( $key, $excludeKeys)===false ){
					$qs .=  urlencode($key) .'=' . urlencode($value) . '&';
				}
			}
			return "?$qs";
		}
		
		static function generatePaginationList($table, $filter, $baseUrl){
			global $wpdb;
			$pg = isset($_GET['pg'])?$_GET['pg']:'0';
			if(!is_numeric($pg)) $pg = 0;
			$sql = "select count(*) from  $table $filter";
			
			$rs =  $wpdb->get_col($sql,0);
			$rs = $rs[0];
			$pagine = intval($rs/TOSENDIT_PAGING_RECORDS) + (($rs%TOSENDIT_PAGING_RECORDS!=0)?1:0);
			
			if($pg>$pagine){
				$pg = 0;
				$_GET['pg'] = 0;
			}
			$start = $pg-2;
			$end = $pg+2;
			if($start<0) $start = 0;
			if($end>$pagine) $end = $pagine;
			if($pagine>1){
				echo('<ul class="paginazione">');
				$lastRecord = ($pg*TOSENDIT_PAGING_RECORDS+TOSENDIT_PAGING_RECORDS);
				if($lastRecord>$rs) $lastRecord = $rs;
				echo('<li><span class="displaying-num">Sono visualizzati ' . ($pg*TOSENDIT_PAGING_RECORDS+1) . ' &ndash; ' . $lastRecord . ' su ' . $rs .'</span></li>'); 
				if($start>0){
					echo('<li class="pagina"><a href="'. str_replace('&','&amp;',$baseUrl).'pg=' . ($pg-1) .'">&laquo;</a></li>');
					echo('<li class="pagina">');
					echo('<a href="' . str_replace('&','&amp;',$baseUrl) . 'pg=0">1</a>');
					echo('</li>');
					if($start>1){
						echo('<li>...</li>');
					}
				}
				for($i = $start; $i<$end; $i++){
					echo('<li class="pagina');
					if($i==$pg)echo(' corrente');
					echo('">');
					if($i!=$pg){
						echo('<a href="' . str_replace('&','&amp;',$baseUrl) . 'pg=' . $i .'">' . ($i+1) . '</a>');
					}else{
						echo($i+1);
					}
					echo('</li>');
				}
				if($end<$pagine){
					if($end<$pagine-1){
						echo('<li>...</li>');
					}
					echo('<li class="pagina">');
					echo('<a href="' . $baseUrl . 'pg=' . ($pagine-1) .'">' . ($pagine) . '</a>');
					echo('</li>');
					echo('<li class="pagina"><a href="'. str_replace('&','&amp;',$baseUrl).'pg=' . ($pg+1) .'">&raquo;</a></li>');
				}
				echo('</ul>');
			}
		}
		static function formatDateTime($mysqlDateTime, $baseFormat="%A %d %B %Y"){
			$baseFormat = apply_filters('toSendIt_dateBaseFormat',$baseFormat);
			
			if(preg_match('/(\d+)-(\d+)-(\d+)(( \d+):(\d+):(\d+))*/i', $mysqlDateTime, $output)){
				if(count($output)<=4) $output[5] = $output[6] = $output[7] = '00'; 
				if($output[3]+$output[2]+$output[0]==0){
					$dateTime = '';
				}else{
					#print_r($output);
					$dtm = mktime($output[5], $output[6], $output[7], $output[2], $output[3], $output[1]);
					#print_r($dtm);
					$dateTime = strftime($baseFormat, $dtm);
					if($output[5]!='00' || $output[6] !='00'){
						 $dateTime .= ' alle ore ' . strftime(apply_filters('toSendIt_timeBaseFormat','%H:%M'), $dtm);
					}
				}
			}else{
				$dateTime = $mysqlDateTime;
			}
			return $dateTime;
		}
		
		static function identifyParameters( $params, $defaultData ){
			$keyData = array_keys($defaultData);
			if (is_array($params)){
				for($i = 0; $i<count($params);$i++){
					if(isset($keyData[$i])){
						$defaultData[$keyData[$i]] = trim($params[$i]);
					}
				}
			}else{
				$defaultData[$keyData[0]] = $params; 
			}
			
			foreach($defaultData as $key => $value){
				if($value==null) unset($defaultData[$key]);
			}
			return $defaultData	;
		
		}
				
		
		static function showTinyMCE(){
	   		
			/** In main plugin file **/
			if(PAFacileBackend::isPAFacileEditPage()){
				/*
				wp_enqueue_script( 'common' );
				wp_enqueue_script( 'jquery-color' );
				wp_print_scripts('editor');
				if (function_exists('add_thickbox')) add_thickbox();
				wp_print_scripts('media-upload');
				#if (function_exists('wp_editor')) wp_editor();
				wp_admin_css();
				wp_enqueue_script('utils');
				do_action("admin_print_styles-post-php");
				do_action('admin_print_styles');
				*/
			}
					
		}
		
		static function pluginDirectory(){
			$filePath = preg_split('/[\\/|\\\]/',dirname(__FILE__));
			$pluginDirectory = $filePath[count($filePath)-1];
			return WP_PLUGIN_URL .'/'.$pluginDirectory;
			
		}
		
		static function buildAuditTrailTable($table, $id){
			global $wpdb;
			$auditTrailTable = $wpdb->prefix . TOSENDIT_DB_AUDIT_TRAIL;
			
			$sql = "select * from $auditTrailTable where tabella_rif='$table' and id_tab_rif='$id' order by quando desc, id asc";
			
			$auditTrail = $wpdb->get_results($sql);
			if(count($auditTrail)>0){
				echo('<ul id="audit-trail">');
				$lastDateName = '';
				foreach($auditTrail as $key => $row){
					$user = get_user_by('id', $row->user_id);
					$theName = $user->display_name;
					$newDateName = $row->quando .'@'. $theName;
					# <span class="date"><strong>'.$row->quando . '</strong> ' . $theName . '';
					if($newDateName != $lastDateName){
						if($lastDateName != ''){
							?>
							</ul>
						</li>
							<?php
						}
						?>
						<li>
							<span class="date">
								<strong><?php echo $row->quando ?></strong>
							</span>
							<span class="user">
								<?php echo $theName ?>
							</span>
							<ul class="at-entry">
						<?php
						$lastDateName = $newDateName;
					}
					?>
								<li><?php echo $row->azione ?></li>
					<?php
				}
				if($lastDateName!='')
					echo('</ul></li>');
					
				echo('</ul>');
			}
			
			
		}
		
		static function hasAttachments($table, $id){
			global $wpdb;
			$sql = 'select * from ' . $wpdb->prefix . TOSENDIT_DB_ATTACHS . ' where tabella_rif="' . $table .'" and id_tabella_rif="' . $id . '"';
			$rows = $wpdb->get_results($sql);
			return (count($rows)>0);
		}
		
		static function displayFileUploadBox($table, $id){
			
			global $wpdb;
			if(isset($_GET['delatch']) && is_admin()){
				$sql = 'delete from '. $wpdb->prefix . TOSENDIT_DB_ATTACHS . ' where id=' . $_GET['delatch'] . ' and tabella_rif="' . $table .'" and id_tabella_rif="' . $id . '"';
				$wpdb->query($sql);
			}
			$sql = 'select * from ' . $wpdb->prefix . TOSENDIT_DB_ATTACHS . ' where tabella_rif="' . $table .'" and id_tabella_rif="' . $id . '"';
			$rows = $wpdb->get_results($sql);
			?>
			<div class="stuffbox uploadbox">
				<?php 
				if(is_admin()) echo('<h3>Allegati:</h3>');
				?>
				<div class="inside">
					<?php 
					
					?>
					<ul class="attachment-box">
						<?php 
						if(count($rows)==0){
							?>
							<li>Non ci sono documenti allegati</li>
							<?php
						}
						foreach($rows as $rowId => $row ){
							?>
							<li class="attach file-<?php echo preg_replace('/.*\.([^.]+)$/i', '$1',$row->file_url )?>">
								<?php 
								if(is_admin()){
									$deleteAttachUrl = $_SERVER['REQUEST_URI'];
									$pos = strpos($deleteAttachUrl,'&delatch=');
									if($pos!==false){
										$deleteAttachUrl = substr($deleteAttachUrl,0, $pos);
									}
									$deleteAttachUrl .= ('&delatch=' . $row->id);
									?>
									<span class="delete">
										<a class="deletion" href="<?php echo $deleteAttachUrl?>">x</a>
									</span>
									<?php 
								}
								?>
								<a href="<?php echo($row->file_url); ?>" ><?php echo(($row->titolo!=''&& $row->titolo!=null)?$row->titolo:basename($row->file_url) ); ?>
								</a>
								<span class="file-info">
									<?php 
									$url = $row->file_url;
									$blogUrl = get_bloginfo('siteurl');
									$path = substr($url, strlen($blogUrl)+1);
									$file = ABSPATH . $path;
									@$sz = filesize($file);
									 
									echo('<br /><abbr title="Dimensione">Dim.</abbr> ' . toSendItGenericMethods::fileSize($sz));
									echo('<br />Hash: ' . hash_file('md5', $file));

									?>
								</span>
							
							</li>	
							<?php
						}
						if(is_admin()){
							?>
							<li class="upload-form">
								<h4>Inserisci nuovo allegato</h4>
								
								<div>
									<p>
										<label for="attach_title">Titolo:</label>
										<input type="text" name="attach_title" id="attach_title" />
									</p>
									<p>
										<label for="allegato">Aggiungi nuovo allegato:</label>
										<input type="file" name="allegato" id="allegato" />
									</p>
									
								</div>
							</li>
							<?php 
						}
						?>
					</ul>
				</div>
			</div>
			<?php 
		}
		static function doUploadFile($prefix, $fileObjectName, $tableName, $id, $year = 0, $month =0, $title = ''){
			
			if(isset($_FILES) && isset($_FILES[$fileObjectName]) && $_FILES[$fileObjectName]['size']!=0){
				
				$upload = wp_upload_bits($prefix.$_FILES[$fileObjectName]["name"], null, file_get_contents($_FILES[$fileObjectName]["tmp_name"]));
				if($upload['error']){
					toSendItGenericMethods::createMessage('Problema durante il caricamento del file: ' . $upload['error']);
				}else{
					global $wpdb;
					$uploadTableName = $wpdb->prefix . TOSENDIT_DB_ATTACHS;
					if($title=='') $title = $_FILES[$fileObjectName]["name"];
					$wpdb->insert($uploadTableName, array(
						'tabella_rif' => $tableName,
						'titolo'		=> $title,
						'id_tabella_rif' => $id,
						'file_url' => $upload['url']
					));
				}
			}
		}
		
		static function checkForTable($tableName){
			global $wpdb;
			if($wpdb->get_var("SHOW TABLES LIKE '$tableName'") != $tableName){
				toSendItGenericMethods::createMessage("La tabella $tableName non esiste!");
				return false;
			}
			return true;
			
		}
		
		static function identifySectionAndAction(){
			
			# Problema notice
			$page = isset($_GET['page'])?$_GET['page']:'';
			
			$info = preg_split('#\-#', $page);
			if(count($info)>3 || count($info)==1) return array($page, '');
			if(count($info)==3){
				$info[0] = $info[0]. '-'.$info[1];
				$info[1] = '-'.$info[2];
			}else{
				$info[0] = $page;
				$info[1] = isset($_GET['action'])?$_GET['action']:'';
			}
			return array($info[0], $info[1]);
			
			
		}
		
		static function getActionFromPage($pageBase){
			$page = $_GET['page'];
			if(substr($page, 0,strlen($pageBase)) == $pageBase){
				$baseAction = substr($page,strlen($pageBase));
				if(isset($_GET['action']) && ($baseAction=='' || $baseAction=='-edit')){
					return $_GET['action'];
				}
				return substr($page,strlen($pageBase));
			}else{
				return '';
			}
		}
	
		static function createMessage($msg, $directPrint = false){
			#$msg = addcslashes($msg,'\\');
			$html = '<div id="message" class="error fade"><p><strong>' . $msg . '</strong></p></div>';
			if(!$directPrint){
				$func_body = 'echo ' . "'" . addslashes($html) . "'" . ';';
				add_action('admin_notices', create_function('', $func_body) );
			}else{
				echo $html;
			}
		}
		
		
		static function drawDateField($prefix, $date = null, $nothingIfZero=true){
			if($date==null || $date=='') $date = '--'; 
			global $wp_locale;
			@list($yy,$mm,$dd) = split('-',$date);
			$month = '<span class="field-day"><label class="screen-reader-text"  for="'.$prefix.'_mm">Mese:</label> '."<select id=\"{$prefix}_mm\" name=\"{$prefix}_mm\">\n";
			$month .= '<option value=""></option>';
			for ( $i = 1; $i < 13; $i = $i +1 ) {
				$month .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
				if ( $i == $mm )
					$month .= ' selected="selected"';
				$month .= '>' . $wp_locale->get_month_abbrev( $wp_locale->get_month( $i ) ) . "</option>\n";
			}
			
			$month .= '</select></span>';
			if($nothingIfZero){	
				if($dd=='00') $dd ='';
				if($mm=='00') $mm ='';
				if($yy=='0000') $yy = '';
			}
			$day = '<span class="field-month"><label class="screen-reader-text"  for="'.$prefix.'_dd">Giorno:</label> <input size="2" type="text" id="'.$prefix.'_dd" name="'.$prefix.'_dd" value="'.$dd.'" /></span>'; 		
			$year ='<span class="field-year"><label class="screen-reader-text"  for="'.$prefix.'_yy">Anno:</label> <input size="4" type="text" id="'.$prefix.'_yy" name="'.$prefix.'_yy" value="'.$yy.'" /></span>';
			
			echo($day.'' . $month . '' . $year);
			
		}

	static function drawDateTimeField($prefix, $date = null, $nothingIfZero=true){
		
			$date = split(' ',$date);
			self::drawDateField($prefix, $date[0], $nothingIfZero);
			
			if(count($date)==1) $date[1]='00:00';
			if($date[1]=='00:00' && $nothingIfZero){
				$ora='';
				$minuto = '';
			}else{
				list($ora, $minuto) = split(':', $date[1]);
			}
			
			$hour = '<label class="screen-reader-text"  for="'.$prefix.'_hh">Ore:</label> '."<select id=\"{$prefix}_mm\" name=\"{$prefix}_hh\">\n";
			$hour .= '<option value=""></option>';
			for ( $i = 0; $i < 24; $i = $i +1 ) {
				$hour .= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
				if ( $i == intval( $ora) ) $hour .= ' selected="selected"';
				$hour .= '>' . zeroise($i,2) . "</option>\n";
			}
			
			$hour .= '</select>';
			$minute = '<label class="screen-reader-text"  for="'.$prefix.'_nn">Minuti:</label> '."<select id=\"{$prefix}_mm\" name=\"{$prefix}_nn\">\n";
			$minute.= '<option value=""></option>';
			
			for ( $i = 0; $i < 60; $i = $i +1 ) {
				$minute.= "\t\t\t" . '<option value="' . zeroise($i, 2) . '"';
				if ( zeroise($i, 2) == $minuto ){
					$minute .= ' selected="selected"';
				}
				$minute.= '>' . zeroise($i,2) . "</option>\n";
			}
			
			$minute .= '</select>';
			
			echo($hour.' ' . $minute);
			
		}		
	}
	
}

?>