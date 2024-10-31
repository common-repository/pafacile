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

/* =====================
 *  SINCE VERSION 1.5.6
   ===================== */

function displayTipiAttoPublic($params){
/* 
 * Questa informazione non sarà mai pubblica
 * */	
}

function displayTipiAtto(){
	global $wpdb, $current_user;
	toSendItGenericMethods::mergeSearchFilter('ricerca_determine');
	
	$type 	= $_GET['type'];
	$option = $_GET['option'];
	$when 	= $_GET['when'];
	$data 	= $_GET['data_yy'] . '-'.$_GET['data_mm'].'-'.$_GET['data_dd'];
	if($data=='--') $data = '';
	$office = $_GET['office'];
	?>
	<div class="wrap">
		<div id="icon-edit-pages" class="icon32">
			<br/>
		</div>
		<h2>Elenco delle tipologie di atto pubblicabili nell'albo on-line</h2>
		<form method="GET" id="post-filter" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<?php 
			$filtro = array();
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ATTO;
			$tableOrganigramma = $wpdb->prefix . TOSENDIT_PAFACILE_DB_USERS_TO_ORGANIGRAMMA;
			$sql = "select * from $tableName";

			$sql = toSendItGenericMethods::applyPaginationLimit( "select * from $tableName $filtro order by codice, descrizione");
			$baseUrl = toSendItGenericMethods::rebuildQueryString(array('pg'));
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl ); 
			$results = $wpdb->get_results($sql);
			?>
			<table class="widefat post fixed">
				<thead>
					<tr>
						<th class="wide-10-text">Codice</th>
						<th class="wide-text">Descrizione</th>
						<th class="wide-10-text">Raggruppamento</th>
						<th class="wide-10-text">Durata Pubblicazione (in <abbr title="giorni">gg</abbr>)</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					
					foreach($results as $i => $row){
						$url = '?page='.TOSENDIT_PAFACILE_BANDI_EDIT_HANDLER.'&id='.$row->id;
						?>
						
						<tr>
							<td><?php echo $row->codice ?></td>
							<td class="wide-text">
								<a href="<?php echo $url?>"><?php echo($row->descrizione) ?></a>
								<div class="row-actions">
									<span class="edit"><a href="?page=<?php echo(TOSENDIT_PAFACILE_TIPO_ATTO_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">Modifica</a> |</span>
									<span class="delete"><a href="?page=<?php echo(TOSENDIT_PAFACILE_TIPO_ATTO_DELETE_HANDLER)?>&id=<?php echo($row->id)?>">Elimina</a></span>
								</div>
							</td>
							<td><?php echo $row->raggruppamento ?></td>
							<td><?php echo $row->durata_pubblicazione ?></td>
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
if(is_admin()) displayTipiAtto();
?>