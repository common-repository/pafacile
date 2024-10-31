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

function displayAlboPretorioPublic($params, $extraParams = array()){
	global $wpdb;
	$params = toSendItGenericMethods::identifyParameters($params,
		array(
			'kind'			=> 'table', 		// Può essere table (elenco dei bandi) oppure box (il form di ricerca)
			'tipo'			=> '',				// Può essere uno dei tipi disponibili
			'dpd'			=> '0000-00-00',	// Data pubblicazione all'albo dal
			'dpa'			=> '0000-00-00'		// Data pubblicazione all'albo al
		)
	);
	$params = array_merge ($params, $extraParams);
	
	extract($params);
	switch($params['kind']){
		case 'box':
			
			AlboPretorio::form($params);
			break;
		case 'table':
		default:
			AlboPretorio::elenco($params);
			break;
	}
}

function displayAlboPretorio(){
	global $wpdb, $current_user;
	if(!isset($_GET['status'])) $__status = TOSENDIT_PAFACILE_ATTO_PUBBLICATO.','.TOSENDIT_PAFACILE_ATTO_PROROGATO;
	if(isset($_GET['status'])) $__status = $_GET['status'];
	if(isseT($_POST['status']))$__status = $_POST['status']; 
	
	toSendItGenericMethods::mergeSearchFilter('ricerca_albo');
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
			
	$type 	= isset($_GET['type'])?$_GET['type']:'';
	$numero_registro = isset($_GET['numero_registro'])?$_GET['numero_registro']:'';
	$option = isset($_GET['option'])?$_GET['option']:'';
	$data 	= (isset($_GET['data_dd']) && isset($_GET['data_mm']) && isset($_GET['data_yy']))?toMySQLDate($_GET['data_dd'], $_GET['data_mm'], $_GET['data_yy'], true):'0000-00-00';
	$office = isset($_GET['office'])?$_GET['office']:'';
	$sql = "select status, count(status) cnt from $tableName where status <> 0 group by status";
	$status = $wpdb->get_results($sql);
	$numeroBozze = 0;
	$numeroPendenti = 0;
	$numeroPubbliche = 0;
	$numeroPubblicate = 0;
	$numeroAnnullate = 0; 
	foreach($status as $value){
		switch($value->status){
			case TOSENDIT_PAFACILE_ATTO_PUBBLICATO:
			case TOSENDIT_PAFACILE_ATTO_PROROGATO:
				$numeroPubblicate+=$value->cnt;
				break;
			case TOSENDIT_PAFACILE_ATTO_ANNULLATO:
				$numeroAnnullate+=$value->cnt;
				break;
			case TOSENDIT_PAFACILE_ATTO_PREPARATO:
				$numeroPendenti+=$value->cnt;
				break;
		}
	}
	$sql = "select count(*) as cnt from $tableName where status =0 and owner = {$current_user->ID} ";
	$status = $wpdb->get_row($sql);
	$numeroBozze = $status->cnt;
	if(!is_numeric( $numeroBozze) ) $numeroBozze = 0;
	
	$sql = "select count(*) as cnt from $tableName where status in (1,2,3) and (now() <= pubblicata_al or data_proroga is not null and now() <=data_proroga)";
	$status = $wpdb->get_row($sql);
	$numeroPubbliche = $status->cnt;
	$numeroPubblicate -= $numeroPubbliche;
	?>
	<div id="elenco-albo-pretorio" class="wrap">
		<div id="icon-edit-pages" class="icon32">
			<br/>
		</div>
		<h2>Affissioni all'albo on-line</h2>
		<form method="GET" id="post-filter" action="<?php echo $_SERVER['REQUEST_URI']?>">
			<ul class="subsubsub">
				<li>
					<?php 
					if($numeroBozze>0){
						?>
						<a <?php echo ($__status==TOSENDIT_PAFACILE_ATTO_BOZZA)?'class="current"':''?> href="?page=<?php echo TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER.'&status='.TOSENDIT_PAFACILE_ATTO_BOZZA ?>">Bozza (<?php echo $numeroBozze ?>)</a>
						<?php 
					}else{
						?>
						<em>Bozza (0)</em>
						<?php 
					}
					?>
					|
				</li>
				<li>
					<?php 
					if($numeroPendenti>0){
						?>
						<a <?php echo ($__status==TOSENDIT_PAFACILE_ATTO_PREPARATO)?'class="current"':''?> href="?page=<?php echo TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER.'&status='.TOSENDIT_PAFACILE_ATTO_PREPARATO ?>">Pendenti (<?php echo $numeroPendenti ?>)</a>
						<?php 
					}else{
						?>
						<em>Pendenti (0)</em>
						<?php
					}
					?>
					|
				</li>
				<li>
					<a 
						<?php echo ($__status==TOSENDIT_PAFACILE_ATTO_PUBBLICATO.','.TOSENDIT_PAFACILE_ATTO_PROROGATO)?'class="current"':''?> href="?page=<?php echo TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER.'&status='.TOSENDIT_PAFACILE_ATTO_PUBBLICATO.','.TOSENDIT_PAFACILE_ATTO_PROROGATO ?>" >Pubbliche/Prorogate (<?php echo $numeroPubbliche ?>)</a>
					|
				</li>
				<li>
					<a <?php echo ($__status==TOSENDIT_PAFACILE_ATTO_ANNULLATO)?'class="current"':''?>  href="?page=<?php echo TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER.'&status='.TOSENDIT_PAFACILE_ATTO_ANNULLATO ?>">Annullate (<?php echo $numeroAnnullate ?>)</a>
					|
				</li>
				<li>
					<a <?php echo ($__status=='1')?'class="current"':''?> href="?page=<?php echo TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER.'&status=1' ?>">Pubblicate (<?php echo $numeroPubblicate ?>)</a>
				</li>
			</ul>
			<div class="tablenav" style="height: auto;">
				<input type="hidden" name="page" value="<?php echo TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER?>" />
				<input type="hidden" name="status" value="<?php echo $__status ?>" />
				<label class="screen-reader-text" for="pa_type">Tipologia di documento:</label>
				<select name="type" id="pa_type">
					<option value="">Qualsiasi</option>
					<?php 
					// Since ver 1.5
					$tblTipiAtto = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ATTO;
					$sql ="select codice,descrizione,raggruppamento from $tblTipiAtto order by raggruppamento, descrizione";
					$results = $wpdb->get_results($sql);
					$raggruppamento = '';
					foreach($results as $result){
						if($raggruppamento!=$result->raggruppamento){
							if($raggruppamento!='') echo('</optgroup>');
							$raggruppamento = $result->raggruppamento;
							echo("<optgroup label=\"$raggruppamento\">");
						} 
						?>
						<option value="<?php echo $result->codice ?>"
							<?php echo($type==$result->codice?'selected="selected"':'');?>
							><?php echo($result->descrizione) ?></option>
						<?php
					}
					if($raggruppamento!='') echo('</optgroup>');
					?>
				</select>
				<label class="screen-reader-text" for="pa_option">Criterio:</label>
				<select name="option" id="pa_option">
					<option value="dq" <?php echo($option=='dq'?'selected="selected"':'');?>>Qualsiasi data</option>
					<option value="da" <?php echo($option=='da'?'selected="selected"':'');?>>Data Atto</option>
					<option value="dp" <?php echo($option=='dp'?'selected="selected"':'');?>>Data Protocollo</option>
					<option value="df" <?php echo($option=='df'?'selected="selected"':'');?>>Data Fascicolazione</option>
					<option value="dr" <?php echo($option=='dr'?'selected="selected"':'');?>>Data Rep. Gen.</option>
					<option value="dpub" <?php echo($option=='dpub'?'selected="selected"':'');?> >Data pubblicazione</option>
				</select>
				<label for="data_dd">in:</label>
				<?php
				toSendItGenericMethods::drawDateField('data', $data);
				?>
				<label for="numero-atto">Numero registro:</label>
				<input type="text" name="numero_registro" maxlength="5" size="5" id="numero-atto" value="<?php echo $numero_registro ?>" />
				<br />
				<label for="pa_office">Ufficio:</label>
				<?php
				toSendItPAFacile::buildOfficeSelector('office','pa_office','',$office,true);
				?>
				<input type="submit" class="button-secondary" onclick="document.getElementById('post-search-input').value='';" value="Esegui ricerca">
				<span class="cboth" >&nbsp;</span>
			</div>
			<?php 
			$filter = array();
			if($type!='') $filter[]="tipo ='$type'";
			if($numero_registro!='') $filter[]="numero_registro='$numero_registro'";
			if($data!='0000-00-00'){
				$break = true;
				$filtro = array();
				switch($option){
					case 'dq':
						$break = false;
					case 'dpub':
						$filtro[] = "('$data' between pubblicata_dal and pubblicata_al) or data_proroga is not null and ('$data' between pubblicata_dal and data_proroga) ";
						if($break) break;
					case 'dp':
						$filtro[] = "('$data' = protocollo_data)";
						if($break) break;
					case 'dr':
						$filtro[] = "('$data' = repertorio_data)";
						if($break) break;
					case 'df':
						$filtro[] = "('$data' = fascicolo_data)";
						if($break) break;
					case 'da':
						$filtro[] = "('$data' = atto_data)";
						if($break) break;
				}
				$filtro = join($filtro, ' or ');
				$filter[] = "($filtro)";
				
			}
			$status = $__status;
			switch($__status){
				case TOSENDIT_PAFACILE_ATTO_BOZZA:		
					$filter[] = "`status`=0 and owner = {$current_user->ID}";
					break;
				case TOSENDIT_PAFACILE_ATTO_PREPARATO:	
				case TOSENDIT_PAFACILE_ATTO_ANNULLATO:	
					 $filter[] = "`status`='$__status'";
					 break;
				case TOSENDIT_PAFACILE_ATTO_PUBBLICATO:		// Pubblicate
					// Prorogate (filtro implicito = 2)
					$filter[] = "(`status` in (1,2) and ((data_proroga is null and now()>pubblicata_al) or (data_proroga is not null and now()>data_proroga)))";
					break;
				case TOSENDIT_PAFACILE_ATTO_PUBBLICATO.','.TOSENDIT_PAFACILE_ATTO_PROROGATO:		// Pubblicate
					$filter[] = "(`status` in($__status) and now()<=pubblicata_al or data_proroga is not null and now()<=data_proroga)";
					break;
			}
			if($office!='') $filter[] = " and id_ufficio = $office";
			
			$filtro = join($filter, ' and ');
			if($filtro!='') $filtro = 'where ' . $filtro;
			$sql = toSendItGenericMethods::applyPaginationLimit( "select * from $tableName $filtro order by status, pubblicata_dal, numero_registro");
			#echo($sql);
			$baseUrl = toSendItGenericMethods::rebuildQueryString(array('pg'));
			toSendItGenericMethods::generatePaginationList($tableName, $filtro, $baseUrl ); 
			$results = $wpdb->get_results($sql);
			?>
			<table class="widefat post fixed">
				<thead>
					<tr>
						<th class="wide-10-text">
							<abbr title="Numero">#</abbr> <abbr title="Registro">Reg.</abbr>
						</th>
						<th class="wide-10-text"><abbr title="Numero">#</abbr> Atto</th>
						<th class="wide-10-text">Del</th>
						<th class="wide-10-text">Tipo</th>
						<th class="wide-10-text">Provenienza</th> 
						<th class="wide-text">Oggetto</th>
						<th class="wide-20-text">Periodo pubblicazione</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$gruppi = toSendItGenericMethods::getUserGroups('pafacile');
					
					foreach($results as $i => $row){
						$url = '?page='.TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER.'&id='.$row->id;
						
						$class=($row->status==TOSENDIT_PAFACILE_ATTO_BOZZA)?' draft':'';
						$class=($row->status==TOSENDIT_PAFACILE_ATTO_ANNULLATO)?' annullato':'';
						
						$class = trim($class);
						?>
						<tr <?php echo ($class!='')?"class=\"$class\"":''; ?> >
							<td>
								<?php echo $row->numero_registro ?>
							</td>
							<td>
								<?php echo $row->atto_nr; ?>
							</td>
							<td>
								<?php
								echo toSendItGenericMethods::formatDateTime($row->atto_data,'%d/%m/%Y');
								?>
							</td>
							<td>
								<?php echo(PAFacileDecodifiche::tipoAtto( $row->tipo) ) ?>
							</td>
							<td>
								<?php echo $row->provenienza; ?>
							</td>
							<td class="wide-text">
								<a href="<?php echo $url?>"><?php echo($row->oggetto) ?></a>
								<?php 
							
								if( toSendItGenericMethods::checkMinimalMenuRole($gruppi, TOSENDIT_PAFACILE_ROLE_ALBO_PRETORIO) ){
									?>							
									<div class="row-actions">
										<span class="edit"><a href="?page=<?php echo(TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">Modifica</a></span>
										<?php
										 
										if($row->status==TOSENDIT_PAFACILE_ATTO_BOZZA || $row->status == TOSENDIT_PAFACILE_ATTO_PREPARATO || $row->status == ''){
											?>
											<span class="delete">| <a href="?page=<?php echo(TOSENDIT_PAFACILE_ALBO_PRETORIO_DELETE_HANDLER)?>&id=<?php echo($row->id)?>">Elimina</a></span>
											<?php 
										}
										?>
									</div>
									<?php 
								}
								?>
							</td>
							<td>
								<?php echo(toSendItGenericMethods::formatDateTime( $row->pubblicata_dal) ) ?> &rarr;
								<?php echo(toSendItGenericMethods::formatDateTime( $row->pubblicata_al) ) ?>
							</td>
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

function displayRegistroAlbo(){
	global $wpdb;
	?>
	<div id="elenco-albo-pretorio" class="wrap">
		<div id="icon-edit-pages" class="icon32">
			<br/>
		</div>
		<h2>Registro albo on-line</h2>

		<form method="GET" id="post-filter" action="<?php echo $_SERVER['REQUEST_URI']?>">
			<fieldset>
				<legend>Criteri di ricerca</legend>
				<div>
					<?php 
					$dal	= toMySQLDate($_GET['dal_dd'], $_GET['dal_mm'], $_GET['dal_yy'], true);
					$al		= toMySQLDate($_GET['al_dd'], $_GET['al_mm'], $_GET['al_yy'], true);
					?>
					<label for="dal_dd">Dal:</label>
					<?php toSendItGenericMethods::drawDateField('dal', $dal); ?>
					<label for="al_dd">Al:</label>
					<?php toSendItGenericMethods::drawDateField('al', $al); ?>
					<label for="pa_tipo">Tipo:</label>
					<select name="tipo" id="pa_tipo" class="validator required">
						<option value="">Qualsiasi</option>
						<?php 
						// Since ver 2.3
						$tipo = !isset($_GET['tipo'])?'':$_GET['tipo'];
						$tblTipiAtto = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ATTO;
						$sql ="select codice,descrizione,raggruppamento from $tblTipiAtto order by raggruppamento, descrizione";
						$results = $wpdb->get_results($sql);
						$raggruppamento = '';
						foreach($results as $result){
							if($raggruppamento!=$result->raggruppamento){
								if($raggruppamento!='') echo('</optgroup>');
								$raggruppamento = $result->raggruppamento;
								echo("<optgroup label=\"$raggruppamento\">");
							} 
							?>
							<option value="<?php echo $result->codice ?>"
								<?php echo($tipo==$result->codice?'selected="selected"':'');?>
								><?php echo($result->descrizione) ?></option>
							<?php
						}
						if($raggruppamento!='') echo('</optgroup>');
						?>
					</select>					
					<label for="range">Modalità di inclusione</label>
					<select name="range" id="range">
						<option value="in" <?php echo (($_GET['range']=='in')?'selected="selected"':'') ?>>Esatta</option>
						<option value="out" <?php echo (($_GET['range']=='out')?'selected="selected"':'') ?>>Estesa</option> 
						<option value="low" <?php echo (($_GET['range']=='low')?'selected="selected"':'') ?>>Inizio pubblicazione</option> 
						<option value="high" <?php echo (($_GET['range']=='high')?'selected="selected"':'') ?>>Fine pubblicazione</option>
					</select>
					<p class="help">
						La <em>modalità di inclusione</em> consente di cercare tutti gli atti le cui date di inizio e 
						termine pubblicazione rientreranno esattamente nel range (modalità di inclusione <em>Esatta</em>),
						dove almeno una delle date rientra nel periodo specificato (modalità <em>Estesa</em>), dove la data 
						di inizio pubblicazione è compresa tra le due date specificate (modalità <em>Inizio pubblicazione</em>)
						oppure dove la data di fine pubblicazione è compresa tra le due date specificate (modalità <em>Fine pubblicazione</em>).
					</p>
				</div>
				<div>
					<?php 
					if(!isset($_GET['relata'])) $_GET['relata'] = '';
					$relata =$_GET['relata'];
					?>
					<input type="radio" name="relata" id="relata_0" value="0" <?php echo ($relata=='0')?'checked="checked"':'' ?> />
					<label for="relata_0">Senza certificazione di pubblicazione</label>
		
					<input type="radio" name="relata" id="relata_1" value="1" <?php echo ($relata=='1')?'checked="checked"':'' ?> />
					<label for="relata_1">Con certificazione di pubblicazione</label>
					
					<input type="radio" name="relata" id="relata_all" value="" <?php echo ($relata=='')?'checked="checked"':'' ?> />
					<label for="relata_all">Con o senza certificazione di pubblicazione</label>
					
				</div>				
				
				
				
				<input type="hidden" name="page" value="<?php echo TOSENDIT_PAFACILE_ALBO_PRETORIO_REGISTRO_HANDLER ?>" />
				<input type="submit" value="Applica" class="button" />
			</fieldset>
		</form>
		<?php
		$filter = array();
		
		if($relata == '0') $filter[] = "(data_certificazione is null or data_certificazione='0000-00-00')";
		if($relata == '1') $filter[] = "data_certificazione is not null and data_certificazione<>'0000-00-00'";
		
		if($tipo != '') $filter[] = "tipo = '$tipo'";
		
		if($dal=='0000-00-00') $dal = '';
		if($al =='0000-00-00') $al = '';
		$al = str_replace('00','99', $al);
		if($dal!='' && $al!=''){
			/*
			 	-- pubblicata_dal>= 01/05/2010 and pubblicata_dal<=31/05/2010 or 
				-- pubblicata_al>= 01/05/2010 and pubblicata_al<= 31/05/2010
			 */
			if($_GET['range'] == 'out')
				$filter[] = " ((pubblicata_dal>='$dal' and pubblicata_dal<='$al') or (pubblicata_al>='$dal' and pubblicata_al<='$al'))";
			if($_GET['range'] == 'in')
				$filter[] = " (pubblicata_dal>='$dal' and pubblicata_al<='$al')";

			if($_GET['range'] == 'low')
				$filter[] = " (pubblicata_dal>='$dal' and pubblicata_dal<='$al')";
			if($_GET['range'] == 'high')
				$filter[] = " (pubblicata_al>='$dal' and pubblicata_al<='$al')";
		} else{
			if($dal!='') 	$filter[] = " (pubblicata_dal>='$dal' or pubblicata_al>='$dal')";
			if($al!='') 	$filter[] = " (pubblicata_dal<='$al' or pubblicata_al<='$al')";
		}
		
		$filtro  = join(' and ', $filter);
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
		if($filtro!='') $filtro = "where $filtro";
		$sql = "select * from $tableName $filtro order by pubblicata_dal, pubblicata_al"; 
		$results = $wpdb->get_results($sql);
		?>
		<p>
			Elenco atti 
		<?php 
		if(isset($_GET['relata'])){
			
			if($_GET['relata']=='1'){
				echo "<strong>con certificazione di pubblicazione</strong>";
			}else{
				echo "<strong>senza certificazione di pubblicazione</strong>";
			}
			
		}
		$dal = str_replace("00\-", "01\-", $dal);
		$dal = str_replace("-00", "-01", $dal);
		
		if($al !=''){
			if($_GET['al_mm'] == '' ){
				$al = toMySQLDate(31, 12, $_GET['al_yy']);
			}else{
				if($_GET['dd'] == ''){
					if($_GET['al_mm']==12){
						$al = dateAdd("d",-1, toMySQLDate("01", "01", $_GET['al_yy']+1));
					}else{
						$al = dateAdd("d",-1, toMySQLDate("01", $_GET['al_mm']+1, $_GET['al_yy']));
					}
				}
			}	
			
		}
		echo(" pubblicati");
		
		$dal = toSendItGenericMethods::formatDateTime($dal);
		$al = toSendItGenericMethods::formatDateTime($al);
		
		if($dal!='') echo(" dal <strong>$dal</strong>");
		if($al!='') echo( " al  <strong>$al</strong>");
		?>
		</p>
		
		<table class="widefat post fixed">
			<thead>
				<tr>
					<th class="wide-10-text">N°<br/>registro</th>
					<th class="wide-10-text">N°<br/>atto</th>
					<th class="wide-10-text">Data<br />atto</th>
					<th class="wide-10-text">Tipo<br />pubblicazione</th>
					<th class="wide-10-text">Provenienza</th>
					<th class="wide-30-text">Oggetto</th>
					<th class="wide-10-text">Dal</th>
					<th class="wide-10-text">Al</th>
				</tr>
			</thead>
			<?php
			foreach($results as $row){
				
				?>
				<tr>
					<td>
						<?php echo $row->numero_registro; ?>
					</td>
					<td>
						<?php echo $row->atto_nr ?>
					</td>
					<td>
						<?php echo toSendItGenericMethods::formatDateTime($row->atto_data,'%d/%m/%y') ?>
					</td>
					<td>
						<?php echo PAFacileDecodifiche::tipoAtto( $row->tipo ) ?>
					</td>
					<td>
						<?php echo $row->provenienza ?>
					</td>
					<td>
						<a href="?page=<?php echo(TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">
							<?php echo $row->oggetto ?>
						</a>
					</td>
					<td>
						<?php echo toSendItGenericMethods::formatDateTime($row->pubblicata_dal,'%d/%m/%y') ?>
					</td>
					<td>
						<?php echo toSendItGenericMethods::formatDateTime($row->pubblicata_al,'%d/%m/%y') ?>
					</td>
				</tr>
				<?php 
			}
			?>
		</table>
	</div>
	<?php
}


if(is_admin()) {
	$action=toSendItGenericMethods::getActionFromPage($baseAction);
	if($action == TOSENDIT_PAFACILE_EDIT){
		displayAlboPretorio();
	}else{
		displayRegistroAlbo();
	}
}
?>