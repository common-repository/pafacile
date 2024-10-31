<?php

# Spostato nel file tosendit-pa.php
# define('PAFACILE_PLUGIN_BASE_DIRECTORY', basename( dirname(__FILE__) ) );
class PAFacileUpdateManager{
	
	/* Metodi di base del plugin */
	static function installPlugin(){
		global $wpdb;
		/*
		 * Rimossa dipendenza da RoleScoper
		$tableName = $wpdb->prefix . 'groups_rs';
		if($wpdb->get_var("SHOW TABLES LIKE '$tableName'") != $tableName) {
			
			toSendItPAFacile::createMessage("Role Scoper non è installato, PAFacile richiede Role Scoper per funzionare!");
			return false;
		}
		*/
		$installedVersion = get_option( "PAFacile_db_version" );
		
		# Impostata esplicitamente la directory di inclusione. Per risolvere alcuni
		# problemi con le installazioni in locale su XAMPP e WAMP (in ambiente Windows).
		require_once PAFACILE_PLUING_DIRECTORY .'/db.php';

		/* SINCE VERSION 1.3 */
		$tableNameOrgani 		= $wpdb->prefix . 	TOSENDIT_PAFACILE_DB_ORGANI;
		$tableNameOrganiRel 	= $tableNameOrgani . '_rel';
		$tableNameDelibere 		= $wpdb->prefix . 	TOSENDIT_PAFACILE_DB_DELIBERE;
		$tableNameDetermine		= $wpdb->prefix . 	TOSENDIT_PAFACILE_DB_DETERMINE;
		$tableNameOrdinanze 	= $wpdb->prefix . 	TOSENDIT_PAFACILE_DB_ORDINANZE;
		$tableNameBandi 		= $wpdb->prefix . 	TOSENDIT_PAFACILE_DB_BANDI;
		$tableNameOrganigramma	= $wpdb->prefix .	TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
		$tableNameAlboPretorio	= $wpdb->prefix .	TOSENDIT_PAFACILE_DB_ALBO_PRETORIO;
		$tableNameIncarichi		= $wpdb->prefix .	TOSENDIT_PAFACILE_DB_INCARICHI;
		$tableNameTipoAtto 		= $wpdb->prefix .	TOSENDIT_PAFACILE_DB_TIPO_ATTO;
		$tableNameTipoOrgano 	= $wpdb->prefix .	TOSENDIT_PAFACILE_DB_TIPO_ORGANO;
		$tableNameAttachs		= $wpdb->prefix .	TOSENDIT_DB_ATTACHS;
		/* ------------------- */
		
		if(TOSENDIT_PAFACILE_DB_VERSION!=$installedVersion){
			
			$sql = toSendItPAFacileGetDbStructure();
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			$wpdb->show_errors(true);
			dbDelta($sql, true);
			
			// Since Version 1.4.4
			if(TOSENDIT_PAFACILE_VERSION=='1.4.4' && TOSENDIT_PAFACILE_DB_VERSION=='1.3.9'){
				$wpdb->query("ALTER TABLE $tableNameAlboPretorio ADD INDEX `NUM_REG` (`numero_registro` ASC, `id` ASC, `pubblicata_dal` ASC)");
				$wpdb->query("ALTER TABLE $tableNameAlboPretorio ADD INDEX `DEF_NUM_REG` (`status` ASC, `numero_registro` ASC, `id` ASC, `pubblicata_dal` ASC);");
			}
			
			# Bandi, Gare e Concorsi
			$value = $wpdb->get_var("select count(*) from $tableNameTipoAtto");
			if($value==null || $value==0){
				$wpdb->query("insert into $tableNameTipoAtto values (null,'co', 'Bando di Concorso','Bandi, Gare e Concorsi','15')");
				$wpdb->query("insert into $tableNameTipoAtto values (null,'ga', 'Bando di Gara','Bandi, Gare e Concorsi','15')");
				$wpdb->query("insert into $tableNameTipoAtto values (null,'gr', 'Graduatoria','Bandi, Gare e Concorsi','15')");
				$wpdb->query("insert into $tableNameTipoAtto values (null,'es', 'Esito','Bandi, Gare e Concorsi','15')");
				$wpdb->query("insert into $tableNameTipoAtto values (null,'ba', 'Altri bandi','Bandi, Gare e Concorsi','15')");
				
				# Delibere, Determine e ordinanze
				$wpdb->query("insert into $tableNameTipoAtto values (null,'dg', 'Delibera di Giunta','Delibere, Determine e ordinanze','15')");
				$wpdb->query("insert into $tableNameTipoAtto values (null,'dc', 'Delibera di Consiglio','Delibere, Determine e ordinanze','15')");
				$wpdb->query("insert into $tableNameTipoAtto values (null,'dt', 'Determina','Delibere, Determine e ordinanze','15')");
				$wpdb->query("insert into $tableNameTipoAtto values (null,'or', 'Ordinanza','Delibere, Determine e ordinanze','15')");
				
				# Altro
				$wpdb->query("insert into $tableNameTipoAtto values (null,'ma', 'Pubblicazione di matrimonio','Altro','15')");
				$wpdb->query("insert into $tableNameTipoAtto values (null,'pe', 'Permesso di costruire','Altro','15')");
				$wpdb->query("insert into $tableNameTipoAtto values (null,'al', 'Altro','Altro','15')");
			}

			$value = $wpdb->get_var("select count(*) from $tableNameTipoOrgano");
			if($value==null || $value==0){
				$wpdb->query("insert into $tableNameTipoOrgano values(null, 's', 'Sindaco')");
				$wpdb->query("insert into $tableNameTipoOrgano values(null, 'v', 'Vicesindaco')");
				$wpdb->query("insert into $tableNameTipoOrgano values(null, 'sc', 'Segretario comunale')");
				$wpdb->query("insert into $tableNameTipoOrgano values(null, 'd', 'Direttore generale')");
				$wpdb->query("insert into $tableNameTipoOrgano values(null, 'a', 'Assessori')");
				$wpdb->query("insert into $tableNameTipoOrgano values(null, 'c', 'Consiglieri')");
			}
			
			
			$value = $wpdb->get_var("select count(*) from $tableNameOrganiRel");
			if($value==null || $value==0){
				$wpdb->query("insert into $tableNameOrganiRel (tipo, id_organo) select 'a', id from $tableNameOrgani a where a.is_assessore='y' and a.tipo<>'a'");
				$wpdb->query("insert into $tableNameOrganiRel (tipo, id_organo) select 'c', id from $tableNameOrgani a where a.is_consigliere='y' and a.tipo<>'c'");
			}
			
			add_option("PAFacile_dbVersion", TOSENDIT_PAFACILE_DB_VERSION);
			update_option("PAFacile_dbVersion", TOSENDIT_PAFACILE_DB_VERSION);
			
			if(get_option('PAFacile_Credits','')=='') update_option('PAFacile_Credits', 'y');
			
		}

		toSendItGenericMethods::checkForTable($tableNameOrgani);
		toSendItGenericMethods::checkForTable($tableNameBandi);
		toSendItGenericMethods::checkForTable($tableNameDelibere);
		toSendItGenericMethods::checkForTable($tableNameDetermine);
		toSendItGenericMethods::checkForTable($tableNameOrdinanze);
		toSendItGenericMethods::checkForTable($tableNameOrganigramma);
		toSendItGenericMethods::checkForTable($tableNameAlboPretorio);
		toSendItGenericMethods::checkForTable($tableNameIncarichi);
		toSendItGenericMethods::checkForTable($tableNameAttachs);
		
	}
	
}