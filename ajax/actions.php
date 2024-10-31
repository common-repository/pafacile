<?php

add_action('wp_ajax_lista_bandi', 		array('toSendItPAFacileAjax','listaBandi'));
add_action('wp_ajax_giorni_atto', 		array('toSendItPAFacileAjax','giorniAttoAlboPretorio'));
add_action('wp_ajax_shortcode',			array('toSendItPAFacileAjax','shortcodeForm'));

class toSendItPAFacileAjax{
	function giorniAttoAlboPretorio(){
		global $wpdb;
		$sql = 'select durata_pubblicazione from ' . $wpdb->prefix. TOSENDIT_PAFACILE_DB_TIPO_ATTO . ' where codice="'.$_POST['tipo'].'"';
		$result = $wpdb->get_row($sql);
		echo ($result->durata_pubblicazione);
		die();
	}
	function listaBandi(){
		global $wpdb;
		$sql = 'select * from ' . $wpdb->prefix. TOSENDIT_PAFACILE_DB_BANDI . ' where tipo="'.$_POST['tipo'].'" order by data_pubblicazione desc, data_scadenza desc';
		$results = $wpdb->get_results($sql);
		if(count($results)>0){
			echo '<ul class="results">';
			foreach($results as $key => $rs){
				echo '<li id="bando-'.$rs->id.'" onclick="selezionaBando('. $rs->id . ')">';
				echo (toSendItPAFacile::formattaInfoBando($rs));
				echo '</li>';
			}
			echo '</ul>';
		}else{
			echo('<p>Spiacenti, nessun documento disponibile per il tipo di ricerca eseguita</p>');
			
		}
		die();
	}
	
	function shortcodeForm(){
		?>
		<table id="pafacile-table" class="form-table">
			<tr>
				<th><label for="pafacile-mce-type">Tipo:</label></th>
				<td>
					<select id="pafacile-mce-type">
						<option value="alboPretorio">Albo Pretorio</option>
						<option value="bandi">Bandi di Gara, Concorsi</option>
						<option value="delibere">Delibere</option>
						<option value="determine">Determine</option>
						<option value="incarichiProfessionali">Incarichi Professionali</option>
						<option value="ordinanze">Ordinanze</option>
						<option value="organigramma">Organigramma</option>
						<option value="organi">Organi di Governo</option>
						<option value="sovvenzioni">Sovvenzioni</option>
						<option value="statistiche">Statistiche</option>
					</select>
				</td>
			</tr>
			<tr id="pafacile-mce-aspetto">
				<th><label for="pafacile-mce-action">Aspetto:</label></th>
				<td>
					<select id="pafacile-mce-action">
						<option value="box">Modulo di ricerca</option>
						<option value="list">Elenco risultati</option>
						<option value="opendata">Link agli opendata</option>
					</select>
				</td>
			</tr>
			<tr id="pafacile-mce-bandi">
				<th>Opzioni aggiuntive:</th>
				<td>
					
					<p>
						<input type="checkbox" id="bandi-archive" />
						<label for="bandi-archive">Archivio bandi</label>
					</p>
					
				</td>
			</tr>
			<tr id="pafacile-mce-statistiche">
				<th><label for="pafacile-mce-action">Numero di giorni:</label></th>
				<td>
					<input type="text" id="pafacile-mce-giorni" value="0" />
					<p>
						L'opzione <strong>statistiche</strong> funzionerà solo se è stata configurata la sezione <em>statistiche</em> di PAFacile.
					</p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="button" id="pafacile-mce-submit" class="button-primary" value="Conferma" name="submit" />
		</p>
		<p>
			<strong>Nota importante:</strong> la pagina che presenterà un modulo di ricerca o un elenco non deve
			necessariamente coincidere con la pagina che mostrerà il dettaglio della medesima informazione.
			Per tale scopo configurare PAFacile (è richiesto un livello amministrativo).
		</p>
		<?php
		die();
	}
}

?>
