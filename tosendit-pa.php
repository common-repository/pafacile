<?php
/**
 * @package toSend.it
 * @author toSend.it di Luisa Marra
 * @version 2.6.0
 */
/*
Plugin Name: PA Facile
Plugin URI: http://wordpress.org/extend/plugins/pafacile/
Description: PAFacile è un plugin nato per consentire alle pubbliche amministrazione di gestire la trasparenza amministrativa secondo gli obblighi di legge. Il plugin è l'unico in Italia a consentire l'adeguamento di un sito web di una pubblica amministrazione agli ultimi aggiornamenti normativa in materia di Albo Pretorio on-line, Bandi di Gara, Delbere e determinazioni, Ordinanze, Organigramma, Incarichi professionali, Sovvenzioni.
Author: toSend.it di Luisa Marra
Version: 2.6.1
Author URI: http://toSend.it
*/

#define('TOSENDIT_PAFACILE_VERSION', '2.3');
#define('TOSENDIT_PAFACILE_VERSION', '2.4.3');
#define('TOSENDIT_PAFACILE_VERSION', '2.4.4');
# define('TOSENDIT_PAFACILE_VERSION', '2.4.5');
#define('TOSENDIT_PAFACILE_VERSION', '2.4.6');
#define('TOSENDIT_PAFACILE_VERSION', '2.4.7');
#define('TOSENDIT_PAFACILE_VERSION', '2.4.8');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.0');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.1');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.2');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.3');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.4');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.5');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.6');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.7');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.8');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.9');
#define('TOSENDIT_PAFACILE_VERSION', '2.5.10');
#define('TOSENDIT_PAFACILE_VERSION', '2.6.0');
define('TOSENDIT_PAFACILE_VERSION', '2.6.1');

# è PAFacile in un installazione di default
define('PAFACILE_PLUGIN_BASE_DIRECTORY', basename( dirname(__FILE__) ) );

# in base a quanto installato riporta /var/www/wp-content/plugins/pafacile
define('PAFACILE_PLUING_DIRECTORY', WP_PLUGIN_DIR.'/'. PAFACILE_PLUGIN_BASE_DIRECTORY);
require_once PAFACILE_PLUING_DIRECTORY .'/toSendIt.php';
require_once PAFACILE_PLUING_DIRECTORY .'/db.php';
require_once PAFACILE_PLUING_DIRECTORY .'/definitions.php';

#Since ver 1.6
require_once PAFACILE_PLUING_DIRECTORY .'/PAFacileDecodifiche.php';
require_once PAFACILE_PLUING_DIRECTORY .'/PAFacileUpdateManager.php';

require_once PAFACILE_PLUING_DIRECTORY .'/toSendItPAFacilePages.php';
require_once PAFACILE_PLUING_DIRECTORY .'/toSendItPAFacileWidgets.php';
require_once PAFACILE_PLUING_DIRECTORY .'/ajax/actions.php';

