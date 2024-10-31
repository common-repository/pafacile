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


function getFromOffice($officeId, $displayHidden = true){
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
	$sql = 'select * from '. $tableName . ' where id_ufficio_padre = "' . $officeId . '"';
	if(!$displayHidden) $sql .= ' and mostra_su_organigramma="y"';
	$sql .='order by ordine, nome';
	
	$results = $wpdb->get_results($sql);
	return $results;
}

function caricaLivello($id_padre, $livello = '', $abilitazione_determine='n', $abilitazione_ordinanze='n', $suOrganigramma='y'){
	$results = getFromOffice($id_padre);
	for($i = 0; $i<count($results); $i++){
		$row = $results[$i];
		if($i < count($results)-1 ){
			$tmpLivello = $livello .'2';
		}else{
			$tmpLivello = $livello . '1';
		}
		$immagine = TOSENDIT_PAFACILE_PLUGIN_URL . '/images/tree/?structure=' . $tmpLivello;
		#if($suOrganigramma == 'y') $suOrganigramma= $row->mostra_su_organigramma;
		?>
		<tr>
			<td  class="wide-50-text" style="background: url('<?php echo $immagine?>') center left no-repeat; padding-left: <?php echo 20*(strlen($livello)+1)+5?>px">
				<strong><?php echo $row->nome; ?></strong>
				<div class="row-actions">
					<span class="edit"><a href="?page=<?php echo(TOSENDIT_PAFACILE_ORGANIGRAMMA_EDIT_HANDLER) ?>&id=<?php echo($row->id) ?>">Modifica</a> |</span>
					<span class="delete"><a href="?page=<?php echo(TOSENDIT_PAFACILE_ORGANIGRAMMA_DELETE_HANDLER)?>&id=<?php echo($row->id)?>">Elimina</a></span>
				</div>
			</td>
			<td class="wide-20-text"><?php echo $row->telefono?> <br />
				<a href="mailto:<?php echo $row->email?>"><?php echo $row->email?></a></td>
			<td class="wide-10-text text-center"><?php echo ($suOrganigramma=='y' && $row->mostra_su_organigramma=='y')?'Sì':'No'?></td>
			<td class="wide-10-text text-center"><?php echo ($row->abilitato_determine=='y' || $abilitazione_determine =='y')?'Sì':'No'?></td>
			<td class="wide-10-text text-center"><?php echo ($row->abilitato_ordinanze=='y' || $abilitazione_ordinanze =='y')?'Sì':'No'?></td>
		</tr>
		<?php 
		if($i < count($results)-1 ){
			$tmpLivello = $livello .'0';
		}else{
			$tmpLivello = $livello . '3';
		}
		
		caricaLivello($results[$i]->id, $tmpLivello, ($abilitazione_determine=='y'?'y':$row->abilita_figli_determine), ($abilitazione_ordinanze=='y'?'y':$row->abilita_figli_ordinanze), (($suOrganigramma==$row->mostra_su_organigramma)?$suOrganigramma:'n'));
	}
}

function getLevelOffset($officeId){
	global $wpdb;
	
	$sql ='SELECT id_ufficio_padre FROM ' . $wpdb->prefix.TOSENDIT_PAFACILE_DB_ORGANIGRAMMA." WHERE id='$officeId'";
	#echo($sql);
	
	$rs = $wpdb->get_results($sql);
	#print_r($rs);
	if($rs[0]->id_ufficio_padre==0){
		return 1;
	}else{
		return 1 + getLevelOffset($rs[0]->id_ufficio_padre);
	}
}

