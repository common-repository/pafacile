<?php 
global $saveHandler;
$saveHandler = array(
	TOSENDIT_PAFACILE_BANDI_HANDLER 			=> 	'doSaveBandi',
	TOSENDIT_PAFACILE_DELIBERE_HANDLER			=>	'doSaveDelibere',
	TOSENDIT_PAFACILE_DETERMINE_HANDLER			=>	'doSaveDetermine',
	TOSENDIT_PAFACILE_ORGANI_HANDLER			=>	'doSaveOrgani',
	TOSENDIT_PAFACILE_ORGANIGRAMMA_HANDLER		=>	'doSaveOrganigramma',
	TOSENDIT_PAFACILE_ORDINANZE_HANDLER			=>	'doSaveOrdinanze',
	TOSENDIT_PAFACILE_ALBO_PRETORIO_HANDLER		=>	'doSaveAlboPretorio',
	TOSENDIT_PAFACILE_INCARICHI_PROF_HANDLER	=>	'doSaveIncarico',
	TOSENDIT_PAFACILE_TIPO_ATTO_HANDLER			=>	'doSaveTipoAtto',
	
	# Since Ver 1.6
	TOSENDIT_PAFACILE_TIPO_ORGANO_HANDLER		=>	'doSaveTipoOrgano',
		
	# Since Ver 2.5
	TOSENDIT_PAFACILE_SOVVENZIONI_HANDLER		=> 'doSaveSovvenzione',
) ;

function doRedirect($id, $doRedirect, $handler){
	if($id!=0 && $doRedirect){
	
		$url = $_SERVER['PHP_SELF'];
		$url .= '?page=' . $handler .'&id=' . $id;
		
		header('Location: '. $url, true);
		exit();
	}
}

function auditTrailAPDecodificaStato($stato){
	
	switch($stato){
		
		case 0:
			$stato = 'Bozza';
			break;
		case 1:
			$stato = 'Pubblicato';
			break;
		case 2:
			$stato = 'Prorogato';
			break;
		case 8:
			$stato = 'Pronto per la pubblicazione';
			break;
		case 9:
			$stato = 'Annullato';
			break;
	}
	return $stato;
}

/**
 * Decodifica per l'Audit Trail il tipo di bando
 * @param string $tipo
 * @return string
 */
function auditTrailBandiDecodificaTipo($tipo){
	switch($tipo){
		case 'co':
			$decodifica = 'Bando di Concorso';
			break;
				
		case 'ga':
			$decodifica = 'Bando di Gara';
			break;
		case 'gr':
			$decodifica = 'Graduatoria';
			break;
		case 'ba':
			$decodifica = 'Altri bandi';
			break;
		case 'pr':
			$decodifica = 'Proroga';
			break;
	}
	
	return $decodifica;
}

function auditTrailAPDecodificaTipoCertificazione($tipo){
	switch($tipo){
		case '0':
			$stato ="senza che siano state presentate osservazioni né opposizioni";
			break;
		case '1':
			$stato ="con osservazioni";
			break;
	}
	return $tipo;
}

// Since Ver. 1.4

