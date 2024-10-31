<?php 
function displayIncarichi(){
	toSendItGenericMethods::mergeSearchFilter('ricerca_ordinanze');
	$dal_dd 	= date('d');
	$dal_mm 	= date('m');
	$dal_yy 	= date('Y')-1;

	if($dal_dd==29 && $dal_mm==2) $dal_dd = 28;
	
	$al_dd		= date('d');
	$al_mm 	= date('m');
	$al_yy 	= date('Y');
	
	if(isset($_GET['dal_dd']) && is_numeric($_GET['dal_dd'])) 	$dal_dd 	= $_GET['dal_dd'];
	if(isset($_GET['dal_mm']) && is_numeric($_GET['dal_mm'])) 	$dal_mm 	= $_GET['dal_dd'];
	if(isset($_GET['dal_yy']) && is_numeric($_GET['dal_yy'])) 	$dal_yy 	= $_GET['dal_yy'];
	
	if(isset($_GET['al_dd']) && is_numeric($_GET['al_dd'])) 	$al_dd 	= $_GET['al_dd'];
	if(isset($_GET['al_mm']) && is_numeric($_GET['al_mm'])) 	$al_mm 	= $_GET['al_dd'];
	if(isset($_GET['al_yy']) && is_numeric($_GET['al_yy'])) 	$al_yy 	= $_GET['al_yy'];

	if($dal_dd<1 || $dal_dd>31) $dal_dd = '01';
	if($dal_mm<1 || $dal_mm>12) $dal_mm = '01';
	
	if($al_dd<1 || $al_dd>31) $dal_dd = '31';
	if($al_mm<1 || $al_mm>12) $dal_mm = '12';
	
	$dal = $dal_yy . '-'.$dal_mm .'-'. $dal_dd;
	$al = $al_yy . '-'.$al_mm .'-'. $al_dd;
	
	?>
	<div class="wrap">
		<div id="icon-edit-pages" class="icon32">
		<br/>
		</div>
		<h2>Elenco degli incarichi professionali</h2>
		<form method="GET" id="post-filter" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<p class="search-box">
				<input type="hidden" name="page" value="<?php echo TOSENDIT_PAFACILE_INCARICHI_PROF_EDIT_HANDLER?>" />
				<label class="screen-reader-text" for="post-search-input">Cerca un incarico per nominativo:</label>
				<input id="post-search-input" type="text" value="<?php echo(isset($_GET['s'])?$_GET['s']:'') ?>" name="s"/>
				<input class="button" type="submit" value="Cerca incarico"/>
			</p>
			<div class="tablenav">
				<label for="dal_dd">Dal:</label>
				<?php 
				toSendItGenericMethods::drawDateField('dal', $dal);
				?>
				<label for="al_dd">Al:</label>
				<?php 
				toSendItGenericMethods::drawDateField('al', $al);
				?>
				<input type="submit" class="button-secondary" onclick="document.getElementById('post-search-input').value='';" value="Esegui ricerca">
			</div>
			<?php 
			global $wpdb, $current_user;
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_INCARICHI;
			$filter = array();
			if(isset($_GET['s']) && $_GET['s']!=''){
				$filter[] = "nominativo like '%{$_GET['s']}%'";
			}else{
				$filter[] = "dal >= '$dal'";
				$filter[] = "al <= '$al'";
			}
			$sql = "SELECT * FROM $tableName ";
			if(count($filter)>0) $filtro = 'where ' . join(' or ', $filter);
			
			$sql = toSendItGenericMethods::applyPaginationLimit( "$sql $filtro order by dal desc, nominativo desc, al asc");
			
			$baseUrl = toSendItGenericMethods::rebuildQueryString(array('pg'));
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl );
			?>
			<table class="widefat post fixed">
				<thead>
					<tr>
						<th class="wide-text">Oggetto</th>
						<th class="wide-10-text">Nominativo</th>
						<th class="wide-20-text">Dal</th>
						<th class="wide-20-text">Al</th>
						<th class="wide-20-text">Compenso</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$rows = $wpdb->get_results( $sql );
					foreach($rows as $rowId => $row){
						?>
						<tr>
							<td class="wide-text">
								<?php echo($row->oggetto_incarico);?>
								<div class="row-actions">
									<span class="edit"><a href="?page=<?php echo(TOSENDIT_PAFACILE_INCARICHI_PROF_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">Modifica</a> |</span>
									<span class="delete"><a href="?page=<?php echo(TOSENDIT_PAFACILE_INCARICHI_PROF_DELETE_HANDLER)?>&id=<?php echo($row->id)?>">Elimina</a></span>
								</div>
								
							</td>
							<td>
							<?php echo($row->nominativo);?>
							</td>
							<td>
								<?php echo(toSendItGenericMethods::formatDateTime( $row->dal) )?>
							</td>
							<td>
								<?php echo(toSendItGenericMethods::formatDateTime( $row->al) )?>
							</td>
							<td>
								<?php echo($row->compenso);?>
							</td>
						</tr>
						<?php
					}
					?>
				</tbody>
			</table>
		</form>
		<?php 
		toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl );
		?>
	</div>
	<?php 
	
}

function displayIncarichiProfessionaliPublic($params, $extraParams = array()){
	/*
	 * Utilizzato dai widget o sulla pagina
	 */
	$params = toSendItGenericMethods::identifyParameters($params,
		array(
			'kind'		=> 'table', 		// Pu� essere table (elenco delle ordinanze) oppure box (il box di ricerca)
			'dal_dd'	=> '',
			'dal_mm'	=> '',
			'dal_yy'	=> '',
			'al_dd'	=> '',
			'al_mm'	=> '',
			'al_yy'	=> ''
		)
	);
	$params = array_merge ($params, $extraParams);
	switch($params['kind']){
		case 'box':
			toSendItPAFacileContents::mostraIncarichiForm($params); 
			break;
		case 'table':
		default:
			toSendItPAFacileContents::mostraIncarichiElenco($params);
			break;
	}
}

if(is_admin()) displayIncarichi();
?>