<?php
		 
function toSendItPAFacileGetDBStructure(){
	global $wpdb;
	$tableNameOrgani 		= $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANI;
	$tableNameDelibere 		= $wpdb->prefix . TOSENDIT_PAFACILE_DB_DELIBERE;
	$tableNameDetermine		= $wpdb->prefix . TOSENDIT_PAFACILE_DB_DETERMINE;
	$tableNameOrdinanze 	= $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORDINANZE;
	$tableNameBandi 		= $wpdb->prefix . TOSENDIT_PAFACILE_DB_BANDI;
	$tableNameOrganigramma	= $wpdb->prefix. TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
	
	// Since Version 1.1.0
	$tableNameAlboPretorio	= $wpdb->prefix.TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
		# field "status" added in version 1.4

	// Since Version 1.2.0
	$tableNameIncarichi		= $wpdb->prefix.TOSENDIT_PAFACILE_DB_INCARICHI;
	
	$tableAttachs 			= $wpdb->prefix . TOSENDIT_DB_ATTACHS;
	
	// Since Version 1.4.0
	$tableAuditTrail 		= $wpdb->prefix . TOSENDIT_DB_AUDIT_TRAIL;
	
	// Since Version 1.5.0
	$tableNameTipoAtto		= $wpdb->prefix. TOSENDIT_PAFACILE_DB_TIPO_ATTO;
	
	// Since Version 1.6
	$tableNameTipoOrgano		= $wpdb->prefix. TOSENDIT_PAFACILE_DB_TIPO_ORGANO;
	
	
	$tableNameUsersToOrganigramma = $wpdb->prefix . TOSENDIT_PAFACILE_DB_USERS_TO_ORGANIGRAMMA;
	
	// Since Version 2.5
	$tableNameSovvenzioni	= $wpdb->prefix. TOSENDIT_PAFACILE_DB_SOVVENZIONI;
	
	return ("
	
	CREATE TABLE $tableNameTipoAtto (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			codice VARCHAR(10),
			descrizione VARCHAR(150),
			raggruppamento VARCHAR(150),
			durata_pubblicazione mediumint(9),
			PRIMARY KEY  (id)
	);
	
	CREATE TABLE $tableNameTipoOrgano (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			codice VARCHAR(10),
			descrizione VARCHAR(150),
			PRIMARY KEY  (id)
	);
	
	CREATE TABLE {$tableNameOrgani}_rel (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			id_organo mediumint(9),
			tipo VARCHAR(10),
			PRIMARY KEY  (id)
	);
	
	CREATE TABLE $tableAuditTrail (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			user_id mediumint(9) NOT NULL,
			tabella_rif VARCHAR(150),
			id_tab_rif mediumint(9),
			quando DATETIME,
			azione LONGTEXT,
			PRIMARY KEY  (id)
		);
	
	CREATE TABLE $tableNameIncarichi (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			nominativo tinytext NOT NULL,
			motivo_incarico longtext NOT NULL,
			dal date,
			al date,
			data_pubblicazione date,
			compenso VARCHAR(150) NOT NULL,
			provv_rep_gen_nr mediumint(9),
			provv_rep_gen_del date,
			".
			
			// Since V 2.4 - Adempimento delibera CIVIT n.105/2010
			"cf_nominativo varchar(16),
			oggetto_incarico tinytext,
			soggetto_conferente longtext,
			modalita_selezione varchar(250),
			tipo_rapporto varchar(250),
			".
			"PRIMARY KEY  (id)
		);
	
	CREATE TABLE $tableNameAlboPretorio (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			numero_registro mediumint(9),
			tipo VARCHAR(150) NOT NULL,
			repertorio_nr mediumint(9),
			repertorio_data date,
			protocollo_nr mediumint(9),
			protocollo_data date,
			fascicolo_nr mediumint(9),
			fascicolo_data date,
			pubblicata_dal date,
			pubblicata_al date,
			atto_nr mediumint(9),
			atto_data date,
			provenienza tinytext,
			materia tinytext,
			id_ufficio mediumint(9),
			dirigente VARCHAR(150),
			responsabile VARCHAR(150),
			oggetto tinytext NOT NULL,
			descrizione longtext NOT NULL,
			status varchar(2) NOT NULL,
			data_proroga date,
			annullato_il date,
			motivo longtext,
			owner mediumint,
			data_certificazione date,
			tipo_certificazione varchar(1),
		    PRIMARY KEY  (id)
		);
	
	CREATE TABLE $tableNameOrgani (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			tipo SET('a','c','s','v','d','sc'),
			nominativo tinytext NOT NULL,
			deleghe tinytext NOT NULL,
			dettagli longtext NOT NULL,
			in_carica_dal DATE,
			in_carica_al DATE,
			is_assessore SET('y','n'),
			is_consigliere SET('y','n'),
			is_presidente SET('y','n'),
			ordine mediumint(9) NOT NULL DEFAULT '99',
		    PRIMARY KEY  (id)
		);
		
		CREATE TABLE $tableNameDelibere (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			tipo SET('c','g'),
			oggetto tinytext NOT NULL,
			numero mediumint(9) NOT NULL,
			data_seduta DATE,
			data_albo DATE,
			descrizione LONGTEXT,
			PRIMARY KEY  (id)
		);
		
		CREATE TABLE $tableNameDetermine (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			numero mediumint(9) NOT NULL,
			id_ufficio mediumint(9),
			data_adozione DATE,
			oggetto tinytext NOT NULL,
			descrizione LONGTEXT,
			PRIMARY KEY  (id)
		);
		
		CREATE TABLE $tableNameOrdinanze (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			numero mediumint(9) NOT NULL,
			id_ufficio mediumint(9),
			data_adozione DATE,
			oggetto tinytext NOT NULL,
			descrizione LONGTEXT,
			PRIMARY KEY  (id)
		);
		
		CREATE TABLE $tableNameOrganigramma (
		  id mediumint(9) NOT NULL AUTO_INCREMENT,
		  id_ufficio_padre mediumint(9) NOT NULL DEFAULT '0',
		  nome TINYTEXT NOT NULL,
		  email TINYTEXT NOT NULL,
		  pec TINYTEXT NOT NULL,
		  telefono VARCHAR(150) NOT NULL,
		  fax VARCHAR(150) NOT NULL,
		  indirizzo VARCHAR(200) NOT NULL,
		  descrizione LONGTEXT NOT NULL,
		  mostra_su_organigramma SET('y','n'),
		  abilitato_determine SET('y','n'),
		  abilitato_ordinanze SET('y','n'),
		  abilita_figli_determine SET('y','n'),
		  abilita_figli_ordinanze SET('y','n'),
		  ordine mediumint(9) NOT NULL DEFAULT '0',
		  dirigente VARCHAR(200),
		  responsabile VARCHAR(200),
		  mostra_bandi SET('y','n'),
		  mostra_determinazioni SET('y','n'),
		  PRIMARY KEY  (id)
		);
		
		CREATE TABLE $tableNameUsersToOrganigramma (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			id_utente mediumint(9) NOT NULL,
			id_organigramma mediumint(9) NOT NULL,
			PRIMARY KEY  (id)
		);
		CREATE TABLE $tableNameBandi (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			tipo varchar(2),
			id_padre mediumint(9) NOT NULL,
			id_ufficio mediumint(9) NOT NULL,
			oggetto tinytext NOT NULL,
			descrizione longtext NOT NULL,
			data_pubblicazione date NOT NULL,
			data_scadenza datetime NOT NULL,
			data_esito date NOT NULL,
			importo VARCHAR(50),
			annotazioni_importo TINYTEXT,
			procedura VARCHAR(150),
			categoria VARCHAR(150),
			aggiudicatario tinytext NOT NULL,
			".
			// Since V. 2.4.4
			"estremi varchar(50),
			".
			"PRIMARY KEY  (id)
		);
		
		CREATE TABLE $tableNameSovvenzioni (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			ragione_sociale VARCHAR(200),
			partita_iva VARCHAR(11),
			codice_fiscale VARCHAR(16),
			indirizzo VARCHAR(200),
			cap VARCHAR(5),
			citta VARCHAR(60),
			provincia VARCHAR(2),
			importo VARCHAR(50),
			norma VARCHAR(150),
			id_ufficio MEDIUMINT(9),
			dirigente VARCHAR(100),
			modo_individuazione LONGTEXT,
			data_pubblicazione DATETIME,
			PRIMARY KEY (id)
		);
		
		CREATE TABLE $tableAttachs (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			tabella_rif varchar(150) NOT NULL,
			id_tabella_rif mediumint(9) NOT NULL,
			titolo tinytext,
			file_url longtext,
			PRIMARY KEY  (id)
		);
		
		update $tableNameOrgani set ordine = 99 where ordine is null;
		update $tableNameAlboPretorio set `status` = '1' where `status` is null or `status` = '';

	");
}
?>