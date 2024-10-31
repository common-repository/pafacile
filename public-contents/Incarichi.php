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
	 * Procedure per la visualizzaizone degli incarichi professionali
	 * - mostraOrdinanze(): visualizza il modulo di ricerca per le ordinanze e l'elenco delle ultime ordinanze o della corrispondenza delle ordinanze trovate
	 * - mostraOrdinanzeForm(): visualizza il modulo di ricerca delle ordinanze
	 * - mostraOrdianzneElenco(): visualizza l'elenco delle ordinanze filtrate secondo i parametri del form di ricerca.
	 *****************************************************************/

require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/iContents.php';

class Incarichi implements iContents{
	
	public static function mostra($buffer){
		$itemId = isset($_GET['itemId'])?$_GET['itemId']:0;
		if(is_numeric($itemId) && $itemId!=0){
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
		if(isset($p['incarichi_id']) && $p['incarichi_id']!=0){
			if(isset($params) && is_array($params)) extract($params);
			extract($_GET);
			!isset($nominativo) && $nominativo = '';
			!isset($dal_yy) && $dal_yy = '';
			!isset($dal_mm) && $dal_mm = '';
			!isset($dal_dd) && $dal_dd = '';
			!isset($al_yy) && $al_yy = '';
			!isset($al_mm) && $al_mm = '';
			!isset($al_dd) && $al_dd = '';
				
			?>
			<form method="get" class="incarichi" action="<?php echo get_permalink($p['incarichi_id']) ?>">
				<div id="nominativo-incarico">
					<label for="nominativo">Nominativo:</label>
					<input type="text" id="nominativo" name="nominativo" value="<?php echo $nominativo?>" />
				</div>
				<fieldset id="data-dal-incarico">
					<legend>Dal giorno:</legend>
					<?php 
					toSendItGenericMethods::drawDateField('dal', $dal_yy.'-'.$dal_mm.'-'.$dal_dd);
					?>
				</fieldset>
				<fieldset id="data-al-incarico">
					<legend>Al giorno:</legend>
					<?php 
					toSendItGenericMethods::drawDateField('al', $al_yy.'-'.$al_mm.'-'.$al_dd);
					?>
				</fieldset>
				<p>
					<input type="submit" value="Cerca..." />
				</p>
			</form>
			<?php 
		}else{
			toSendItPAFacileContents::PAFacileConfigurationError();
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
		isset($nominativo) && $nominativo!='' && $filtro[] = "nominativo like '%$nominativo%'";
		isset($dal_dd) && isset($dal_mm) && isset($dal_yy) && 
			is_numeric($dal_dd) && is_numeric($dal_mm) && is_numeric($dal_yy) && $filtro[] = "dal >= '$dal_yy-$dal_mm-$dal_dd'";
		isset($al_dd) && isset($al_mm) && isset($al_yy) && 
			is_numeric($al_dd) && is_numeric($al_mm) && is_numeric($al_yy) && $filtro[] = " al <= '$al_yy-$pa_al_mm-$al_dd'";
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_INCARICHI;
		$sql = 'select * from ' . $tableName;
		
		if(count($filtro)>0){
			$filtro = ' where ' . implode(' and ', $filtro);
			$suffisso = '';
		}else{
			$suffisso = ' dell\'ultimo anno';
			$filtro = ' where ADDDATE(dal, 366)>now()';
		}
		$sql = toSendItGenericMethods::applyPaginationLimit($sql.  ' order by dal desc, al desc');
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
			}else{
				?>
				<p>
					<?php 
					echo $opzioni['dichiarazioneNegativa'];
					?>
				</p> 
				<?php
			}
		}else{
			$permalinks = get_option('PAFacile_permalinks');
			$baseUrl = get_permalink($permalinks['incarichi_id']);
			$url=$baseUrl.'?itemId='; 
			?>
			<h<?php echo $subLevel ?>>Incarichi professionali <?php echo $suffisso?></h<?php echo $subLevel ?>>
			<?php 
			$baseUrl .= toSendItGenericMethods::rebuildQueryString(array('pg'));
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl);
			?>
			<table cellpadding="0" cellspacing="0">
				<tr>
					<th>Nominativo</th>
					<th>Dal</th>
					<th>Al</th>
					<th>Compenso</th>
					<th>Rep. Gen. nr.</th>
					<th>Rep. Gen. del</th>
					<th>Data pubblicazione</th>
				</tr>
				<?php 
				$j = 0;
				foreach($results as $key => $rs){
					#print_r($rs);
					?>
					<tr <?php echo (($j++%2)==0)?'class="odd"':'' ?>>
						<td><a href="<?php echo $url.$rs->id?>"><?php echo($rs->nominativo)?></a></td>
						<td><?php echo(toSendItGenericMethods::formatDateTime( $rs->dal) )?></td>
						<td><?php echo(toSendItGenericMethods::formatDateTime( $rs->al) )?></td>
						<td><?php echo($rs->compenso)?></td>
						<td><?php echo($rs->provv_rep_gen_nr)?></td>
						<td><?php echo(toSendItGenericMethods::formatDateTime($rs->provv_rep_gen_del) )?></td>
						<td><?php echo(toSendItGenericMethods::formatDateTime($rs->data_pubblicazione)) ?></td>
					</tr>
					<?php 
				}
				?>
			</table>
			<?php 
		}
	}
	public static function dettagli($id){
	
		global $wpdb;
		
		$opzioni = get_option('PAFacile_settings');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_INCARICHI;
		$sql = 'select * from ' . $tableName .' where id="' . $id .'"';
		
		$rs = $wpdb->get_row($sql);
		if($rs==null) return false;
		?>
		<div class="ordinanza">
			<h<?php echo $subLevel?>><?php echo $rs->oggetto_incarico ?></h<?php echo $subLevel?>>
			<dl>
				<dt>Conferito a:</dt>
				<dd><?php echo $rs->nominativo ?> (Codice Fiscale/Partita IVA: <?php echo $rs->cf_nominativo ?> ) </dd>
				<dt>Modalità della selezione:</dt>
				<dd><?php echo $rs->modalita_selezione ?></dd>
				<dt>Tipo di rapporto:</dt>
				<dd><?php echo $rs->tipo_rapporto ?></dd>
				<dt>Provvedimento Repertorio Generale:</dt>
				<dd>nr. <?php echo $rs->provv_rep_gen_nr ?> del <?php echo(toSendItGenericMethods::formatDateTime( $rs->provv_rep_gen_del)) ?></dd>
			</dl>
			<h<?php echo $subLevel+1 ?>>Durata dell'incarico</h<?php echo $subLevel+1?>>
			<dl>
				<dt>Dal:</dt>
				<dd><?php echo(toSendItGenericMethods::formatDateTime( $rs->dal)) ?></dd>
				<dt>Al:</dt>
				<dd><?php echo(toSendItGenericMethods::formatDateTime( $rs->al)) ?></dd>
			</dl>
				
			<h<?php echo 	$subLevel+1?>>Motivo dell'incarico</h<?php echo $subLevel+1?>>
			<p>
			<?php 
			$rs->motivo_incarico = apply_filters( 'default_content', $rs->motivo_incarico);
			echo(wpautop(wptexturize( $rs->motivo_incarico) ));
			?>
			</p>
			<h<?php echo 	$subLevel+1?>>Allegati</h<?php echo $subLevel+1?>>
			<?php 
			toSendItGenericMethods::displayFileUploadBox($tableName, $rs->id);
			?>
		</div>
		<?php 
		return true;
	
	}
}

?>