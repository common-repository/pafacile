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

function displayTipiOrganiPublic($params){
/* 
 * Questa informazione non sarà mai pubblica
 * */	
}

function displayTipiOrgani(){
	global $wpdb, $current_user;
	?>
	<div class="wrap">
		<div id="icon-edit-pages" class="icon32">
			<br/>
		</div>
		<h2>Elenco delle tipologie di organi di governo</h2>
		<form method="GET" id="post-filter" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
			<?php 
			$filtro = array();
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ORGANO;
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
					</tr>
				</thead>
				<tbody>
					<?php 
					
					foreach($results as $i => $row){
						$url = '?page='.TOSENDIT_PAFACILE_TIPO_ORGANO_EDIT_HANDLER.'&id='.$row->id;
						?>
						
						<tr>
							<td><?php echo $row->codice ?></td>
							<td class="wide-text">
								<a href="<?php echo $url?>"><?php echo($row->descrizione) ?></a>
								<div class="row-actions">
									<span class="edit"><a href="<?php echo($url) ?>">Modifica</a> |</span>
									<span class="delete"><a href="<?php echo($url) ?>">Elimina</a></span>
								</div>
							</td>
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
if(is_admin()) displayTipiOrgani();
?>