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


/****************************************************************
	 * Procedure per la visualizzazione pubblica delle delibere:
	 * 
	 * - mostra(): mostra sia il modulo di ricerca che l'elenco
	 * - form(): mostra il modulo di ricerca delle delibere
	 * - elenco(): mostra la tabella dei risultati delle delibere
	 * 
	 *****************************************************************/
require_once PAFACILE_PLUING_DIRECTORY .'/public-contents/iContents.php';

class Delibere implements iContents{
	
	public static function mostra($buffer){
		$itemId = $_GET['itemId'];
		if(isset($itemId) && is_numeric($itemId)){
			ob_start();
			// Mostro il dettaglio di un bando
			if(!self::dettagli($itemId)){
				unset($_GET['itemId']);
				echo($buffer);
			}
			$buffer = ob_get_clean();	
		}
		return $buffer;	
		
	}
	public static function form($params = null){
		if(isset($params) && is_array($params)) extract($params);
		$p = get_option('PAFacile_permalinks');
		if(isset($p['delibere_id']) && $p['delibere_id']!=0){
			extract($_GET);
			?>
			<form method="get" class="delibere" action="<?php echo get_permalink($p['delibere_id']) ?>"> 
				<div class="delibere-oggetto">
					<label for="oggetto_delibera">Tutte le delibere che hanno il seguente testo nell'oggetto:</label>
					<?php # v. 2.6.1: Patch per XSS segnalata da Gianni Amato ?>
					<input id="oggetto_delibera" type="text" value="<?php echo( htmlspecialchars( $pa_subject) )?>" name="pa_subject"/>
				</div>
				<div class="delibere-tipo">
					<label for="pa_type">Mostra le delibere di:</label>
					<select name="pa_type" id="pa_type">
						<option value="" <?php echo($pa_type==''?'selected="selected"':''); ?> >Qualsiasi tipo</option>
						<option value="g" <?php echo($pa_type=='g'?'selected="selected"':''); ?> >Giunta</option>
						<option value="c" <?php echo($pa_type=='c'?'selected="selected"':''); ?> >Consiglio</option>
					</select>
				</div>
				<div class="delibere-range">
					<label for="pa_when">Deliberate: </label>
					<select name="pa_when" id="pa_when">
						<option value="=" <?php echo($pa_when=='='?'selected="selected"':''); ?> >in data</option>
						<option value="&lt;" <?php echo($pa_when=='<'?'selected="selected"':'');?> >prima del</option>
						<option value="&gt;" <?php echo($pa_when=='>'?'selected="selected"':'');?> >dopo il</option>
					</select>
				</div>
				<div class="delibere-data">
					<?php 
					# v. 2.6.1: Patch per XSS segnalata da Gianni Amato
					toSendItGenericMethods::drawDateField('pa', htmlspecialchars( $pa_yy.'-'.$pa_mm.'-'.$pa_dd) );
					?>
				</div>
				<p>
					<input type="submit" class="button-secondary" value="Esegui ricerca">
				</p>
			</form>
			<?php 
		}
	}
	
