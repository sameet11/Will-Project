<?php
/*  ================================Functions For sql query ================================== */
/******************************************
select_query() 
Just pass needed parameters to this select_query() function & get result from database. We can use join query also.

Parameter :
1) $con - database connection.
2) Column names - provide 'string with comma separate' like "userid, username, category" .
3) Table name - write 'table name' .
4) Where condition - write 'where' conditions with 'and' keyword like "userid=$id" OR "userid=$id and department='developer' ".
5) Order by - add column name with ASC/ DESC like "username" OR "username DESC".
6) Limit - specify limit of records like "0, 1" OR "10, 20" OR "1".
7) Group by - specify column name like "username"

Note : Please don't use this function if you want to apply having clause condition. 
**********************************************/

function select_query($con, $columns_names, $table_name, $where, $orderby, $limit, $group_by)
{ 
	if(trim($where)!="")
	{
		$where_condition=" WHERE ".$where;
	}
	else
	{
		$where_condition="";
	}  
	if(trim($group_by)!="")
	{
		$group_by_condition=" GROUP BY ".$group_by;
	}
	else
	{
		$group_by_condition="";
	}  
	if(trim($orderby)!="")
	{
		$orderby_condition=" ORDER BY ".$orderby;
	}
	else
	{
		$orderby_condition="";
	} 
	if(trim($limit)!="")
	{
		$limit_condition=" LIMIT ".$limit;
	}
	else
	{
		$limit_condition="";
	}  
	//combination of all clause / conditions --------------------------
	$get_select_query="select $columns_names from $table_name $where_condition $group_by_condition $orderby_condition $limit_condition";
	$get_select_query_sql = mysqli_query($con, $get_select_query) or die(mysqli_error($con));
	$select_query_return=array();
	
    if(mysqli_num_rows($get_select_query_sql)>0) 
	{ 
		while ($get_select_query_rows = mysqli_fetch_assoc($get_select_query_sql))
		{
			$select_query_return[] =$get_select_query_rows;
		}
		return  json_encode($select_query_return);
	} else{
		return json_encode($select_query_return);
	}
} 

/**************************
insert_query() 
Just pass needed parameters to this insert_query() function. This function return last inserted id also
 
Parameter :
1) $con - database connection.
2) Column names - provide 'array with comma separate' like array('tickit_number','topic'). //exact column names from table
2) Values - provide 'array with comma separate' like array($_POST['ticket_number'],$_POST['topic']). // variables from GET / POST method. 
3) Table name - write 'table name'. 
**********************/

function insert_query($con, $columns, $values, $table_name) 
{
	if(count($columns)>0 && count($values)>0)
	{
		$columns_in_string = implode(",",$columns); 
		$values_in_string = "'" . implode ( "', '", $values ) . "'"; 
		$insertion_query="insert into $table_name ($columns_in_string) values ($values_in_string) ";
		$insertion_query_sql = mysqli_query($con, $insertion_query) or die(mysqli_error($con));
		
		if ($insertion_query_sql) 
		{ 
			return mysqli_insert_id($con); //return last inserted id
		} 
		else 
		{
			return ""; 
		}
	} 
}
/*****************************************
update_query() - start
// Just pass needed parameters to this update_query() function.

Parameter :
1) $con - database connection.
2) Table name - write 'table name' .
3) Column & value - provide 'string with comma separate' like "generated_by_seen='No', generated_with_seen=''. 
5) Where condition - write 'where' conditions with 'and' keyword like "userid=$id" OR "userid=$id and department='developer' ".
  
**********************/

function update_query($con, $table_name, $columns_values, $where)
{
	if(trim($columns_values)!="")
	{
		if(trim($where)!="")
		{
			$where_condition=" WHERE ".$where;
		}
		else
		{
			$where_condition="";
		}  
		 
		$update_query="update $table_name set $columns_values $where_condition ";
		$update_query_sql = mysqli_query($con, $update_query) or die(mysqli_error($con));
		
		if ($update_query_sql) 
		{ 
			return true;  
		} 
		else 
		{
			return false; 
		} 
	} 
}

/*************************
delete_query()
// Just pass needed parameters to this delete_query() function.

Parameter :
1) $con - database connection.
2) Table name - write 'table name' .
3) Where condition - write 'where' conditions with 'and' keyword like "userid=$id" OR "userid=$id and department='developer' ".
  
**********************/
function delete_query($con, $table_name, $where)
{ 
	if(trim($where)!="")
	{
		$where_condition=" WHERE ".$where;
	}
	else
	{
		$where_condition="";
	}  
	 
    $delete_query="delete from  $table_name  $where_condition ";
	$delete_query_sql = mysqli_query($con, $delete_query) or die(mysqli_error($con));
	
	if ($delete_query_sql) 
	{ 
		return true;  
	} 
	else 
	{
		return false; 
	}  
} 
/*================================Functions For sql query End ================================== */

