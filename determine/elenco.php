<?php
# require_once 'public-contents/Determine.php';
/*
 * Since Version 2.5.10
* Avoid XSS vulnerability discovered by Dejan Lukan many thanks!
*/
if (!empty($_SERVER['SCRIPT_FILENAME']) &&
		basename(__FILE__)             == basename($_SERVER['SCRIPT_FILENAME']) &&            // Same script file
		basename(dirname(__FILE__)) == basename(dirname($_SERVER['SCRIPT_FILENAME']))    // Same directory
)
	die ('Please do not load this page directly. Thanks to Dejan Lukan for the notification!');

function displayDeterminePublic($params, $extraParams = array()){
	$params = toSendItGenericMethods::identifyParameters($params,
		array(
			'kind'			=> 'box', 		// Pu� essere box (modulo di ricerca) o table (elenco delle delle determine)
			'id_office'		=> 0,
			'pa_dal_dd'		=> null,
			'pa_dal_mm'		=> null,
			'pa_dal_yy'		=> null,
			'pa_al_dd'		=> null,
			'pa_al_mm'		=> null,
			'pa_al_yy'		=> null,
			'limit'			=> null
		)
	);
	$params = array_merge ($params, $extraParams);
	switch($params['kind']){
		case 'box':
			return Determine::form($params);
			break;
		case 'list':
		case 'table':
			return Determine::elenco($params);
			break;
	}
}

function displayDetermine($idUfficio = ''){
	toSendItGenericMethods::mergeSearchFilter('ricerca_determine');
	$output = toSendItGenericMethods::identifyParameters( $idUfficio, 
		apply_filters('determine_list_search_parameters', array('idUfficio' => '', 'displaySearchBox' => true,'displayTable' => true,'url' => $_SERVER['REQUEST_URI'])) );
	extract($output);
	
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
		<h2><?php echo apply_filters('determine_list_title','Elenco delle determinazioni')?></h2>
		<form method="GET" id="post-filter" action="<?php echo $url ?>">
			<p class="search-box">
				<input type="hidden" name="page" value="<?php echo TOSENDIT_PAFACILE_DETERMINE_EDIT_HANDLER ?>" />
				<label class="screen-reader-text" for="post-search-input">Cerca una determina che abbia il seguente testo:</label>
				<input id="post-search-input" type="text" value="<?php echo($_GET['s'])?>" name="s"/>
				<input class="button" type="submit" value="Cerca determina"/>
			</p>
			<div class="tablenav">
				<?php do_action('determine_list_before_search_form'); ?>
				<h4 class="screen-reader-text">Cerca determina</h4>
				<label for="pa_when">Adottata:</label>
				<select name="when" id="pa_when">
					<option value="=" <?php echo($PAFacileWhen=='='?'selected="selected"':''); ?> >in data</option>
					<option value="&lt;" <?php echo($PAFacileWhen=='<'?'selected="selected"':'');?> >prima del</option>
					<option value="&gt;" <?php echo($PAFacileWhen=='>'?'selected="selected"':'');?> >dopo il</option>
				</select>
				<?php 
				toSendItGenericMethods::drawDateField('pa', $PAFacileYear.'-'.$PAFacileMonth.'-'.$PAFacileDay);
				?><br />
				<label for="pa_office">Ufficio/Area/Settore/Organo:</label>
				<?php
				toSendItPAFacile::buildOfficeSelector('office','pa_office','',$PAFacileOffice,true);
				do_action('determine_list_after_search_form');
				?>
				<input class="button" type="submit" class="button-secondary" onclick="if(document.getElementById('post-search-input')) document.getElementById('post-search-input').value='';" value="Esegui ricerca">
			</div>
			<?php 
			global $wpdb, $current_user;
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_DETERMINE;
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
			$filter = apply_filters('determine_list_search_filter', $filter);
			if(count($filter)>0) $filtro = 'where ' . join(' and ', $filter);
			$baseUrl = toSendItGenericMethods::rebuildQueryString(array('pg'));
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl );
			
			$sql = toSendItGenericMethods::applyPaginationLimit( "$sql $filtro order by data_adozione desc, numero desc");
			
			
			do_action('determine_list_before_table');
			?>
			<table class="widefat post fixed">
				<thead>
					<tr>
						<?php do_action('determine_list_before_header_columns'); ?>
						<th class="wide-10-text"><?php echo apply_filters('determine_list_number_header','Numero');?></th>
						<th class="wide-20-text"><?php echo apply_filters('determine_list_date_header','Data di adozione')?></th>
						<th class="wide-text"><?php echo apply_filters('determine_list_subject_header','Oggetto')?></th>
						<th class="wide-30-text"><?php echo apply_filters('determine_list_office_header','<abbr title="Ufficio">Uff.</abbr>/Area/<abbr title="Settore">Sett.</abbr>')?></th>
						<?php do_action('determine_list_after_header_columns'); ?>
					</tr>
				</thead>
				<tbody>
					<?php
					$rows = $wpdb->get_results( $sql );
					foreach($rows as $rowId => $row){
						?>
						<tr>
							<?php do_action('determine_list_before_data_columns', $row); ?>
							<td  class="wide-10-text">
								<?php
								echo(apply_filters('determine_list_number_value',$row->numero)); 
								?>
							</td>
							<td class="wide-20-text">
								<?php echo apply_filters('determine_list_date_value', toSendItGenericMethods::formatDateTime( $row->data_adozione) )?>
							</td>
							<td class="wide-text">
								<a href="?page=<?php echo(TOSENDIT_PAFACILE_DETERMINE_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">
									<?php echo apply_filters('determine_list_subject_header',$row->oggetto);?>
								</a>
								<?php 
								if(toSendItPAFacile::isUserAuthorizedFor($row->id_ufficio)){
									?>
									<div class="row-actions">
										<?php do_action('determine_list_before_actions', $row)?>
										<span class="edit"><a href="?page=<?php echo(TOSENDIT_PAFACILE_DETERMINE_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">Modifica</a> |</span>
										<span class="delete"><a href="?page=<?php echo(TOSENDIT_PAFACILE_DETERMINE_DELETE_HANDLER)?>&id=<?php echo($row->id)?>">Elimina</a></span>
										<?php do_action('determine_list_after_actions', $row)?>
									</div>
									<?php 
								}
								?>
							</td>
							<td class="wide-30-text">
								<?php echo apply_filters('determine_list_office_value',$row->ufficio);?>
							</td>
							<?php do_action('determine_list_after_data_columns', $row); ?>
						</tr>
						<?php 
					}
					?>
				</tbody>
			</table>
			<?php 
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl );
			?>
		</form>
	</div>
	<?php 
}
if(is_admin()) displayDetermine(); 
?>