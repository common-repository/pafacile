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

function displayDeliberePublic($params, $extraParams = array()){
	global $wpdb;
	
	$params = toSendItGenericMethods::identifyParameters($params,
		array(
			'kind'					=> 'box', 		// Può essere box (modulo di ricerca) o table (elenco delle delibere)
			'PAFacileType' 	=> null,
			'PAFacileWhen'	=> null,
			'PAFacileDay'	=> null,
			'PAFacileMonth' => null,
			'PAFacileYear'	=> null
		)
	);
	$params = array_merge ($params, $extraParams);
	extract($params);
	
	switch($kind){
		case 'box':
			return Delibere::form($params);	
			break;
		case 'list':
		case 'table':
			return Delibere::elenco($params);
			break;			
			
	}
	
}

function displayDelibere($limite = '0'){
	toSendItGenericMethods::mergeSearchFilter('ricerca_delibere');
	$output = toSendItGenericMethods::identifyParameters( $limite , array('limite' => '0') );
	extract($output);
	
	$admin = is_admin();
	$PAFacileDay 	= date('d');
	if($PAFacileDay>28) $PAFacileDay = 28;
	$PAFacileMonth 	= date('m')-1;
	$PAFacileYear 	= date('Y');
	$PAFacileType	= '';
	$PAFacileWhen	= '>';
	if(isset($_GET['pa_dd']) && is_numeric($_GET['pa_dd'])) 	$PAFacileDay 	= $_GET['pa_dd'];
	if(isset($_GET['pa_mm']) && is_numeric($_GET['pa_mm'])) 	$PAFacileMonth 	= $_GET['pa_mm'];
	if(isset($_GET['pa_yy']) && is_numeric($_GET['pa_yy'])) 	$PAFacileYear 	= $_GET['pa_yy'];
	if(isset($_GET['type']))									$PAFacileType	= $_GET['type'];
	if(isset($_GET['when']))									$PAFacileWhen	= $_GET['when'];
	?>
	<div class="wrap">
		<?php 
		if($admin){
			?>
			<div id="icon-edit-pages" class="icon32">
			<br/>
			</div>
			<h2>Elenco delle delibere</h2>
			<form method="GET" id="post-filter" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<input type="hidden" name="page" value="<?php echo TOSENDIT_PAFACILE_DELIBERE_EDIT_HANDLER ?>" />
				
				<p class="search-box">
					<label class="screen-reader-text" for="post-search-input">Cerca una delibera che abbia il seguente testo:</label>
					<input id="post-search-input" type="text" value="<?php echo( htmlspecialchars( $_GET['s'] ) ); ?>" name="s"/>
					<input class="button" type="submit" value="Cerca delibera"/>
				</p>
				<div class="tablenav">
					<label for="pa_type">Mostra le delibere di:</label>
					<select name="type" id="pa_type">
						<option value="" <?php echo($PAFacileType==''?'selected="selected"':''); ?> >Qualsiasi tipo</option>
						<option value="g" <?php echo($PAFacileType=='g'?'selected="selected"':''); ?> >Giunta</option>
						<option value="c" <?php echo($PAFacileType=='c'?'selected="selected"':''); ?> >Consiglio</option>
					</select>
					<label for="pa_when">Deliberate: </label>
					<select name="when" id="pa_when">
						<option value="=" <?php echo($PAFacileWhen=='='?'selected="selected"':''); ?> >in data</option>
						<option value="&lt;" <?php echo($PAFacileWhen=='<'?'selected="selected"':'');?> >prima del</option>
						<option value="&gt;" <?php echo($PAFacileWhen=='>'?'selected="selected"':'');?> >dopo il</option>
					</select>
					<?php 
					toSendItGenericMethods::drawDateField('pa', $PAFacileYear.'-'.$PAFacileMonth.'-'.$PAFacileDay);
					?>
					<input type="submit" class="button-secondary" onclick="document.getElementById('post-search-input').value='';" value="Esegui ricerca">
				</div>
				<?php 
			}
			?>
		</form>
		<?php 
		global $wpdb;
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_DELIBERE;
		if(isset($_GET['s']) && $_GET['s']!=''){
			# -- Changed in Ver 1.4:
			# Ricerca delbiera per numero 
			# $filtro = "where oggetto like '%{$_GET['s']}%'";
			
			# Ver 2.6.1 - DB Security Violation
			$s = esc_sql( $_GET['s'] );
			$filtro = "where oggetto like '%$s%' or numero='$s'";
		}else{
			$mysqlData = "'$PAFacileYear-$PAFacileMonth-$PAFacileDay'";
			if($mysqlData=="'--'"){
				$mysqlData = "'0000-00-00'";
				$PAFacileWhen = '>'; 
			}
			$mysqlTipo = (($PAFacileType!='')?"tipo='$PAFacileType' and ":'');
			$mysqlQuando = $PAFacileWhen;
			
			$mysqlFiltroData = "data_seduta $mysqlQuando $mysqlData ";
			$filtro = "where $mysqlTipo ( $mysqlFiltroData )";
		}
		
		$sql = toSendItGenericMethods::applyPaginationLimit( "select * from $tableName $filtro order by data_seduta desc, numero desc");
		$baseUrl = toSendItGenericMethods::rebuildQueryString(array('pg'));
		toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl ); 
		
		?>
		<table <?php if($admin) echo(' class="widefat post fixed" ')?> >
			<thead>
				<tr>
					<th class="wide-20-text">Numero</th>
					<th class="wide-20-text">Tipo</th>
					<th class="wide-60-text">Oggetto</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if($limite!='0') $sql .= ' limit ' . $limite;
				$rows = $wpdb->get_results( $sql );
				#print_r($sql);
				#print_r($rows);
				foreach($rows as $rowId => $row){
					if($admin){
						$url = '?page='.TOSENDIT_PAFACILE_DELIBERE_EDIT_HANDLER.'&id='.$row->id;
					}else{
						$url = $_SERVER['REQUEST_URI'];
						$url .= ((strpos($url,'?')!==false)?'&':'?').'ref=' . $row->id;
					}
					?>
					<tr>
						<td class="wide-20-text" <?php if(!$admin) echo('rowspan="2"');?> >
							<?php
							echo($row->numero); 
							?>
						</td>
						<td class="wide-text">
							<?php echo(PAFacileDecodifiche::tipoDocumento($row->tipo))?>
							
						</td>
						<td class="wide-text">
							<?php 
							if($admin){
								?>
								<a href="<?php echo($url);?>">
									<?php echo($row->oggetto)?>
								</a>
								
								<dl>
									<dt>Data deliberazione</dt>
									<dd><?php echo(toSendItGenericMethods::formatDateTime( $row->data_seduta) )?></dd>
									<?php 
									if($row->data_albo!=null && $row->data_albo!='' && $row->data_albo != '0000-00-00'){
										?>
										<dt>Data di pubblicazione all'albo</dt>
										<dd><?php echo(toSendItGenericMethods::formatDateTime( $row->data_albo) )?></dd>
										<?php 
									}
									?>
								</dl>
								<div class="row-actions">
									<span class="edit"><a href="?page=<?php echo(TOSENDIT_PAFACILE_DELIBERE_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">Modifica</a> |</span>
									<span class="delete"><a href="?page=<?php echo(TOSENDIT_PAFACILE_DELIBERE_DELETE_HANDLER)?>&id=<?php echo($row->id)?>">Elimina</a></span>
								</div>
								<?php 
							}else{
								echo($row->oggetto);
								?>
								<dl>
									<dt>Data deliberazione</dt>
									<dd><?php echo(nl2br($row->data_seduta))?></dd>
									<dt>Data di pubblicazione all'albo</dt>
									<dd><?php echo($row->data_albo)?></dd>
								</dl>
								<?php 
							}
							?>
						</td>
					</tr>
					<?php
					if(!$admin){
						echo('<tr><td colspan="2">');
						toSendItGenericMethods::displayFileUploadBox($tableName, $row->id);
						echo('</td></tr>');
					}
						
				}
				?>
			</tbody>
		</table>
	 	<?php 
	 	toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl ); 
		?>
	</div>
	<?php 
}
if(is_admin()) displayDelibere();
?>