/*===================================functions used for security=====================================*/
//allow only alphabets and spaces
function StringAlphaSpaceCleaner($data)
{
    //remove space bfore and after
    $data = trim($data);
    //remove slashes
    $data = stripslashes($data);
    $data=(filter_var($data, FILTER_SANITIZE_STRING));
    $data = preg_replace("/[^A-Za-z ]/",'',$data);
    return $data;
}
// allow only alphabets and Numeric value
function StringAlphaNumericCleaner($data)
{
    //remove space bfore and after
    $data = trim($data);
    //remove slashes
    $data = stripslashes($data);
    $data=(filter_var($data, FILTER_SANITIZE_STRING));
    $data = preg_replace("/[^A-Za-z0-9]/",'',$data);
    return $data;
}
//allow String value including some special char.
function StringCleaner($data)
{
    //remove space bfore and after
    $data = trim($data);
    //remove slashes
    $data = stripslashes($data);
    $data=(filter_var($data, FILTER_SANITIZE_STRING));
    return $data;
}
//To SANITIZE email
function EmailCleaner($Email)
{
    $Email = trim($Email);
    $Email=(filter_var($Email, FILTER_SANITIZE_EMAIL));
    return $Email;
}
// allow only Integer value
function NumberCleaner($Number)
{
    $Number = trim($Number);
    $Number=(filter_var($Number, FILTER_SANITIZE_NUMBER_INT));
    $Number=preg_replace("/[^0-9]/",'',$Number);
    return $Number;
}
// allow only Float value
function FloatNumberCleaner($Number)
{
    $Number = trim($Number);
    $float_Number=(filter_var($Number, FILTER_SANITIZE_NUMBER_FLOAT,FILTER_FLAG_ALLOW_FRACTION));
    $float_Number=preg_replace("/[^0-9.]/",'',$float_Number);
    return $float_Number;
}
// To SANITIZE Date value
function DateCleaner($date)
{
    $date = trim($date);
    $date = stripslashes($date);
    $date=(filter_var($date, FILTER_SANITIZE_STRING));
    $date = preg_replace("([^0-9/-])", "", $date);
    return $date;
}

/* 
To use those function only call the function with the parameter
Eg.
<?php
$number=123ABC;
NumberCleaner($number);
?>
Output:
123 
*/
/*===================================functions used for security End=====================================*/
/*================================Functions For Encryption and Decription ================================== */
function encryption($value)
{
    $rand_number=(rand(1000,9999));
    $str = 'abcdefghijklmnopqustuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $rand_char=substr(str_shuffle($str), 0, 4);
    $ascii = "";
    
    for ($i = 0; $i < strlen($rand_char); $i++)
    {
        $ascii .= ord($rand_char[$i]);
    }
    return $final_result=$rand_char."".(($value*$rand_number)+$ascii)."".$rand_number;
}

function decryption($value)
{
    $ascii = "";
    for ($i = 0; $i < strlen(substr($value, 0, 4)); $i++)
    {
        $ascii .= ord(substr($value, 0, 4)[$i]);
    }
return ((substr($value,4, -4))-$ascii)/(substr($value, -4));
}
/*
 To use the functions only call the function with the parameter
Eg.
<?php
$id=38;
$id= encryption ($id);
?>
Output:
cEJP1000049768092
<?php
$id= cEJP1000049768092;
$id= decryption ($id);
?>
Output:
38
 */
 
function signature_enc($val)
{
    $salt_val=$val."kalpak@system@argha";
    $hash=md5($salt_val);
    $key_values = array();
    $key_values[$val]=$hash;
    return $key_values;
}

function signature_dec($REFERER)
{
    $splitval=substr($REFERER,(strripos($REFERER,"/"))+1);
    if($splitval){$arr_val=$_SESSION['sig'][$splitval];}else{$arr_val="";}
    $salt_val=$splitval."kalpak@DM@argha";
    $hash=md5($salt_val);
    if($hash!=$arr_val){header("Location: index.php");die();}//else{$_SESSION['sig']=$key_values[$splitval]="";}
}
/* //signature triggered
$_SESSION['sig']=signeture_enc(substr($_SERVER['REQUEST_URI'],(strripos($_SERVER['REQUEST_URI'],"/"))+1));


//signature authenticates and removed
if(isset($_SERVER['HTTP_REFERER'])){$REFERER=$_SERVER['HTTP_REFERER'];}else{$REFERER="";}
signeture_dec($REFERER);
$_SESSION['sig']=$key_values[$REFERER]=""; */
/*  ===============================Functions For Encryption and Decription END================================== */

