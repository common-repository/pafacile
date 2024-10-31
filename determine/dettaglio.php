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

global $wpdb, $current_user;
$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_DETERMINE;
$id = $_GET['id'];
$row = $wpdb->get_row('select * from ' . $tableName . ' where id="' . $id . '"');

if($row==null && isset($id)){
	
	require 'elenco.php';
	exit();
}

if(!toSendItPAFacile::isUserAuthorizedFor($row->id_ufficio)){
	toSendItGenericMethods::createMessage('Non si hanno i diritti per modificare questa determina', true);
	require 'elenco.php';
}else{
	
	$pa_id_ufficio = $row->id_ufficio;
	$pa_oggetto = htmlspecialchars($row->oggetto);
	$pa_data_adozione = $row->data_adozione;
	$pa_numero =  $row->numero;
	?>
	<div class="wrap">
		<?php 
		do_action('determina_header');
		?>
		<div id="icon-edit-pages" class="icon32"><br/></div>
		<h2><?php echo apply_filters('determina_detail_title','Gestione Determina')?></h2>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" enctype="multipart/form-data" >
			<div id="poststuff" class="has-right-sidebar">
				<input type="hidden" name="id" value="<?php echo($id); ?>" />
				<div class="inner-sidebar">
					<div class="postbox">
						<h3><?php echo apply_filters('determina_settings_title','Impostazioni');?></h3>
						<div class="inside">
							<strong><?php echo apply_filters('determina_settings_date_label','Data di adozione')?>:</strong><br />
							<?php 
							toSendItGenericMethods::drawDateField('data_adozione', $pa_data_adozione);
							?>
						</div>
						<?php 
						do_action('determina_after_settings');
						?>
						<div id="major-publishing-actions">
							<div id="delete-action">
								<a class="submitdelete deletion" href="?page=<?php echo TOSENDIT_PAFACILE_DETERMINE_HANDLER?>">Annulla</a>
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
								<label class="screen-reader-text" for="title"><?php echo apply_filters('determina_subject_label','Oggetto')?>:</label>
								<input size="30" type="text" name="oggetto" id="title" value="<?php echo $pa_oggetto?>" />
							</div>
						</div>
						<?php 
						do_action('determina_after_title');
						?>
						<div class="stuffbox">
							<h3><?php echo apply_filters('determina_about_section_title','Informazioni sulla determina'); ?></h3>
							<div class="inside">
								<table class="form-table">
									<tbody>
										<tr>
											<th scope="row">
												<label for="pa_numero"><?php echo apply_filters('determina_number_label','Numero');?>:</label>
											</th>
											<td>
												<input type="text" name="numero" id="pa_numero" value="<?php echo($pa_numero);?>" />
											</td>
										</tr>
										<tr>
											<th scope="row">
												<label for="pa_id_ufficio"><?php echo apply_filters('determina_office_label','Area/Settore/Ufficio')?>:</label>
											</th>
											<td>
												<?php 
												toSendItPAFacile::buildOfficeSelector('id_ufficio', 'pa_id_ufficio', 'widefat', $row->id_ufficio, true, 'abilitato_determine="y"');
												?>
											</td>
										</tr>
														
									</tbody>
								</table>
								<?php 
								do_action('determina_after_information_table');
								?>
							</div>
						</div>
						<div class="stuffbox">
							<?php the_editor($row->descrizione,'descrizione','title',false); ?>
						</div>
						<?php 
						do_action('determina_after_content');
						toSendItGenericMethods::displayFileUploadBox($tableName, $id);
						do_action('determina_after_upload_box');
						?>					
					</div>
				</div>
			</div>
		</form>
	</div>
	<?php 
}
?>