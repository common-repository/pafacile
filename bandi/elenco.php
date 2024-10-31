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


function displayBandiPublic($params, $extraParams = array()){
	global $wpdb;
	
	$params = toSendItGenericMethods::identifyParameters($params,
		array(
			'kind'			=> 'list', 		// Può essere table (elenco dei bandi) oppure box (il form di ricerca)
		)
	);
	$params = array_merge($params, $extraParams);
	
	extract($params);
	switch($kind){
		case 'box':
			BandiGare::form($params);
			break;
		case 'list':
		default:
			BandiGare::elenco($params);
			break;
	}
	
}

function displayBandi(){
	global $wpdb, $current_user;
	toSendItGenericMethods::mergeSearchFilter('ricerca_determine');
	
	$type 	= isset($_GET['type'])?$_GET['type']:'';
	$option = isset($_GET['option'])?$_GET['option']:'';
	$when 	= isset($_GET['when'])?$_GET['when']:'';
	$data 	= isset($_GET['data_yy'])?($_GET['data_yy'] . '-'.$_GET['data_mm'].'-'.$_GET['data_dd']):'';
	if($data=='--') $data = '';
	$office = isset($_GET['office'])?$_GET['office']:'';
	?>
	<div class="wrap">
		<div id="icon-edit-pages" class="icon32">
			<br/>
		</div>
		<h2>Elenco bandi gare, concorsi e graduatorie</h2>
		<form method="GET" id="post-filter" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<input type="hidden" name="page" value="<?php echo TOSENDIT_PAFACILE_BANDI_EDIT_HANDLER ?>" />
			<p class="search-box">
				<label class="screen-reader-text" for="post-search-input">Cerca un'elemento che abbia il seguente oggetto:</label>
				<input id="post-search-input" type="text" value="<?php echo(isset($_GET['s'])?$_GET['s']:'')?>" name="s"/>
				<input class="button" type="submit" value="Cerca informazione..."/>
			</p>
			<div class="tablenav">
				<label class="screen-reader-text" for="pa_type">Tipologia di documento:</label>
				<select name="type" id="pa_type">
					<option value="">Qualsiasi</option>
					<option value="co" <?php echo($type=='co'?'selected="selected"':'');?> >Bando di Concorso</option>
					<option value="ga" <?php echo($type=='ga'?'selected="selected"':'');?> >Bando di Gara</option>
					<option value="gr" <?php echo($type=='gr'?'selected="selected"':'');?> >Graduatoria</option>
					<!-- 
					<option value="es" <?php echo($type=='es'?'selected="selected"':'');?> >Esito</option>
					 -->
					<option value="ba" <?php echo($type=='ba'?'selected="selected"':'');?> >Altri bandi</option>
					<option value="pr" <?php echo($type=='pr'?'selected="selected"':'');?> >Proroga</option>
					 
				</select>
				<label class="screen-reader-text" for="pa_option">Criterio:</label>
				<select name="option" id="pa_option">
					<option value="data_pubblicazione" <?php echo($option=='data_pubblicazione'?'selected="selected"':'');?> >Pubblicato</option>
					<option value="data_scadenza" <?php echo($option=='data_scadenza'?'selected="selected"':'');?> >Scaduto</option>
					<option value="data_esito" <?php echo($option=='data_esito'?'selected="selected"':'');?> >Aggiudicato</option>
				</select>
				<label class="screen-reader-text" for="pa_when">Quando:</label>
				<select name="when" id="pa_when">
					<option value="=" <?php echo($when=='='?'selected="selected"':''); ?> >in data</option>
					<option value="&lt;" <?php echo($when=='<'?'selected="selected"':'');?> >prima del</option>
					<option value="&gt;" <?php echo($when=='>'?'selected="selected"':'');?> >dopo il</option>
				</select>
				<?php 
				toSendItGenericMethods::drawDateField('data', $data);
				?><br style="display: block; clear: left;" />
				<label for="pa_office">Ufficio:</label>
				<?php
				toSendItPAFacile::buildOfficeSelector('office','pa_office','',$office,true);
				?>
				<input type="submit" class="button-secondary" onclick="document.getElementById('post-search-input').value='';" value="Esegui ricerca">
			</div>
			<?php 
			$filtro = array();
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_BANDI;
			$tableOrganigramma = $wpdb->prefix . TOSENDIT_PAFACILE_DB_USERS_TO_ORGANIGRAMMA;
			#$sql = "select * from $tableName where id_ufficio in (select id_organigramma from $tableOrganigramma where id_utente={$current_user->ID}) ";
			$sql = "select * from $tableName";
			if($type!='') $filtro[]="tipo ='$type'";
			if($option!='' && $when!='' && $data!='' ) $filtro[]= " $option $when '$data'";
			if($office!='') $filtro[]= " id_ufficio = $office";
			if(count($filtro)>0) $filtro = join(' and ', $filtro);
						else	 $filtro = '';
			if($filtro!='') $filtro = ' where ' . $filtro;
			$sort = $option . (($option!='')?' desc,':'');
			$sql = toSendItGenericMethods::applyPaginationLimit( "select * from $tableName $filtro order by $sort data_scadenza desc, data_pubblicazione desc");
			$baseUrl = toSendItGenericMethods::rebuildQueryString(array('pg'));
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl ); 
			#echo($sql);
			$results = $wpdb->get_results($sql);
			?>
			<table class="widefat post fixed">
				<thead>
					<tr>
						<th class="wide-10-text"><?php echo apply_filters('pafacile_bandi_etichetta_tipo' , 			'Tipo');  ?></th>
						<th class="wide-10-text"><?php echo apply_filters('pafacile_bandi_etichetta_estremi' , 			'Estremi');  ?></th>
						<th class="wide-text"><?php echo apply_filters('pafacile_bandi_etichetta_oggetto' , 			'Oggetto');  ?></th>
						<th class="wide-10-text"><?php echo apply_filters('pafacile_bandi_etichetta_pubblicato_il' , 	'Pubblicato il');  ?></th>
						<th class="wide-10-text"><?php echo apply_filters('pafacile_bandi_etichetta_scade_il' , 		'Scade il');  ?></th>
						<th class="wide-10-text"><?php echo apply_filters('pafacile_bandi_etichetta_aggiudicato_il' , 	'Aggiudicato il');  ?></th>
						<th class="wide-20-text"><?php echo apply_filters('pafacile_bandi_etichetta_ufficio' , 			'Ufficio');  ?></th>
					</tr>
				</thead>
				<tbody>
					<?php 
					
					foreach($results as $i => $row){
						$url = '?page='.TOSENDIT_PAFACILE_BANDI_EDIT_HANDLER.'&id='.$row->id;
						?>
						<tr>
							<td><?php echo(PAFacileDecodifiche::tipoBando( $row->tipo) ) ?></td>
							<td><?php echo($row->estremi); ?></td>
							<td class="wide-text">
								<a href="<?php echo $url?>"><?php echo($row->oggetto) ?></a>
								<?php 
								if(toSendItPAFacile::userIsIn($row->id_ufficio)){
									?>							
									<div class="row-actions">
										<span class="edit"><a href="?page=<?php echo(TOSENDIT_PAFACILE_BANDI_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">Modifica</a> |</span>
										<span class="delete"><a href="?page=<?php echo(TOSENDIT_PAFACILE_BANDI_DELETE_HANDLER)?>&id=<?php echo($row->id)?>">Elimina</a></span>
									</div>
									<?php 
								}
								?>
							</td>
							<td><?php echo(toSendItGenericMethods::formatDateTime( $row->data_pubblicazione) ) ?></td>
							<td><?php echo(toSendItGenericMethods::formatDateTime( $row->data_scadenza) ) ?></td>
							<td><?php echo(toSendItGenericMethods::formatDateTime( $row->data_esito) ) ?></td>
							<td><?php echo(PAFacileDecodifiche::officeNameById($row->id_ufficio)) ?></td>
						</tr>
						<?php 
					}
					?>
				</tbody>
			</table>
		</form>
	</div>
	<?php 	
	
}

if(is_admin()) displayBandi();
?>