if(!function_exists('initPAFacile')){
	
function dateAdd($interval,$number,$dateTime) {
	$dateTime = (strtotime($dateTime) != -1) ? strtotime($dateTime) : $dateTime;      
    $dateTimeArr=getdate($dateTime);

    $yr=$dateTimeArr['year'];
    $mon=$dateTimeArr['mon'];
    $day=$dateTimeArr['mday'];
    $hr=$dateTimeArr['hours'];
    $min=$dateTimeArr['minutes'];
    $sec=$dateTimeArr['seconds'];

    switch($interval) {
        case "s"://seconds
            $sec += $number;
            break;

        case "n"://minutes
            $min += $number;
            break;

        case "h"://hours
            $hr += $number;
            break;

        case "d"://days
            $day += $number;
            break;

        case "ww"://Week
            $day += ($number * 7);
            break;

        case "m": //similar result "m" dateDiff Microsoft
            $mon += $number;
            break;

        case "yyyy": //similar result "yyyy" dateDiff Microsoft
            $yr += $number;
            break;

        default:
            $day += $number;
         }      
               
        $dateTime = mktime($hr,$min,$sec,$mon,$day,$yr);
        $dateTimeArr=getdate($dateTime);
       
        $nosecmin = 0;
        $min=$dateTimeArr['minutes'];
        $sec=$dateTimeArr['seconds'];

        if ($hr==0){$nosecmin += 1;}
        if ($min==0){$nosecmin += 1;}
        if ($sec==0){$nosecmin += 1;}
       
        if ($nosecmin>2){     return(date("Y-m-d",$dateTime));} else {     return(date("Y-m-d G:i:s",$dateTime));}
}

	class toSendItPAFacile{ 
		
		public static $fromWidget = false;
		
		static function isMemberOf($idMembro, $memberCode){
			global $wpdb;
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANI.'_rel';
			$sql = "select * from $tableName where id_organo='$idMembro' and tipo='$memberCode'";
			$rs = $wpdb->get_row($sql);
			if(is_object($rs)){
				return true;
			}else{
				return false;
			}
		}
		
		static public function displayContentTable(	$type, $title, $descriptionColumnKey, $columns, $filters, 
										$classes, $tableName, $editMinRole, $deleteMinRole,
										$editHandler, $deleteHandler){
			
			$opzioni = get_option('PAFacile_settings');
			$subLevel = 3;
			isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
			
			$permalinks = get_option('PAFacile_permalinks');
			/*
			 * Per il dettaglio
			*/
			if(isset($permalinks[$type.'_id'])){
				$publicUrl = get_permalink($permalinks[$type.'_id']);
			}else{
				$publicUrl = get_permalink();
			}
			$publicUrl.='?itemId=';
			$wpHooksPrefix = "pafacile_{$type}";
			$wpHooksPrefix .= is_admin()?'_admin':'';
			$descriptionColumnKey = is_admin()?$descriptionColumnKey:'';
			
			$columns = apply_filters("{$wpHooksPrefix}_columns", $columns);
			$classes = apply_filters("{$wpHooksPrefix}_columns_class", $classes);
			$filters = apply_filters("{$wpHooksPrefix}_filter_columns", $filters);
			
			$campi = array_keys($columns);
			
			$campi = "id,".implode(",", $campi);
			
			global $wpdb;
			$tableName = $wpdb->prefix . $tableName;
			
			$filter = array();
			$whereCond = array();
			
			$_GET = stripslashes_deep($_GET);
			
			foreach($filters as $getKey => $fieldCond ){
			
				if(isset($_GET[$getKey]) && $_GET[$getKey]!=''){
					$filter[] = $_GET[$getKey];
					$whereCond[] = $fieldCond;
				}
			}
			
			$wc = implode(" and ", $whereCond);
			
			if($wc != ''){
				$wc = "where $wc";
				$wc = $wpdb->prepare($wc, $filter);
			}
			$sql = "select $campi from $tableName $wc";
			
			$sql = toSendItGenericMethods::applyPaginationLimit( $sql );
			$baseUrl = toSendItGenericMethods::rebuildQueryString(array('pg'));
			toSendItGenericMethods::generatePaginationList($tableName, $wc, $baseUrl );
			$results = $wpdb->get_results($sql, ARRAY_A);
			
			$gruppi = toSendItGenericMethods::getUserGroups('pafacile');
			if(is_admin() || count($results)>0){
				
				if(is_admin()){
					?>
					<div id="elenco-<? echo $type ?>" class="wrap">
						<div id="icon-edit-pages" class="icon32">
							<br/>
						</div>
						<h2><?php echo $title ?></h2>
						<form method="GET" id="post-filter" action="<?php echo $_SERVER['REQUEST_URI']?>">
							<?php do_action($wpHooksPrefix . '_before_form_content'); ?>
							<div class="tablenav" style="height: auto;">
								<input type="hidden" name="page" value="<?php echo $editHandler ?>" />
								<?php do_action($wpHooksPrefix .'_filter_form'); ?>
							</div>
							<?php
				}
				
				$tableClass = is_admin()?"widefat post fixed":"pafacile-public-table-contents";
				if(!is_admin()){
				
					$tableExtraAttributes = 'id="pafacile-table-'. $type . '"';
				
				}
				?>
				<table class="<?php echo $tableClass ?>">
					<thead>
						<tr>
						<?php 
						foreach($columns as $colKey => $colName){
							if($colKey!='id'){
								do_action("{$wpHooksPrefix}_before_{$colKey}_column_header");
								?>
								<th class="<?php echo isset($classes[$colKey])?$classes[$colKey]:''; ?>">
									<?php 
									echo $colName;
									?>
									
								</th>
								<?php
								do_action("{$wpHooksPrefix}_after_{$colKey}_column_header");
							}
						}
						?>
					</tr>
					</thead>
					<tbody>
						<?php 
						foreach($results as $rowIndex => $rowData){
							do_action("{$wpHooksPrefix}_before_row", $rowIndex);
							?>
							<tr>
								<?php 
								foreach($rowData as $colKey => $value){
									if($colKey != 'id'){
										do_action("{$wpHooksPrefix}_before_{$colKey}_column_data", $value);
										?>
										<td class="<?php echo isset($classes[$colKey])?$classes[$colKey]:''; ?>">
											<?php 
											if(!is_admin()) echo("<a href=\"$publicUrl{$rowData['id']}\">");
											echo $value; 
											if(!is_admin()) echo("</a>");
											
											if( is_admin() && 
												$colKey == $descriptionColumnKey && 
												toSendItGenericMethods::checkMinimalMenuRole($gruppi, array($editMinRole, $deleteMinRole) ) 
											){
												?>
												<div class="row-actions">
													<?php 
													if(toSendItGenericMethods::checkMinimalMenuRole($gruppi, $editMinRole)){
														?>
														<span class="edit"><a href="?page=<?php echo $editHandler ?>&id=<?php echo $rowData['id'] ?>">Modifica</a></span>
														<?php 
													}
													if(toSendItGenericMethods::checkMinimalMenuRole($gruppi, $deleteMinRole)){
														?>
														<span class="delete">| <a href="?page=<?php echo $deleteHandler ?>&id=<?php echo $rowData['id'] ?>">Elimina</a></span>
														<?php 
													}
													?>
												</div>
												<?php 
											}
											?>
										</td>
										<?php
										do_action("{$wpHooksPrefix}_after_{$colKey}_column_data", $value);
									}
								}
								?>
							</tr>
							<?php
							do_action("{$wpHooksPrefix}_after_row", $rowIndex);
						}
						?>
					</tbody>
				</table>
				<?php 
				if(is_admin()){
					?>
						</form>
					</div>
					<?php
				}
						
			}else{
				
				
				if(count($results)==0){
							
					if(count($filter)>0){
						if(apply_filters("display_{$type}_not_found_default_message", true)){
							?>
							<h<?php echo $subLevel?>>Spiacenti</h<?php echo $subLevel?>>
							<p>La ricerca effettuata non ha prodotto risultati</p>
							<?php
						}
					} else {
						
						if(count($filter) == 0){
							
							do_action("pafacile_{$type}_empty");
							
						}
					}
				}
				
			}
		}
	
		
		
		static function formattaInfoBando($rs){
			$buffer = '';
			$buffer .='(<em>' . PAFacileDecodifiche::tipoBando($rs->tipo) .'</em>): <strong>'.$rs->oggetto.'</strong><br />';
			$buffer .='Data pubblicazione: ' . toSendItGenericMethods::formatDateTime($rs->data_pubblicazione).'<br />';
			$buffer .='Data scadenza: ' . toSendItGenericMethods::formatDateTime($rs->data_scadenza).'<br />';
			$buffer .='Data esito: ' . toSendItGenericMethods::formatDateTime($rs->data_esito).'<br />';
			return $buffer;
		}
		
		static function userIsIn($officeId){
			if($officeId==0) return true;
			global $wpdb, $current_user;
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_USERS_TO_ORGANIGRAMMA;
			$sql = "select id from $tableName where id_organigramma=$officeId and id_utente={$current_user->ID}"; 
			$r = $wpdb->get_results($sql);
			return (count($r)!=0);
		}
				
		
		static function inOrganigramma($idUtente, $idOrganigramma){
			
			global $wpdb;
			
			$tableName = $wpdb->prefix .TOSENDIT_PAFACILE_DB_USERS_TO_ORGANIGRAMMA;
			
			$sql = "select * from $tableName where id_utente='$idUtente' and id_organigramma='$idOrganigramma'";
			$rs = $wpdb->get_row($sql);
			return ($rs!=null);
		}
		
		static function isUserAuthorizedFor($idUfficio){
			global $current_user, $wpdb;
			// Ufficio nullo
			if(!is_numeric( $idUfficio) || $idUfficio=='0' ){
				#return true;
				$returnValue = true;
			}else{
				$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_USERS_TO_ORGANIGRAMMA;
				$organigramma = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
				$sql = 'select id_organigramma from ' . $tableName . ' where id_utente="' . $current_user->ID . '" and id_organigramma ="' . $idUfficio .'"';
				if($wpdb->get_var($sql)!=$idUfficio){
					if($idUfficio==0){
						$returnValue = false;
					}else{
						
						$sql = 'select id_ufficio_padre from '. $organigramma . ' where id = "' . $idUfficio .'"';
				  		$returnValue = self::isUserAuthorizedFor( $wpdb->get_var($sql) );
					}
				}else{
					$returnValue = true;
					#return true;
				}
			}
			$returnValue = apply_filters('PAFacile_IsUserAuthorizedFor',$returnValue);
			return  $returnValue;
		}
		
		private static function buildSubOfficeOptions($id, $currentValue, $extraFilter=null, $level =0){
			global $wpdb;
			$organigrammaTableName 	= $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
			$sql = "select id, nome from $organigrammaTableName where id_ufficio_padre=  $id";
			if($extraFilter!=null) $sql .= ' and ' . $extraFilter;
			$results = $wpdb->get_results($sql);
			$itemSelected = false;
			foreach($results as $key => $value){
				$found=($value->id==$currentValue);
				echo('<option style="padding-left: ' .(20* $level) . 'px" value="'  .$value->id .'" ' . ($found?'selected="selected"':'') . ' >');
				echo(htmlspecialchars($value->nome));
				echo('</option>');
				if(self::buildSubOfficeOptions($value->id, $currentValue, $extraFilter, $level+1) || $found) $itemSelected = true;
			}
			return $itemSelected;
		}
		
		static function buildOfficeSelector( $name , $id, $class, $currentValue, $allowBlank = false, $conditionExtra = null){
			global $wpdb, $current_user;
			?>
			<select class="<?php echo $class;?>" name="<?php echo $name ?>" id="<?php echo $id?>">
				<?php 
				if($allowBlank) echo('<option value="">-</option>');
				$organigrammaTableName 	= $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
				$users2orgTableName 	= $wpdb->prefix . TOSENDIT_PAFACILE_DB_USERS_TO_ORGANIGRAMMA;
				$sql = "select id, nome from $organigrammaTableName where id in(select id_organigramma from $users2orgTableName where id_utente='{$current_user->ID}') ";
				if($conditionExtra != null){
					$sql .= ' and ' . $conditionExtra;
				}
				$results = $wpdb->get_results($sql);
				$itemSelected = false;
				foreach($results as $key => $value){
					$found=($value->id==$currentValue);
					echo('<option value="'  .$value->id .'" ' . ($found?'selected="selected"':'') . ' >');
					echo(htmlspecialchars($value->nome));
					echo('</option>');
					if(self::buildSubOfficeOptions($value->id, $currentValue, $conditionExtra, 1) || $found) $itemSelected =true;
					
				}
				if(!$itemSelected && $currentValue!=''){
					echo('<option value="'.$currentValue.'" selected="selected">'. PAFacileDecodifiche::officeNameById($currentValue)) . '</option>';
				}
				?>
			</select>
			<?php
			#echo($sql);
				 
		}

		static function buildOfficeSelectorForObject( $name , $id, $class, $curentValue, $table){
			global $wpdb;
			?>
			<select class="<?php echo $class;?>" name="<?php echo $name ?>" id="<?php echo $id?>">
				<option value="">-</option>
				<?php 
				$organigrammaTableName 	= $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
				$relatedTableName 		= $wpdb->prefix . $table;
				$sql = "select id, nome from $organigrammaTableName where id in(select distinct id_ufficio from $relatedTableName)";
				$results = $wpdb->get_results($sql);
				
				foreach($results as $key => $value){
					echo('<option value="'  .$value->id .'" ' . (($value->id==$curentValue)?'selected="selected"':'') . ' >');
					echo(htmlspecialchars($value->nome));
					echo('</option>');
				}
				?>
			</select>
			<?php 
		}
		
		function doSave(){
			# identifyHandler
			list($page, $action) = toSendItGenericMethods::identifySectionAndAction(); 
			$actions= array(TOSENDIT_PAFACILE_EDIT,TOSENDIT_PAFACILE_NEW);
			
			if(array_search( $action , $actions )!==false){
				# Risoluzione anomalia XAMPP
				require_once(PAFACILE_PLUING_DIRECTORY .'/doSave.php');
				global $saveHandler;
				
				if(isset($saveHandler[$page])){
					$fn = $saveHandler[$page];
					$fn(); 
				}else{
					toSendItGenericMethods::createMessage("Non sono riuscito a identificare la funzione &quot$page&quot;");
					print_r($saveHandler);
					exit();
				}
			}
		}
		
		/*
		 * Since ver 1.6
		 */
		static function replaceContents($obj, $fromWidget = false){
			if(is_singular() || $fromWidget ){
				$array = $obj;
				
				ob_start();
				/*
				 * Identifica la sezione
				 */
				$section = array_shift($array);
				# error_log($section);
				/*
				 * Evitiamo che si facciano cose strane
				 */
				/*
				 * v 2.4.7 Bugfix: Mancava il controllo per il case insensitive.
				 */
				$section = preg_replace('#[^a-z]#i', '', $section);
				
				$fnName= strtoupper(substr($section,0,1)).substr($section,1);
				/*
				 * Costruisce il nome del metodo
				 */
				$defaultMethod = "display" . $fnName;
				$baseMethod =  "display" . $section . 'Public';
				
				/*
				 * Include il file specifico dalla sezione
				 */
				require_once PAFACILE_PLUING_DIRECTORY .'/' .$section.'/elenco.php';
				$method = (function_exists($baseMethod))?$baseMethod:$defaultMethod;
				/*
				 * Estrae il tipo di visualizzazione dall'elenco dei parametri
				 */
				$actionParameter =  array_shift($array);
				
				/*
				 * Invoca il metodo specifico fornendo la tipologia di azione e l'elenco dei parametri aggiuntivi
				 */
				$method($actionParameter, $array);
				$buffer = ob_get_clean();
			}else{
				$buffer = '';
			}
			return $buffer;
		}
		
		
			
	}
	
	function initPAFacile(){
		
		setlocale(LC_TIME, "it_IT.utf8");
		
		if(is_admin()){
			require_once PAFACILE_PLUING_DIRECTORY .'/PAFacileBackend.php';
			#require_once 'toSendItPAFacileHelp.php';
			require_once PAFACILE_PLUING_DIRECTORY .'/doSave.php';
			
			add_action('admin_menu', array('PAFacileBackend','createMenu'));
			add_action('admin_init', array('PAFacileBackend','loadScriptsAndStylesheets'));
			add_action('admin_init', array('toSendItPAFacile','doSave'));
			add_filter('admin_head', array('PAFacileBackend', 'setAdminHeader'));
			add_action( 'show_user_profile', 		array('PAFacileBackend', 'userProfilePage') );
			add_action( 'edit_user_profile',		array('PAFacileBackend', 'userProfilePage') );
			add_action( 'edit_user_profile_update',	array('PAFacileBackend', 'userProfileSave') );
			add_action( 'personal_options_update',	array('PAFacileBackend', 'userProfileSave') );
			
			require_once PAFACILE_PLUING_DIRECTORY .'/mce/plugins.php';
		}else{
			# Since Ver 1.6
			require_once PAFACILE_PLUING_DIRECTORY .'/PAFacileFrontend.php';		
			require_once PAFACILE_PLUING_DIRECTORY .'/toSendItPAFacileContents.php';
			add_action('wp_head', 		array('PAFacileFrontend','setTemplateHeader'));	
			add_shortcode('PAFacile', 	array('PAFacileFrontend', 'manageShortcode'));
			add_filter('the_content', 	array('PAFacileFrontend', 'parseContents'), 10);
			
			# Since Ver 2.5.10
			require_once PAFACILE_PLUING_DIRECTORY .'/PAFacileOpenData.php';
				
		}		

		/**
		 * Registrazione widget
		 */
		toSendItPAFacileWidgets::init();
	}
	
	
	function toMySQLDate($day, $month, $year, $fixPossible = false){
		if($day == '') $day = '00';
		if($month=='') $month='00';
		if($year=='') $year = '0000';
		
		if($fixPossible){
			if($year=='0000' && ($month!='00' || $day!='00')) $year = date('Y');
			
			if($year!='0000' && $month=='00'){
				$month = date('m');
				if($month!='00' && $day=='00') $day = date('d');
			}
			
		}
	
		if(strlen($day)<2) $day = '0' .$day;
		if(strlen($month)<2) $month = '0' . $month;
	
		return $year .'-'.$month.'-'.$day;
		
	}
	
	register_activation_hook(__FILE__,array('PAFacileUpdateManager','installPlugin'));
	add_action("plugins_loaded", "initPAFacile");
	if(!is_admin() && get_option("PAFacile_Credits")=='y') add_action("wp_footer", 'toSendItPAFacileCredits');
	
}

function toSendItPAFacileCredits(){
	?>
	<p id="pafacile-credits">
		Questo sito usa <a  href="http://toSend.it/prodotti/pafacile/">PAFacile</a> sviluppato da <a href="http://tosend.it">toSend.it - we make IT easy!</a>
	</p>
	<?php
}

?>