function doCreateAuditTrail($table, $data){
	global $wpdb;
	$id = 0;
	$auditTrail = array();
	if(isset($_GET['id'])){
		$id = $_GET['id'];
		$theRow = $wpdb->get_row("select * from $table where id=$id");
	}else{
		$auditTrail[] = "ha creato il dato";
		$theRow = null; 
	}

	// Tabella decodifica Audit Trail
	$tableNameOrgani 		= $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANI;
	$tableNameDelibere 		= $wpdb->prefix . TOSENDIT_PAFACILE_DB_DELIBERE;
	$tableNameDetermine		= $wpdb->prefix . TOSENDIT_PAFACILE_DB_DETERMINE;
	$tableNameOrdinanze 	= $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORDINANZE;
	$tableNameBandi 		= $wpdb->prefix . TOSENDIT_PAFACILE_DB_BANDI;
	$tableNameOrganigramma	= $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
	$tableNameAlboPretorio	= $wpdb->prefix . TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
	$tableNameIncarichi		= $wpdb->prefix . TOSENDIT_PAFACILE_DB_INCARICHI;
	$tableNameSovvenzioni	= $wpdb->prefix . TOSENDIT_PAFACILE_DB_SOVVENZIONI;
	
	$decAT = array(
		# Informazioni critiche dell'albo pretorio
		"$tableNameAlboPretorio.numero_registro"	=> "il numero registro",
		"$tableNameAlboPretorio.tipo"				=> array("il tipo pubblicazione", array("PAFacileDecodifiche","tipoAtto") ),
		"$tableNameAlboPretorio.repertorio_nr"		=> "il numero del repertorio",
		"$tableNameAlboPretorio.repertorio_data"	=> "la data del repertorio",
		"$tableNameAlboPretorio.protocollo_nr"		=> "il numero di protocollo",
		"$tableNameAlboPretorio.protocollo_data"	=> "la data di protocollo",
		"$tableNameAlboPretorio.fascicolo_nr"		=> "numero di fascicolo",
		"$tableNameAlboPretorio.fascicolo_data"		=> "la data fascicolazione",
		"$tableNameAlboPretorio.pubblicata_dal"		=> "la data di affissione",
		"$tableNameAlboPretorio.pubblicata_al"		=> "la data di ritiro",
		"$tableNameAlboPretorio.atto_nr"			=> "il numero dell'atto",
		"$tableNameAlboPretorio.atto_data"			=> "la data dell'atto",
		"$tableNameAlboPretorio.provenienza"		=> "la provenienza",
		"$tableNameAlboPretorio.materia"			=> "la materia",
		"$tableNameAlboPretorio.id_ufficio"			=> "l'ufficio",
		"$tableNameAlboPretorio.id_ufficio"			=> "l'ufficio",
		"$tableNameAlboPretorio.dirigente"			=> "il dirigente",
		"$tableNameAlboPretorio.responsabile"		=> "il responsabile",
		"$tableNameAlboPretorio.oggetto"			=> "l'oggetto",
		"$tableNameAlboPretorio.annullato_il"		=> "la data di annullamento",
		"$tableNameAlboPretorio.data_proroga"		=> "la data di proroga",
		"$tableNameAlboPretorio.motivo"				=> "il motivo dell'annullamento",
		"$tableNameAlboPretorio.status"				=> array( "lo stato", "auditTrailAPDecodificaStato"),
		# Since Ver 1.4.1
		"$tableNameAlboPretorio.data_certificazione"=> "la data di certificazione di pubblicazione",
		"$tableNameAlboPretorio.tipo_certificazione"=> array( " certificazione ", "auditTrailAPDecodificaTipoCertificazione"),
	

		# Since Ver 2.4.0
		"$tableNameIncarichi.nominativo"			=> "il soggetto incaricato",
		"$tableNameIncarichi.cf_nominativo"			=> "il CF/PIVA dell'incaricato",
		"$tableNameIncarichi.dal"					=> "la data di avvio",
		"$tableNameIncarichi.al"					=> "la data di conclusione",
		"$tableNameIncarichi.data_pubblicazione"	=> "la data di pubblicazione",
		"$tableNameIncarichi.compenso"				=> "il compenso",
		"$tableNameIncarichi.provv_rep_gen_nr"		=> "il numero del repertorio generale",
		"$tableNameIncarichi.provv_rep_gen_del"		=> "la data del repertorio generale",
		
		"$tableNameIncarichi.oggetto_incarico"		=> "l'oggetto dell'incarico",
		"$tableNameIncarichi.soggetto_conferente"	=> "l'oggetto dell'incarico",
		"$tableNameIncarichi.modalita_selezione"	=> "la modalità di selezione",
		"$tableNameIncarichi.tipo_rapporto"			=> "la tipologia di rapporto",

		# Since Ver 2.4.4
		# TODO: Mancano id_padre e id_ufficio.
		"$tableNameBandi.tipo"						=> array( "il tipo", "auditTrailBandiDecodificaTipo"),
		"$tableNameBandi.estremi"					=> "gli estremi",
		"$tableNameBandi.oggetto"					=> "l'oggetto",
		"$tableNameBandi.descrizione"				=> "la descrizione",
		"$tableNameBandi.data_pubblicazione"		=> "la data di pubblicazione",
		"$tableNameBandi.data_scadenza"				=> "la data di scadenza",
		"$tableNameBandi.data_esuti"				=> "la data dell'esito",
		"$tableNameBandi.importo"					=> "l'importo",
		"$tableNameBandi.annotazioni_importo"		=> "le annotazioni sull'importo",
		"$tableNameBandi.procedura"					=> "la procedura",
		"$tableNameBandi.categoria"					=> "la categoria",
		"$tableNameBandi.aggiudicatario"			=> "l'aggiudicatario",
		
		# Since Ver 2.5
		# TODO: Mancano id_ufficio.
		"$tableNameSovvenzioni.ragione_sociale"		=> "il nome dell'impresa/soggetto beneficiario",
		"$tableNameSovvenzioni.codice_fiscale"		=> "il codice fiscale",
		"$tableNameSovvenzioni.partita_iva"			=> "la partita IVA",
		"$tableNameSovvenzioni.indirizzo"			=> "l'indirizzo",
		"$tableNameSovvenzioni.cap"					=> "il CAP",
		"$tableNameSovvenzioni.citta"				=> "la città",
		"$tableNameSovvenzioni.provincia"			=> "la provincia",
		"$tableNameSovvenzioni.dirigente"			=> "il funzionario/dirigente responsabile del procedimento",
		"$tableNameSovvenzioni.importo"				=> "l'importo",
		"$tableNameSovvenzioni.norma"				=> "la norma o titolo a base dell'attribuzione",
			
		# TODO: aggiungere le altre informazioni per l'audit trail
		# Roadmap: da introdurre nella versione 3.0
	
		'NOTHING'
	);
	if($theRow){
		foreach($data as $key => $value){
			$rVal = $theRow->$key;
			if($value != $rVal && isset($decAT["$table.$key"])){
				if(is_array($decAT["$table.$key"])){
					$info = $decAT["$table.$key"][0]; 
					$rVal = call_user_func( $decAT["$table.$key"][1], $rVal );
					$value = call_user_func( $decAT["$table.$key"][1], $value );
				}else{
					$info = $decAT["$table.$key"]; 
				}
				$from = ($rVal!='')?" da <strong>$rVal</strong>":'';
				$to = ($value!='')?" a <strong>$value</strong>":" a vuoto";
						
				$action = ($rVal!='')?'modificato':'impostato';
				$auditTrail[] = "ha $action <em>$info</em>$from$to";
			}
		}
	}
	return $auditTrail;
}