function get_display_name($con,$userMaster_id)
{
	$result_fields="display_name";
	$result_table="user_master"; 
	$result_condition= "(user_master_id='$userMaster_id') ";
	return $result_data = json_decode(select_query($con, $result_fields,$result_table,$result_condition,"","" ,""));
}
/******************CHECK OTHER MASTER ACCESS FOR EMPLOYEE*****************/
function check_other_master_access_rights_for_logged_employee($con,$userMaster_id)
{
	$category="employee";
    $field="user_master_id,category";
    $table="user_master";
    $condition="(user_master_id='$userMaster_id') and (category='$category')";
    $data=json_decode(select_query($con, $field,$table,$condition,"","" ,""));

    if($data!='')
    {
    	
		$user_access_fields="user_access_id,user_master_id,department_master_id,designation_master_id,functionality,add_functionality,edit_functionality,delete_functionality,view_functionality,view_all_functionality";
		$user_access_table="user_access"; 
		$where_condition= "(user_master_id='$userMaster_id') and ( (add_functionality=1) or (edit_functionality=1) or (delete_functionality=1) or (view_functionality=1) or (view_all_functionality=1)) ";
		return $useraccess_master_data = json_decode(select_query($con, $user_access_fields,$user_access_table,$where_condition,"","" ,""));
    }
}

/*********** Function For Check master access for logged in employee ***********/
/************ here by passing user id  we can get users master access rights *************/
function check_master_access_rights_for_logged_users($con,$userMaster_id)
{
	$master_Access_fields="master_table_access";
	$master_Access_table="user_master"; 
	$master_Access_condition= "(user_master_id='$userMaster_id') ";
	return $masterAccess_data = json_decode(select_query($con, $master_Access_fields,$master_Access_table,$master_Access_condition,"","" ,""));
}
/********** check other access rights for logged in user ************************/
/*
here, by passing uer id we can fetch add_functionality,
edit_functionality, 
view_functionality 
and delete_functionality access right for logged user
*/
function check_access_rights_for_logged_users($con,$userMaster_id)
{
    $user_access_fields="user_access_id,user_master_id,department_master_id,designation_master_id,functionality,add_functionality,edit_functionality,delete_functionality,view_functionality,view_all_functionality";
	$user_access_table="user_access"; 
	$where_condition= "(user_master_id='$userMaster_id') and ( (add_functionality=1) or (edit_functionality=1) or (delete_functionality=1) or (view_functionality=1) or (view_all_functionality=1)) ";
	return $useraccess_master_data = json_decode(select_query($con, $user_access_fields,$user_access_table,$where_condition,"","" ,""));
}
/********** check other access rights of masters for logged in user ************************/
function check_inner_access_rights_for_logged_users($con,$userMaster_id,$functionlity)
{
	$user_Inner_Access_fields="add_functionality,edit_functionality,delete_functionality,view_functionality,view_all_functionality";
	$user_Inner_Access_table="user_access"; 
	$Inner_Access_Where_condition= "(user_master_id='$userMaster_id') and (functionality=$functionlity)  ";
	return $userInnerAccess_master_data = json_decode(select_query($con, $user_Inner_Access_fields,$user_Inner_Access_table,$Inner_Access_Where_condition,"","" ,""));
}
//************************************ function for project access rights ****************************//
/*
here, by passing user id and functionality id , get access for rights by functionality 
*/
function check_access_for_project($con,$userMaster_id,$functionlity)
{
	$user_Inner_Access_fields="add_functionality,edit_functionality,delete_functionality,view_functionality,view_all_functionality";
	$user_Inner_Access_table="user_access"; 
	$Inner_Access_Where_condition= "(user_master_id='$userMaster_id') and (functionality=$functionlity)  ";
	return $userInnerAccess_master_data = json_decode(select_query($con, $user_Inner_Access_fields,$user_Inner_Access_table,$Inner_Access_Where_condition,"","" ,""));
}
//*******************************************************************************************//
/********** function for check page accessing rights for user by passing userid and  functionality array ************************/
function check_page_access($con, $functionalityName, $page, $userid, $accessibleFunctionalityArray)
{
	if(in_array($functionalityName,$accessibleFunctionalityArray))
	{ 
		$Page_Acces_master_data = json_decode(select_query($con, "functionality,functionality_id","functionality_master","functionality='$functionalityName' " ,"","" ,""));
		$functionality_idDB=$Page_Acces_master_data[0]->functionality_id;
		$check_Inner_Access_Of_Logged_Employee=check_inner_access_rights_for_logged_users($con,$userid,$functionality_idDB);
		if($page=="add" && $check_Inner_Access_Of_Logged_Employee[0]->add_functionality==1)
		{
			return true;
		} 
		else if($page=="edit" && $check_Inner_Access_Of_Logged_Employee[0]->edit_functionality==1)
		{
			return true;
		} 
		else if($page=="view" && $check_Inner_Access_Of_Logged_Employee[0]->view_functionality==1)
		{
			return true;
		}  
		else
		{
			 return false;
		}
		$Page_Acces_master_data = json_decode(select_query($con, "functionality,functionality_id","functionality_master","functionality='$functionalityName' " ,"","" ,""));
	}
	else
	{
		 return false;
	}
}
