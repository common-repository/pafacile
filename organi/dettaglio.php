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



function organiDettaglio(){
	
	global $wpdb; 
	$id = $_GET['id'];
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANI;
	$row = $wpdb->get_row('select * from ' . $tableName . ' where id="' . $id.'"');
	
	$pa_type = $row->tipo;
	$pa_nominativo = htmlspecialchars($row->nominativo);
	$pa_deleghe = $row->deleghe;
	$pa_informazioni = $row->dettagli;
	$pa_dal = $row->in_carica_dal;
	$pa_al =  $row->in_carica_al;
	#$is_assessore = ($row->is_assessore=='y');
	#$is_consigliere = ($row->is_consigliere=='y');
	#$is_presidente = ($row->is_presidente=='y');
	$pa_ordine = $row->ordine;
	?>
	<div class="wrap">
		<div id="icon-users" class="icon32"><br/></div>
		<h2>Gestione nominativo</h2>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
			<div id="poststuff" class="has-right-sidebar">
				<div class="inner-sidebar">
					<div class="postbox">
					
						<h3><label for="pa_type">Tipo incarico:</label></h3>
						<div class="inside">
							<select name="type" id="pa_type" class="widefat">
								<option value="">Non specificato</option>
								<?php 
								$tnTipiOrgano = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ORGANO;
								$sql = "select * from $tnTipiOrgano order by descrizione";
								$items = $wpdb->get_results($sql);
								foreach($items as $item){
									?>
									<option value="<?php echo $item->codice ?>" <?php echo(($pa_type==$item->codice)?'selected="selected"':''); ?> ><?php echo $item->descrizione ?></option>
									<?php
								}
								?>
								<!-- 
								<option value="s" <?php echo($pa_type=='s'?'selected="selected"':''); ?> >Sindaco</option>
								<option value="v" <?php echo($pa_type=='v'?'selected="selected"':''); ?> >Vicesindaco</option>
								<option value="sc" <?php echo($pa_type=='sc'?'selected="selected"':''); ?> >Segretario Comunale</option>
								<option value="d" <?php echo($pa_type=='d'?'selected="selected"':''); ?> >Direttore Generale</option>
								<option value="a" <?php echo($pa_type=='a'?'selected="selected"':''); ?> >Assessori</option>
								<option value="c" <?php echo($pa_type=='c'?'selected="selected"':''); ?> >Consiglieri</option>
								-->
							</select>
							
							<?php 
							foreach($items as $item){
								?>
								<p>
									<input type="checkbox" name="member_is[]" value="<?php echo $item->codice ?>" <?php echo (toSendItPAFacile::isMemberOf($id, $item->codice)?'checked="checked"':'')?> id="is_<?php echo $item->codice ?>" />
									<label for="is_<?php echo $item->codice ?>">Mostra anche nell'elenco <em><?php echo $item->descrizione ?></em></label>
								</p>
								<?php 
							}
							?>
							<p>
								<strong>Informazione:</strong>
								Attivando le caselle di cui sopra si specifica che questa persona sarà presentata nell'opportuno elenco pubblico.
							</p>
							<!-- 
							<p> 
								<input type="checkbox" name="is_consigliere" value="y" <?php echo ($is_consigliere?'checked="checked"':'')?> id="is_consigliere" /> <label for="is_consigliere">Mostra anche tra i consiglieri</label>
							</p>
							<p>
								<input type="checkbox" name="is_assessore" value="y" <?php echo ($is_assessore?'checked="checked"':'')?> id="is_assessore" /> <label for="is_assessore">Mostra anche tra gli assessori</label>
							</p>
							<p>
								<input type="checkbox" name="is_presidente" value="y" <?php echo ($is_presidente?'checked="checked"':'')?> id="is_presidente" /> <label for="is_presidente">&Egrave; presidente del consiglio</label>
							</p>
							 -->
							<p>
								<label for="pa_ordine">Ordine di visualizzazione:</label>
								<input type="text" name="ordine" id="pa_ordine" size="4" value="<?php echo($pa_ordine)?>" />
							</p>
						</div>
						<div class="inside">
							<strong>In carica dal:</strong><br />
							<?php 
							toSendItGenericMethods::drawDateField('pa_dal', $pa_dal);
							?>
							<p>
								Solitamente coincide con la data di inizio del mandato
							</p>
						</div>
						<div class="inside">
							<strong>In carica fino al:</strong><br />
							<?php 
							toSendItGenericMethods::drawDateField('pa_al', $pa_al);
							?>
							<p>
								Solitamente coincide con la data di termine del mandato.
							</p>
						</div>
						<div id="major-publishing-actions">
							<div id="delete-action">
								<a class="submitdelete deletion" href="?page=<?php echo TOSENDIT_PAFACILE_ORGANI_EDIT_HANDLER?>">Annulla</a>
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
								<label class="screen-reader-text" for="title">Nominativo:</label>
								<input size="30" type="text" name="nominativo" id="title" value="<?php echo $pa_nominativo?>" />
							</div>
						</div>
						
						<div class="stuffbox">
							<h3><label for="pa_deleghe">Deleghe</label></h3>
							<div class="inside">
								<textarea class="widefat" rows="10" cols="30" name="deleghe" id="pa_deleghe"><?php echo $pa_deleghe?></textarea>
							</div>
						</div>
						<?php 
						the_editor($pa_informazioni,'informazioni','deleghe',true);
						?>
						
					</div>
				</div>
			</div>
			
		</form>
	</div>
	<?php 
}

if(is_admin()) organiDettaglio();
?>