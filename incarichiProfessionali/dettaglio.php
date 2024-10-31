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

function getDettaglio(){
	global $wpdb, $current_user;
	
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_INCARICHI;
	$id = isset($_GET['id'])?$_GET['id']:0;
	$row = $wpdb->get_row('select * from ' . $tableName . ' where id="' . $id . '"');
	
	if($row==null){
		if($id!='0'){
			require 'elenco.php';
			exit();
		}else{
			$row = new stdClass();
			$row->id = '';
			$row->nominativo = '';
			$row->cf_nominativo = '';
			$row->dal = '';
			$row->al = '';
			$row->modalita_selezione = '';
			$row->tipo_rapporto = '';
			
			$row->oggetto_incarico = '';
			$row->motivo_incarico = '';
			
			$row->compenso = '';
			$row->provv_rep_gen_nr = '';
			$row->provv_rep_gen_del = '';
		}
	}
	
	?>
	<div class="wrap">
		<div id="icon-edit-pages" class="icon32"><br/></div>
		<h2>Gestione incarico professionale</h2>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" enctype="multipart/form-data" >
			<div id="poststuff" class="has-right-sidebar">
				<input type="hidden" name="id" value="<?php echo($row->id); ?>" />
				<div class="inner-sidebar">
					<div class="postbox">
						<h3>Soggetto conferente</h3>
						<div class="inside">
							<p>
								<label for="nominativo">Nominativo:</label><br />
								<input size="30" type="text" name="nominativo" id="nominativo" value="<?php echo $row->nominativo?>" />
							</p>
							<p>
								<label for="cf-nominativo">Codice Fiscale/P.IVA:</label>
								<input size="30" type="text" name="cf_nominativo" id="cf-nominativo" value="<?php echo $row->cf_nominativo?>" />
							</p>
							<p>
								<strong>Importante:</strong>
								per adempiere a quanto specificato nella delibera <em>CiViT 2/2012</em>
								bisogna <a href="#attach_title"><strong>allegare il Curriculium Vitae</strong></a>
								del soggetto a cui viene conferito l'incarico.
							</p>
						</div>
					</div>
					<div class="postbox">
						<h3>Periodo incarico</h3>
						<div class="inside">
							<p>
								<label for="dal_dd">Dal:</label>
								<?php toSendItGenericMethods::drawDateField('dal', $row->dal); ?>
							</p>
							<p>
								<label for="al_dd">Al:</label>
								<?php toSendItGenericMethods::drawDateField('al', $row->al); ?>
							</p>
						</div>
					</div>
					<div class="postbox">
						<div class="inside">
							<p>
								<label for="modalita_selezione">Modalità selezione:</label>
								<input size="30" type="text" name="modalita_selezione" id="modalita_selezione" value="<?php echo $row->modalita_selezione ?>" />
							</p>
							<p>
								<label for="tipo_rapporto">Tipo rapporto:</label>
								<input size="30" type="text" name="tipo_rapporto" id="tipo_rapporto" value="<?php echo $row->tipo_rapporto ?>" />
							</p>
						</div>
						
						<div id="major-publishing-actions">
							<div id="delete-action">
								<a class="submitdelete deletion" href="?page=<?php echo TOSENDIT_PAFACILE_INCARICHI_PROF_HANDLER?>">Annulla</a>
							</div>
							<div id="publishing-action">
								<input class="button-primary"  type="submit" value="Salva" />
							</div>
							<div class="clear" ></div>
						</div>
					</div>
					
				</div>
				<div id="post-body">
					<div id="post-body-content">
						<div id="titlewrap">
							<div id="titlediv">
								<label class="screen-reader-text" for="title">Oggetto dell'incarico:</label>
								<input size="30" type="text" name="oggetto" id="title" value="<?php echo $row->oggetto_incarico ?>" />
							</div>
						</div>
						<div class="stuffbox">
							<?php wp_editor($row->motivo_incarico,'motivo_incarico'); ?>
						</div>
						<div class="stuffbox">
							<h3>Informazioni sull'incarico</h3>
							<div class="inside">
								<table class="form-table">
									<tbody>
										<tr>
											<th scope="row">
												<label for="compenso">Compenso corrisposto:</label>
											</th>
											<td>
												<input type="text" name="compenso" id="compenso" value="<?php echo($row->compenso);?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="provv_rep_gen_nr">Provv. Rep. Gen. nr.:</label>
											</th>
											<td>
												<input type="text" name="provv_rep_gen_nr" id="provv_rep_gen_nr" value="<?php echo($row->provv_rep_gen_nr);?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="provv_rep_gen_del_dd">Provv. Rep. Gen. del:</label>
											</th>
											<td>
												<?php toSendItGenericMethods::drawDateField('provv_rep_gen_del', $row->provv_rep_gen_del); ?>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<?php 
						toSendItGenericMethods::displayFileUploadBox($tableName, $id);
						toSendItGenericMethods::buildAuditTrailTable($tableName, $id);
						?>		
					</div>
				</div>
			</div>
			
		</form>
	</div>
	<?php 
}
getDettaglio();
?>