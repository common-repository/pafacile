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



function adminDettaglioBandi(){
global $wpdb;
$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_BANDI;
$id = (isset($_GET['id']) && is_numeric($_GET['id']))?$_GET['id']:0;
$id = intval($id);
$row = $wpdb->get_row('select * from ' . $tableName . ' where id="' . $id . '"');

if(!is_object($row)){
	
	$row = new stdClass();
	$row->tipo 					= '';
	$row->estremi				= '';
	$row->id_padre				= 0;
	$row->id_ufficio	 		= 0;
	$row->oggetto				= '';
	$row->descrizione			= '';
	$row->data_pubblicazione 	= '0000-00-00';
	$row->data_scadenza 		= '0000-00-00 00:00';
	$row->data_esito	 		= '0000-00-00';
	$row->importo				= '';
	$row->annotazioni_importo	= '';
	$row->procedura				= '';
	$row->categoria				= '';
	$row->aggiudicatario		= '';
	
}

?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"><br/></div>
	<h2>Gestione Bandi, Gare e Graduatorie</h2>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" enctype="multipart/form-data" >
		<div id="poststuff" class="has-right-sidebar">
			<input type="hidden" name="id" value="<?php echo($id); ?>" />
			<div class="inner-sidebar">
				<div class="postbox">
					<h3>Tipologia e validità</h3>
					<div class="inside">
						<p>
							<strong>Tipo documento:</strong>
						</p>
						<p>
							<input type="radio" name="tipo" id="tipo_co" value="co" <?php echo($row->tipo=='co'?'checked="checked"':'');?> /> <label for="tipo_co">Bando di Concorso</label> <br />
						</p> 
						<p>
							<input type="radio" name="tipo" id="tipo_ga" value="ga" <?php echo($row->tipo=='ga'?'checked="checked"':'');?> /><label for="tipo_ga">Bando di Gara</label> <br />
						</p> 
						<p>
							<input type="radio" name="tipo" id="tipo_gr" value="gr" <?php echo($row->tipo=='gr'?'checked="checked"':'');?> /><label for="tipo_gr">Graduatoria</label> <br />
						</p> 
						<p>
							<input type="radio" name="tipo" id="tipo_ba" value="ba" <?php echo($row->tipo=='ba'?'checked="checked"':'');?> /><label for="tipo_ba">Altri bandi</label> <br />
						</p>
						<p>
							<input type="radio" name="tipo" id="tipo_pr" value="pr" <?php echo($row->tipo=='pr'?'checked="checked"':'');?> /><label for="tipo_pr">Proroga</label> <br />
						</p>
						<p>
							<input type="radio" name="tipo" id="tipo_es" value="es" <?php echo($row->tipo=='es'?'checked="checked"':'');?> /><label for="tipo_es">Esito</label> <br />
						</p>
						<p>
							<label for="pa_estremi">Estremi:</label>
							<input class="widefat" type="text" name="estremi" id="pa_estremi" value="<?php echo esc_attr($row->estremi) ?>" />
						</p>
						<p>
							<strong>Data di pubblicazione:</strong><br />
							<?php 
							toSendItGenericMethods::drawDateField('data_pubblicazione', $row->data_pubblicazione);
							?>
						</p>
						<p>
							Fino a questa data il documento non sarà visibile pubblicamente.
						</p>
						<p>
							<strong>Data di scadenza:</strong><br />
							<?php 
							toSendItGenericMethods::drawDateTimeField('data_scadenza', $row->data_scadenza);
							?>
						</p>
						<p>
							In caso di bando o gara questa informazione indicherà fino a che data
							sarà visibile tra i bandi/gare in corso questo documento. 
						</p>
						<p>
							<strong>Data dell'esito:</strong><br />
							<?php 
							toSendItGenericMethods::drawDateField('data_esito', $row->data_esito);
							?>
						</p>
						<p>
							In caso di esito questa informazione indicherà la data in cui si è aggiudicata
							la gara o il concorso. 
						</p>
					</div>
					<div id="major-publishing-actions">
						<div id="delete-action">
							<a class="submitdelete deletion" href="?page=<?php echo TOSENDIT_PAFACILE_BANDI_EDIT_HANDLER?>">Annulla</a>
						</div>
						<?php 
						if (toSendItPAFacile::userIsIn($row->id_ufficio)){
							?>
							<div id="publishing-action">
								<input class="button-primary"  type="submit" value="Salva" />
							</div>
							<?php 
						}
						?>
						<div class="clear" ></div>
					</div>
				</div>
			</div>
			<div id="post-body">
				<div id="post-body-content">
					<div id="titlewrap">
						<div id="titlediv">
							<label class="screen-reader-text" for="title">Oggetto:</label>
							<input size="30" type="text" name="oggetto" id="title" value="<?php echo htmlspecialchars( $row->oggetto )?>" />
						</div>
					</div>
					<div class="stuffbox">
						<?php wp_editor($row->descrizione,'descrizione'); ?>
					</div>
					<p><br /></p>
					<div class="stuffbox">
						<h3>Altre informazioni</h3>
						<div class="inside">
							<table class="form-table">
							
								<tbody>
									<tr>
										<th>
											Riferito a:
										</th>
										<td colspan="2">
											<div id="bando-selezionato">
												<?php 
												$filtroWhere = " where id='{$row->id_padre}'";
												$sql = 'select id, tipo, oggetto, data_pubblicazione, data_scadenza, data_esito from ' . $tableName . $filtroWhere;
												$rowx = $wpdb->get_row($sql);
												
												if($rowx!=null){
													echo toSendItPAFacile::formattaInfoBando($rowx);
												}else{
													echo('Nessun documento ');
												}														
												?>
											</div>
											<a href="javascript:selezionaBando()">Cambia</a>  <a href="javascript:rimuoviRiferimento()">Rimuovi riferimento</a>
											<input type="hidden" name="id_padre" id="pa_id_padre" value="<?php $row->id_padre?>" />
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="pa_procedura">Procedura:</label>
										</th>
										<td>
											<input class="widefat" type="text" name="procedura" id="pa_procedura" value="<?php echo htmlspecialchars($row->procedura)?>" />
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="pa_categoria">Categoria:</label>
										</th>
										<td>
											<input class="widefat" type="text" name="categoria" id="pa_categoria" value="<?php echo htmlspecialchars($row->categoria)?>" />
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="pa_aggiudicatario">Aggiudicatario:</label>
										</th>
										<td>
											<input class="widefat" type="text" name="aggiudicatario" id="pa_aggiudicatario" value="<?php echo htmlspecialchars($row->aggiudicatario)?>" />
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="pa_id_ufficio">Area/Settore/Ufficio di riferimento:</label>
										</th>
										<td>
											<?php 
											toSendItPAFacile::buildOfficeSelector('id_ufficio','pa_id_ufficio','widefat',$row->id_ufficio, false);
											?>
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label  for="pa_importo">Importo Euro:</label>
										</th>
										<td>
											<input class="widefat" type="text" name="importo" value="<?php echo $row->importo?>" id="pa_importo" />
										</td>
									</tr>
									<tr>
										<th scope="row">
											<label for="pa_annotazioni_importo">Annotazioni sull'importo:</label>
										</th>
										<td>
											<input class="widefat" type="text" name="annotazioni_importo" value="<?php echo htmlspecialchars( $row->annotazioni_importo) ?>" id="pa_annotazioni_importo" />
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
	<div id="cerca-bando" class="search-box">
		<h3>Ricerca bando</h3>
		<label for="src-tipo">Tipo:</label>
		<select name="tipo" id="src-tipo">
			<option value="ba">Altro bando</option>
			<option value="co">Bando di Concorso</option>
			<option value="ga">Bando di gara</option>
			<option value="gr">Graduatoria</option>
			<option value="pr">Proroga</option>
			<option value="es">Esito</option>
		</select>
		<a href="javascript:cercaBando()" class="button-primary">Cerca...</a>
		<div id="bandi-results" class="elenco-risultati">
			<strong>Scegliere il tipo di documento</strong>
		</div>
	</div>
</div>
<?php 
}

adminDettaglioBandi();
?>