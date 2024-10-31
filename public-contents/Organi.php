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

	/* **************************************************
	 * Metodi per la visualizzazione degli organi istituzionali
	 * mostraOrgani(): visualizza modulo di ricerca ed elenco degli organi attuali
	 * mostraOrganiForm(): visualizza il form di ricerca degli organi
	 * mostraOrganiElenco(): visualizza l'elenco degli organi istituzionali filtrati secondo i criteri del modulo
	 * mostraOrganiDettaglio(): visualizza la scheda di dettaglio del singolo organo istituzionale
	 ************************************************** */

require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/iContents.php';
class Organi implements iContents{
	
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
		global $wpdb;
		
		$p = get_option('PAFacile_permalinks');
		if(isset($p['organi_id']) && $p['organi_id']!=0){
			isset($params) && is_array($params) && extract($params);
			extract($_GET);
			?>
			<form method="get" class="organi" action="<?php the_permalink($p['organi_id'])?>">
				<div id="organi-nominativo">
					<label for="pa_nominativo">Cerca nominativo:</label>
					<input id="pa_nominativo" type="text" value="<?php echo($pa_nominativo)?>" name="pa_nominativo"/>
				</div>
				<div id="organi-data">
					<label for="pa_dd">Mostra la situazione alla data:</label>
					<?php 
					toSendItGenericMethods::drawDateField('pa', $pa_yy.'-'.$pa_mm.'-'.$pa_dd);
					?>
				</div>
				<div id="organi-tipo">
			
					<label for="pa_type">Mostra solo:</label>
					<?php 
					$tableTipiOrgano = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ORGANO;
					$sql = "select * from $tableTipiOrgano order by descrizione";
					$tipiOrgano = $wpdb->get_results($sql);
					?>
					<select name="type" id="pa_type">
						<option value="" <?php echo($type==''?'selected="selected"':''); ?> >Mostra tutto</option>
						<?php
						foreach($tipiOrgano as $tipoOrgano){
							?>
							<option value="<?php echo $tipoOrgano->codice ?>" <?php echo(($type==$tipoOrgano->codice)?'selected="selected"':''); ?> >
								<?php echo $tipoOrgano->descrizione ?>
							</option>
							<?php
						}
						?>
					</select>
			
				</div>
				
				<p>
					<input type="submit" class="button-secondary" value="Esegui ricerca">
				</p>
			</form>
			<?php
		}
	}
	public static function elenco($params = null){
		global $wpdb;
		
		$opzioni = get_option('PAFacile_settings');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		
		$p = get_option('PAFacile_permalinks');
		if(isset($p['organi_id']) && $p['organi_id']!=0){
			isset($params) && is_array($params) && extract($params);
			extract($_GET);
			$filtro = array();
			if(isset($type) && $type!=''){
				$tableOrganiRel = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANI . '_rel';
				$filtro[] = "(tipo='$type' or id in (select id_organo from $tableOrganiRel where tipo='$type'))";
			}
			isset($pa_nominativo) && $pa_nominativo != '' && $filtro[] = "nominativo like '%$pa_nominativo%'";
			if(isset($pa_dd) && isset($pa_mm) && isset($pa_yy) &&
								is_numeric($pa_dd) && is_numeric($pa_mm) && is_numeric($pa_yy) ){
				$filtro[] = "(in_carica_dal<='$pa_yy-$pa_mm-$pa_dd' and (in_carica_al>='$pa_yy-$pa_mm-$pa_dd' or in_carica_al is null or in_carica_al = '0000-00-00'))";
			}else{
				$filtro[] = '(in_carica_al>="'. date('Y-m-d').'" or in_carica_al is null or in_carica_al="0000-00-00") and in_carica_dal<="'. date('Y-m-d').'"';
			}
			$filtro = join(' and ', $filtro);
			
			if($filtro == ''){
				$filtro = 'where in_carica_al is null or in_carica_al = "0000-00-00"';
				$title = 'Organi istituzionali attualmente in carica';
			}else{
				$title = 'Risultati della ricerca';
				$filtro = "where $filtro"; 
			}
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANI;
			
			$sql = "select * from $tableName $filtro order by ordine asc, tipo desc, nominativo asc";
			$sql = toSendItGenericMethods::applyPaginationLimit($sql);

			$result = $wpdb->get_results($sql);
			if(count($result)==0){
				if(count($filtro)>0){
					?>
					<h<?php echo $subLevel ?>>La ricerca non ha restituito risultati</h<?php echo $subLevel ?>>
					<p>
						Spiacenti la ricerca non ha prodotto risultati utili.
					</p>
					<?php 
				}
			}else{
				?>
				<h<?php echo $subLevel ?>><?php echo $title?></h<?php echo $subLevel ?>>
				<?php 
				$url = get_permalink($p['organi_id']);
				$baseUrl =$url. toSendItGenericMethods::rebuildQueryString(array('pg'));
				toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl);
				?>
				<table cellpadding="0" cellspacing="0">
					<tr>
						<th>Carica</th>
						<th>Nominativo</th>
						<th>Deleghe</th>
						<th>In carica dal</th>
						<th>In carica fino al</th>
					</tr>
					<?php 
					$j = 0;
					foreach($result as $index => $rs){
						?>
						<tr <?php echo (($j++%2)==0)?'class="odd"':'' ?>>
							<td>
								<?php 
								echo(PAFacileDecodifiche::tipoOrgano($rs->tipo));
								$altriTipi = PAFacileDecodifiche::elencoTipiOrgano($rs->id, true);
								if($altriTipi!='') echo(", $altriTipi");
								?>
							</td>
							<td>
								<a href="<?php echo $url?>?itemId=<?php echo $rs->id?>"><?php echo($rs->nominativo)?></a>
							</td>
							<td>
								<?php echo(nl2br($rs->deleghe)); ?>
							</td>
							<td>
								<?php echo(toSendItGenericMethods::formatDateTime( $rs->in_carica_dal) ); ?>
							</td>
							<td>
								<?php 
								if($rs->in_carica_al!='0000-00-00')
									echo(toSendItGenericMethods::formatDateTime( $rs->in_carica_al) ); 
								else echo('&nbsp;');
									?>
							</td>
						</tr>
						<?php 
						
					}
					?>
				</table>
				<?php
			}
		}
	}
	
	public static function dettagli($itemId){
		global $wpdb;
		
		$opzioni = get_option('PAFacile_settings');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		
		if(!is_numeric($itemId)) return false;
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANI;
		$rs = $wpdb->get_row("select * from $tableName where id = $itemId");
		if($rs == null) return false;
		?>
		<div class="organo-istituzionale tipo-organo-<?php echo $rs->tipo?>">
			<h<?php echo $subLevel ?>><?php echo(PAFacileDecodifiche::tipoOrgano($rs->tipo))?>: <?php echo($rs->nominativo)?></h<?php echo $subLevel ?>>
			<dl>
				<dt>In carica dal:</dt>
				<dd><?php echo toSendItGenericMethods::formatDateTime( $rs->in_carica_dal )?></dd>
				<?php 
				if($rs->in_carica_al!=null && $rs->in_carica_al!='0000-00-00'){
					?>
					<dt>al:</dt>
					<dd><?php echo toSendItGenericMethods::formatDateTime( $rs->in_carica_al )?></dd>
					<?php 
				}
				$altreCariche = PAFacileDecodifiche::elencoTipiOrgano($rs->id, true);
				if($altreCariche!=''){
					?>
					<dt>Altre cariche:</dt>
					<dd>
						<?php echo $altreCariche ?>
					</dd>
					<?php 
				}
				?>
			</dl>
			<div class="deleghe">
				<h4>Deleghe</h4>
				<?php echo wpautop(wptexturize( $rs->deleghe) ); ?>
			</div>
			<?php 
			if($rs->dettagli!=''){
				?>
				<div class="dettagli">
					<h4>Altre informazioni</h4>
					<?php
				 	echo wpautop(wptexturize( $rs->dettagli) ); 
					?>
				</div>
				<?php 
			}
			?>
		</div>
		<?php 
		return true;
	}
}
?>