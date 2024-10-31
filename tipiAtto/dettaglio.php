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

function adminDettaglioTipiAtto(){
global $wpdb;
$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ATTO;
$id = '0'.$_GET['id'];
$id = intval($id);
$row = $wpdb->get_row('select * from ' . $tableName . ' where id="' . $id . '"');
?>
<div class="wrap">
	<div id="icon-edit-pages" class="icon32"><br/></div>
	<h2>Gestione tipologia di atto pubblicabile nell'albo on-line</h2>
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']?>">
		<div id="poststuff" class="has-right-sidebar">
			<input type="hidden" name="id" value="<?php echo($id); ?>" />
			<div class="inner-sidebar">
				<div class="postbox">
					<h3>Tipologia e durata</h3>
					<div class="inside">
						<p>
							<label for="codice">Codice:</label><br />
							<input type="text" name="codice" maxlength="10" id="codice" value="<?php echo $row->codice ?>" />
						</p>
						<?php 
						if($row->id!=0){
							?>
							<p>
								<strong>Attenzione: </strong> modificando questo dato si
								perderà l'associazione con le tipologie di atto nell'albo 
								on-line.
								Questo comporterà che tutti gli atti pubblicati con codice
								<strong><?php echo $row->codice ?></strong> <em><?php echo $row->descrizione ?></em>
								non saranno più identificate.
							</p>
							<?php 
						}?>
						<p>
							<label for="durata_pubblicazione">Numero di giorni di pubblicazione:</label><br />
							<input type="text" name="durata_pubblicazione" id="durata_pubblicazione" value="<?php echo $row->durata_pubblicazione ?>" />
						</p> 
						<p>
							Cambiare l'informazione di cui sopra, non corrisponde a modificare il tutti gli atti già
							inseriti nell'albo on-line. 
						</p>
					</div>
					<div id="major-publishing-actions">
						<div id="delete-action">
							<a class="submitdelete deletion" href="?page=<?php echo TOSENDIT_PAFACILE_TIPO_ATTO_EDIT_HANDLER?>">Annulla</a>
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
							<label for="title">Descrizione:</label>
							<input size="30" type="text" name="descrizione" id="title" value="<?php echo htmlspecialchars( $row->descrizione )?>" />
						</div>
					</div>
					<div>
						<label for="raggruppamento">Raggruppamento:</label>
						<input class="widefat" size="30" type="text" name="raggruppamento" id="raggruppamento" value="<?php echo htmlspecialchars( $row->raggruppamento)?>" />
					</div>
				</div>
			</div>
		</div>
	</form>
</div>
<?php 
}

adminDettaglioTipiAtto();
?>