function doSave($table, $data, $handler, $checkForRedirect, $FailErrorMessage, $manageUpload= false, $filePrefix = '', $afterInsert = '' ){
	global $wpdb;
	
	$auditTrail = doCreateAuditTrail($table, $data);
	
	$doRedirect = false;
	$id = 0;

	if(isset($_GET['id']) && $_GET['id']!=0){
	
		$id = $_GET['id'];
		$wpdb->show_errors(true);
		do_action('pafacile_do_update', array('id'=> $id, 'data'=>$data) );
		
		$wpdb->update($table, $data, array('id'=> $id) );
		
		$doRedirect = false;	
	}else{

		do_action('pafacile_do_insert', array('id'=> $id, 'data'=>$data) );
		if($wpdb->insert($table, $data)){
			
			$id = $wpdb->insert_id;
			//$_POST['id'] = $id;
			$doRedirect = true;
		}else{
			
			toSendItGenericMethods::createMessage($FailErrorMessage);
		}
	
		#if($wpdb->insert($table, $data)) $id = $wpdb->insert_id;
	}
	do_action('pafacile_do_save', array('id'=> $id, 'data'=>$data) );
		
	$fn = "doSave_$afterInsert";
	if($afterInsert!='' && function_exists($fn)) $fn($id);

	$attach_title = stripslashes($_POST['attach_title']);
	if($id!=0){
		if($manageUpload) toSendItGenericMethods::doUploadFile($filePrefix,'allegato', $table, $id,0,0, $attach_title);
		if(count($auditTrail)>0){
			global $current_user;
			$atTable = $wpdb->prefix . TOSENDIT_DB_AUDIT_TRAIL;
			$obj = array(
				'tabella_rif'	=> $table,
				'id_tab_rif'	=> $id,
				'user_id'		=> $current_user->ID,
				'quando'		=> date('Y-m-d H:i:s') 
			);
			for($i=0; $i<count($auditTrail); $i++){
				$obj['azione'] = $auditTrail[$i];
				apply_filters('do_save_audit_trail', $obj );
				$wpdb->insert($atTable, $obj);
			}
		}
	}
	 
	if($checkForRedirect) doRedirect($id,$doRedirect, $handler );
	return $id;
}

