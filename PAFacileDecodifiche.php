<?php
class PAFacileDecodifiche{
	
	static function officeNameById($office_id){
		global $wpdb;
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
		return $wpdb->get_var("select nome from $tableName where id=$office_id");
	}

	static function areaByOfficeId($office_id){
		global $wpdb;
		$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
		$rs = $wpdb->get_row("select id, id_ufficio_padre from $tableName where id=$office_id");
		if($rs== null) return 0;
		if($rs->id_ufficio_padre=='0') 
			return $rs->id;
		else 
			return self::areaByOfficeId($rs->id_ufficio_padre);
		
	}

		static function generics($table, $field, $fieldKey, $fieldValue){
			global $wpdb;
			$tableName = $wpdb->prefix . $table;
			return $wpdb->get_var("select $field from $tableName where $fieldKey='$fieldValue'");
			
		}
		static function officeIdFromName($officeName){
			global $wpdb;
			$tableName = $wpdb->prefix . TOSENDIT_PAFACILE_DB_ORGANIGRAMMA;
			return $wpdb->get_var("select id from $tableName where nome='$officeName'");
			
		}
		static function tipoAtto($tipo){
			// Since Ver 1.5
			return self::generics(TOSENDIT_PAFACILE_DB_TIPO_ATTO, 'descrizione', 'codice', $tipo);
		}
		static function tipoBando($tipo){
			$return = '';
			switch($tipo){
				case 'co': $return  = 'Bando di concorso';	break;
				case 'ga': $return  = 'Bando di gara';		break;
				case 'es': $return  = 'Esito';				break;
				case 'ba': $return = 'Altro bando';			break;
				case 'gr': $return = 'Graduatoria';			break;
				case 'pr': $return = 'Proroga';				break;
			}
			
			return $return;
		}
		
		static function tipoDocumento($type){
			switch($type){
				case 'g':	return 'Delibera di giunta';
				case 'c':	return 'Delibera di consiglio';
			}
		}
		
		static function tipoOrgano($type){
			return self::generics(TOSENDIT_PAFACILE_DB_TIPO_ORGANO, 'descrizione', 'codice', $type);
		}
		
		static function elencoTipiOrgano($id, $stringOutput = false){
			global $wpdb;
			$tableOrganiRel = $wpdb->prefix .  TOSENDIT_PAFACILE_DB_ORGANI . '_rel';
			$tableTipiOrgani = $wpdb->prefix .  TOSENDIT_PAFACILE_DB_TIPO_ORGANO;
			
			$sql = "select tpo.codice, tpo.descrizione from $tableOrganiRel tor left join $tableTipiOrgani tpo on tpo.codice = tor.tipo where tor.id_organo='$id' ";
			$rows = $wpdb->get_results($sql);
			
			if($stringOutput){
				$output = '';
				foreach($rows as $row){
					if($output!='') $output .=', ';
					$output .= $row->descrizione;
					
				}
				return $output;
			}else{
				return $rows;
				
			}
		}
			
}