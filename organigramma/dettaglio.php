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



function displayDettaglioOrganigramma(){
	global $wpdb; 
	$id = (isset($_GET['id']) && is_numeric($_GET['id']))?$_GET['id']:0;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
	$sql = 'select * from ' . $tableName . ' where id="' . $id.'"';
	$row = $wpdb->get_row($sql);
	
	if(is_null($row)){
		
		$row = new stdClass();
		$row->nome 						= '';
		$row->ordine 					= 99;
		$row->descrizione 				= '';
		$row->dirigente 				= '';
		$row->responsabile 				= '';
		$row->indirizzo 				= '';
		$row->email						= '';
		$row->pec						= '';
		$row->telefono					= '';
		$row->fax						= '';
		$row->mostra_su_organigramma	= 'n';
		$row->abilitato_determine		= 'n';
		$row->abilita_figli_determine	= 'n';
		$row->abilitato_ordinanze		= 'n';
		$row->abilita_figli_ordinanze	= 'n';
		$row->mostra_determinazioni		= 'n';
		$row->mostra_bandi				= 'n';
		
	}
	
	?>
	<div class="wrap">
		<div id="icon-users" class="icon32"><br/></div>
		<h2>Gestione nodo dell'organigramma</h2>
		<form method="post" enctype="multipart/form-data" action="<?php echo $_SERVER['REQUEST_URI']?>">
			<div id="poststuff" class="has-right-sidebar">
				<div class="inner-sidebar">
					<div class="postbox">
						
						<h3>Autorizzazioni</h3>
						<div class="inside">
							<p>
								<input type="checkbox" value="y" id="pa_mostra_su_organigramma" name="mostra_su_organigramma" <?php if($row->mostra_su_organigramma=='y') echo('checked="checked"'); ?> />
								<label for="pa_mostra_su_organigramma">Mostra questo nodo e tutti i figli sull'organigramma</label>
							</p>
							<p>
								<input type="checkbox" value="y" id="pa_abilitato_determine" name="abilitato_determine" <?php if($row->abilitato_determine=='y') echo('checked="checked"'); ?> />
								<label for="pa_abilitato_determine">Abilitato alla gestione delle determine del proprio ufficio</label>
							</p>
							<p>
								<input type="checkbox" value="y" id="pa_abilita_figli_determine" name="abilita_figli_determine" <?php if($row->abilita_figli_determine=='y') echo('checked="checked"'); ?> />
								<label for="pa_abilita_figli_determine">Abilitato tutti i sottonodi alla gestione delle determine</label>
							</p>
							<p>
								<input type="checkbox" value="y" id="pa_abilitato_ordinanze" name="abilitato_ordinanze" <?php if($row->abilitato_ordinanze=='y') echo('checked="checked"'); ?> />
								<label for="pa_abilitato_determine">Abilitato alla gestione delle ordinanze del proprio ufficio</label>
							</p>
							<p>
								<input type="checkbox" value="y" id="pa_abilita_figli_ordinanze" name="abilita_figli_ordinanze" <?php if($row->abilita_figli_ordinanze=='y') echo('checked="checked"'); ?> />
								<label for="pa_abilita_figli_ordinanze">Abilitato tutti i sottonodi alla gestione delle ordinanze d'ufficio</label>
							</p>
							<p>
								<label for="pa_ordine_visualizzazione">Ordine:</label>
								<input type="text" name="ordine" id="pa_ordine_visualizzazione" value="<?php echo $row->ordine?>" /> 
							</p>
							
							<!-- New in: 2.1 -->
							<h4>Box informativi aggiuntivi</h4>
							<p>
								<input type="checkbox" value="y" id="pa_mostra_determinazioni" name="mostra_determinazioni" <?php if($row->mostra_determinazioni=='y') echo ('checked="checked"')?> />
								<label for="pa_mostra_determinazioni">Visualizza l'elenco delle determinazioni su questo nodo dell'organigramma</label>
							</p>
							<p>
								<input type="checkbox" value="y" id="pa_mostra_bandi" name="mostra_bandi" <?php if($row->mostra_bandi=='y') echo ('checked="checked"')?> />
								<label for="pa_mostra_bandi">Visualizza l'elenco dei bandi di gara, concorsi, graduatorie su questo nodo dell'organigramma</label>
							</p>
						</div>
						
						<div id="major-publishing-actions">
							<div id="delete-action">
								<a class="submitdelete deletion" href="?page=<?php echo TOSENDIT_PAFACILE_ORGANIGRAMMA_HANDLER?>">Annulla</a>
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
								<label class="screen-reader-text" for="title">Nome:</label>
								<input size="30" type="text" name="nome" id="title" value="<?php echo htmlspecialchars($row->nome)?>" />
							</div>
						</div>
						<div class="stuffbox">
						<?php wp_editor($row->descrizione,'descrizione'); ?>
						</div>
						<div class="stuffbox">
							<h3>Altre informazioni</h3>
							<div class="inside">
								<table class="form-table">
								
									<tbody>
										<tr>
											<th scope="row">
												<label for="pa_dirigente">Dirigente:</label>
											</th>
											<td>
												<input class="widefat" type="text" name="dirigente" id="pa_dirigente" value="<?php echo htmlspecialchars($row->dirigente); ?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_responsabile">Responsabile:</label>
											</th>
											<td>
												<input class="widefat" type="text" name="responsabile" id="pa_responsabile" value="<?php echo htmlspecialchars($row->responsabile); ?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_indirizzo">Indirizzo (se diverso dalla sede Comunale):</label>
											</th>
											<td>
												<input class="widefat" type="text" name="indirizzo" id="pa_indirizzo" value="<?php echo htmlspecialchars($row->indirizzo); ?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_email">Indirizzo Email:</label>
											</th>
											<td>
												<input class="widefat" type="text" name="email" id="pa_email" value="<?php echo htmlspecialchars($row->email); ?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_pec">Indirizzo <abbr title="Posta Elettronica Certificata">PEC</abbr>:</label>
											</th>
											<td>
												<input class="widefat" type="text" name="pec" id="pa_pec" value="<?php echo htmlspecialchars($row->pec); ?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_telefono">Telefono:</label>
											</th>
											<td>
												<input class="widefat" type="text" name="telefono" id="pa_telefono" value="<?php echo htmlspecialchars($row->telefono); ?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_fax">Fax:</label>
											</th>
											<td>
												<input class="widefat" type="text" name="fax" id="pa_fax" value="<?php echo htmlspecialchars($row->fax); ?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_id_ufficio_padre">Struttura superiore:</label>
											</th>
											<td>
												<select class="widefat" name="id_ufficio_padre" id="pa_id_ufficio_padre">
													<option value="0">L'ufficio non dipende da nessuna struttura superiore</option>
													<?php 
													$sql = "select id, nome from $tableName where id <> '$id'"; 
													$results = $wpdb->get_results($sql);
													foreach($results as $key => $value){
														echo('<option value="'  .$value->id .'" ' . (($value->id==$row->id_ufficio_padre)?'selected="selected"':'') . ' >');
														echo(htmlspecialchars($value->nome));
														echo('</option>');
														
													}
													?>
												</select>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>
						<div class="stuffbox">
							<h3>Utenti associati a questo nodo</h3>
							<div class="inside">
							
								<?php 
								$sql = 'select ID, user_nicename from ' . $wpdb->prefix .'users order by user_nicename';
								$results = $wpdb->get_results($sql);
								if(count($results)>0){
									?>
									<table class="form-table">
										<tbody>								
											<?php 
											for($i = 0; $i<count($results); $i++){
												$rus = $results[$i];
												$u = new WP_User($rus->ID);
												if(($i%3)==0) echo('<tr>');
												echo('<td>');
												$fullName = $u->last_name . ' ' . $u->first_name;
												if($fullName==' ') $fullName = $rus->user_nicename;
												
												echo('<input type="checkbox" id="the_user_' . $rus->ID .'" name="binded_user[]" value="' . $rus->ID .'" '. (toSendItPAFacile::inOrganigramma($rus->ID,$id)?'checked="checked"':'') .' />');
												echo('<label for="the_user_' . $rus->ID .'">' . $fullName .'</label>');
												echo('</td>');
												if(($i%3)==2) echo('</tr>'); 
											}
											if(($i%3)!=2) echo('</tr>');
											?>
										</tbody>
									</table>
									<?php
								}
								?>
							
							</div>
						</div>
						<?php 
						toSendItGenericMethods::displayFileUploadBox($tableName, $id);
						?>					
					</div>
				</div>
			</div>
			
		</form>
	</div>
	<?php 

}

if(is_admin()) displayDettaglioOrganigramma();
?>