function doSaveBandi(){
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_BANDI;
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);
		extract($_POST);
		
		$data_pubblicazione = "$data_pubblicazione_yy-$data_pubblicazione_mm-$data_pubblicazione_dd";
		$data_scadenza = "$data_scadenza_yy-$data_scadenza_mm-$data_scadenza_dd";
		$data_esito ="$data_esito_yy-$data_esito_mm-$data_esito_dd";
		if(isset($data_scadenza_hh) && $data_scadenza_hh!=''){
			$data_scadenza .= " $data_scadenza_hh:";
			if(isset($data_scadenza_nn) && $data_scadenza_nn!=''){
				$data_scadenza .= $data_scadenza_nn;
				
			}else{
				$data_scadenza = '00';
			}
		}
		$data = array(
			'tipo'					=> $tipo,
			'data_pubblicazione' 	=> $data_pubblicazione,
				
			'estremi'				=> $estremi,	# Since V 2.4.4
			
			'data_scadenza' 		=> $data_scadenza,
			'data_esito'			=> $data_esito,
			'oggetto'				=> $oggetto,
			'descrizione'			=> $descrizione,
			'id_ufficio'			=> $id_ufficio, 
			'id_padre'				=> $id_padre,
			'importo'				=> $importo,
			'annotazioni_importo'	=> $annotazioni_importo,
			'aggiudicatario'		=> $aggiudicatario,	
			'procedura'				=> $procedura,
			'categoria'				=> $categoria,
		); 
		$data = apply_filters('do_save_bando', $data);
		
		doSave($tableName, $data, TOSENDIT_PAFACILE_BANDI_EDIT_HANDLER, true, 'Impossibile salvare questo documento', true);
	}
}

function doSaveOrganigramma(){
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);
		$mostra_su_organigramma = 'n';
		$abilitato_determine = 'n';
		$abilita_figli_determine = 'n';
		$abilitato_ordinanze = 'n';
		$abilita_figli_ordinanze = 'n';
		
		# Since V 2.4.4
		$mostra_determinazioni = 'n';
		$mostra_bandi = 'n';
		
		extract($_POST);
		
		$data= array(
			'nome' 						=> $nome,
			'descrizione'	 			=> $descrizione,
			'email' 					=> $email,
			'pec'						=> $pec,
			'telefono' 					=> $telefono,
			'id_ufficio_padre' 			=> $id_ufficio_padre,
			'mostra_su_organigramma' 	=> $mostra_su_organigramma,
			'abilitato_determine' 		=> $abilitato_determine,
			'abilitato_ordinanze' 		=> $abilitato_ordinanze,
			'abilita_figli_determine' 	=> $abilita_figli_determine,
			'abilita_figli_ordinanze' 	=> $abilita_figli_ordinanze,
			'fax'						=> $fax,
			'indirizzo'					=> $indirizzo,
			'ordine'					=> $ordine,
			'dirigente'					=> $dirigente,
			'responsabile'				=> $responsabile,
				
			# Since V 2.4.4
			'mostra_bandi'				=> $mostra_bandi,
			'mostra_determinazioni'		=> $mostra_determinazioni,
		);
		$data = apply_filters('do_save_organigramma', $data);
		
		$id = doSave($tableName, $data, TOSENDIT_PAFACILE_ORGANIGRAMMA_EDIT_HANDLER, false, 'Impossibile salvare questa informazione nell\'organigramma',true);
		
		if($id!=0){
			// Associa tutti gli utenti
			if(!isset($binded_user)) $binded_user = array();
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_USERS_TO_ORGANIGRAMMA;
			$sql = 'delete from ' . $tableName. ' where id_organigramma="' . $id .'"';
			$wpdb->query($sql);
			for($i=0; $i<count($binded_user); $i++){
				$data = array(
					'id_organigramma'	=> $id,
					'id_utente'			=> $binded_user[$i]
				);
				$data = apply_filters('do_save_utenti_organigramma', $data);
				$wpdb->insert($tableName, $data);
			}
			doRedirect($id,true, TOSENDIT_PAFACILE_ORGANIGRAMMA_EDIT_HANDLER );
		}
	}
}


