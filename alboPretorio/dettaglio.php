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


function buildModuloAlboPretorio(){
	global $wpdb, $current_user;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
	$id = isset($_GET['id'])?$_GET['id']:'0';
	$id = intval($id);
	$row = $wpdb->get_row('select * from ' . $tableName . ' where id="' . $id . '"');
	if(!is_object($row)){
		$row = new stdClass();
		$row->data = '';
		$row->numero_registro = 0;
		$row->tipo = '';
		$row->oggetto = '';
		$row->descrizione = '';
		$row->pubblicata_dal = '';
		$row->pubblicata_al = '';
		$row->status = TOSENDIT_PAFACILE_ATTO_BOZZA;
		$row->data_certificazione = '';
		$row->motivo = '';
		$row->repertorio_data = '';
		$row->repertorio_nr = '';
		$row->protocollo_nr = '';
		$row->protocollo_data = '';
		$row->fascicolo_nr = '';
		$row->fascicolo_data = '';
		$row->atto_nr = '';
		$row->atto_data = '';
		$row->provenienza = '';
		$row->materia = '';
		$row->id_ufficio = 0;
		$row->dirigente = '';
		$row->responsabile = '';
		
	}
	$certificazioneIsNotSet = ($row->data_certificazione==null || $row->data_certificazione=='0000-00-00');
	
	?>
	<div class="wrap">
		<div id="icon-edit-pages" class="icon32"><br/></div>
		<h2>Albo on-line: modulo di pubblicazione</h2>
		<div id="validator-msg" style="display: none;"></div>
		<form id="modulo-albo-pretorio" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" enctype="multipart/form-data" class="validate">
			<div id="poststuff" class="has-right-sidebar">
				<input type="hidden" name="id" value="<?php echo($id); ?>" />
				<div class="inner-sidebar">
					<div class="postbox">
						<h3>Tipologia e validità</h3>
						<div class="inside">
							<?php
							$opzioni = get_option('PAFacile_settings');
							 
							$gruppi = toSendItGenericMethods::getUserGroups('pafacile');
							if( toSendItGenericMethods::checkMinimalMenuRole($gruppi, array(TOSENDIT_PAFACILE_ROLE_ALBO_PRETORIO))){
								
								$registroReadOnly ='';
								if($opzioni['AlboPretorioEsclusivo']=='y') $registroReadOnly ='readonly="readonly"';
								if(isset($row) && isset($row->numero_registro) && is_numeric($row->numero_registro) && $row->numero_registro!=0){
									$numeroRegistro = $row->numero_registro;
								}else{
									$numeroRegistro = toSendItGenericMethods::getNextAvailableValue($tableName, 'numero_registro','pubblicata_dal');
								}
								?>
								<p>
									<label for="numero_registro">Numero di registro:</label>
									<input type="text" <?php echo $registroReadOnly ?> name="numero_registro" id="numero_registro" value="<?php echo $numeroRegistro ?>" />
									<input type="hidden" name="numero_registro_calc" value="<?php echo $numeroRegistro ?>" />
									<input type="hidden" name="db_numero_registro" value="<?php echo $row->numero_registro ?>" />
								</p>
								<p>
									Il numero sopra indicato è proposto in automatico in dipendenza dell'ultimo progressivo
									specificato nell'anno per l'ultimo inserimento.<br />
									<?php 
									if($registroReadOnly!=''){
										?>
										<strong>Non è possibile modificare il numero di registro in quanto 
										le impostazioni attuali di PAFacile non lo consentono.</strong>
										<?php 
									}else{
										?>
										<strong>Non modificare il valore sopra indicato per lasciare a PAFacile il compito di calcolare il
										corretto progressivo.</strong>
										<?php 
									}
									?>
								</p>
								<?php 
							}
							?>
							<p class="form-field form-required">
								<label for="pa_tipo">Tipo di pubblicazione:</label>
								<select name="tipo" id="pa_tipo" class="validator required">
									<option value="">Non definita</option>
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
											<?php echo($row->tipo==$result->codice?'selected="selected"':'');?>
											><?php echo($result->descrizione) ?></option>
										<?php
									}
									if($raggruppamento!='') echo('</optgroup>');
									?>
								</select>
								<span id="dati-per-albo">&nbsp;</span>
							</p>
							<p>
								<strong>Data di pubblicazione:</strong><br />
								<?php 
								toSendItGenericMethods::drawDateField('pubblicata_dal', $row->pubblicata_dal);
								?>
							</p>
							<p>
								Dalla suddetta data il documento sarà visibile pubblicamente.
							</p>
							<p>
								<?php 
								$giorni_pubblicazione = ($row->pubblicata_dal!='' && $row->pubblicata_al!='')?toSendItGenericMethods::dateDiff($row->pubblicata_dal, $row->pubblicata_al):0;
								?>
								<label for="giorni_pubblicazione">Numero di giorni di pubblicazione all'albo:</label>
								<input class="validator required nint gt__value_0" type="text" size="4" name="giorni_pubblicazione" id="giorni_pubblicazione" value="<?php echo $giorni_pubblicazione ?>" />
								<strong><?php echo toSendItGenericMethods::formatDateTime($row->pubblicata_al) ?></strong>
							</p>
							
							<p>
								Fino a questa data il documento sarà visibile in evidenza sull'albo pretorio on-line.
							</p>
							<?php
							if(!isset($row->status) || $row->status==TOSENDIT_PAFACILE_ATTO_BOZZA){
								?>
								<p>
									<input type="radio" name="status" value="<?php echo TOSENDIT_PAFACILE_ATTO_BOZZA ?>" id="status-<?php echo TOSENDIT_PAFACILE_ATTO_BOZZA ?>" <?php echo ((!isset($row->status) || $row->status==TOSENDIT_PAFACILE_ATTO_BOZZA)?'checked="checked"':'') ?> />
									<label for="status-<?php echo TOSENDIT_PAFACILE_ATTO_BOZZA ?>">Metti in bozza l'atto da pubblicare</label>
								</p>
								<?php
								#if( !array_search(TOSENDIT_PAFACILE_ROLE_ALBO_PRETORIO, $gruppi )){
									?>
									<p>
										<input type="radio" name="status" value="<?php echo TOSENDIT_PAFACILE_ATTO_PREPARATO ?>" id="status-<?php echo TOSENDIT_PAFACILE_ATTO_PREPARATO ?>" <?php echo (($row->status==TOSENDIT_PAFACILE_ATTO_PREPARATO)?'checked="checked"':'') ?> />
										<label for="status-<?php echo TOSENDIT_PAFACILE_ATTO_PREPARATO ?>">Notifica per la pubblicazione</label>
									</p>
									<?php
								#}
							}
							if($row->status==TOSENDIT_PAFACILE_ATTO_PREPARATO){
								?>
								<p style="color: #f00;">
									<strong>In attesa di pubblicazione</strong>
								</p>
								<?php
							}
							if( toSendItGenericMethods::checkMinimalMenuRole($gruppi, array(TOSENDIT_PAFACILE_ROLE_ALBO_PRETORIO)) ){
								if($certificazioneIsNotSet){
									?>
									<p>
										<input type="radio" name="status" value="<?php echo TOSENDIT_PAFACILE_ATTO_PUBBLICATO ?>" id="status-<?php echo TOSENDIT_PAFACILE_ATTO_PUBBLICATO ?>" <?php echo (($row->status==TOSENDIT_PAFACILE_ATTO_PUBBLICATO || $row->status==TOSENDIT_PAFACILE_ATTO_PREPARATO)?'checked="checked"':'') ?> />
										<label for="status-<?php echo TOSENDIT_PAFACILE_ATTO_PUBBLICATO ?>">Pubblica l'atto</label>
									</p>
									<?php
								}
								if(!is_null($row->status) && ($row->status==TOSENDIT_PAFACILE_ATTO_PUBBLICATO || $row->status==TOSENDIT_PAFACILE_ATTO_PROROGATO) ){
									/*
									 * Solo se un atto è stato già pubblicato è possibile prorogare
									 * o annullare l'atto
									 */
									if($certificazioneIsNotSet){
										?>
										<p>
											<input type="radio" 
												name="status" value="<?php echo TOSENDIT_PAFACILE_ATTO_PROROGATO ?>" id="status-<?php echo TOSENDIT_PAFACILE_ATTO_PROROGATO ?>" <?php echo ($row->status==TOSENDIT_PAFACILE_ATTO_PROROGATO?'checked="checked"':'') ?>
												/>
											<label for="status-<?php echo TOSENDIT_PAFACILE_ATTO_PROROGATO ?>">Proroga la pubblicazione</label>
										</p>
										<p id="data-proroga" style="display: none;">
											<strong>Proroga fino al:</strong><br />
											<?php 
											toSendItGenericMethods::drawDateField('data_proroga', $row->data_proroga);
											?>
										</p>
										<p>
											<input type="radio" 
												name="status" value="<?php echo TOSENDIT_PAFACILE_ATTO_ANNULLATO ?>" id="status-<?php echo TOSENDIT_PAFACILE_ATTO_ANNULLATO ?>" <?php echo ($row->status==TOSENDIT_PAFACILE_ATTO_ANNULLATO?'checked="checked"':'') ?>
												/>
											<label for="status-<?php echo TOSENDIT_PAFACILE_ATTO_ANNULLATO ?>">Annulla la pubblicazione</label>
										</p>
										<p id="data-annullamento" style="display: none;">
											<strong>Data di annullamento:</strong><br />
											<?php 
											toSendItGenericMethods::drawDateField('annullato_il', $row->annullato_il);
											?>
											<strong style="color: #f00;">
												<br />
												Attenzione, una volta annullato l'atto non sarà più modificabile.
											</strong>
										</p>
										<?php
									}
									if($certificazioneIsNotSet){
										
										$currentDateTime = date('Y-m-d H:i:s');
										$pubblicazioneScaduta = 
											(
												$row->pubblicata_al<$currentDateTime && 
												($row->data_proroga == null || $row->data_proroga == '0000-00-00') 
											||  ($row->data_proroga != null && $row->data_proroga != '0000-00-00') && $row->data_proroga<$currentDateTime 
											);
										
										if(	$pubblicazioneScaduta ) { 
											
											?>
											<h4>Certificato di pubblicazione</h4>
											<p>
												<input type="radio" name="tipo_certificazione" value="0" id="tipo_certificazione_0">
												<label for="tipo_certificazione_0">Senza certificazione su osservazioni</label>
											</p>
											<p>
												<input type="radio" name="tipo_certificazione" value="1" id="tipo_certificazione_1">
												<label for="tipo_certificazione_1">Con certificazione su osservazioni</label>
											</p>
											<p>
												<strong>Data di certificazione:</strong><br />
												<?php 
												toSendItGenericMethods::drawDateField('data_certificazione', $row->data_certificazione);
												?>
											</p>
											<?php
										}
									
									}
								} 
							}else if(	$row->status==TOSENDIT_PAFACILE_ATTO_PUBBLICATO || 
										$row->status==TOSENDIT_PAFACILE_ATTO_PROROGATO 
										/* || $row->status=='3' */){
								?>
								<p>
									<strong>Atto pubblicato nell'albo on-line. Non è possibile riportarlo nello stato di bozza</strong>
								</p>
								<?php
							}
							?>
						</div>
						<div id="major-publishing-actions">
							<div id="delete-action">
								<a class="submitdelete deletion" href="?page=<?php echo TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER?>">&laquo; Torna</a>
							</div>
							<?php 
							/*
							if (((array_search(TOSENDIT_PAFACILE_ROLE_ALBO_PRETORIO,$gruppi)!==false) ||
								(array_search(TOSENDIT_PAFACILE_ROLE_EDITORE_ALBO_PRETORIO,$gruppi)!==false) && $row->status != 1) &&
								($row->data_certificazione == null || $row->data_certificazione == '0000-00-00')
							*/
							$isEditoreAlbo = toSendItGenericMethods::checkMinimalMenuRole($gruppi, array(TOSENDIT_PAFACILE_ROLE_EDITORE_ALBO_PRETORIO) );
							$isGestoreAlbo = toSendItGenericMethods::checkMinimalMenuRole($gruppi, array(TOSENDIT_PAFACILE_ROLE_ALBO_PRETORIO) );
							if( 
									/*
									 * L'editore può salvare solo se gli atti sono in bozza oppure è un nuovo documento.
									 * Bugfix: Anche il gestore in questo caso può
									 */
									($isEditoreAlbo || $isGestoreAlbo) && 
									(is_null($row->status) || $row->status == TOSENDIT_PAFACILE_ATTO_BOZZA) ||
									
									/*
									 * Il gestore può salvare tutti gli atti non siano annullati e solo se non esiste già una 
									 * data di certificazione.
									 */
									$isGestoreAlbo &&
									(is_null($row->status) || 
										$row->status == TOSENDIT_PAFACILE_ATTO_BOZZA ||
										$row->status == TOSENDIT_PAFACILE_ATTO_PREPARATO ||
										$row->status == TOSENDIT_PAFACILE_ATTO_PUBBLICATO ||
										$row->status == TOSENDIT_PAFACILE_ATTO_PROROGATO) &&
									( is_null( $row->data_certificazione ) || $row->data_certificazione == '0000-00-00')
									/*
									 * L'amministratore può pubblicare l'atto a prescindere
									 */
									
									
							  ){
								?>
								<div id="publishing-action">
									<input class="button-primary" id="save-button"  type="submit" value="Salva" />
								</div>
								<?php 
							}
							?>
							<div class="clear" ></div>
						</div>
					</div>
					<div class="postbox">
						<h3>Altre informazioni</h3>
						<div class="inside">
							<fieldset>
								<legend>Repertorio Generale</legend>
								<p>
									<label for="pa_rep_nr">Numero:</label>
									<input type="text" name="repertorio_nr" id="pa_rep_nr" value="<?php echo htmlspecialchars($row->repertorio_nr)?>" />
								</p>
								<p>
									<label for="repertorio_data_dd">Data:</label>
									<?php toSendItGenericMethods::drawDateField('repertorio_data', $row->repertorio_data); ?>
								</p>
							</fieldset>
							<fieldset>
								<legend>Protocollo</legend>
								<p>
									<label for="pa_prot_nr">Numero:</label>
									<input type="text" name="protocollo_nr" id="pa_prot_nr" value="<?php echo htmlspecialchars($row->protocollo_nr)?>" />
								</p>
								<p>
									<label for="protocollo_data_dd">Data:</label>
									<?php toSendItGenericMethods::drawDateField('protocollo_data', $row->protocollo_data); ?>
								</p>
							</fieldset>
							<fieldset>
								<legend>Fascicolo</legend>
								<p>
									<label for="pa_fasc_nr">Numero:</label>
									<input type="text" name="fascicolo_nr" id="pa_fasc_nr" value="<?php echo htmlspecialchars($row->fascicolo_nr)?>" />
								</p>
								<p>
									<label for="fascicolo_data_dd">Data:</label>
									<?php 
									toSendItGenericMethods::drawDateField('fascicolo_data', $row->fascicolo_data);
									?>
								</p>
							</fieldset>
							<fieldset>
								<legend>Atto</legend>
								<p>
									<label for="pa_atto_nr">Numero:</label>
									<input type="text" name="atto_nr" id="pa_atto_nr" value="<?php echo htmlspecialchars($row->atto_nr)?>" />
								</p>
								<p>
									<label for="data_atto_dd">Data:</label>
									<?php 
									toSendItGenericMethods::drawDateField('data_atto', $row->atto_data);
									?>
								</p>
							</fieldset>
						</div>
					</div>
				</div>

				<div id="post-body">
					<div id="post-body-content">
						<?php 
						if(!$certificazioneIsNotSet){
							# c'è una data di certificazione, quindi devo inserire la certificazione di pubblicazione
							?>
							<div class="updated">
								<p>
									&Egrave; disponibile una certificazione di pubblicazione per questo atto.
									<a href="?page=<?php echo $_GET['page'] ?>&id=<?php echo $id ?>&printout=y">Consulta la certificazione di pubblicazione</a>
								</p>
							</div>
							<?php
						}
						?>
						<strong><label for="title">Oggetto:</label></strong>

						<div id="titlewrap">
							<div id="titlediv">
								<label class="screen-reader-text" for="title">Oggetto:</label>
								<input class="required validator" size="30" type="text" name="oggetto" id="title" value="<?php echo htmlspecialchars( $row->oggetto )?>" />
							</div>
						</div>
						<div id="dettaglio" class="stuffbox">
							<strong>Dettagli:</strong>
							<?php wp_editor($row->descrizione,'descrizione'); ?>
						</div>
						<div class="stuffbox" id="testo-annulla-atto">
							<strong><label for="motivo">Motivo dell'annullamento:</label></strong>
							<!--  <textarea id="motivo" name="motivo" style="width:100%;" rows="10"><?php echo $row->motivo ?></textarea> --> 
							<?php 
							wp_editor($row->motivo, 'motivo' , array(
								'media_buttons'	=> false,
								'teeny'			=> true,
								'quicktags'		=> false
							));
							?>
						</div>
						<div class="stuffbox">
							<h3>Altre informazioni</h3>
							<div class="inside">
								<table class="form-table pafacile-data-table">
									<tbody>
										<tr>
											<th scope="row">
												<label for="pa_provenienza">Provenienza:</label>
											</th>
											<td colspan="3">
												<input class="widefat" type="text" id="pa_provenienza" name="provenienza" value="<?php echo htmlspecialchars( $row->provenienza );?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_materia">Materia:</label>
											</th>
											<td colspan="3">
												<input class="widefat" type="text" id="pa_materia" name="materia" value="<?php echo htmlspecialchars( $row->materia);?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_id_ufficio">Ufficio/Area/Settore di riferimento:</label>
											</th>
											<td colspan="3">
												<?php 
												toSendItPAFacile::buildOfficeSelector('id_ufficio','pa_id_ufficio','widefat',$row->id_ufficio, false);
												?>
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label  for="pa_dirigente">Dirigente:</label>
											</th>
											<td colspan="3">
												<input class="widefat" type="text" name="dirigente" value="<?php echo htmlspecialchars( $row->dirigente )?>" id="pa_dirigente" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_responsabile">Responsabile:</label>
											</th>
											<td colspan="3">
												<input class="widefat" type="text" name="responsabile" value="<?php echo htmlspecialchars( $row->responsabile ) ?>" id="pa_responsabile" />
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

if(isset($_GET['printout']) && $_GET['printout']=='y'){
	require_once PAFACILE_PLUING_DIRECTORY .'/alboPretorio/stampa.php';
}else{
	buildModuloAlboPretorio();
}
?>