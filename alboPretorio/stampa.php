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

function buildStampaAlboPretorio(){
	global $wpdb, $current_user;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
	$id = '0'.$_GET['id'];
	$id = intval($id);
	$row = $wpdb->get_row('select * from ' . $tableName . ' where id="' . $id . '"');
	$certificazioneIsNotSet = ($row->data_certificazione==null || $row->data_certificazione=='0000-00-00');
	$numeroRegistro = $row->numero_registro;
	$tipoAtto = PAFacileDecodifiche::tipoAtto($row->tipo);	
	?>
	<div class="wrap">
		<div id="poststuff">
			<div id="post-body">
				<div id="post-body-content">
					<div id="back-buttons-box">
						<a class="back" href="?page=<?php echo $_GET['page'] ?>&id=<?php echo $id ?>">Torna alla pubblicazione</a>
						<?php do_action('pafacile_albopretorio_before_print_item'); ?>
						<script type="text/javascript"><!--
							document.write('<a class="print" href="javascript:window.print()">Stampa questo certificato di pubblicazione</a>');
							// -->
						</script>
						<?php do_action('pafacile_albopretorio_after_print_item'); ?>
					</div>
					<?php 
					if(!$certificazioneIsNotSet){
						# c'è una data di certificazione, quindi devo inserire la certificazione di pubblicazione
						?>
						<div id="certificazione-di-pubblicazione">
							<h2>Certificato di pubblicazione</h2>
							<?php 
							$p = get_option('PAFacile_settings');
							$testoCertificazione = $p['certificazione_pubblicazione_' . abs($row->tipo_certificazione)];
							if($rs->data_proroga!=null and $rs->data_proroga!='0000-00-00'){
								$pubblicata_al = toSendItGenericMethods::formatDateTime( $row->data_proroga );
							}else{
								$pubblicata_al = toSendItGenericMethods::formatDateTime( $row->pubblicata_al ); 
							}
							$testoCertificazione = str_replace("@pubblicazione_dal;", toSendItGenericMethods::formatDateTime( $row->pubblicata_dal ),$testoCertificazione);
							$testoCertificazione = str_replace("@pubblicazione_al;", $pubblicata_al,$testoCertificazione);
							$testoCertificazione = str_replace("@data_certificazione;", toSendItGenericMethods::formatDateTime( $row->data_certificazione),$testoCertificazione);
							$tempUser = get_user_by('id', $row->owner);
							$theName = $tempUser->display_name;
							$testoCertificazione = str_replace("@incaricato;", $theName,$testoCertificazione);
							$testoCertificazione = nl2br($testoCertificazione);
							echo $testoCertificazione;
							?>
							<hr />
							<div id="testo-originale">
								<h3><?php echo $row->oggetto ?></h3>
								<dl>
									<dt class="orig-numero-registro">Numero registro:</dt>
									<dd class="orig-numero-registro"><?php echo $row->numero_registro?></dd>
									<dt class="orig-tipo-atto">Tipo atto:</dt>
									<dd class="orig-tipo-atto"><?php echo PAFacileDecodifiche::tipoAtto($row->tipo) ?></dd>
									<dt class="orig-provenienza">Provenienza:</dt>
									<dd class="orig-provenienza"><?php echo $row->provenienza?></dd>
									<dt class="orig-materia">Materia:</dt>
	 								<dd class="orig-materia"><?php echo $row->materia ?></dd>
	 								<dt class="orig-ufficio">Ufficio/Area/Settore di riferimento:</dt>
									<dd class="orig-ufficio"><?php echo PAFacileDecodifiche::officeNameById($row->id_ufficio) ?></dd>
									<dt class="orig-dirigente">Dirigente:</dt>
									<dd class="orig-dirigente"><?php echo $row->dirigente ?></dd>
									<dt class="orig-responsabile">Responsabile:</dt>
									<dd class="orig-responsabile"><?php echo $row->responsabile ?></dd>
								</dl>
								<div>
									<?php 
									echo(wpautop(wptexturize( $rs->descrizione) ));
									?>
								</div>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
	<?php 
}
buildStampaAlboPretorio();
?>