function doSave_setRelazioniTipo($id){
	global $wpdb;
	
	$tableNameOrgani = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANI;
	$tableOrganiRel = "{$tableNameOrgani}_rel";
	
	$member_is = $_POST['member_is'];
	foreach($member_is as $is) {
		if(!toSendItPAFacile::isMemberOf($id,$is)){
			$data = apply_filters('do_save_organo_rel', $data);
			$wpdb->insert($tableOrganiRel,
			array(
				'id_organo' => $id,
				'tipo'		=> $is
			));
		}
	}
	
	// Elimino le voci non più interessanti
	
	$member_is = implode("','",$member_is);
	$member_is = "'$member_is'";
	
	$sql = "delete from $tableOrganiRel where tipo not in($member_is)";
	
	$wpdb->query($sql);
	
}

function doSaveOrgani(){
	
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANI;
	
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);
		$in_carica_dal = $_POST['pa_dal_yy'] . '-' . $_POST['pa_dal_mm'] . '-' .$_POST['pa_dal_dd'];
		$in_carica_al = $_POST['pa_al_yy'] . '-' . $_POST['pa_al_mm'] . '-' .$_POST['pa_al_dd'];
		#print_r($_POST);
		$data = array(
			'tipo'			=> $_POST['type'],
			'in_carica_dal' => $in_carica_dal,
			'in_carica_al' 	=> $in_carica_al,
			'nominativo'	=> $_POST['nominativo'],
			'deleghe'		=> $_POST['deleghe'],
			'dettagli'		=> $_POST['informazioni'],
			'is_assessore'	=> ((isset($_POST['is_assessore']) && $_POST['is_assessore']=='y')?'y':'n'),
			'is_consigliere'=> ((isset($_POST['is_consigliere']) && $_POST['is_consigliere']=='y')?'y':'n'),
			'is_presidente'=> ((isset($_POST['is_presidente']) && $_POST['is_presidente']=='y')?'y':'n'),
			'ordine'		=>	$_POST['ordine']
		); 
		$data = apply_filters('do_save_organo', $data);
		
		doSave($tableName, $data, TOSENDIT_PAFACILE_ORGANI_EDIT_HANDLER, true, 'Non è stato possibile salvare l\'organo istituzionale.', false, '', 'setRelazioniTipo');
	}
	
}

function doSaveDelibere(){
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_DELIBERE;
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);
		$data_seduta = $_POST['seduta_yy'] . '-' . $_POST['seduta_mm'] . '-' .$_POST['seduta_dd'];
		//$data_albo = $_POST['albo_yy'] . '-' . $_POST['albo_mm'] . '-' .$_POST['albo_dd'];
		$data = array(
			'tipo'			=> $_POST['type'],
			
			'numero'		=> $_POST['numero'],
			'oggetto'		=> $_POST['oggetto'],
			'descrizione'	=> $_POST['descrizione']
		);
		$data['data_seduta']	= ($data_seduta!='--')?$data_seduta:'';
		//$data['data_albo']		= ($data_albo!='--')?$data_albo:'';
		$data = apply_filters('do_save_delibera', $data);
		
		doSave($tableName, $data, TOSENDIT_PAFACILE_DELIBERE_EDIT_HANDLER, true, 'Delibera non salvata', true, $_POST['type'] . $_POST['numero'] . '-'.$data_seduta.'-');
	}
}

function doSaveDetermine(){
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_DETERMINE;
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);
		$data_adozione = $_POST['data_adozione_yy'] . '-' . $_POST['data_adozione_mm'] . '-' .$_POST['data_adozione_dd'];
		$data = array(
			'id_ufficio'	=> $_POST['id_ufficio'],
			
			'numero'		=> $_POST['numero'],
			'oggetto'		=> $_POST['oggetto'] ,
			'descrizione'	=> $_POST['descrizione']
			
		);
		$data['data_adozione']	= ($data_adozione!='--')?$data_adozione:'';
		$data = apply_filters('do_save_determine', $data);
		doSave($tableName, $data, TOSENDIT_PAFACILE_DETERMINE_EDIT_HANDLER, true, 'Determina non salvata', true, $_POST['numero'] . '-'.$data_adozione.'-');
	}
}

