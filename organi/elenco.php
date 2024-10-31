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


function displayOrgani(){
	toSendItGenericMethods::mergeSearchFilter('ricerca_organi');
	
	$PAFacileDay 	= date('d');
	$PAFacileMonth 	= date('m');
	$PAFacileYear 	= date('Y');
	$PAFacileType	= '';
	
	if(isset($_GET['day']) && is_numeric($_GET['day'])) 	$PAFacileDay 	= $_GET['day'];
	if(isset($_GET['month']) && is_numeric($_GET['month'])) $PAFacileMonth 	= $_GET['month'];
	if(isset($_GET['year']) && is_numeric($_GET['year'])) 	$PAFacileYear 	= $_GET['year'];
	if(isset($_GET['type']))								$PAFacileType	= $_GET['type'];
	
	?>
	<div class="wrap">
		<div id="icon-users" class="icon32">
			<br/>
		</div>
		<h2>Organi di governo</h2>
		<!-- <p>a
			Per verificare lo stato della giunta o del consiglo ad una 
			specifica data, compilare il modulo seguente:
		</p>
		-->
		<form method="GET" id="post-filter" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<p class="search-box">
				<input type="hidden" name="page" value="<?php echo TOSENDIT_PAFACILE_ORGANI_EDIT_HANDLER ?>" />
				<label class="screen-reader-text" for="post-search-input">Cerca nominativo:</label>
				<input id="post-search-input" type="text" value="<?php echo($_GET['s'])?>" name="s"/>
				<input class="button" type="submit" value="Cerca nominativo"/>
			</p>
			<div class="tablenav">
				<label for="pa_dd">Mostra la situazione alla data:</label>
				<?php 
				toSendItGenericMethods::drawDateField('pa', $PAFacileYear.'-'.$PAFacileMonth.'-'.$PAFacileDay);
				?>
				<label for="pa_type">Mostra solo:</label>
				<?php 
				global $wpdb;
				$tableTipiOrgano = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ORGANO;
				$sql = "select * from $tableTipiOrgano order by descrizione";
				$tipiOrgano = $wpdb->get_results($sql);
				?>
				<select name="type" id="pa_type">
					<option value="" <?php echo($PAFacileType==''?'selected="selected"':''); ?> >Mostra tutto</option>
					<?php
					foreach($tipiOrgano as $tipoOrgano){
						?>
						<option value="<?php echo $tipoOrgano->codice ?>" <?php echo(($PAFacileType==$tipoOrgano->codice)?'selected="selected"':''); ?> >
							<?php echo $tipoOrgano->descrizione ?>
						</option>
						<?php
					}
					?>
				</select>
				
				<input type="submit" class="button-secondary" onclick="document.getElementById('post-search-input').value='';" value="Esegui ricerca">
			</div>
		</form>
		<?php 
		/*
		 * Interrogo il DB di WP
		 */
		global $wpdb;
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANI;
		$tableNameRel = $tableName.'_rel';
		
		$filter = array();
		if(isset($_GET['s']) && $_GET['s']!=''){
			$filter[] = "nominativo like '%{$_POST['s']}%';";
			
		}else{
			
			$mysqlData = "'$PAFacileYear-$PAFacileMonth-$PAFacileDay'";
			$mysqlFiltroData = "($mysqlData between in_carica_dal and in_carica_al ) or ";
			$mysqlFiltroData .= "((in_carica_al is null or in_carica_al = '0000-00-00') and in_carica_dal<=$mysqlData) or";
			$mysqlFiltroData .= "((in_carica_dal is null or in_carica_dal = '0000-00-00') and in_carica_al>=$mysqlData)";
			
			$filter[] = "($mysqlFiltroData)";
		}
		($PAFacileType!='') && $filter[] = "(tipo='$PAFacileType' or id in (select id_organo from $tableNameRel where tipo='$PAFacileType'))";
		
		$sql = "SELECT * FROM $tableName";
		if(count($filter)>0) $filtro = 'where ' . join(' and ',$filter);
		$sql = toSendItGenericMethods::applyPaginationLimit( "$sql $filtro order by tipo, nominativo");
		$baseUrl = toSendItGenericMethods::rebuildQueryString(array('pg'));
		toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl );
		$rows = $wpdb->get_results( $sql );
		?>
		<table class="widefat post fixed">
			<thead>
				<tr>
					<th>Tipo</th>
					<th>Nominativo</th>
					<th>Deleghe</th>
					<th>In carica dal</th>
					<th>In carica fino al</th>
				</tr>
			</thead>
			<?php
			foreach($rows as $rowId => $row){
				?>
				<tr>
					<td>
						<?php 
						echo(PAFacileDecodifiche::tipoOrgano($row->tipo));
						$output = PAFacileDecodifiche::elencoTipiOrgano($row->id, true);
						if($output!=''){
							echo("<br />(<em>$output</em>)");
						}
						?>
					</td>
					<td>
						<a href="?page=<?php echo(TOSENDIT_PAFACILE_ORGANI_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">
							<?php echo($row->nominativo)?>
						</a>
						<div class="row-actions">
							<span class="edit"><a href="?page=<?php echo(TOSENDIT_PAFACILE_ORGANI_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">Modifica</a> |</span>
							<span class="delete"><a href="?page=<?php echo(TOSENDIT_PAFACILE_ORGANI_DELETE_HANDLER)?>&id=<?php echo($row->id)?>">Elimina</a></span>
							<!-- <span class="view"><a href="#">Visualizza</a></span> -->
						</div>
						
					</td>
					<td>
						<?php echo(nl2br($row->deleghe))?>
					</td>
					<td>
						<?php echo(toSendItGenericMethods::formatDateTime( $row->in_carica_dal) )?>
					</td>
					<td>
						<?php echo(toSendItGenericMethods::formatDateTime( $row->in_carica_al) )?>
					</td>
				</tr>
				<?php 
			}
			?>
		</table>
		<?php 
		toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl );
		?>
	</div>
	<?php 
	}

	function displayOrganiPublic($params = null){
		
		global $wpdb;
		
		$params = toSendItGenericMethods::identifyParameters($params,
			array(
				'kind'			=> 'table', 		// Pu� essere table (elenco dei bandi) oppure box (il form di ricerca)
			)
		);
		$params = array_merge ($params, $extraParams);
		extract($params);
		switch($kind){
			case 'box':
				toSendItPAFacileContents::mostraOrganiForm($params);
				break;
			case 'table':
			default:
				toSendItPAFacileContents::mostraOrganiElenco($params);
				break;
				
				
		}		
	}
	
	if(is_admin()) displayOrgani();
	
?>