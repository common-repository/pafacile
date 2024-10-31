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

function buildModuloSovvenzioni(){
	global $wpdb, $current_user;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_SOVVENZIONI;
	$id = isset($_GET['id'])?$_GET['id']:'0';
	$id = intval($id);
	$row = $wpdb->get_row('select * from ' . $tableName . ' where id="' . $id . '"');
	if(!is_object($row)){
		$row = new stdClass();
		$row->ragione_sociale = '';
		$row->partita_iva = 0;
		$row->codice_fiscale = '';
		$row->indirizzo = '';
		$row->cap = '';
		$row->citta = '';
		$row->provincia = '';
		$row->importo = '';
		$row->norma = '';
		$row->id_ufficio = 0;
		$row->dirigente = '';
		$row->modo_individuazione = '';
		$row->data_pubblicazione = '';
	}
	?>
	<div class="wrap">
		<div id="icon-edit-pages" class="icon32"><br/></div>
		<h2>Sovvenzioni, contributi e sussidi: modulo di gestione</h2>
		<form id="modulo-sovvenzioni" method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" enctype="multipart/form-data">
			<div id="poststuff" class="has-right-sidebar">
				<input type="hidden" name="id" value="<?php echo($id); ?>" />
				<div class="inner-sidebar">
					<div class="postbox">
						<h3>Pubblicazione</h3>
						<div class="inside">
							<?php
							$opzioni = get_option('PAFacile_settings');
							$gruppi = toSendItGenericMethods::getUserGroups('pafacile');
							?>
							<p>
								<strong>Data di assegnazione:</strong><br />
								<?php 
								toSendItGenericMethods::drawDateTimeField('data_pubblicazione', $row->data_pubblicazione, true);
								?>
							</p>
							<p class="help">
								L'informazione sarà resa visibile a partire dalla data sopra riportata nella sezione delle sovvenzioni.
							</p>
						</div>
						<div id="major-publishing-actions">
							<div id="delete-action">
								<a class="submitdelete deletion" href="?page=<?php echo TOSENDIT_PAFACILE_SOVVENZIONI_EDIT_HANDLER?>">&laquo; Torna</a>
							</div>
							<?php 
							if( toSendItGenericMethods::checkMinimalMenuRole($gruppi, TOSENDIT_PAFACILE_ROLE_SOVVENZIONI ) ){
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
				</div>

				<div id="post-body">
					<div id="post-body-content">
						<strong><label for="title">Nome impresa/soggetto beneficiario:</label></strong>
						<div id="titlewrap">
							<div id="titlediv">
								<label class="screen-reader-text" for="title">Nome impresa/soggetto beneficiario:</label>
								<input class="required validator" size="30" type="text" name="ragione_sociale" id="title" value="<?php echo esc_attr( $row->ragione_sociale )?>" />
							</div>
						</div>
						
						<div class="stuffbox">
							<h3>Dati fiscali</h3>
							<div>
								<table class="form-table pafacile-data-table">
									<tbody>
										<tr>
											<th scope="row">
												<label for="codice_fiscale">Codice Fiscale:</label>
											</th>
											<td colspan="3">
												<input maxlength="16" size="24" type="text" id="codice_fiscale" name="codice_fiscale" value="<?php echo esc_attr( $row->codice_fiscale );?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="partita_iva">Partita IVA:</label>
											</th>
											<td colspan="3">
												<input maxlength="11" size="16" type="text" id="partita_iva" name="partita_iva" value="<?php echo esc_attr( $row->partita_iva );?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="indirizzo">Indirizzo:</label>
											</th>
											<td colspan="3">
												<input class="widefat" type="text" id="indirizzo" name="indirizzo" value="<?php echo esc_attr( $row->indirizzo );?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="cap">CAP:</label>
											</th>
											<td colspan="3">
												<input type="text" id="cap" name="cap" maxlength="5" size="7" value="<?php echo esc_attr( $row->cap );?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="citta">Città:</label>
											</th>
											<td colspan="3">
												<input class="widefat" type="text" id="citta" name="citta" value="<?php echo esc_attr( $row->citta );?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="provincia">Provincia:</label>
											</th>
											<td colspan="3">
												<input type="text" id="provincia" name="provincia" maxlength="2" size="4" value="<?php echo esc_attr( $row->provincia );?>" />
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						
						</div>
						
						<div id="dettaglio" class="stuffbox">
							<strong>Modalità seguita per l'individuazione del beneficiario:</strong>
							<?php wp_editor($row->modo_individuazione,'modo_individuazione', array('media_buttons' => false)); ?>
						</div>
						
						<div class="stuffbox">
							<h3>Altre informazioni</h3>
							<div>
								<table class="form-table pafacile-data-table">
									<tbody>
										<tr>
											<th scope="row">
												<label for="id_ufficio">Ufficio/Area/Settore di riferimento:</label>
											</th>
											<td colspan="3">
												<?php 
												toSendItPAFacile::buildOfficeSelector('id_ufficio','id_ufficio','widefat',$row->id_ufficio, false);
												?>
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="dirigente">Funzionario/dirigente responsabile del procedimento:</label>
											</th>
											<td colspan="3">
												<input class="widefat" type="text" id="dirigente" name="dirigente" value="<?php echo esc_attr( $row->dirigente);?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="importo">Importo:</label>
											</th>
											<td colspan="3">
												<input class="widefat" type="text" id="importo" name="importo" value="<?php echo esc_attr( $row->importo );?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="norma">Norma o titolo a base dell'attribuzione:</label>
											</th>
											<td colspan="3">
												<input class="widefat" type="text" id="norma" name="norma" value="<?php echo esc_attr( $row->norma );?>" />
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

buildModuloSovvenzioni();

?>