function doSaveIncarico(){
	
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_INCARICHI;
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);

		$data = array(
			'nominativo'		=> $_POST['nominativo'],
			'motivo_incarico'	=> $_POST['motivo_incarico'], 
			'dal'				=> toMySQLDate($_POST['dal_dd'] ,$_POST['dal_mm'], $_POST['dal_yy'], true),
			'al'				=> toMySQLDate($_POST['al_dd'] ,$_POST['al_mm'], $_POST['al_yy'], true),
			'data_pubblicazione'=> date('Y-m-d'),
			'compenso'			=> $_POST['compenso'],
			'provv_rep_gen_nr'	=> $_POST['provv_rep_gen_nr'],
			'provv_rep_gen_del'	=> toMySQLDate($_POST['provv_rep_gen_del_dd'], $_POST['provv_rep_gen_del_mm'], $_POST['provv_rep_gen_del_yy'], true),
			'oggetto_incarico'	=> $_POST['oggetto'],
			'modalita_selezione'=> $_POST['modalita_selezione'],
			'tipo_rapporto'		=> $_POST['tipo_rapporto'],
			'cf_nominativo'		=> $_POST['cf_nominativo']
			
		);
		$data = apply_filters('do_save_incarico', $data);
		doSave($tableName, $data, TOSENDIT_PAFACILE_INCARICHI_PROF_EDIT_HANDLER, true, 'Incarico non salvato', true, $_POST['provv_rep_gen_nr'] . '-'.$_POST['provv_rep_gen_del_yy'] .'-');
	}
}


function doSaveTipoAtto(){
	
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ATTO;
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);

		$data = array(
			'codice'				=> $_POST['codice'],
			'descrizione'			=> $_POST['descrizione'], 
			'durata_pubblicazione'	=> $_POST['durata_pubblicazione'],
			'raggruppamento'		=> $_POST['raggruppamento']
		);
		$data = apply_filters('do_save_tipo_atto', $data);
		doSave($tableName, $data, TOSENDIT_PAFACILE_TIPO_ATTO_EDIT_HANDLER, true, 'Tipo atto non salvato', false);
	}
}

function doSaveTipoOrgano(){
	
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_TIPO_ORGANO;
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);

		$data = array(
			'codice'				=> $_POST['codice'],
			'descrizione'			=> $_POST['descrizione']
		);
		
		$data = apply_filters('do_save_tipo_organo', $data);
		
		doSave($tableName, $data, TOSENDIT_PAFACILE_TIPO_ORGANO_EDIT_HANDLER, true, 'Tipo organo non salvato', false);
	}
}

