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



function displayOrdinanze(){
	toSendItGenericMethods::mergeSearchFilter('ricerca_ordinanze');
	$admin = is_admin();
	$PAFacileDay 	= date('d');
	if($PAFacileDay>28) $PAFacileDay = 28;
	$PAFacileMonth 	= date('m')-1;
	$PAFacileYear 	= date('Y');
	$PAFacileWhen	= '>';
	if(isset($_GET['pa_dd']) && is_numeric($_GET['pa_dd'])) 	$PAFacileDay 	= $_GET['pa_dd'];
	if(isset($_GET['pa_mm']) && is_numeric($_GET['pa_mm'])) 	$PAFacileMonth 	= $_GET['pa_mm'];
	if(isset($_GET['pa_yy']) && is_numeric($_GET['pa_yy'])) 	$PAFacileYear 	= $_GET['pa_yy'];
	if(isset($_GET['when']))									$PAFacileWhen	= $_GET['when'];
	if(isset($_GET['office']))									$PAFacileOffice = $_GET['office'];
	
	?>
	<div class="wrap">
		<div id="icon-edit-pages" class="icon32">
		<br/>
		</div>
		<h2>Elenco delle ordinanze</h2>
		<form method="GET" id="post-filter" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<p class="search-box">
				<input type="hidden" name="page" value="<?php echo TOSENDIT_PAFACILE_ORDINANZE_EDIT_HANDLER ?>" />
				<label class="screen-reader-text" for="post-search-input">Cerca un'ordinanza che abbia il seguente testo:</label>
				<input id="post-search-input" type="text" value="<?php echo($_GET['s'])?>" name="s"/>
				<input class="button" type="submit" value="Cerca ordinanza"/>
			</p>
			<div class="tablenav">
				<label for="pa_when">Adottata: </label>
				<select name="when" id="pa_when">
					<option value="=" <?php echo($PAFacileWhen=='='?'selected="selected"':''); ?> >in data</option>
					<option value="&lt;" <?php echo($PAFacileWhen=='<'?'selected="selected"':'');?> >prima del</option>
					<option value="&gt;" <?php echo($PAFacileWhen=='>'?'selected="selected"':'');?> >dopo il</option>
				</select>
				<?php 
				toSendItGenericMethods::drawDateField('pa', $PAFacileYear.'-'.$PAFacileMonth.'-'.$PAFacileDay);
				?><br />
				<label for="pa_office">Emanante:</label>
				<?php
				toSendItPAFacile::buildOfficeSelector('office','pa_office','',$PAFacileOffice,true);
				?>
				<input type="submit" class="button-secondary" onclick="document.getElementById('post-search-input').value='';" value="Esegui ricerca">
			</div>
			<?php 
			global $wpdb, $current_user;
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORDINANZE;
			$organigrammaTableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
			$filter = array();
			if(isset($_GET['s']) && $_GET['s']!=''){
				$filter[] = "oggetto like '%{$_GET['s']}%'";
			}else{
				$mysqlData = "'$PAFacileYear-$PAFacileMonth-$PAFacileDay'";
				
				if(!preg_match('/\d{4}\-\d{2}\-\d{2}/',$mysqlData)){
					$mysqlData = "'0000-00-00'";
					$PAFacileWhen = '>'; 
				}
				$mysqlQuando = $PAFacileWhen;
				$filter[] = "data_adozione $mysqlQuando $mysqlData ";
				if($PAFacileOffice!='') $filter[] = "id_ufficio='$PAFacileOffice'";
			}
			$sql = "SELECT d.*, o.nome as ufficio FROM $tableName d left join $organigrammaTableName o on d.id_ufficio = o.id";
			if(count($filter)>0) $filtro = 'where ' . join(' and ', $filter);
			
			$sql = toSendItGenericMethods::applyPaginationLimit( "$sql $filtro order by data_adozione desc, numero desc");
			
			$baseUrl = toSendItGenericMethods::rebuildQueryString(array('pg'));
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl );
			?>
			<table class="widefat post fixed">
				<thead>
					<tr>
						<th class="wide-10-text">Numero</th>
						<th class="wide-text">Oggetto</th>
						<th class="wide-20-text">Data di adozione</th>
						<th class="wide-30-text">Emanante</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$rows = $wpdb->get_results( $sql );
					foreach($rows as $rowId => $row){
						?>
						<tr>
							<td <?php if(!$admin) echo('rowspan="2"');?> >
								<?php
								echo($row->numero); 
								?>
							</td>
							<td class="wide-text">
								<a href="?page=<?php echo(TOSENDIT_PAFACILE_ORDINANZE_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">
									<?php echo($row->oggetto);?>
								</a>
								<?php 
								if(toSendItPAFacile::isUserAuthorizedFor($row->id_ufficio)){
									?>
									<div class="row-actions">
										<span class="edit"><a href="?page=<?php echo(TOSENDIT_PAFACILE_ORDINANZE_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">Modifica</a> |</span>
										<span class="delete"><a href="?page=<?php echo(TOSENDIT_PAFACILE_ORDINANZE_DELETE_HANDLER)?>&id=<?php echo($row->id)?>">Elimina</a></span>
									</div>
									<?php
								} 
								?>
							</td>
							<td>
								<?php echo(toSendItGenericMethods::formatDateTime( $row->data_adozione) )?>
							</td>
							<td>
								<?php echo($row->ufficio);?>
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

function displayOrdinanzePublic($params, $extraParams = array()){
	/*
	 * Utilizzato dai widget o sulla pagina
	 */
	$params = toSendItGenericMethods::identifyParameters($params,
		array(
			'kind'		=> 'table', 		// Pu� essere table (elenco delle ordinanze) oppure box (il box di ricerca)
			'numero' 	=> '',
			'id_office'	=> '',
			'pa_dal_dd'	=> '',
			'pa_dal_mm'	=> '',
			'pa_dal_yy'	=> '',
			'pa_al_dd'	=> '',
			'pa_al_mm'	=> '',
			'pa_al_yy'	=> ''
		)
	);
	$params = array_merge ($params, $extraParams);
	switch($params['kind']){
		case 'box':
			toSendItPAFacileContents::mostraOrdinanzeForm($params); 
			break;
		case 'table':
		default:
			toSendItPAFacileContents::mostraOrdinanzeElenco($params);
			break;
	}
}

if(is_admin()) displayOrdinanze();
?>