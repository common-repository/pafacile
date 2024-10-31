<?php
/*
 * Since Version 2.5.10
* Avoid XSS vulnerability discovered by Dejan Lukan many thanks!
*/
if (!empty($_SERVER['SCRIPT_FILENAME']) &&
		basename(__FILE__)             == basename($_SERVER['SCRIPT_FILENAME']) &&            // Same script file
		basename(dirname(__FILE__)) == basename(dirname($_SERVER['SCRIPT_FILENAME']))    // Same directory
)
	die ('Please do not load this page directly. Thanks to Dejan Lukan for the notification!');

	
	/****************************************************************
	 * Procedure per la visualizzazione pubblica delle Determine
	 * - mostraDetermine(): visualizza l'elenco delle determine e il modulo di ricerca
	 * - mostraDetermineForm(): visualizza il modulo di ricerca delle determine
	 * - mostraDetermineElenco(): visualizza l'elenco delle determine
	 *****************************************************************/

require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/iContents.php';
class Determine extends PAFacilePublicBaseClass implements iContents{
	public static function mostra($buffer){
		$itemId = $_GET['itemId'];
		if(isset($itemId) && is_numeric($itemId)){
			ob_start();
			// Mostro il dettaglio di una determina
			if(!self::dettagli($itemId)){
				unset($_GET['itemId']);
				echo($buffer);
			}
			$buffer = ob_get_clean();
			
		}
		return $buffer;	
		
	}
	public static function form($params = null ){
		if(isset($params) && is_array($params)) extract($params);
		$p = get_option('PAFacile_permalinks');
		if(isset($p['determine_id']) && $p['determine_id']!=0){
			extract($_GET);
			?>
			<form method="get" class="determine" action="<?php echo get_permalink($p['determine_id']) ?>">
				<div id="numero-determina">
					<label for="numero">Numero:</label>
					<input type="text" name="numero" id="numero" value="<?php echo $numero?>" />
				</div>
				<div id="testo-determina">
					<label for="testo">Ricerca nel testo:</label>
					<input type="text" name="testo" id="testo" value="<?php echo $testo?>" />
				</div>
				<div id="id-ufficio-determina">
					<label for="id_ufficio"><abbr title="Ufficio">Uff.</abbr>/Area/<abbr title="Settore">Sett.</abbr>:</label>
					<?php 
					toSendItPAFacile::buildOfficeSelectorForObject('id_office','id_ufficio','',$id_office, TOSENDIT_PAFACILE_DB_DETERMINE);
					?>
				</div>
				<fieldset id="data-dal-determina">
					<legend>Dal giorno:</legend>
					<?php
					toSendItGenericMethods::drawDateField('pa_dal', $pa_dal_yy.'-'.$pa_dal_mm.'-'.$pa_dal_dd);
					?>
				</fieldset>
				<fieldset id="data-al-determina">
					<legend>Al giorno:</legend>
					<?php 
					toSendItGenericMethods::drawDateField('pa_al', $pa_al_yy.'-'.$pa_al_mm.'-'.$pa_al_dd);
					?>
				</fieldset>
				<div class="submit">
					<input type="submit" value="Cerca..." />
				</div>
			</form>
			<?php 
		}else{
			toSendItPAFacileContents::PAFacileConfigurationError();
		}
	}
	public static function elenco($params = null){
		global $wpdb;
		$hideNoResults = false;
		$opzioni = get_option('PAFacile_settings');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		
		if(isset($params) && is_array($params)) extract($params);
		extract($_GET);
		$filtro = array();
		$last7days = false;
		if(isset($numero) && is_numeric($numero)){
			$numero!=0 && $filtro[] = "numero='$numero'";
		}
		if(isset($testo) && $testo!=''){
			$filtro[] = "oggetto like '%$testo%'";
		}
		if(isset($id_office) && is_numeric($id_office)){
			$filtro[] = "id_ufficio='$id_office'";
		}
		$pa_dal = (isset($pa_dal_dd) && isset($pa_dal_mm) && isset($pa_dal_yy) )?
						toMySQLDate($pa_dal_dd, $pa_dal_mm, $pa_dal_yy, false) : '0000-00-00';
		
		$pa_al = (isset($pa_al_dd) && isset($pa_al_mm) && isset($pa_al_yy) )? 
						toMySQLDate($pa_al_dd, $pa_al_mm, $pa_al_yy, false) : '9999-99-99';
						
		$filtro[] = self::buildDataFilter('data_adozione', $pa_dal, $pa_al);
		
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_DETERMINE;
		$filtro = self::purgeFilter($filtro);
		$filter = implode($filtro, ' and ');
		if($filter!='') $filter = ' where ' . $filter;
		$suffisso = '';
		
		$permalinks = get_option('PAFacile_permalinks');
		$url = get_permalink($permalinks['determine_id']);
		$url.=toSendItGenericMethods::rebuildQueryString(array('pg'));
		
		$sql = toSendItGenericMethods::applyPaginationLimit("select * from $tableName $filter order by data_adozione desc, numero desc");
		$results = $wpdb->get_results($sql);
		if(count($results)==0){
			if(count($_GET)>0 && !$hideNoResults){
				?>
				<h<?php echo $subLevel?>>Spiacenti, nessun risultato trovato.</h<?php echo $subLevel?>>
				<p>
					La ricerca effettuata non ha prodotto alcun risultato, 
					si consiglia di modificare i criteri e ripetere la ricerca.
				</p>
				<?php
			}	
		}else{
			$pgurl = $url;
			$url.='itemId=';
			?>
			<h<?php echo $subLevel?>>Determine <?php echo $suffisso?></h<?php echo $subLevel?>>
			<?php 
			toSendItGenericMethods::generatePaginationList($tableName, $filter,$pgurl);
			?>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<?php do_action('determine_list_before_header_columns',true); ?>
					<th><?php echo apply_filters('determine_list_number_header','Numero')?></th>
					<th><?php echo apply_filters('determine_list_date_header','Data adozione')?></th>
					<th><?php echo apply_filters('determine_list_subject_header','Oggetto')?></th>
					<th><?php echo apply_filters('determine_list_office_header','<abbr title="Ufficio">Uff.</abbr>/Area/<abbr title="Settore">Sett.</abbr>')?></th>
					<?php do_action('determine_list_after_header_columns',true); ?>
				</tr>
				<?php 
				$j=0;
				foreach($results as $key => $rs){
					?>
					<tr <?php echo (($j++%2)==0)?'class="odd"':'' ?>>
						<?php do_action('determine_list_before_data_columns',$rs, true); ?>
						<td >
							<?php echo apply_filters('determine_list_number_value',$rs->numero)?>
						</td>
						<td><?php echo apply_filters('determine_list_date_value',toSendItGenericMethods::formatDateTime( $rs->data_adozione) )?></td>
						<td>
							<a title="Consulta il dettaglio della determina" href="<?php echo $url.$rs->id?>"><?php echo apply_filters('determine_list_subject_value',htmlspecialchars( $rs->oggetto) );?></a>
						</td>
						<td><?php echo apply_filters('determine_list_office_value',PAFacileDecodifiche::officeNameById( $rs->id_ufficio ) )?></td>
						<?php do_action('determine_list_after_header_columns',$rs, true); ?>
					</tr>
					<?php 
				}
				?>
			</table>
			<?php 
			toSendItGenericMethods::generatePaginationList($tableName, $filter,$pgurl);
		}
	}
	public static function dettagli($id){
		global $wpdb;
		
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_DETERMINE;
		$sql = 'select * from ' . $tableName .' where id=' . $id .' and data_adozione<="' .date('Y-m-d') .'"';
		$rs = $wpdb->get_row($sql);
		if($rs==null) return false;
		?>
		<div class="determina">
			<h4><?php echo $rs->oggetto ?></h4>
			<dl>
				<dt>Numero determina:</dt>
				<dd><?php echo $rs->numero ?></dd>
				<dt><abbr title="Ufficio">Uff.</abbr>/Area/<abbr title="Settore">Sett.</abbr>:</dt>
				<dd><?php echo PAFacileDecodifiche::officeNameById($rs->id_ufficio) ?></dd>
				<dt>Data adozione:</dt>
				<dd><?php echo(toSendItGenericMethods::formatDateTime( $rs->data_adozione)) ?></dd>
				<dt>Data pubblicazione all'albo pretorio:</dt>
				<dd><?php echo(toSendItGenericMethods::formatDateTime( $rs->data_albo)) ?></dd>
				<?php 
				do_action('determine_details_definition_list', $rs);
				?>
			</dl>
			<p>
			<?php 
			$rs->descrizione = apply_filters( 'default_content', $rs->descrizione);
			
			echo(wpautop(wptexturize( $rs->descrizione) ));
			?>
			</p>
			<h5>Allegati</h5>
			<?php 
			toSendItGenericMethods::displayFileUploadBox($tableName, $rs->id);
			?>
		</div>
		<?php 
		return true;
	}
}
?>