function displayOrganigrammaPublic($officeId,$livello='', $url='', $levelOffset = -1){
	if(isset($_GET['oid']) && is_numeric($_GET['oid'])){
		global $wpdb;
		$oid = $_GET['oid'];
		unset($_GET['oid']);
		// Devo mostrare il dettaglio...
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
		$sql = 'select * from ' .$tableName.' where id="' . $oid .'" order by ordine, nome, id';
		$row = $wpdb->get_row($sql);
		?>
		<h3 class="organigramma-ufficio"><?php echo $row->nome; ?></h3>
		<dl class="organigramma-recapiti">
			<?php 
			if($row->email!=''){
				?>
				<dt>Indirizzo email: </dt>
				<dd><a href="mailto:<?php echo $row->email; ?>"><?php echo $row->email; ?></a></dd>
				<?php 
			}
			if($row->pec!=''){
				?>
				<dt>Indirizzo <abbr title="posta elettronica certificata">PEC</abbr>: </dt>
				<dd><a href="mailto:<?php echo $row->pec; ?>"><?php echo $row->pec; ?></a></dd>
				<?php 
				
			}
			if($row->telefono!=''){
				?>
				<dt>Telefono: </dt>
				<dd><?php echo $row->telefono; ?></dd>
				<?php 
			}
			if($row->fax!=''){
				?>
				<dt>Fax: </dt>
				<dd><?php echo $row->fax; ?></dd>
				<?php 
			}
			if($row->indirizzo!=''){
				?>
				<dt>Indirizzo:</dt>
				<dd><?php echo $row->indirizzo?></dd>
				<?php 
			}
			if($row->dirigente!=''){
				?>
				<dt>Dirigente:</dt>
				<dd><?php echo $row->dirigente?></dd>
				<?php 
			}
			if($row->responsabile!=''){
				?>
				<dt>Responsabile:</dt>
				<dd><?php echo $row->responsabile?></dd>
				<?php 
			}
			?>
		</dl>
		<div class="organigramma-dettaglio"><?php echo wpautop(wptexturize( $row->descrizione) ); ?><span class="cboth">&nbsp;</span></div>
		<?php
		if($row->mostra_bandi=='y')	# New in ver 2.1
			BandiGare::elenco(array(
				'office' => $row->id,
				'hideNoResults' => true,
				'subLevel' => 4,
				'limit' => 10,
				'title' => 'Bandi, Gare, Concorsi e Graduatorie'
			));
		if($row->mostra_bandi=='y')	# New in ver 2.2
			Determine::elenco(array(
				'id_office' => $row->id,
				'hideNoResults' => true,
				'subLevel' => 4,
				'limit' => 10
			));
			
		ob_start();
		displayOrganigrammaPublic($oid, '', $_SERVER['REDIRECT_URL']);
		$buffer = ob_get_clean();
		
		if(trim($buffer)!=''){
			echo("<h4>L'organigramma della struttura</h4>" . $buffer);
		}
		if(toSendItGenericMethods::hasAttachments($table, $row->id)){
			?>
			<h4>Allegati</h4>
			<?php 
			toSendItGenericMethods::displayFileUploadBox($tableName, $row->id);
		}
		
	}else{
		
		if($url=='') $url = $_SERVER['REQUEST_URI'];
		if(is_array($officeId)){
			$output = toSendItGenericMethods::identifyParameters( $officeId, array('officeId' => '0', 'livello' => '', 'url'=>$_SERVER['REQUEST_URI']));
			extract($output);
			if($officeId=='' || is_array($officeId)) $officeId=0;
		}

		if($levelOffset==-1){
			if($officeId==0){ 
				$levelOffset = 0;
			}else{
				$levelOffset = getLevelOffset($officeId);
			}
		#	echo($officeId . ' ' . $levelOffset);
		}
	
		
		$results = getFromOffice($officeId,false);
		
		if(count($results)>0) echo('<ul class="organigramma">');
		for($i = 0; $i<count($results); $i++){
			$row = $results[$i];
			if($i < count($results)-1 ){
				$tmpLivello = $livello .'2';
			}else{
				$tmpLivello = $livello . '1';
			}
			$immagine = TOSENDIT_PAFACILE_PLUGIN_URL . '/images/tree/?structure=' . $tmpLivello;
			if($i < count($results)-1 ){
				$tmpLivello = $livello .'0';
			}else{
				$tmpLivello = $livello . '3';
			}
			$urlDtl = $url . ((strpos($url,'?')===false)?'?':'&');
			$urlDtl .= 'oid='.$row->id;
			?>
			<li class="level-<?php echo strlen($tmpLivello)+$levelOffset?>"><div style="background: url('<?php echo $immagine?>') center left no-repeat; padding-left: <?php echo 20*(strlen($livello)+1)+5?>px">
				<a href="<?php echo $urlDtl?>"><?php echo $row->nome; ?></a>
				</div>
				<?php 
				displayOrganigrammaPublic($row->id, $tmpLivello, $url, $levelOffset);
				?>
			</li>
			<?php 
		}
		if(count($results)>0) echo('</ul>');
	}
}

function displayOrganigramma(){
	?>
	<div class="wrap">
	<div id="icon-users" class="icon32">
	<br/>
	</div>
	<h2>Gestione Organigramma</h2>
	
	<table class="widefat post fixed">
		<thead>
			<tr>
				<th class="wide-50-text">Ufficio/Nominativo</th>
				<th class="wide-20-text">Telefono<br />Email</th>
				<th class="wide-10-text text-center">Visibile</th>
				<th class="wide-10-text text-center">Determine</th>
				<th class="wide-10-text text-center">Ordinanze</th>
			</tr>
		</thead>
		<?php 
		caricaLivello(0);
		?>
	</table>
	</div>
	<?php 
}
if(is_admin()) displayOrganigramma();
?>