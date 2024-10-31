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


require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/iContents.php';

class BandiGare  extends PAFacilePublicBaseClass implements iContents{
	public static $displayed = false;
	
	
	public static function mostra($buffer){
		/*
		 * Se ho già visualizzato (sono in un contesto d'archivio) non devo ripresentare il contenuto.
		 */
		if(self::$displayed) return $buffer;
		self::$displayed = true;
		$itemId = isset($_GET['itemId'])?$_GET['itemId']:null;
		if(!is_null($itemId) && is_numeric($itemId)){
			
			ob_start();
			// Mostro il dettaglio di un bando
			
			if(!self::dettagli($itemId)){
				unset($_GET['itemId']);
				#return $buffer;
				#self::mostra($buffer);
			}else{
			}
			$buffer = ob_get_clean();
		}else{
			# Non faccio nulla
		}
		
		return $buffer;
	}
	
	public static function form($params=null){
		
		isset($params) && is_array( $params) && extract($params);
		$opzioni = get_option('PAFacile_settings');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		$isArchive = (isset($params['archive']) && $params['archive']=='y');
		if($isArchive && isset($_GET['itemId'])){
			echo self::mostra('');
			return true;
		}
		$p = get_option('PAFacile_permalinks');
		$hasPermalink = ( isset($p['bandi_id']) && $p['bandi_id']!=0 );
		if($hasPermalink || $isArchive){
			if($isArchive){
				$submitUrl = ''; 
			}else{
				$submitUrl = get_permalink($p['bandi_id']);
			}
			?>
			<form method="get" action="<?php echo $submitUrl ?>" class="bandi">
				<?php 
				if(isset($_GET['type'])) $type = $_GET['type'];
				?>
				<div class="bando-type">
					<?php 
					if($isArchive){
						?>
						<input type="hidden" name="archiveResults" value="y" />
						<?php 
					}
					?>
					<label for="pa_type">Tipo:</label>
					<select name="type" id="pa_type">
						<option value="">Qualsiasi tipo</option>
						<option value="co" <?php echo($type=='co'?'selected="selected"':'');?> >Bando di Concorso</option>
						<option value="ga" <?php echo($type=='ga'?'selected="selected"':'');?> >Bando di Gara</option>
						<option value="gr" <?php echo($type=='gr'?'selected="selected"':'');?> >Graduatoria</option>
						<option value="es" <?php echo($type=='es'?'selected="selected"':'');?> >Esito</option>
						<option value="ba" <?php echo($type=='ba'?'selected="selected"':'');?> >Altri bandi</option>
					</select>
				</div>
				<?php 
				if(isset($_GET['id_office'])) $id_office = $_GET['id_office'];
				?>
				<div class="bando-office-id">
					<label for="id_office">Indetto dall'Ufficio/Area/Settore:</label>
					<?php 
					toSendItPAFacile::buildOfficeSelectorForObject('id_office','id_office','',$id_office, TOSENDIT_PAFACILE_DB_BANDI);
					?>
				</div>
				<div class="bando-date">
					<h<?php echo $subLevel?>>Data Pubblicazione:</h<?php echo $subLevel?>>
					<fieldset>
						<legend>dal</legend>
						<?php 
						if(isset($_GET['dp_dal_yy'])) $dp_dal = $_GET['dp_dal_yy'] .'-'.$_GET['dp_dal_mm'].'-'.$_GET['dp_dal_dd'];
						toSendItGenericMethods::drawDateField('dp_dal',$dp_dal, true);
						?>
					</fieldset>
					<fieldset>
						<legend>al</legend>
						<?php 
						if(isset($_GET['dp_al_yy'])) $dp_al = $_GET['dp_al_yy'] .'-'.$_GET['dp_al_mm'].'-'.$_GET['dp_al_dd'];
						toSendItGenericMethods::drawDateField('dp_al',$dp_al, true);
						?>
					</fieldset>
				</div>
				<div>
					<h<?php echo $subLevel?>>Data Scadenza:</h<?php echo $subLevel?>>
					<fieldset>
						<legend>dal</legend>
						<?php 
						if(isset($_GET['ds_dal_yy'])) $ds_dal = $_GET['ds_dal_yy'] .'-'.$_GET['ds_dal_mm'].'-'.$_GET['ds_dal_dd'];
						toSendItGenericMethods::drawDateField('ds_dal',$ds_dal, true);
						?>
					</fieldset>
					<fieldset>
						<legend>al</legend>
						<?php 
						if(isset($_GET['ds_al_yy'])) $ds_al = $_GET['ds_al_yy'] .'-'.$_GET['ds_al_mm'].'-'.$_GET['ds_al_dd'];
						toSendItGenericMethods::drawDateField('ds_al',$ds_al, true);
						?>
					</fieldset>
				</div>
				<div class="submit-box">
					<input type="submit" value="Cerca..." />
				</div>
			</form>
			<?php
			if($isArchive && isset($_GET['archiveResults'])){
				self::elenco(array('archive' => 'y'));
			}
		}else{
			toSendItPAFacileContents::PAFacileConfigurationError();
		}
	}
	public static function elenco($params=null){
		global $wpdb;
		$opzioni = get_option('PAFacile_settings');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		$hideNoResults = false;
		/*
			'officeName' 	=> '',
			'type'			=> '',			// Vuoto vuol dire qualsiasi tipo
			'last' 			=> 0,			// Deve mostrare solo gli ultimi avvisi (0 tutti quelli non scaduti)
			'url'			=> $_SERVER['REQUEST_URI'],
			'resultTitle'	=> 'Risultati della ricerca',
		 */
		isset($params) && is_array( $params) && extract($params);
		if(isset($officeName) && $officeName!=''){
			$office = PAFacileDecodifiche::officeIdFromName($officeName);
		}
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_BANDI;
		$tableOrganigramma = $wpdb->prefix . TOSENDIT_PAFACILE_DB_USERS_TO_ORGANIGRAMMA;
		$sql = "select * from $tableName ";
		
		$filter = array();

		# Since V 2.4.6
		if(isset($_GET['dp_dal_dd']) && isset($_GET['dp_al_dd'])){
			# Since V. 2.4.4
			#$dp_al = toMySQLDate($_GET['dp_al_dd'], $_GET['dp_al_mm'], $_GET['dp_al_yy'], false);
			#$ds_dal = toMySQLDate($_GET['ds_dal_dd'], $_GET['ds_dal_mm'], $_GET['ds_dal_yy'], false);
			#$ds_al = toMySQLDate($_GET['ds_al_dd'], $_GET['ds_al_mm'], $_GET['ds_al_yy'], false);
			$dp_dal = toMySQLDate(isset($_GET['dp_dal_dd'])?$_GET['dp_dal_dd']:'01', isset($_GET['dp_dal_mm'])?$_GET['dp_dal_mm']:'01', isset($_GET['dp_dal_yy'])?$_GET['dp_dal_yy']:'1900', false);
			$dp_al = toMySQLDate(isset($_GET['dp_al_dd'])?$_GET['dp_al_dd']:'31', isset($_GET['dp_al_mm'])?$_GET['dp_al_mm']:'12', isset($_GET['dp_al_yy'])?$_GET['dp_al_yy']:date('Y'), false);
			$ds_dal = toMySQLDate(isset($_GET['ds_dal_dd'])?$_GET['ds_dal_dd']:'01', isset($_GET['ds_dal_mm'])?$_GET['ds_dal_mm']:'01', isset($_GET['ds_dal_yy'])?$_GET['ds_dal_yy']:'1900', false);
			$ds_al = toMySQLDate(isset($_GET['ds_al_dd'])?$_GET['ds_al_dd']:'31', isset($_GET['ds_al_mm'])?$_GET['ds_al_mm']:'12', isset($_GET['ds_al_yy'])?$_GET['ds_al_yy']:date('Y'), false);
			$filter[] = self::buildDataFilter('data_pubblicazione', $dp_dal, $dp_al);
			$filter[] = self::buildDataFilter('data_scadenza', $ds_dal, $ds_al);
		}else{
			$filter[] = "(data_pubblicazione <= now() and (data_scadenza >= now() or data_scadenza is null))";
		}
		
		if(isset($type) && $type!='') $filter[]="tipo ='$type'";
		if(isset($office) && $office!='') $filter[]= "id_ufficio = $office";
		
		if(isset($_GET['type']) && $_GET['type']!='') $filter[] .= "tipo='{$_GET['type']}'";
		if(isset($_GET['id_office']) && $_GET['id_office']!='') $filter[] .= "id_ufficio='{$_GET['id_office']}'";
		$filter = self::purgeFilter($filter);
		
				
		$filtro = join(' and ',$filter);
		 
		if($filtro!=''){
			$filtro = ' where ' . $filtro; //join(' and ', $filtro);
			if(!isset($title)) $title = 'Risultati della ricerca';
		}else{
			#Rimosso nella versione 2.1
			/*
			$filtro = 'where data_scadenza> now()or ADDDATE(data_pubblicazione, 31) >now()  order by data_scadenza asc';
			$title = 'Prossimi alla scadenza/Recenti';
			*/
		}
		$order = "order by data_pubblicazione desc, data_scadenza desc";
		$sql = toSendItGenericMethods::applyPaginationLimit("$sql $filtro $order");
		# echo("<pre>$sql</pre>");
		$results = $wpdb->get_results($sql);
		if(count($results)==0){
			
			$hideNoResults = $hideNoResults || (count($_GET)==0);
			
			if($filtro!='' && !$hideNoResults){
				?>
				<h<?php echo $subLevel?>>Spiacenti</h<?php echo $subLevel?>>
				<p>La ricerca effettuata non ha prodotto risultati</p>
				<?php
			} else {
				
				if(count($filter) == 0){
					
					do_action('pafacile_bandi_empty');
					
				}
			}
		}else{
			if(isset($title)){
				?>
				<h<?php echo $subLevel?>><?php echo $title; ?></h<?php echo $subLevel?>>
				<?php
			} 
			$permalinks = get_option('PAFacile_permalinks');
			/*
			 * Per il dettaglio
			 */
			if(isset($params['archive']) && $params['archive'] == 'y'){
				$url = toSendItGenericMethods::rebuildQueryString(array('itemId')) . 'itemId='; 
					
			}else{
				$url = get_permalink($permalinks['bandi_id']);
				$url.='?itemId=';
			}
			
			/*
			 * Per la paginazione
			 */
			$baseUrl = ''. toSendItGenericMethods::rebuildQueryString(array('pg'));
			
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl);
			?>
			<table cellpadding="0" cellspacing="0">
				<thead>
					<tr>
						<?php do_action('pafacile_bandi_before_table_head_columns') ?>
						<th><?php echo apply_filters('pafacile_bandi_etichetta_tipo' , 			'Tipo');  	?></th>
						<th><?php echo apply_filters('pafacile_bandi_etichetta_estremi' , 		'Estremi'); ?></th>
						<th><?php echo apply_filters('pafacile_bandi_etichetta_oggetto' , 		'Oggetto'); ?></th>
						<th><?php echo apply_filters('pafacile_bandi_etichetta_pubblicato_il', 	'Pubblicato il');  ?></th>
						<th><?php echo apply_filters('pafacile_bandi_etichetta_scade_il' , 		'Scade il');?></th>
						<th><?php echo apply_filters('pafacile_bandi_etichetta_ufficio' , 		'Ufficio');	?></th>
						<?php do_action('pafacile_bandi_after_table_head_columns') ?>
					</tr>
				</thead>
				<tbody>
					<?php
					$j = 0;
					foreach($results as $i => $row){
						?>
						<tr <?php echo (($j++%2)==0)?'class="odd"':'' ?>>
							<?php do_action('pafacile_bandi_before_table_data_columns') ?>
							<td><?php echo(PAFacileDecodifiche::tipoBando( $row->tipo) ) ?></td>
							<!-- Since V. 2.4.4 -->
							<td><?php echo($row->estremi) ?></td>
							<!-- / Since V. 2.4.4 -->
							<td>
								<a href="<?php echo $url. $row->id?>"><?php echo($row->oggetto) ?></a>
							</td>
							<td><?php echo(toSendItGenericMethods::formatDateTime( $row->data_pubblicazione) ) ?></td>
							<td><?php echo(toSendItGenericMethods::formatDateTime( $row->data_scadenza) ) ?></td>
							<td><?php echo(PAFacileDecodifiche::officeNameById($row->id_ufficio)) ?></td>
							<?php do_action('pafacile_bandi_after_table_data_columns') ?>
						</tr>
						<?php 
					}
					?>
				</tbody>
			</table>
			<?php
		}
	}
	public static function dettagli($id){
		
		global $wpdb;
		
		$opzioni = get_option('PAFacile_settings');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		
		
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_BANDI;
		$sql = 'select * from ' . $tableName .' where id=' . $id .' and data_pubblicazione<="' .date('Y-m-d') .'"';
		$rs = $wpdb->get_row($sql);
		if($rs==null) return false;
		
		$permalinks = get_option('PAFacile_permalinks');
		
		$urlUfficio = get_permalink($permalinks['organigramma_id']);
		$urlBandi = get_permalink($permalinks['bandi_id']);
		# Since V 2.4.4 - Bugfix: Problema nel link all'ufficio
		$urlUfficio.='?oid='; 
		
		$arrayInfoHeader = array();
		
		# Since ver 2.4.4
		#$arrayInfoHeader['Pubblicato il'] = toSendItGenericMethods::formatDateTime( $rs->data_pubblicazione);
		$arrayInfoHeader['Data di pubblicazione'] = toSendItGenericMethods::formatDateTime( $rs->data_pubblicazione);
		if(!is_null($rs->estremi))  $arrayInfoHeader['Estremi'] = $rs->estremi;
		if(!is_null($rs->procedura)) $arrayInfoHeader['Procedura'] = $rs->procedura;
		if(!is_null($rs->categoria)) $arrayInfoHeader['Categoria'] = $rs->categoria;
		if(!is_null($rs->id_padre) && $rs->id_padre!=0){
			$sql = 'select id,tipo, oggetto from ' . $tableName .' where id=' . $rs->id_padre;
			$rsPadre = $wpdb->get_row($sql);
			$arrayInfoHeader[PAFacileDecodifiche::tipoBando($rsPadre->tipo) . ' a cui si riferisce'] = '<a href="'.$urlBandi.'?itemId='. $rsPadre->id.'">'.$rsPadre->oggetto.'</a>';
		}
		
		$sql = 'select id,tipo, oggetto from '.$tableName .' where id_padre=' . $id;
		$records = $wpdb->get_results($sql);
		$esisteProroga = false;
		if($records){
			for($i=0; $i<count($records); $i++){
				$rsPadre = $records[0];
				if($rsPadre->tipo=='pr'){
					$esisteProroga = true;
					$urlProroga = $urlBandi.'?itemId='. $rsPadre->id;
				}
				$arrayInfoHeader['Collegamento ' . ($i+1). ': '. PAFacileDecodifiche::tipoBando($rsPadre->tipo) . ' riferito al presente documento'] = '<a href="'.$urlBandi.'?itemId='. $rsPadre->id.'">'.$rsPadre->oggetto.'</a>';
			}
			
		}
		
		$area = PAFacileDecodifiche::areaByOfficeId($rs->id_ufficio);
		$arrayInfoHeader['Area/Settore di competenza'] = '<a href="' . ($urlUfficio . $area  ).'">'.PAFacileDecodifiche::officeNameById($area).'</a>';
		if($rs->id_ufficio!=$area){
			$arrayInfoHeader['Ufficio/Settore di competenza'] = '<a href="'.($urlUfficio . $rs->id_ufficio).'">'.PAFacileDecodifiche::officeNameById($rs->id_ufficio).'</a>';
		}
		?>
		<div class="bando type-<?php echo  $rs->tipo?>">
			<h<?php echo $subLevel?>><?php echo PAFacileDecodifiche::tipoBando($rs->tipo) ?>
			<?php 
			if($rs->data_esito!='0000-00-00') echo('(Aggiudicato)');
			?>: <?php echo $rs->oggetto ?></h<?php echo $subLevel?>>
			<?php 
			if($esisteProroga){
				?>
				<div class="notifica">
					<strong>Attenzione!</strong><br /> 
					La data di scadenza riportata nella presente scheda è stata prorogata.
					Per poter consultare i nuovi termini di scadenza, si prega di 
					<a href="<?php echo($urlProroga)?>">consultare la proroga</a>.
				</div>
				<?php
			}
			if(count($arrayInfoHeader!=0)){
				?>
				<dl>
					<?php
					$i=0; 
					foreach($arrayInfoHeader as $key => $value){
						if($value!=null){
							$i+=1;
							$class=(($i%2)==0)?"even":'odd';
							?>
							<dt class="<?php echo $class?>"><?php echo $key ?>:</dt>
							<dd class="<?php echo $class?>"><?php echo $value ?><br /></dd>
							<?php
						}
					}
					?>
				</dl>
				<?php
			}?>
			<hr class="cleft ghosted" />
			<?php 
			$dataScadenza 	= preg_replace('#[0\-: ]#','', $rs->data_scadenza);
			$dataEsito 		= preg_replace('#[0\-: ]#','', $rs->data_esito);
			$importo		= $rs->importo;
			
			if($dataScadenza!='' || $dataEsito!='' || $importo!=''){
				?>
				<h4><?php echo($rs->tipo=='pr'?'Nuovi t':'T')?>ermini</h4>
				<dl>
					<?php
					
					if($dataScadenza!=''){
						?>
						<dt>Data scadenza:</dt>
						<dd><?php echo(toSendItGenericMethods::formatDateTime( $rs->data_scadenza)) ?><br /></dd>
						<?php 
					}
					if($dataEsito!=''){
						?>
						<dt>Data aggiudicazione:</dt>
						<dd><?php echo(toSendItGenericMethods::formatDateTime( $rs->data_esito)) ?><br /></dd>
						<dt>Aggiudicatario:</dt>
						<dd><?php echo($rs->aggiudicatario)?><br /></dd>
						<?php 
					} 
					if($importo!=''){
						?>
						<dt>Importo:</dt>
						<dd>
							<?php 
							echo($rs->importo);
							
							if($rs->annotazioni_importo!=''){
								
								echo("({$rs->annotazioni_importo})");
							}
							?>
							
						</dd>
						<?php 
					}
					do_action('pafacile_bandi_after_dettagli');
					?>
				</dl>
				<hr class="cleft ghosted" />
				<p>
				<?php
			} 
			$rs->descrizione = apply_filters( 'default_content', $rs->descrizione);
			// $rs->descrizione = apply_filters( 'the_content', $rs->descrizione);
			
			echo(wpautop(wptexturize( $rs->descrizione) ));
			?>
			</p>
			<?php 
			if(toSendItGenericMethods::hasAttachments($tableName, $rs->id)){
				?>
				<h4>Allegati</h4>
				<?php 
				toSendItGenericMethods::displayFileUploadBox($tableName, $rs->id);
			}
			?>
		</div>
		<?php 
		return true;
	
	}
}
?>