function doSaveAlboPretorio(){
	global $wpdb, $current_user;
	
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);
		# $data_adozione = $_POST['repertorio_nr'] . '-' . $_POST['data_adozione_mm'] . '-' .$_POST['data_adozione_dd'];
		//$gruppi = toSendItGenericMethods::getUserGroups();
		
		if(isset($_POST['db_numero_registro']) && $_POST['status']==TOSENDIT_PAFACILE_ATTO_PUBBLICATO){
			if( $_POST['db_numero_registro']=='0'){
				if($_POST['numero_registro_calc'] == $_POST['numero_registro']){
					$_POST['numero_registro'] = "-1";
				}
			}
		}else{
			if($_POST['status']==TOSENDIT_PAFACILE_ATTO_BOZZA){
				$_POST['db_numero_registro'] = '';	
				$_POST['numero_registro'] = '0';
			}
		}
		$pubblicata_dal = toMySQLDate($_POST['pubblicata_dal_dd'],$_POST['pubblicata_dal_mm'],$_POST['pubblicata_dal_yy'], true);
		$pubblicata_al = dateAdd('d', $_POST['giorni_pubblicazione'], $pubblicata_dal);
		$data = array(
			'id_ufficio'		=> isset($_POST['id_ufficio'])?$_POST['id_ufficio']:0,
			'numero_registro'	=> $_POST['numero_registro'], 
			'tipo'				=> $_POST['tipo'],
			'repertorio_nr'		=> $_POST['repertorio_nr'],
			'repertorio_data'	=> toMySQLDate($_POST['repertorio_data_dd'] ,$_POST['repertorio_data_mm'], $_POST['repertorio_data_yy'], true),
			'protocollo_nr'		=> $_POST['protocollo_nr'],
			'protocollo_data'	=> toMySQLDate($_POST['protocollo_data_dd'], $_POST['protocollo_data_mm'], $_POST['protocollo_data_yy'], true),
			'fascicolo_nr'		=> $_POST['fascicolo_nr'],
			'fascicolo_data'	=> toMySQLDate($_POST['fascicolo_data_dd'], $_POST['fascicolo_data_mm'], $_POST['fascicolo_data_yy'], true),
			'pubblicata_dal'	=> $pubblicata_dal,
			'pubblicata_al'		=> $pubblicata_al,
			'atto_nr'			=> $_POST['atto_nr'], 
			'atto_data'			=> toMySQLDate($_POST['data_atto_dd'],$_POST['data_atto_mm'],$_POST['data_atto_yy']),
			'provenienza'		=> $_POST['provenienza'],
			'materia'			=> $_POST['materia'],
			'dirigente'			=> $_POST['dirigente'],
			'responsabile'		=> $_POST['responsabile'],
			'oggetto'			=> $_POST['oggetto'],
			'descrizione'		=> $_POST['descrizione'],

			# Since Ver 1.4 (lo status dell'Albo pretorio)
			'status'			=> $_POST['status'],			
			'data_proroga'		=> isset($_POST['data_proroga_dd'])?toMySQLDate($_POST['data_proroga_dd'],$_POST['data_proroga_mm'],$_POST['data_proroga_yy']):'0000-00-00',
			'annullato_il'		=> isset($_POST['annullato_il_dd'])?toMySQLDate($_POST['annullato_il_dd'],$_POST['annullato_il_mm'],$_POST['annullato_il_yy']):'0000-00-00',
			# Since Ver 1.4.1 
			# - i documenti in bozza sono visibili per utente
			# - è stato introdotta la certificazione
			'owner'				=>$current_user->ID,
			'data_certificazione'	=> isset($_POST['data_certificazione_dd'])?toMySQLDate($_POST['data_certificazione_dd'],$_POST['data_certificazione_mm'],$_POST['data_certificazione_yy']):'0000-00-00',
			'tipo_certificazione'	=> isset($_POST['tipo_certificazione'])?$_POST['tipo_certificazione']:null
		
		);
		$data = apply_filters('do_save_albo_pretorio', $data);
		doSave($tableName, $data, TOSENDIT_PAFACILE_ALBO_PRETORIO_EDIT_HANDLER, true, 'Pubblicazione non salvata', true, $_POST['repertorio_nr'] . '-'.$_POST['repertorio_data_yy'] .'-', 'setNumeroRegistroAlbo');
		
	}
}