	public static function elenco($params = null){
		
		$opzioni = get_option('PAFacile_settings');
		$subLevel = 3;
		isset($opzioni['LivelloHeader']) && $subLevel=$opzioni['LivelloHeader'];
		
		
		if(count($_GET)==0){
			# Changed in Ver. 2.1
			/*
			$PAFacileDay 	= date('d');
			($PAFacileDay>28) &&	$PAFacileDay = 28; # Compatibilità con Febbraio
			$PAFacileMonth 	= date('m')-1;
			$PAFacileYear 	= date('Y');
			if($PAFacileMonth == 0){
				$PAFacileMonth = 12;
				$PAFacileYear -= 1;
			}
			$PAFacileType	= '';
			*/
			$PAFacileDay = '';
			$PAFacileMonth = '';
			$PAFacileYear = '';
		}
		if(isset($params) && is_array($params)) extract($params);
		
		$PAFacileWhen	= '>';
		isset($_GET['pa_dd']) && is_numeric($_GET['pa_dd']) && 	$PAFacileDay 	= $_GET['pa_dd'];
		isset($_GET['pa_mm']) && is_numeric($_GET['pa_mm']) && 	$PAFacileMonth 	= $_GET['pa_mm'];
		isset($_GET['pa_yy']) && is_numeric($_GET['pa_yy']) && 	$PAFacileYear 	= $_GET['pa_yy'];
		isset($_GET['pa_type']) &&  $PAFacileType	= $_GET['pa_type'];
		isset($_GET['pa_when']) && $PAFacileWhen	= $_GET['pa_when'];

		global $wpdb;
		
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_DELIBERE;
		$mysqlOggetto = '';
		$filter = array();
		isset($_GET['pa_subject']) && $_GET['pa_subject']!='' && $filter[]  = "oggetto like '%{$_GET['pa_subject']}%'";
		($PAFacileType!='') &&  $filter[] = "tipo='$PAFacileType'";
		if(is_numeric($PAFacileDay) && is_numeric($PAFacileMonth) && is_numeric($PAFacileYear)){
			$mysqlData = "'$PAFacileYear-$PAFacileMonth-$PAFacileDay'";
		}else{
			$mysqlData = "'0000-00-00'";
			$PAFacileWhen = '>'; 
		}
		$mysqlQuando = $PAFacileWhen;
		$filter[] = "data_seduta $mysqlQuando $mysqlData ";
		
		$baseUrl =get_permalink($p['delibere_id']);
		$baseUrl.=toSendItGenericMethods::rebuildQueryString(array('pg'));
		$filtro = join($filter, " and ");
		if($filtro!='') $filtro = "where $filtro";
		toSendItGenericMethods::generatePaginationList($tableName, $filtro,$baseUrl);
		$sql = toSendItGenericMethods::applyPaginationLimit("select * from $tableName $filtro order by data_seduta desc, numero desc");
		$rows = $wpdb->get_results( $sql );
		if(count($rows)==0){
			
			if(count($filter)>1){
				?>
				<h<?php echo $subLevel?>>Nessuna delibera trovata</h<?php echo $subLevel?>>
				<p>I criteri specificati nel modulo di ricerca delle delibere non hanno portato ad alcun risultato.</p>
				<p>Provare a modificare i parametri e ripetere la ricerca.</p>
				<?php
			}else{
				
				do_action("pafacile_delibere_empty");
				
			}
		}else{
			$permalinks = get_option('PAFacile_permalinks');
			$url = get_permalink($permalinks['delibere_id']);
			$url.='?itemId='; 
			?>
			<h<?php echo $subLevel?>>Elenco delle delibere</h<?php echo $subLevel?>>
			<table cellpadding="0" cellspacing="0" >
				<thead>
					<tr>
						<?php do_action('pafacile_delibere_before_table_head_columns') ?>
						<th class="wide-20-text">Numero</th>
						<th class="wide-text">Tipo</th>
						<th class="wide-text">Oggetto</th>
						<?php do_action('pafacile_delibere_after_table_head_columns') ?>
					</tr>
				</thead>
				<tbody>
					<?php
					$j =0;
					foreach($rows as $rowId => $row){
						$url = $_SERVER['REQUEST_URI'];
						$url .= ((strpos($url,'?')!==false)?'&':'?').'itemId=' . $row->id;
						?>
						<tr <?php echo (($j++%2)==0)?'class="odd"':'' ?>>
							<?php do_action('pafacile_delibere_before_table_data_columns') ?>
							<td class="wide-20-text" >
								<?php echo($row->numero); ?>
							</td>
							<td class="wide-text">
								<?php echo(PAFacileDecodifiche::tipoDocumento($row->tipo))?>
								
							</td>
							<td class="wide-text">
								<a href="<?php echo $url?>"><?php echo $row->oggetto ?></a>
								<dl>
									<dt>Data deliberazione</dt>
									<dd><?php echo(toSendItGenericMethods::formatDateTime( $row->data_seduta ) )?></dd>
								</dl>
								
							</td>
							<?php do_action('pafacile_delibere_after_table_data_columns') ?>
						</tr>
						<?php
					}
					?>	
				</tbody>
			</table>
			<?php
			toSendItGenericMethods::generatePaginationList($tableName, $filtro,$baseUrl);
		}
		
	}
	
	public static function dettagli($id){
	global $wpdb;
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_DELIBERE;
		$sql = 'select * from ' . $tableName .' where id=' . $id .' and data_seduta<="' .date('Y-m-d') .'"';
		$rs = $wpdb->get_row($sql);
		if($rs==null) return false;
		?>
		<div class="delibera">
			<h4><?php echo $rs->oggetto ?></h4>
			<dl>
				<dt>Numero delibera di <?php echo ($rs->tipo=='c')?'Consiglio':'Giunta' ?>:</dt>
				<dd><?php echo $rs->numero ?></dd>
				<dt>Data seduta:</dt>
				<dd><?php echo(toSendItGenericMethods::formatDateTime( $rs->data_seduta)) ?></dd>
				<?php do_action('pafacile_delibere_after_dettagli'); ?>
			</dl>
			<p>
			<?php 
			$rs->descrizione = apply_filters( 'default_content', $rs->descrizione);
			// $rs->descrizione = apply_filters( 'the_content', $rs->descrizione);
			
			echo(wpautop(wptexturize( $rs->descrizione) ));
			?>
			</p>
			<h5>Allegati</h5>
			<?php 
			toSendItGenericMethods::displayFileUploadBox($tableName, $rs->id);
			?>
		</div>
		<?php 
		return true;
	
	}
}
?>