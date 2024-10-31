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
 * Procedure per la visualizzazione pubblica dell'albo pretorio:
 * 
 * - mostra(): mostra sia il modulo di ricerca che l'elenco
 * - form(): mostra il modulo di ricerca delle delibere
 * - elenco(): mostra la tabella dei risultati delle delibere
 * - dettagli(): mostra i dettagli
 * 
 *****************************************************************/
require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/iContents.php';
class AlboPretorio extends PAFacilePublicBaseClass implements iContents {

	public static function mostra($buffer){
		$itemId = isset($_GET['itemId'])?$_GET['itemId']:'';
		if(isset($itemId) && is_numeric($itemId)){
			
			ob_start();
			// Mostro il dettaglio di un atto pubblicato nell'albo
			if(!self::dettagli($itemId)){
				unset($_GET['itemId']);
				echo($buffer);
			}
			$buffer = ob_get_clean();
			
		}
		return $buffer;	
	}
	
	public static function form($params = null){
		if(isset($params) && is_array($params)) extract($params);
		$p = get_option('PAFacile_permalinks');
		if(isset($p['albopretorio_id']) && $p['albopretorio_id']!=0){
			extract($_GET);
			!isset($tipo) && $tipo = '';
			?>
			<form method="get" class="albopretorio" action="<?php echo get_permalink($p['albopretorio_id']) ?>">
				<div class="tipopubblicazione" id="ap-tipopubblicazione">
					<label for="pa_tipo">Tipo di pubblicazione:</label>
					<select name="tipo" id="pa_tipo">
						<option value="">Tutte</option>
						<?php 
						// Since ver 1.5
						global $wpdb;
						$tblTipiAtto = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ATTO;
						$sql ="select codice,descrizione,raggruppamento from $tblTipiAtto order by raggruppamento, descrizione";
						$results = $wpdb->get_results($sql);
						$raggruppamento = '';
						foreach($results as $result){
							if($raggruppamento!=$result->raggruppamento){
								if($raggruppamento!='') echo('</optgroup>');
								$raggruppamento = $result->raggruppamento;
								echo("<optgroup label=\"$raggruppamento\">");
							}
							?>
							<option value="<?php echo $result->codice ?>"
								<?php echo($tipo==$result->codice?'selected="selected"':'');?>
								><?php echo($result->descrizione) ?></option>
							<?php
						}
						if($raggruppamento!='') echo('</optgroup>');
						?>
					</select>
				</div>
				<div class="oggetto" id="ap-oggetto">
				 	<label for="oggetto">L'oggetto contiene il seguente testo:</label>
				 	<input type="text" name="oggetto" id="oggetto" class="widefat" />
				</div>
				<fieldset class="data cboth" id="ap-pubblicazione-dal">
			 		<legend>Pubblicato a partire dal</legend>
				 	<?php 
					toSendItGenericMethods::drawDateField('dpd', toMySQLDate($_GET['dpd_dd'], $_GET['dpd_mm'], $_GET['dpd_yy']) );
					?>
				</fieldset>
				<fieldset class="data" id="ap-pubblicazione-al">
			 		<legend>Pubblicato fino al</legend>
			 		<?php toSendItGenericMethods::drawDateField('dpa', toMySQLDate($_GET['dpa_dd'], $_GET['dpa_mm'], $_GET['dpa_yy'])) ?>
				</fieldset>
				<div id="ap-datiatto">
					<div id="ap-tiporicerca">
						<label for="tipo-ricerca">Ricerca per:</label>
						<select name="tr" id="tipo-ricerca">
							<option value="">Atto, Fascicolo, Protocollo e Repertorio Generale</option>
							<option value="atto" <?php echo $tr=='atto'?'checked="checked"':'' ?>>Atto</option>
							<option value="fascicolo" <?php echo $tr=='fascicolo'?'checked="checked"':'' ?>>Fascicolo</option>
							<option value="protocollo" <?php echo $tr=='protocollo'?'checked="checked"':'' ?>>Protocollo</option>
							<option value="repertorio" <?php echo $tr=='repertorio'?'checked="checked"':'' ?>>Repertorio Generale</option>
						</select>
					</div>
					
				 	<div id="ap-numeroatto">
				 		<label for="numero">Numero:</label>
				 		<input type="text" name="nr" id="numero" value="<?php echo($nr) ?>" />
				 	</div>
				 	<fieldset class="data cboth">
					 	<legend>Emesso dopo il</legend><?php 
						toSendItGenericMethods::drawDateField('add', toMySQLDate($_GET['add_dd'], $_GET['add_mm'], $_GET['add_yy']) );
						?>
				 	</fieldset>
				 	<fieldset class="data">
					 	<legend>Emesso prima del</legend>
					 	<?php 
						toSendItGenericMethods::drawDateField('ada', toMySQLDate($_GET['ada_dd'], $_GET['ada_mm'], $_GET['ada_yy']) );
						?>
				 	</fieldset>
				 </div>
				 <p class="submit-area">
				 	<input type="submit" value="Cerca" />
				 </p>
			</form>
			<?php
		}
	}
	public static function elenco($params = null){
		if(isset($params) && is_array($params)) extract($params);
		extract($_GET);
		!isset($tr) && $tr = '';
		$p = get_option('PAFacile_permalinks');
		
		$opzioni = get_option('PAFacile_settings');
		$privacy = ($opzioni['AlboPretorioPrivacy'] =='y');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		
		if(isset($p['albopretorio_id']) && $p['albopretorio_id']!=0){
			
			$filter = array();

			$dpd = toMySQLDate($dpd_dd,$dpd_mm,$dpd_yy);
			$dpa = toMySQLDate($dpa_dd,$dpa_mm,$dpa_yy);
			if(!isset($pg) || $pg=='') $pg=0;
			if(isset($tipo) && $tipo!='') $filter[] = "tipo='$tipo'";
			if(isset($oggetto) && $oggetto!='') $filter[] = "oggetto like '%$oggetto%'";
			$filter[] = self::buildDataFilter('pubblicata_dal', $dpd, '0000-00-00'); 
			$filter[] = self::buildDataFilter('pubblicata_al', '0000-00-00',  $dpa); 
			
			if($tr==''){
				
				// Ricerca per tutte le date in Albo
				$data_da = toMySQLDate($add_dd, $add_mm, $add_yy, false);
				$data_a =  toMySQLDate($ada_dd, $ada_mm, $ada_yy, false);
				
				$filterAllDate = array();
				$filterAllDate[] = self::buildDataFilter('repertorio_data', $data_da, $data_a);
				$filterAllDate[] = self::buildDataFilter('fascicolo_data', 	$data_da, $data_a);
				$filterAllDate[] = self::buildDataFilter('atto_data', 		$data_da, $data_a);
				$filterAllDate[] = self::buildDataFilter('protocollo_data', $data_da, $data_a);
				$filterAllDate = self::purgeFilter($filterAllDate);
				if(count($filterAllDate)>0) $filter[] = join(' or ', $filterAllDate);
				
				if(isset($nr) && is_numeric($nr)){
					$filter[] = "( repertorio_nr = '$nr' or fascicolo_nr = '$nr' or atto_nr = '$nr' or protocollo_nr = '$nr')";
				}
				
			}else{
				
				if($tr=='protocollo' || $tr=='fascicolo' || $tr=='atto' || $tr=='repertorio'){
					// Ricerca per tutte le date in Albo
					$data_da = toMySQLDate($add_dd, $add_mm, $add_yy, false);
					$data_a =  toMySQLDate($ada_dd, $ada_mm, $ada_yy, false);
					$filter[] = self::buildDataFilter($tr, $data_da, $data_a);	
				}
				if(isset($nr) && is_numeric($nr)) $filter[] = "( {$tr}_nr = '$nr')";
				
			}
			
			global $wpdb;
			$table = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
			// Since Ver 1.4 -- Solo gli atti con stato "pubblico" saranno visibili
			# $filter[] = "`status` <> '0' ";
			
			// Since Ver 1.4.1 -- Solo gli atti con stato "pubblico" saranno visibili
			# 0 = Bozza
			# 8 = Pronti per la pubblicazione
			
			$filter[] = "`status` not in(0,8) ";
			
			$filter = self::purgeFilter($filter);
			# --------------------------------------------------------------------------------------------
			# Bug segnalato da Comune di Sala Consilina: l'atto risulta pubblicato per 1 giorno in meno
			# --------------------------------------------------------------------------------------------
			$filtro_pubblicata_al 	= 'date_add(pubblicata_al, interval 1 day)';
			$filtro_data_proroga	= 'date_add(data_proroga, interval 1 day)';
			
			$oldFilter = $filter;
			$filter = self::purgeFilter($filter);
			
			$filtro = join($filter, ' and ');
			
			if(count($filter)==1 ||  $privacy){
				$filtro = "$filtro and ";
				$filtro .= "(now() between pubblicata_dal and $filtro_pubblicata_al) or (data_proroga is not null and data_proroga<>'0000-00-00') and (now() between pubblicata_dal and $filtro_data_proroga)";
			}
			# --------------------------------------------------------------------------------------------
			if($filtro !='') $filtro = "where $filtro";
			# Since Ver 1.4.2 -- ordinamento decrescente degli atti.
			#$sql = toSendItGenericMethods::applyPaginationLimit("select * from $table $filtro");
			$sql = toSendItGenericMethods::applyPaginationLimit("select * from $table $filtro order by if(data_proroga is not null, data_proroga, pubblicata_al) desc, pubblicata_dal desc, id desc");
			
			# echo($sql);
			
			$results = $wpdb->get_results($sql);
			if(count($results)==0){
				if(count($filter)>0){
					?>
					<h<?php echo $subLevel?>>Spiacenti</h<?php echo $subLevel?>>
					<p>La ricerca non ha prodotto risultati</p>
					<?php
				}else{
					// Do nothing
					do_action('pafacile_albopretorio_empty');
				} 
			}else{		
				$baseUrl =get_permalink($p['albopretorio_id']);
				$baseUrl.=toSendItGenericMethods::rebuildQueryString(array('pg'));
				?>
				<h<?php echo $subLevel?>>Elenco delle affissioni all'albo online</h<?php echo $subLevel?>>
				<?php 
				toSendItGenericMethods::generatePaginationList($table, $filtro, $baseUrl);
				?>
				<table cellpadding="0" cellspacing="0">
					<thead>
						<tr>
							<?php do_action('pafacile_albopretorio_before_table_head_columns'); ?>
							<th>Numero Registro</th>
							<th>Provenienza</th>
							<th>Area</th>
							<th>Tipologia</th>
							<th>Oggetto</th>
							<th>Pubblicato il</th>
							<th>Scadenza</th>
							<?php do_action('pafacile_albopretorio_after_table_head_columns'); ?>
						</tr>
					</thead>
					<tbody>
						<?php 
						$i = 1;
						foreach($results as $key => $row){
							$theClass = '';
							$rowUrl = $baseUrl . 'itemId=' .$row->id . '&amp;pg=' . $pg;
							if($row->numero_registro==null) $row->numero_registro = 'N/D';
							
							$theClass = (($i++%2)==0)?'odd':'' ;
							if($row->status == TOSENDIT_PAFACILE_ATTO_ANNULLATO) $theClass .= " deleted";
							
							# Introdotto nella versione 2.1
							$numeroRegistro = apply_filters('pafacile_albo_pretorio_numero_registro', $row);
							if(is_object($numeroRegistro) ) $numeroRegistro = $row->numero_registro;
							?>
							<tr <?php echo ($theClass!='')?"class=\"$theClass\"":'' ?>>
								<?php do_action('pafacile_albopretorio_before_table_data_columns'); ?>
								<td>
									<a href="<?php echo $rowUrl ?>"><?php echo $numeroRegistro ?></a>
								</td>
								<td>
									<a href="<?php echo $rowUrl ?>"><?php echo $row->provenienza ?></a>
								</td>
								<td>
									<a href="<?php echo $rowUrl ?>"><?php echo PAFacileDecodifiche::officeNameById( PAFacileDecodifiche::areaByOfficeId($row->id_ufficio) ) ?></a>
								</td>
								<td>
									<a href="<?php echo $rowUrl ?>"><?php echo PAFacileDecodifiche::tipoAtto($row->tipo) ?></a>
								</td>
								<td>
									<a href="<?php echo $rowUrl ?>"><?php echo $row->oggetto ?></a>
								</td>
								<td>
									<a href="<?php echo $rowUrl ?>"><?php echo toSendItGenericMethods::formatDateTime($row->pubblicata_dal,'%d/%m/%Y') ?></a>
								</td>
								<td>
									<a href="<?php echo $rowUrl ?>"><?php  echo toSendItGenericMethods::formatDateTime($row->pubblicata_al,'%d/%m/%Y') ?></a>
									<?php 
									# Bugfix - errore nell'espressione della condizione 
									if($row->data_proroga!=null && $row->data_proroga!='0000-00-00'){
										?><br />
										Proroga al: <?php echo toSendItGenericMethods::formatDateTime($row->data_proroga,'%d/%m/%Y') ?>
										<?php
									}
									?>
								</td>
								<?php do_action('pafacile_albopretorio_after_table_data_columns'); ?>
							</tr>
							<?php 
						}
						?>
					</tbody>	
				</table>
				<?php
				toSendItGenericMethods::generatePaginationList($table, $filtro, $baseUrl);
				
			}		
		}
	}
	public static function dettagli($id){
		#echo('provo a scrivere i dettagli');
		$p = get_option('PAFacile_permalinks');
		global $wpdb;
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
		$sql = 'select * from ' . $tableName .' where id=' . $id;

		$opzioni = get_option('PAFacile_settings');
		$privacy = ($opzioni['AlboPretorioPrivacy'] =='y');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		
		if($privacy) $sql.=' and pubblicata_dal<="' .date('Y-m-d') .'" and (pubblicata_al>="' .date('Y-m-d') .'" or data_proroga is not null and data_proroga >="'.date('Y-m-d'). '")';
		
		$rs = $wpdb->get_row($sql);
		if($rs==null) return false;
		$baseUrl =get_permalink($p['albopretorio_id']) .'?';
		$qs = '';
		foreach($_GET as $key => $value){
			if($key!='itemId'){
				if($qs!='') $qs.='&';
				$qs.=urlencode($key) . '=' . urlencode($value);
			}
		}
		$baseUrl.= $qs;
		?>
		<div class="albopretorio">
			<h<?php echo $subLevel?>><?php echo($rs->oggetto)?></h<?php echo $subLevel?>>
			<p>
				<a href="<?php echo $baseUrl ?>">Torna all'elenco</a>
			</p>
			<dl>
				<dt>Numero registro:</dt>
				<dd>
					<?php 
					$numeroRegistro = apply_filters('pafacile_albo_pretorio_numero_registro', $rs);
					if(is_object($numeroRegistro) ) $numeroRegistro = $rs->numero_registro;
					echo($numeroRegistro);
					?>
				</dd>
				<dt>Tipo di atto:</dt>
				<dd><?php echo(PAFacileDecodifiche::tipoAtto($rs->tipo)) ?></dd>
				<?php 
				if(is_numeric($rs->atto_nr) && $rs->atto_nr!=0){
					?>
					<dt>Numero atto:</dt>
					<dd><?php echo $rs->atto_nr ?></dd>
					<dt>Data atto:</dt>
					<dd><?php echo(toSendItGenericMethods::formatDateTime( $rs->atto_data)) ?></dd>
					<?php 
				}
				?>
				<dt>Area/Settore/Ufficio di competenza</dt>
				<dd>
					<?php 
					echo(PAFacileDecodifiche::officeNameById($rs->id_ufficio));
					?>
				</dd>
				<?php 
				if(isset($rs->dirigente) && $rs->dirigente!=''){
					?>
					<dt>Dirigente:</dt>
					<dd><?php echo($rs->dirigente) ?></dd>
					<?php 
				}
				if(isset($rs->responsabile) && $rs->responsabile!=''){
					?>
					<dt>Responsabile:</dt>
					<dd><?php echo($rs->responsabile) ?></dd>
					<?php
				}
				?>
				<dt>Pubblicazione all'albo:</dt>
				<dd>
					dal <?php echo(toSendItGenericMethods::formatDateTime( $rs->pubblicata_dal )) ?>
					al  <?php echo(toSendItGenericMethods::formatDateTime( $rs->pubblicata_al )) ?>
					<?php 
					if($rs->data_proroga!=null and $rs->data_proroga!='0000-00-00'){
						?><br />
						<strong>pubblicazione prorogata fino al <?php echo(toSendItGenericMethods::formatDateTime( $rs->data_proroga )) ?></strong>
						<?php
					}
					?>
				</dd>
				<?php 
				if($rs->status==9){
					# La pubblicazione è stata annullata
					?>
					<dt>Annullato in data:</dt>
					<dd><?php echo(toSendItGenericMethods::formatDateTime( $rs->annullato_il )) ?></dd>
					<dt>Motivo:</dt>
					<dd><?php echo $rs->motivo ?></dd>
					<?php
				}
				if(is_numeric($rs->repertorio_nr) && $rs->repertorio_nr!=0){
					?>
					<dt>Numero Repertorio Generale</dt>
					<dd>
						<?php echo($rs->repertorio_nr)?>
					</dd>
					<dt>Data Repertorio Generale</dt>
					<dd>
						<?php echo(toSendItGenericMethods::formatDateTime( $rs->repertorio_data )) ?>
					</dd>
					<?php 
				}
				if(is_numeric($rs->protocollo_nr) && $rs->protocollo_nr!=0){
					?>
					<dt>Numero Protocollo</dt>
					<dd>
						<?php echo($rs->protocollo_nr)?>
					</dd>
					<dt>Data Protocollo</dt>
					<dd>
						<?php echo(toSendItGenericMethods::formatDateTime( $rs->protocollo_data )) ?>
					</dd>
					<?php 
				}
				do_action('pafacile_albopretorio_after_dettagli');
				?>
			</dl>
			<div id="dettaglio-pubblicazione">
				<p>
				<?php 
				$rs->descrizione = apply_filters( 'default_content', $rs->descrizione);
				
				echo(wpautop(wptexturize( $rs->descrizione) ));
				?>
				</p>
			</div>
			<h5>Allegati</h5>
			<?php 
			toSendItGenericMethods::displayFileUploadBox($tableName, $rs->id);
			?>
		</div>
		<?php
		return true;
	}
}