function doSave_setNumeroRegistroAlbo($id, $tipologia = 0){
	global $wpdb;
	$table =$wpdb->prefix . TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
	$sql = "select numero_registro from $table where id='$id'";
	$numero_registro = $wpdb->get_col($sql);
	$numero_registro = $numero_registro[0];
	# error_log("numero registro è $numero_registro" ,0);
	if($numero_registro == -1){
		
		/*
		sleep(1);
		$sql = "select min(numero_registro) cnt from $table where numero_registro > 0 and id<='$id' and year(pubblicata_dal) =year(now())";
		$nr_min = $wpdb->get_col($sql);
		if(count($nr_min)>0) 	$nr_min = $nr_min[0]; 
			else 				$nr_min = 1;
		$elencoStati = TOSENDIT_PAFACILE_ATTO_PUBBLICATO .','. TOSENDIT_PAFACILE_ATTO_PROROGATO . ','. TOSENDIT_PAFACILE_ATTO_ANNULLATO;
		$sql = "select count(*) cnt from $table where (`status` in( $elencoStati ) or numero_registro = -1 ) and id<='$id' and year(pubblicata_dal) =year(now())";
		$max = $wpdb->get_col($sql);
		if(count($max)>0) 	$max = $max[0];
			else			$max = 1;
		$new = ($nr_min+$max-1);
		$sql = "update $table set numero_registro= $new where id='$id'";
		*/
		
		
		/*
		 * Since ver 2.5.9
		 * Se non blocco la tabella in alcune installazioni il numero di albo portebbe sovrapporsi.
		 */
		$sql = "lock tables wp_pa_albopretorio low_priority write";
		$wpdb->query($sql);
		
		/*
		 * Since ver. 2.5.5
		 */
		/*
		 * Since ver 2.5.9
		 * Per evitare il caching del risultato
		 */
		# $sql = "select max(numero_registro) last_nr from $table where numero_registro>0 and year(pubblicata_dal) = year(now())";
		$sql = "select SQL_NO_CACHE max(numero_registro) last_nr from $table where numero_registro>0 and year(pubblicata_dal) = year(now())";
		
		# error_log($sql);
		$ultimoNumero = $wpdb->get_var($sql);
		
		# error_log($ultimoNumero, 0);
		if(is_null($ultimoNumero)) $ultimoNumero = 0; 
		$ultimoNumero = $ultimoNumero +1;
		$sql = "update $table set numero_registro= $ultimoNumero where id='$id'";
		# error_log($sql, 0);
		$wpdb->query($sql);
		/*
		 * Since ver 2.5.9
		 * - Svuoto la cache DB di Wordpress 
		 * - Se non blocco la tabella in alcune installazioni il numero di albo portebbe sovrapporsi.
		 */
		$wpdb->flush();
		$sql = "unlock tables";
		$wpdb->query($sql);
		
	}
}

function doSaveOrdinanze(){
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORDINANZE;
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);
		$data_adozione = $_POST['data_adozione_yy'] . '-' . $_POST['data_adozione_mm'] . '-' .$_POST['data_adozione_dd'];
		$data = array(
			'id_ufficio'	=> $_POST['id_ufficio'],
			'numero'		=> $_POST['numero'],
			'oggetto'		=> $_POST['oggetto'] ,
			'descrizione'	=> $_POST['descrizione']
			
		);
		$data['data_adozione']	= ($data_adozione!='--')?$data_adozione:'';
		
		$data = apply_filters('do_save_ordinanza', $data);
		doSave($tableName, $data, TOSENDIT_PAFACILE_ORDINANZE_EDIT_HANDLER, true, 'Ordinanza non salvata', true, $_POST['numero'] . '-'.$data_adozione.'-');
	}
}

/**
 * @since 2.5
 */
function doSaveSovvenzione(){
	global $wpdb;
	$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_SOVVENZIONI;
	if(isset($_POST) && count($_POST)>0){
		$_POST = stripslashes_deep($_POST);
		$dataPubblicazione= $_POST['data_pubblicazione_yy'] . '-' . 
						 	$_POST['data_pubblicazione_mm'] . '-' .
						 	$_POST['data_pubblicazione_dd'];
		
		$data = array(
			'id_ufficio'			=> $_POST['id_ufficio'],
			'ragione_sociale'		=> $_POST['ragione_sociale'],
			'partita_iva'			=> $_POST['partita_iva'],
			'codice_fiscale'		=> $_POST['codice_fiscale'],
			'indirizzo'				=> $_POST['indirizzo'],			
			'cap'					=> $_POST['cap'],
			'citta'					=> $_POST['citta'],
			'provincia'				=> $_POST['provincia'],
			'importo'				=> $_POST['importo'],
			'norma'					=> $_POST['norma'],
			'dirigente'				=> $_POST['dirigente'],
			'modo_individuazione'	=> $_POST['modo_individuazione'],
			'data_pubblicazione'	=> $dataPubblicazione,
				
  		);
		
		$data = apply_filters('do_save_sovvenzione', $data);
		
		doSave($tableName, $data, TOSENDIT_PAFACILE_SOVVENZIONI_EDIT_HANDLER, true, 'Sovvenzione non salvata', true, $_POST['numero'] . '-'.$data_adozione.'-');
	}
}


?>