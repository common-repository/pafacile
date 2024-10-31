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


global $wpdb;
$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_DELIBERE;
$id = $_GET['id'];
$row = $wpdb->get_row('select * from ' . $tableName . ' where id="' . $id . '"');

$pa_type = $row->tipo;
$pa_oggetto = htmlspecialchars($row->oggetto);
$pa_data_seduta = $row->data_seduta;
$pa_data_albo = $row->data_albo;
$pa_numero =  $row->numero;
?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"><br/></div>
	<h2>Gestione Delibera</h2>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>" enctype="multipart/form-data" >
		<div id="poststuff" class="has-right-sidebar">
			<input type="hidden" name="id" value="<?php echo($id); ?>" />
			<div class="inner-sidebar">
				<div class="postbox">
					<h3>Informazioni delibera</h3>
					<div class="inside">
						<label for="seduta_dd">Data di deliberazione:</label><br />
						<?php 
						toSendItGenericMethods::drawDateField('seduta', $pa_data_seduta);
						?>
						<p>
							<label for="pa_type">Tipo di delibera:</label>
							<select name="type" id="pa_type">
								<option value="g" <?php echo($pa_type=='g'?'selected="selected"':''); ?> >Giunta</option>
								<option value="c" <?php echo($pa_type=='c'?'selected="selected"':''); ?> >Consiglio</option>
							</select>
						</p>
						<p>
							<label for="pa_type">Numero:</label>
							<input type="text" name="numero" id="pa_numero" size="4" value="<?php echo($pa_numero);?>" />
						</p>
				
					</div>
					
					<!-- 
				</div>
				<div class="postbox">
					<h3>Pubblicata all'albo pretorio il:</h3>
					<div class="inside">
						<?php 
						toSendItGenericMethods::drawDateField('albo', $pa_data_albo);
						?>
					</div>
					 -->
					<div id="major-publishing-actions">
						<div id="delete-action">
							<a class="submitdelete deletion" href="?page=<?php echo TOSENDIT_PAFACILE_ADMIN_DELIBERE_HANDLER?>">Annulla</a>
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
							<label class="screen-reader-text" for="title">Oggetto:</label>
							<input size="30" type="text" name="oggetto" id="title" value="<?php echo $pa_oggetto?>" />
						</div>
					</div>
					<div class="stuffbox">
						<?php the_editor($row->descrizione,'descrizione','title',false); ?>
					</div>
					<?php 
					toSendItGenericMethods::displayFileUploadBox($tableName, $id);
					?>
				</div>
			</div>
		</div>
		
	</form>
</div>