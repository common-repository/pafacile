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
	 * Procedure per la visualizzaizone delle ordinanze
	 * - mostraOrdinanze(): visualizza il modulo di ricerca per le ordinanze e l'elenco delle ultime ordinanze o della corrispondenza delle ordinanze trovate
	 * - mostraOrdinanzeForm(): visualizza il modulo di ricerca delle ordinanze
	 * - mostraOrdianzneElenco(): visualizza l'elenco delle ordinanze filtrate secondo i parametri del form di ricerca.
	 *****************************************************************/

require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/iContents.php';

class Ordinanze extends PAFacilePublicBaseClass implements iContents{
	
	public static function mostra($buffer){
		$itemId = $_GET['itemId'];
		if(isset($itemId) && is_numeric($itemId)){
			ob_start();
			// Mostro il dettaglio di un bando
			if(!self::dettagli($itemId)){
				unset($_GET['itemId']);
				echo($buffer);
			}
			$buffer = ob_get_clean();
			
		}
		return $buffer;
		
	}
	public static function form($params = null){

		$p = get_option('PAFacile_permalinks');
		if(isset($p['ordinanze_id']) && $p['ordinanze_id']!=0){
			if(isset($params) && is_array($params)) extract($params);
			extract($_GET);
			?>
			<form method="get" class="ordinanze" action="<?php echo get_permalink($p['ordinanze_id']) ?>">
				<div id="numero-ordinanza">
					<label for="numero">Numero</label>
					<input type="text" name="numero" value="<?php echo $numero?>" />
				</div>
				<div id="id-ufficio-ordinanza">
					<label for="id_office">Emanante:</label>
					<?php 
					toSendItPAFacile::buildOfficeSelectorForObject('id_office','id_office','',$id_office, TOSENDIT_PAFACILE_DB_ORDINANZE);
					?>
				</div>
				<fieldset id="data-dal-ordinanza">
					<legend>Dal giorno:</legend>
					<?php 
					toSendItGenericMethods::drawDateField('pa_dal', $pa_dal_yy.'-'.$pa_dal_mm.'-'.$pa_dal_dd);
					?>
				</fieldset>
				<fieldset id="data-al-ordinanza">
					<legend>Al giorno:</legend>
					<?php 
					toSendItGenericMethods::drawDateField('pa_al', $pa_al_yy.'-'.$pa_al_mm.'-'.$pa_al_dd);
					?>
				</fieldset>
				<p>
					<input type="submit" value="Cerca..." />
				</p>
			</form>
			<?php 
		}else{
			self::PAFacileConfigurationError();
		}
		
	}
	public static function elenco($params = null){
		global $wpdb;
		
		$opzioni = get_option('PAFacile_settings');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		
		if(isset($params) && is_array($params)) extract($params);
		extract($_GET);
		$filtro = array();
		isset($numero) && is_numeric($numero) && $filtro[] = "numero='$numero'";
		isset($id_office) && is_numeric($id_office) && $filtro[] = "id_ufficio='$id_office'";
		
		$pa_dal = (isset($pa_dal_dd) && isset($pa_dal_mm) && isset($pa_dal_yy) ) ? 
					toMySQLDate($pa_dal_dd,$pa_dal_mm,$pa_dal_yy, false) : '0000-00-00';
		$pa_al = (isset($pa_al_dd) && isset($pa_al_mm) && isset($pa_al_yy) ) ? 
					toMySQLDate($pa_al_dd,$pa_al_mm,$pa_al_yy, 	false) : '9999-99-99';
		
		$filtro[] = self::buildDataFilter('data_adozione', $pa_dal, $pa_al);
		$filtro = self::purgeFilter($filtro);
		
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORDINANZE;
		$sql = 'select * from ' . $tableName;
		
		if(count($filtro)>0){
			$filtro = ' where ' . implode(' and ', $filtro);
			$suffisso = '';
		}else{
			if(count($_GET)==0){
				$suffisso = ' recenti';
				$filtro = ' where ADDDATE(data_adozione, 7)>now()';
			}
		}
		$sql = toSendItGenericMethods::applyPaginationLimit($sql. ' ' . $filtro. ' order by data_adozione desc, numero desc');
		#echo($sql);
		$results = $wpdb->get_results($sql);
		if(count($results)==0){
			if(count($_GET)>0){
				?>
				<h<?php echo $subLevel ?>>Spiacenti, nessun risultato trovato.</h<?php echo $subLevel ?>>
				<p>
					La ricerca effettuata non ha prodotto alcun risultato, 
					si consiglia di modificare i criteri e ripetere la ricerca.
				</p>
				<?php
			}	
		}else{
			$permalinks = get_option('PAFacile_permalinks');
			$baseUrl = get_permalink($permalinks['ordinanze_id']);
			$url=$baseUrl.'?itemId='; 
			?>
			<h<?php echo $subLevel ?>>Ordinanze <?php echo $suffisso?></h<?php echo $subLevel ?>>
			<?php 
			$baseUrl .= toSendItGenericMethods::rebuildQueryString(array('pg'));
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl);
			?>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>Numero</th>
					<th>Data adozione</th>
					<th>Oggetto</th>
					<th>Emanante</th>
				</tr>
				<?php
				$j =0; 
				foreach($results as $key => $rs){
					?>
					<tr <?php echo (($j++%2)==0)?'class="odd"':'' ?>>
						<td><?php echo($rs->numero)?></td>
						<td><?php echo(toSendItGenericMethods::formatDateTime( $rs->data_adozione) )?></td>
						<td><a href="<?php echo $url.$rs->id?>"><?php echo($rs->oggetto)?></a></td>
						<td><?php echo PAFacileDecodifiche::officeNameById($rs->id_ufficio)?></td>
					</tr>
					<?php 
				}
				?>
			</table>
			<?php
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl);
			 
		}
	}
	public static function dettagli($id){
	
		global $wpdb;
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORDINANZE;
		$sql = 'select * from ' . $tableName .' where id=' . $id .' and data_adozione<="' .date('Y-m-d') .'"';
		$rs = $wpdb->get_row($sql);
		if($rs==null) return false;
		?>
		<div class="ordinanza">
			<h4><?php echo $rs->oggetto ?></h4>
			<dl>
				<dt>Numero ordinanza:</dt>
				<dd><?php echo $rs->numero ?></dd>
				<dt>Emanante:</dt>
				<dd><?php echo PAFacileDecodifiche::officeNameById($rs->id_ufficio) ?></dd>
				<dt>Data adozione:</dt>
				<dd><?php echo(toSendItGenericMethods::formatDateTime( $rs->data_adozione)) ?></dd>
			</dl>
			<p>
			<?php 
			$rs->descrizione = apply_filters( 'default_content', $rs->descrizione);
			// $rs->descrizione = apply_filters( 'the_content', $rs->descrizione);
			
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