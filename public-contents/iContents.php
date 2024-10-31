<?php
interface iContents{
	public static function mostra($buffer);
	public static function form($params = null);
	public static function elenco($params = null);
	public static function dettagli($id);
}

class PAFacilePublicBaseClass{

	protected static function purgeFilter($filter){
		$tmpFilter = array();
		foreach($filter as $key => $value){
			if($value!=''){
				$tmpFilter[] = $value;
			} 	
		}
		$filter = $tmpFilter;
		return $filter; 
	}  
	
	protected static function purgeKeyArray($filter){
		$tmpFilter = array();
		foreach($filter as $key => $value){
			if(!is_null($value) && $value!=''){
				$tmpFilter[$key] = $value;
			}
		}
		$filter = $tmpFilter;
		return $filter;
	}

	protected static function buildPairValueList($pairs, $type, $output = true){

		$buffer = '';
		
		$pairsContainerTag = apply_filters('pafacile_pairs_container_tag', 'dl');
		$pairsContainerTag = apply_filters('pafacile_'.$type.'_pairs_container_tag', $pairsContainerTag);
		
		$pairContainerTag = apply_filters('pafacile_pair_container_tag', '');	
		$pairContainerTag = apply_filters('pafacile_'.$type.'_pair_container_tag', $pairContainerTag);
		
		$keyTag = apply_filters('pafacile_pair_key_tag', 'dt');
		$keyTag = apply_filters('pafacile_'.$type.'_pair_key_tag', $keyTag);
		
		$dataTag = apply_filters('pafacile_pair_data_tag', 'dd');
		$dataTag = apply_filters('pafacile_'.$type.'_pair_data_tag', $dataTag);
		
		$buffer  =	($pairsContainerTag!='')?"<$pairsContainerTag>":'';
		foreach($pairs as $key => $value){
			
			$buffer .= 	($pairContainerTag!='')?"<$pairContainerTag>":'';
			$buffer .= 		($keyTag!='')?"<$keyTag>":'';
			$buffer .= 			$key;
			$buffer .= 		($keyTag!='')?"</$keyTag>":'';
			$buffer .= 		($dataTag!='')?"<$dataTag>":'';
			$buffer .= 			$value;
			$buffer .= 		($dataTag!='')?"</$dataTag>":'';
			$buffer .= 	($pairContainerTag!='')?"</$pairContainerTag>":'';
		}
		
		$buffer .=	($pairsContainerTag!='')?"</$pairsContainerTag>":'';
		
		if($output)
			echo $buffer;
		else
			return $buffer;
	}
	
	protected static function buildDataFilter($field, $data_da, $data_a){
		
		$filterDa = '';
		$filterA = '';
		
		if($data_da=='0000-00-00'){
			
		}else{
			
			list($year, $month, $day) = preg_split('/\-/',$data_da);
			
			if(preg_match('/[0-9]+\-00\-00/',$data_da)){
				// Ho specificato l'anno ma non il mese e il giorno
				
				$filterDa = "year($field)>= '$year'";
					
			}else if(preg_match('/[0-9]+\-[0-9]+\-00/',$data_da)){
				// Ho specificato l'anno e il mese ma non il giorno
				$filterDa = "(year($field) = '$year' and month($field)>='$month' or (year($field) > '$year'))";
			}else{
				$filterDa = "$field 	>= '$data_da'";
			}
		}
		
		if($data_a=='0000-00-00'){
			
		}else{
			
			list($year, $month, $day) = preg_split('/\-/',$data_a);
			
			if(preg_match('/[0-9]+\-00\-00/',$data_a)){
				// Ho specificato l'anno ma non il mese e il giorno
				
				$filterA = "year($field)<= '$year'";
					
			}else if(preg_match('/[0-9]+\-[0-9]+\-00/',$data_a)){
				// Ho specificato l'anno e il mese ma non il giorno
				$filterA = "(year($field) = '$year' and month($field)<='$month' or (year($field) < '$year'))";
			}else{
				$filterA = "$field 	<= '$data_a'";
			}
		}
		
		$filter = $filterDa;
		if($filter!='' && $filterA!='') $filter .= ' and '; 
		$filter .= $filterA;
		if($filter!='') $filter = "($filter)";
		return $filter;
	}
	

}