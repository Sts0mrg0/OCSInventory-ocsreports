<?php
/*
 * this page makes it possible to seize the MAC addresses for blacklist
 */
 if( $_SESSION['OCS']["lvluser"] != SADMIN )
	die("FORBIDDEN");
$form_name="blacklist";
printEnTete($l->g(703));
if ($protectedPost['onglet'] == "" or !isset($protectedPost['onglet']))
$protectedPost['onglet']=1;
 //d�finition des onglets
$data_on[1]=$l->g(95);
$data_on[2]=$l->g(36);
$data_on[3]=$l->g(116);
if (isset($protectedPost['enre'])){
	if ($protectedPost['BLACK_CHOICE'] == 1){
		$table="blacklist_macaddresses";
		$field="MACADDRESS";
		$field_value=$protectedPost['ADD_MAC_1'];
		unset($protectedPost['ADD_MAC_1']);
		$i=2;
		while ($i<7){
			if ($protectedPost['ADD_MAC_'.$i] != '')
			$field_value.=":".$protectedPost['ADD_MAC_'.$i];
			unset($protectedPost['ADD_MAC_'.$i]);
			$i++;
		}	
	}else{
		$table="blacklist_serials";
		$field="SERIAL";
		$field_value=$protectedPost['ADD_SERIAL'];
		unset($protectedPost['ADD_SERIAL']);
	}
	if (isset($table)){
		$sql="insert into ".$table." (".$field.") value ('".$field_value."')";
//		//no error
		mysql_query($sql, $_SESSION['OCS']["writeServer"]);
		echo "<br><br><center><font face='Verdana' size=-1 color='green'><b>".$l->g(655)."</b></font></center><br>";
		
	}
}
echo "<form action='' name='".$form_name."' id='".$form_name."' method='POST'>";
onglet($data_on,$form_name,"onglet",3);
	echo "<table ALIGN = 'Center' class='onglet'><tr><td align =center>";
echo "<tr><td align=center>";
if ($protectedPost['onglet'] == 1){
	$table_name="blacklist_macaddresses";	
	$list_fields= array('ID'=>'ID',
						'MACADDRESS'=>'MACADDRESS',
						'SUP'=>'ID',
						//'MODIF'=>'ID',
						'CHECK'=>'ID');
	$list_col_cant_del=$list_fields;
	$default_fields=$list_fields; 
	$tab_options['FILTRE']=array('MACADDRESS'=>'MACADDRESS');
	$tab_options['LBL_POPUP']['SUP']='MACADDRESS';	
}elseif($protectedPost['onglet'] == 2){
	$table_name="blacklist_serials";
	$list_fields= array('ID'=>'ID',
						'SERIAL'=>'SERIAL',
						'SUP'=>'ID',
						//'MODIF'=>'ID',
						'CHECK'=>'ID');
	$list_col_cant_del=$list_fields;
	$default_fields=$list_fields; 
	$tab_options['FILTRE']=array('SERIAL'=>'SERIAL');
	$tab_options['LBL_POPUP']['SUP']='SERIAL';
}elseif ($protectedPost['onglet'] == 3){
	$list_action[1]=$l->g(95);
	$list_action[2]=$l->g(36);
	echo "<tr><td align=center colspan=20>".$l->g(700)." : ".show_modif($list_action,"BLACK_CHOICE",2,$form_name)."</td></tr>";
	if ($protectedPost['BLACK_CHOICE'] == 1){
		$javascript="onKeyPress='return scanTouche(event,/[0-9 a-f A-F]/)' 
		  onkeydown='convertToUpper(this)'
		  onkeyup='convertToUpper(this)' 
		  onblur='convertToUpper(this)'
		  onclick='convertToUpper(this)'";
		$i=1;
		$aff="<tr><td align=center>".$l->g(654)." ";
		while ($i<7){
			$aff.=":".show_modif($protectedPost['ADD_MAC_'.$i],'ADD_MAC_'.$i,0,'',array('MAXLENGTH'=>2,'SIZE'=>3,'JAVASCRIPT'=>$javascript));
			$i++;
		}
		$aff.="</td></tr>";	
				
	}elseif ($protectedPost['BLACK_CHOICE'] == 2){
		$aff="<tr><td align=center>".$l->g(702)." : ".show_modif($protectedPost['ADD_SERIAL'],'ADD_SERIAL',0,'',array('MAXLENGTH'=>100,'SIZE'=>30));	
		$aff.="</td></tr>";	
	}
	
	if (isset($aff)){
		$aff.="<tr><td align=center colspan=20>
			<input class='bouton' name='enre' type='submit' value=".$l->g(114)."></td></tr>";
			echo $aff;		
	}


}
if (isset($list_fields)){
	//cas of delete mac address or serial
	if(isset($protectedPost["SUP_PROF"]) and is_numeric($protectedPost["SUP_PROF"])){
		mysql_query("delete from ".$table_name." where id=".$protectedPost["SUP_PROF"], $_SESSION['OCS']["writeServer"]);
	}
	if (isset($protectedPost['del_check']) and $protectedPost['del_check'] != ''){
		$sql="delete from ".$table_name." where id in (".$protectedPost['del_check'].")";
		mysql_query($sql, $_SESSION['OCS']["writeServer"]);
		$tab_options['CACHE']='RESET';
	}
	//print_r($protectedPost);

	$queryDetails = 'SELECT ';
	foreach ($list_fields as $key=>$value){
		if($key != 'SUP' and $key != 'MODIF' and $key != 'CHECK')
		$queryDetails .= $key.',';		
	} 
	$queryDetails=substr($queryDetails,0,-1);
	$queryDetails .= " FROM ".$table_name;
	tab_req($table_name,$list_fields,$default_fields,$list_col_cant_del,$queryDetails,$form_name,100,$tab_options);
	del_selection($form_name);
}	
echo "</td></tr></table>";
echo "</form>";
?>
