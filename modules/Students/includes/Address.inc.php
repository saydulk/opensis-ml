<?php
#**************************************************************************
#  openSIS is a free student information system for public and non-public 
#  schools from Open Solutions for Education, Inc. web: www.os4ed.com
#
#  openSIS is  web-based, open source, and comes packed with features that 
#  include student demographic info, scheduling, grade book, attendance, 
#  report cards, eligibility, transcripts, parent portal, 
#  student portal and more.   
#
#  Visit the openSIS web site at http://www.opensis.com to learn more.
#  If you have question regarding this system or the license, please send 
#  an email to info@os4ed.com.
#
#  This program is released under the terms of the GNU General Public License as  
#  published by the Free Software Foundation, version 2 of the License. 
#  See license.txt.
#
#  This program is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  You should have received a copy of the GNU General Public License
#  along with this program.  If not, see <http://www.gnu.org/licenses/>.
#
#***************************************************************************************

include('../../../Redirect_includes.php');
include 'modules/Students/config.inc.php';
if(clean_param($_REQUEST['values'],PARAM_NOTAGS) && ($_POST['values'] || $_REQUEST['ajax']))
{
	if($_REQUEST['values']['EXISTING'])
	{
		if($_REQUEST['values']['EXISTING']['address_id'] && $_REQUEST['address_id']=='old')
		{
			$_REQUEST['address_id'] = $_REQUEST['values']['EXISTING']['address_id'];
			$address_RET = DBGet(DBQuery("SELECT '' FROM students_join_address WHERE ADDRESS_ID='$_REQUEST[address_id]' AND STUDENT_ID='".UserStudentID()."'"));
			if(count($address_RET)==0)
			{
			DBQuery('INSERT INTO students_join_address (STUDENT_ID,ADDRESS_ID) values(\''.UserStudentID().'\',\''.$_REQUEST[address_id].'\')');
			DBQuery('INSERT INTO students_join_people (STUDENT_ID,PERSON_ID,ADDRESS_ID) SELECT DISTINCT ON (PERSON_ID) \''.UserStudentID().'\',PERSON_ID,ADDRESS_ID FROM students_join_people WHERE ADDRESS_ID=\''.$_REQUEST[address_id].'\'');
			}
		}
		elseif($_REQUEST['values']['EXISTING']['person_id'] && $_REQUEST['person_id']=='old')
		{
			$_REQUEST['person_id'] = $_REQUEST['values']['EXISTING']['person_id'];
			$people_RET = DBGet(DBQuery('SELECT \'\' FROM students_join_people WHERE PERSON_ID=\''.$_REQUEST[person_id].'\' AND STUDENT_ID=\''.UserStudentID().'\''));
			if(count($people_RET)==0)
			{
			DBQuery('INSERT INTO students_join_people (STUDENT_ID,ADDRESS_ID,PERSON_ID) values(\''.UserStudentID().'\',\''.$_REQUEST[address_id].'\',\''.$_REQUEST[person_id].'\')');
			}
		}
	}

	if(clean_param($_REQUEST['values']['address'],PARAM_NOTAGS))
	{
	// echo 'sid= '.$_REQUEST['address_id'];
		if($_REQUEST['address_id']!='new')
		{
			$sql = 'UPDATE address SET ';

			foreach($_REQUEST['values']['address'] as $column=>$value)
			{
				if(!is_array($value)){
                                    
                                $value=paramlib_validation($column,trim($value));
                                $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",trim($value))).'\',';}
				else
				{
					$sql .= $column."='||";
					foreach($value as $val)
					{
						if($val)
							$sql .= str_replace("'","''",str_replace('&quot;','"',$val)).'||';
					}
					$sql .= '\',';
				}
			}
			$sql = substr($sql,0,-1) . ' WHERE ADDRESS_ID=\''.$_REQUEST[address_id].'\'';
			DBQuery($sql);
			$query='SELECT ADDRESS_ID FROM 
students_join_address
 WHERE STUDENT_ID=\''.UserStudentID().'\'';
			$a_ID=DBGet(DBQuery($query));
			$a_ID=$a_ID[1]['ADDRESS_ID'];
			if($a_ID == 0)
			{
				$id=DBGet(DBQuery('SELECT ADDRESS_ID  FROM address WHERE STUDENT_ID=\''.UserStudentID().'\''));
				$id=$id[1]['ADDRESS_ID'];
				DBQuery('UPDATE students_join_address SET ADDRESS_ID=\''.$id.'\',RESIDENCE=\''.$_REQUEST['values']['students_join_address']['RESIDENCE'].'\', MAILING=\''.$_REQUEST['values']['students_join_address']['MAILING'].'\',BUS_PICKUP=\''.$_REQUEST['values']['students_join_address']['BUS_PICKUP'].'\', BUS_DROPOFF=\''.$_REQUEST['values']['students_join_address']['BUS_DROPOFF'].'\' WHERE STUDENT_ID=\''.UserStudentID().'\'');
			if($_REQUEST['r4']=='Y' && $_REQUEST['r4']!='N')
			{
			DBQuery('UPDATE address SET MAIL_ADDRESS=\''.$_REQUEST['values']['address']['ADDRESS'].'\',MAIL_STREET=\''.$_REQUEST['values']['address']['STREET'].'\', MAIL_CITY=\''.$_REQUEST['values']['address']['CITY'].'\',MAIL_STATE=\''.$_REQUEST['values']['address']['STATE'].'\', MAIL_ZIPCODE=\''.$_REQUEST['values']['address']['ZIPCODE'].'\' WHERE STUDENT_ID=\''.UserStudentID().'\'');
			}
			if($_REQUEST['r5']=='Y' && $_REQUEST['r5']!='N')
			{
			DBQuery('UPDATE address SET PRIM_ADDRESS=\''.$_REQUEST['values']['address']['ADDRESS'].'\',PRIM_STREET=\''.$_REQUEST['values']['address']['STREET'].'\', PRIM_CITY=\''.$_REQUEST['values']['address']['CITY'].'\',PRIM_STATE=\''.$_REQUEST['values']['address']['STATE'].'\', PRIM_ZIPCODE=\''.$_REQUEST['values']['address']['ZIPCODE'].'\' WHERE STUDENT_ID=\''.UserStudentID().'\'');
			}
			if($_REQUEST['r6']=='Y' && $_REQUEST['r6']!='N')
			{
			DBQuery('UPDATE address SET SEC_ADDRESS=\''.$_REQUEST['values']['address']['ADDRESS'].'\',SEC_STREET=\''.$_REQUEST['values']['address']['STREET'].'\', SEC_CITY=\''.$_REQUEST['values']['address']['CITY'].'\',SEC_STATE=\''.$_REQUEST['values']['address']['STATE'].'\', SEC_ZIPCODE=\''.$_REQUEST['values']['address']['ZIPCODE'].'\' WHERE STUDENT_ID=\''.UserStudentID().'\'');
			}

		  }		
                  if($a_ID != 0)
                  {
                      $flag=false;
                      if($_REQUEST['same_addr']=='Y')
			{
                          
                          $sql = 'UPDATE address SET ';
                         
                          if($_REQUEST['values']['address']['ADDRESS'])
                          {
                              $sql .= 'MAIL_ADDRESS'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',';
                              $flag=true;
		}
                          if($_REQUEST['values']['address']['STREET'])
                          {
                              $sql .= 'MAIL_STREET'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['CITY'])
                          {
                              $sql .= 'MAIL_CITY'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['STATE'])
                          {
                              $sql .= 'MAIL_STATE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['ZIPCODE'])
                          {
                              $sql .= 'MAIL_ZIPCODE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
                              $flag=true;
                          }
                          $sql = substr($sql,0,-1);
                        $sql.=' WHERE STUDENT_ID=\''.UserStudentID().'\'';
                        if($flag)
                            DBQuery($sql);

			}
			if($_REQUEST['prim_addr']=='Y')
			{
                            $sql = 'UPDATE address SET ';
                         
                          if($_REQUEST['values']['address']['ADDRESS'])
                          {
                              $sql .= 'PRIM_ADDRESS'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['STREET'])
                          {
                              $sql .= 'PRIM_STREET'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['CITY'])
                          {
                              $sql .= 'PRIM_CITY'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['STATE'])
                          {
                              $sql .= 'PRIM_STATE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['ZIPCODE'])
                          {
                              $sql .= 'PRIM_ZIPCODE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
                              $flag=true;
                          }
                          $sql = substr($sql,0,-1);
                        $sql.=' WHERE STUDENT_ID=\''.UserStudentID().'\'';
                        if($flag)
                            DBQuery($sql);
			}
			if($_REQUEST['sec_addr']=='Y')
			{
                            $sql = 'UPDATE address SET ';
                         
                          if($_REQUEST['values']['address']['ADDRESS'])
                          {
                              $sql .= 'SEC_ADDRESS'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['STREET'])
                          {
                              $sql .= 'SEC_STREET'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['CITY'])
                          {
                              $sql .= 'SEC_CITY'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['STATE'])
                          {
                              $sql .= 'SEC_STATE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',';
                              $flag=true;
                          }
                          if($_REQUEST['values']['address']['ZIPCODE'])
                          {
                              $sql .= 'SEC_ZIPCODE'.'=\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
                              $flag=true;
                          }
                          $sql = substr($sql,0,-1);
                        $sql.=' WHERE STUDENT_ID=\''.UserStudentID().'\'';
                        if($flag)
                            DBQuery($sql);
			}
                  }
		}
		else
		{
			/*
			$id = DBGet(DBQuery('SELECT '.db_seq_nextval('ADDRESS_SEQ').' as SEQ_ID '.FROM_DUAL));
			$id = $id[1]['SEQ_ID'];

			$sql = "INSERT INTO address ";

			$fields = 'ADDRESS_ID,STUDENT_ID,';
			$values = "'".$id."','".UserStudentID()."',";
			*/

			$sql = 'INSERT INTO address ';

			$fields = 'STUDENT_ID,';
			$values = '\''.UserStudentID().'\',';


######################################## For Same Mailing Address ###################################

		if($_REQUEST['r4']=='Y' && $_REQUEST['r4']!='N')
		{
			$fields .= 'MAIL_ADDRESS,MAIL_STREET,MAIL_CITY,MAIL_STATE,MAIL_ZIPCODE,';
			$values .= '\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
		}

######################################## For Same Mailing Address ###################################
################################ For Same Primary  Emergency Contact ###################################

		if($_REQUEST['r5']=='Y' && $_REQUEST['r5']!='N')
		{
			$fields .= 'PRIM_ADDRESS,PRIM_STREET,PRIM_CITY,PRIM_STATE,PRIM_ZIPCODE,';
			$values .= '\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
		}

############################### For Same Primary  Emergency Contact ####################################

############################# For Same Secondary  Emergency Contact ####################################

		if($_REQUEST['r6']=='Y' && $_REQUEST['r6']!='N')
		{
			$fields .= 'SEC_ADDRESS,SEC_STREET,SEC_CITY,SEC_STATE,SEC_ZIPCODE,';
			$values .= '\''.str_replace("'","''",$_REQUEST['values']['address']['ADDRESS']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STREET']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['CITY']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['STATE']).'\',\''.str_replace("'","''",$_REQUEST['values']['address']['ZIPCODE']).'\',';
		}
###############################For Same Secondary  Emergency Contact ###################################

			$go = 0;
			foreach($_REQUEST['values']['address'] as $column=>$value)
			{
				if($value)
				{
					$fields .= $column.',';
					$values .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';

                       if($go)
			{

				DBQuery($sql);
                               $id=DBGet(DBQuery('select max(address_id) as ADDRESS_ID  from address'));
                               $id=$id[1]['ADDRESS_ID'];
                               DBQuery('INSERT INTO students_join_address (STUDENT_ID,ADDRESS_ID,RESIDENCE,MAILING,BUS_PICKUP,BUS_DROPOFF) values(\''.UserStudentID().'\',\''.$id.'\',\''.$_REQUEST['values']['students_join_address']['RESIDENCE'].'\',\''.$_REQUEST['values']['students_join_address']['MAILING'].'\',\''.$_REQUEST['values']['students_join_address']['BUS_PICKUP'].'\',\''.$_REQUEST['values']['students_join_address']['BUS_DROPOFF'].'\')');
				$_REQUEST['address_id'] = $id;
			}
		}
	}

	if(clean_param($_REQUEST['values']['people'],PARAM_NOTAGS))
	{
		if($_REQUEST['person_id']!='new')
		{
			$sql = 'UPDATE people SET ';

			foreach($_REQUEST['values']['people'] as $column=>$value)
			{
                            $value=paramlib_validation($column,$value);
                            $sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
			}
			$sql = substr($sql,0,-1) . ' WHERE PERSON_ID=\''.$_REQUEST[person_id].'\'';
			DBQuery($sql);
		}
		else
		{
			//$id = DBGet(DBQuery('SELECT '.db_seq_nextval('PEOPLE_SEQ').' as SEQ_ID '.FROM_DUAL));
                        $id = DBGet(DBQuery('SHOW TABLE STATUS LIKE \'people\''));
                        $id[1]['ID']= $id[1]['AUTO_INCREMENT'];
			$id = $id[1]['ID'];

			$sql = 'INSERT INTO people ';

			$fields = '';
			$values = '';

			$go = 0;
			foreach($_REQUEST['values']['people'] as $column=>$value)
			{
                            $value=paramlib_validation($column,$value);
				if($value)
				{
					$fields .= $column.',';
					$values .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
					$go = true;
				}
			}
			$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($values,0,-1) . ')';
			if($go)
			{
				DBQuery($sql);
				DBQuery('INSERT INTO students_join_people (PERSON_ID,STUDENT_ID,ADDRESS_ID,CUSTODY,EMERGENCY) values(\''.$id.'\',\''.UserStudentID().'\',\''.$get_data['ADDRESS_ID'].'\',\''.str_replace("'","''",$_REQUEST['values']['students_join_people']['CUSTODY']).'\',\''.str_replace("'","''",$_REQUEST['values']['students_join_people']['EMERGENCY']).'\')');
				$_REQUEST['person_id'] = $id;
			}
		}
	}

	if(clean_param($_REQUEST['values']['people_join_contacts'],PARAM_NOTAGS))
	{
		foreach($_REQUEST['values']['people_join_contacts'] as $id=>$values)
		{
			if($id!='new')
			{
				$sql = 'UPDATE people_join_contacts SET ';

				foreach($values as $column=>$value)
				{
					$sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
				}
				$sql = substr($sql,0,-1) . ' WHERE ID=\''.$id.'\'';
				DBQuery($sql);
			}
			else
			{
				if($info_apd || $values['TITLE'] && $values['TITLE']!='Example Phone' && $values['VALUE'] && $values['VALUE']!='(xxx) xxx-xxxx')
				{
					$sql = 'INSERT INTO people_join_contacts ';

					$fields = 'PERSON_ID,';
					$vals = '\''.$_REQUEST[person_id].'\',';

					$go = 0;
					foreach($values as $column=>$value)
					{
						if($value)
						{
							$fields .= $column.',';
							$vals .= '\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
							$go = true;
						}
					}
					$sql .= '(' . substr($fields,0,-1) . ') values(' . substr($vals,0,-1) . ')';
					if($go)
						DBQuery($sql);
				}
			}
		}
	}

	if($_REQUEST['values']['students_join_people'] && $_REQUEST['person_id']!='new')
	{
		$sql = 'UPDATE students_join_people SET ';

		foreach($_REQUEST['values']['students_join_people'] as $column=>$value)
		{ 
                        $value=paramlib_validation($column,$value);
			$sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
		}
		$sql = substr($sql,0,-1) . ' WHERE PERSON_ID=\''.$_REQUEST[person_id].'\' AND STUDENT_ID=\''.UserStudentID().'\'';
		DBQuery($sql);
	}

	if($_REQUEST['values']['students_join_address'] && $_REQUEST['address_id']!='new')
	{
		$sql = 'UPDATE students_join_address SET ';

		foreach($_REQUEST['values']['students_join_address'] as $column=>$value)
		{
			$sql .= $column.'=\''.str_replace("'","''",str_replace("\'","''",$value)).'\',';
		}
		$sql = substr($sql,0,-1) . ' WHERE ADDRESS_ID=\''.$_REQUEST[address_id].'\' AND STUDENT_ID=\''.UserStudentID().'\'';
		DBQuery($sql);
	}
############################Student Join People Address Same as ########################################
if($_REQUEST['r7']=='Y' && $_REQUEST['r7']!='N' && isset($_REQUEST['person_id']))
	{
		$get_data = DBGet(DBQuery("SELECT ADDRESS_ID,ADDRESS,STREET,CITY,STATE,ZIPCODE,BUS_NO,BUS_PICKUP,BUS_DROPOFF FROM address WHERE STUDENT_ID='".UserStudentID()."'"));
		$get_data = $get_data[1];
		DBQuery('UPDATE students_join_people SET ADDN_ADDRESS=\''.str_replace("'","''",$get_data['ADDRESS']).'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
		DBQuery('UPDATE students_join_people SET ADDN_STREET=\''.str_replace("'","''",$get_data['STREET']).'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
		DBQuery('UPDATE students_join_people SET ADDN_CITY=\''.str_replace("'","''",$get_data['CITY']).'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
		DBQuery('UPDATE students_join_people SET ADDN_STATE=\''.str_replace("'","''",$get_data['STATE']).'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
		DBQuery('UPDATE students_join_people SET ADDN_ZIPCODE=\''.$get_data['ZIPCODE'].'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
		DBQuery('UPDATE students_join_people SET ADDN_BUS_PICKUP=\''.$get_data['BUS_PICKUP'].'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
		DBQuery('UPDATE students_join_people SET ADDN_BUS_DROPOFF=\''.$get_data['BUS_DROPOFF'].'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
		DBQuery('UPDATE students_join_people SET ADDN_BUSNO=\''.$get_data['BUS_NO'].'\' WHERE PERSON_ID=\''.$_REQUEST['person_id'].'\'');
	}
############################Student Join People Address Same as ########################################
	unset($_REQUEST['modfunc']);
	unset($_REQUEST['values']);
}

if(clean_param($_REQUEST['modfunc'],PARAM_ALPHAMOD)=='delete')
{
	if($_REQUEST['contact_id'])
	{
		if(DeletePrompt('contact information'))
		{
			DBQuery('DELETE FROM people_join_contacts WHERE ID=\''.$_REQUEST[contact_id].'\'');
			unset($_REQUEST['modfunc']);
		}
	}
	elseif($_REQUEST['person_id'])
	{
		if(DeletePrompt('contact'))
		{
			DBQuery('DELETE FROM students_join_people WHERE PERSON_ID=\''.$_REQUEST[person_id].'\' AND STUDENT_ID=\''.UserStudentID().'\'');
			if(count(DBGet(DBQuery('SELECT STUDENT_ID FROM students_join_people WHERE PERSON_ID=\''.$_REQUEST[person_id].'\'')))==0)
			{
				DBQuery('DELETE FROM people WHERE PERSON_ID=\''.$_REQUEST[person_id].'\'');
				DBQuery('DELETE FROM people_join_contacts WHERE PERSON_ID=\''.$_REQUEST[person_id].'\'');
			}
			unset($_REQUEST['modfunc']);
			unset($_REQUEST['person_id']);
			if(!isset($_REQUEST['address_id']))
			{
				$stu_ad_id = DBGet(DBQuery('SELECT ADDRESS_ID FROM address WHERE STUDENT_ID=\''.UserStudentID().'\''));
				$stu_ad_id = $stu_ad_id[1]['ADDRESS_ID'];
				if(count($stu_ad_id))
					$_REQUEST['address_id']=$stu_ad_id;
				else
					$_REQUEST['address_id']='new';
			}
		}
	}
	elseif($_REQUEST['address_id'])
	{
		if(DeletePrompt('address'))
		{
			DBQuery('UPDATE students_join_people SET ADDRESS_ID=\'0\' WHERE STUDENT_ID=\''.UserStudentID().'\' AND ADDRESS_ID=\''.$_REQUEST[address_id].'\'');
			DBQuery('DELETE FROM students_join_address WHERE STUDENT_ID=\''.UserStudentID().'\' AND ADDRESS_ID=\''.$_REQUEST['address_id'].'\'');
			if(count(DBGet(DBQuery('SELECT STUDENT_ID FROM students_join_address WHERE ADDRESS_ID=\''.$_REQUEST['address_id'].'\'')))==0)
				DBQuery('DELETE FROM address WHERE ADDRESS_ID=\''.$_REQUEST['address_id'].'\'');
			unset($_REQUEST['modfunc']);
			$_REQUEST['address_id']='new';
		}
	}
}

if(!$_REQUEST['modfunc'])
{
	$addresses_RET = DBGet(DBQuery('SELECT a.ADDRESS_ID, sjp.STUDENT_RELATION,a.ADDRESS,a.STREET,a.CITY,a.STATE,a.ZIPCODE,a.BUS_NO,a.BUS_PICKUP,a.BUS_DROPOFF,a.MAIL_ADDRESS,a.MAIL_STREET,a.MAIL_CITY,a.MAIL_STATE,a.MAIL_ZIPCODE,a.PRIM_STUDENT_RELATION,a.PRI_FIRST_NAME,a.PRI_LAST_NAME,a.HOME_PHONE,a.WORK_PHONE,a.MOBILE_PHONE,a.EMAIL,a.PRIM_CUSTODY,a.PRIM_ADDRESS,a.PRIM_STREET,a.PRIM_CITY,a.PRIM_STATE,a.PRIM_ZIPCODE,a.SEC_STUDENT_RELATION,a.SEC_FIRST_NAME,a.SEC_LAST_NAME,a.SEC_HOME_PHONE,a.SEC_WORK_PHONE,a.SEC_MOBILE_PHONE,a.SEC_EMAIL,a.SEC_CUSTODY,a.SEC_ADDRESS,a.SEC_STREET,a.SEC_CITY,a.SEC_STATE,a.SEC_ZIPCODE,  sjp.CUSTODY,sja.MAILING,sja.RESIDENCE FROM address a,students_join_address sja,students_join_people sjp WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=\''.UserStudentID().'\' AND a.ADDRESS_ID=sjp.ADDRESS_ID AND sjp.STUDENT_ID=sja.STUDENT_ID' .
				  ' UNION SELECT a.ADDRESS_ID,\'\' AS STUDENT_RELATION,a.ADDRESS,a.STREET,a.CITY,a.STATE,a.ZIPCODE,a.BUS_NO,a.BUS_PICKUP,a.BUS_DROPOFF,a.MAIL_ADDRESS,a.MAIL_STREET,a.MAIL_CITY,a.MAIL_STATE,a.MAIL_ZIPCODE,a.PRIM_STUDENT_RELATION,a.PRI_FIRST_NAME,a.PRI_LAST_NAME,a.HOME_PHONE,a.WORK_PHONE,a.MOBILE_PHONE,a.EMAIL,a.PRIM_CUSTODY,a.PRIM_ADDRESS,a.PRIM_STREET,a.PRIM_CITY,a.PRIM_STATE,a.PRIM_ZIPCODE,a.SEC_STUDENT_RELATION,a.SEC_FIRST_NAME,a.SEC_LAST_NAME,a.SEC_HOME_PHONE,a.SEC_WORK_PHONE,a.SEC_MOBILE_PHONE,a.SEC_EMAIL,a.SEC_CUSTODY,a.SEC_ADDRESS,a.SEC_STREET,a.SEC_CITY,a.SEC_STATE,a.SEC_ZIPCODE,a.PRIM_CUSTODY AS CUSTODY,sja.MAILING,sja.RESIDENCE FROM address a,students_join_address sja WHERE a.ADDRESS_ID=sja.ADDRESS_ID AND sja.STUDENT_ID=\''.UserStudentID().'\' AND NOT EXISTS (SELECT \'\' FROM students_join_people sjp WHERE sjp.STUDENT_ID=sja.STUDENT_ID AND sjp.ADDRESS_ID=a.ADDRESS_ID) ORDER BY CUSTODY ASC,STUDENT_RELATION'),array(),array('ADDRESS_ID'));
	if(count($addresses_RET)==1 && $_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='old' && $_REQUEST['address_id']!='0')
		$_REQUEST['address_id'] = key($addresses_RET);

	echo '<TABLE border=0><TR><TD valign=top>'; // table 1
	echo '<TABLE border=0><TR><TD valign=top>'; // table 2
	echo '<TABLE border=0 cellpadding=0 cellspacing=0>'; // table 3
	if(count($addresses_RET)>0 || $_REQUEST['address_id']=='new' || $_REQUEST['address_id']=='0')
	{
		$i = 1;
		if(!isset($_REQUEST['address_id']))
			$_REQUEST['address_id'] = key($addresses_RET);

		if(count($addresses_RET))
		{
			foreach($addresses_RET as $address_id=>$addresses)
			{
				echo '<TR>';

				// find other students associated with this address
				$xstudents = DBGet(DBQuery('SELECT s.STUDENT_ID,CONCAT(s.FIRST_NAME,\' \',s.LAST_NAME) AS FULL_NAME,RESIDENCE,BUS_PICKUP,BUS_DROPOFF,MAILING FROM students s,students_join_address sja WHERE s.STUDENT_ID=sja.STUDENT_ID AND sja.ADDRESS_ID=\''.$address_id.'\' AND sja.STUDENT_ID!=\''.UserStudentID().'\''));
				if(count($xstudents))
				{
					$warning = ''._('Other students associated with this address:').'<BR>';
					foreach($xstudents as $xstudent)
					{
						$ximages = '';
						if($xstudent['RESIDENCE']=='Y')
							$ximages .= ' <IMG SRC=assets/house_button.gif>';
						if($xstudent['BUS_PICKUP']=='Y' || $xstudent['BUS_DROPOFF']=='Y')
							$ximages .= ' <IMG SRC=assets/bus_button.gif>';
						if($xstudent['MAILING']=='Y')
							$ximages .= ' <IMG SRC=assets/mailbox_button.gif>';
						$warning .= '<b>'.str_replace(array("'",'"'),array('&#39;','&rdquo;'),$xstudent['FULL_NAME']).'</b>'.$ximages.'<BR>';
					}
					echo '<TD>'.button('warning','','# onMouseOver=\'stm(["'._('Warning').'","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'').'</TD>';
				}
				else
					echo '<TD></TD>';

				$relation_list = '';
				foreach($addresses as $address)
					$relation_list .= ($address['STUDENT_RELATION']&&strpos($address['STUDENT_RELATION'].', ',$relation_list)==false?$address['STUDENT_RELATION']:'---').', ';
				$address = $addresses[1];
				$relation_list = substr($relation_list,0,-2);

				$images = '';
				if($address['RESIDENCE']=='Y')
					#$images .= ' <IMG SRC=assets/house_button.gif>';
				if($address['BUS_PICKUP']=='Y' || $address['BUS_DROPOFF']=='Y')
					#$images .= ' <IMG SRC=assets/bus_button.gif>';
				if($address['MAILING']=='Y')
					#$images .= ' <IMG SRC=assets/mailbox_button.gif>';
				echo '<TD colspan=2 style="border:0; border-style: none none solid none;"><B>'.$relation_list.'</B>'.($relation_list&&$images?'<BR>':'').$images.'</TD>';

				echo '</TR>';

				$style = '';
				if($i!=count($addresses_RET))
					$style = ' style="border:1; border-style: none none dashed none;"';
				elseif($i!=1)
					$style = ' style="border:1; border-style: dashed none none none;"';
				$style .= ' ';

				if($address_id==$_REQUEST['address_id'] && $_REQUEST['address_id']!='0' && $_REQUEST['address_id']!='new')
					$this_address = $address;

				$i++;
				$link = 'onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$address['ADDRESS_ID'].'\';"';
				echo '</TD>';
				echo '<TD></TD>';
				echo '</TR>';
			}
			echo '<TR><TD colspan=3 height=40></TD></TR>';
		}
	}
	else
		echo '';
		
	############################################################################################
		
		$style = '';
		if($_REQUEST['person_id']=='new')
		{
			if($_REQUEST['address_id']!='new')
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'\';" ><TD>';
			else
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id=new\';" ><TD>';
			echo '<A style="cursor:pointer"><b>'._('Student\'s Address').' </b></A>';
		}
		else
		{
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id=$_REQUEST[address_id]\';" onmouseover=\'this.style.color="white";\'><TD>';
			if($_REQUEST['person_id']==$contact['PERSON_ID'])
			echo '<A style="cursor:pointer;color:#FF0000"><b>'._('Student').''._("'").''._('s ').''.''._('Address').' </b></A>';
			elseif($_REQUEST['person_id']!=$contact['PERSON_ID'])
			echo '<A style="cursor:pointer"><b>'._("Student's Address").' </b></A>';
			else
			echo '<A style="cursor:pointer;color:#FF0000"><b>'._('Student\'s Address').' </b></A>';
		}
		echo '</TD>';
		echo '<TD><A><IMG SRC=assets/arrow_right.gif></A></TD>';
		echo '</TR><tr><td colspan=2 class=break></td></tr>';
			
			
			$contacts_RET = DBGet(DBQuery('SELECT p.PERSON_ID,p.FIRST_NAME,p.MIDDLE_NAME,p.LAST_NAME,sjp.ADDN_HOME_PHONE,sjp.ADDN_WORK_PHONE,sjp.ADDN_MOBILE_PHONE,sjp.ADDN_EMAIL,sjp.CUSTODY,sjp.ADDN_ADDRESS,sjp.ADDN_BUS_PICKUP,sjp.ADDN_BUS_DROPOFF,sjp.ADDN_BUSNO,sjp.ADDN_STREET,sjp.ADDN_CITY,sjp.ADDN_STATE,sjp.ADDN_ZIPCODE,sjp.EMERGENCY,sjp.STUDENT_RELATION FROM people p,students_join_people sjp WHERE p.PERSON_ID=sjp.PERSON_ID AND sjp.STUDENT_ID=\''.UserStudentID().'\' ORDER BY sjp.STUDENT_RELATION'));
			$i = 1;
			if(count($contacts_RET))
			{
				foreach($contacts_RET as $contact)
				{
					$THIS_RET = $contact;
					if($contact['PERSON_ID']==$_REQUEST['person_id'])
						$this_contact = $contact;
					$style .= ' ';

					$i++;
					$link = 'onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id='.$contact['PERSON_ID'].'&con_info=old\';"';
					if(AllowEdit())
						$remove_button = button('remove','',"Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&address_id=$_REQUEST[address_id]&person_id=$contact[PERSON_ID]",20);
					else
						$remove_button = '';
					if($_REQUEST['person_id']==$contact['PERSON_ID'])
						echo '<TR><td><table border=0><TR><TD width=20 align=right'.$style.'>'.$remove_button.'</TD><TD '.$link.' '.$style.'>';
					else
						echo '<TR><td><table border=0><TR><TD width=20 align=right'.$style.'>'.$remove_button.'</TD><TD '.$link.' '.$style.' style=white-space:nowrap>';

					$images = '';

					// find other students associated with this person
					$xstudents = DBGet(DBQuery('SELECT s.STUDENT_ID,CONCAT(s.FIRST_NAME,\' \',s.LAST_NAME) AS FULL_NAME,STUDENT_RELATION,CUSTODY,EMERGENCY FROM students s,students_join_people sjp WHERE s.STUDENT_ID=sjp.STUDENT_ID AND sjp.PERSON_ID=\''.$contact['PERSON_ID'].'\' AND sjp.STUDENT_ID!=\''.UserStudentID().'\''));
					if(count($xstudents))
					{
						$warning = ''._('Other students associated with this person:').'<BR>';
						foreach($xstudents as $xstudent)
						{
							$ximages = '';
							if($xstudent['CUSTODY']=='Y')
								$ximages .= ' <IMG SRC=assets/gavel_button.gif>';
							if($xstudent['EMERGENCY']=='Y')
								$ximages .= ' <IMG SRC=assets/emergency_button.gif>';
							$warning .= '<b>'.str_replace(array("'",'"'),array('&#39;','&rdquo;'),$xstudent['FULL_NAME']).'</b> ('.($xstudent['STUDENT_RELATION']?str_replace(array("'",'"'),array('&#39;','&rdquo;'),$xstudent['STUDENT_RELATION']):'---').')'.$ximages.'<BR>';
						}
						$images .= ' '.button('warning','','# onMouseOver=\'stm(["'._('Warning').'","'.$warning.'"],["white","#006699","","","",,"black","#e8e8ff","","","",,,,2,"#006699",2,,,,,"",,,,]);\' onMouseOut=\'htm()\'');
					}

					if($contact['CUSTODY']=='Y')
						$images .= ' <IMG SRC=assets/gavel_button.gif>';
					if($contact['EMERGENCY']=='Y')
						$images .= ' <IMG SRC=assets/emergency_button.gif>';
if ($_REQUEST['person_id']==$contact['PERSON_ID']) {
					echo '<A style="cursor:pointer; font-weight:bold;color:#ff0000" >'.($contact['STUDENT_RELATION']?$contact['STUDENT_RELATION']:'---').''.$images.'</A>';
					} else {
					echo '<A style="cursor:pointer; font-weight:bold;" >'.($contact['STUDENT_RELATION']?$contact['STUDENT_RELATION']:'---').''.$images.'</A>';
					}
					echo '</TD>';
					echo '<TD valign=middle align=right> &nbsp; <A style="cursor: pointer;"><IMG SRC=assets/arrow_right.gif></A></TD>';
					echo '</TR></table></td></tr>';
				}
			}
	############################################################################################	
	
	// New Address
	if(AllowEdit())
	{
		if($_REQUEST['address_id']!=='new' && $_REQUEST['address_id']!=='old')
		{

			echo '<TABLE width=100%><TR><TD>';
			if($_REQUEST['address_id']==0)
				echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%>';
			else
				echo '<TABLE border=0 cellpadding=0 cellspacing=0 width=100%>';
			// New Contact
			if(AllowEdit())
			{
				$style = 'class=break';
			}

			echo '</TABLE>';
		}

		if(clean_param($_REQUEST['person_id'],PARAM_ALPHAMOD)=='new')
		{
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id=new&con_info=old\';" onmouseover=\'this.style.color="white";\' ><TD>';
			echo '<A style="cursor: pointer;color:#FF0000"><b>'._('Add New Contact').'</b></A>';
		}
		else
		{
			echo '<TR onclick="document.location.href=\'Modules.php?modname='.$_REQUEST['modname'].'&include='.$_REQUEST['include'].'&address_id='.$_REQUEST['address_id'].'&person_id=new&con_info=old\';" onmouseover=\'this.style.color="white";\' ><TD>';
			echo '<A style="cursor: pointer;"><b>'._('Add New Contact').'</b></A>';
		}
		echo '</TD>';
		echo '<TD><IMG SRC=assets/arrow_right.gif></TD>';
		echo '</TR>';

	}
	echo '</TABLE>';
	echo '</TD>';
	echo '<TD class=vbreak>&nbsp;</TD><TD valign=top>';

	if(isset($_REQUEST['address_id']) && $_REQUEST['con_info']!='old')
	{
		echo "<INPUT type=hidden name=address_id value=$_REQUEST[address_id]>";

		if($_REQUEST['address_id']!='0' && $_REQUEST['address_id']!=='old')
		{
			$query='SELECT ADDRESS_ID FROM students_join_address WHERE STUDENT_ID=\''.UserStudentID().'\'';
			$a_ID=DBGet(DBQuery($query));
			$a_ID=$a_ID[1]['ADDRESS_ID'];

			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
				$size = true;
			else
				$size = false;

			$city_options = _makeAutoSelect('CITY','address',array(array('CITY'=>$this_address['CITY']),array('CITY'=>$this_address['MAIL_CITY'])),$city_options);
			$state_options = _makeAutoSelect('STATE','address',array(array('STATE'=>$this_address['STATE']),array('STATE'=>$this_address['MAIL_STATE'])),$state_options);
			$zip_options = _makeAutoSelect('ZIPCODE','address',array(array('ZIPCODE'=>$this_address['ZIPCODE']),array('ZIPCODE'=>$this_address['MAIL_ZIPCODE'])),$zip_options);

			echo '<TABLE width=100%><TR><TD>'; // open 3a
			echo '<FIELDSET><LEGEND><FONT color=gray>'._('Student').''._("'").''._('s ').''._('Home').' '._('Address').'</FONT></LEGEND><TABLE width=100%>';
			echo '<TR><td><span class=red>*</span>'._('Address Line 1').'</td><td>:</td><TD style=\"white-space:nowrap\"><table cellspacing=0 cellpadding=0 cellspacing=0 cellpadding=0 border=0><tr><td>'.TextInput($this_address['ADDRESS'],'values[address][ADDRESS]','','class=cell_medium').'</td><td>';
			if($_REQUEST['address_id']!='0')
			{
				$display_address = urlencode($this_address['ADDRESS'].', '.($this_address['CITY']?' '.$this_address['CITY'].', ':'').$this_address['STATE'].($this_address['ZIPCODE']?' '.$this_address['ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>'._('Map it').'</A>';
			}
			echo '</td></tr></table></TD></tr>';
			echo '<TR><td>'._('Address Line 2').'</td><td>:</td><TD>'.TextInput($this_address['STREET'],'values[address][STREET]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>'._('*').'</span>'._('City').'</td><td>:</td><TD>'.TextInput($this_address['CITY'],'values[address][CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>'._('*').'</span>'._('State').'</td><td>:</td><TD>'.TextInput($this_address['STATE'],'values[address][STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td><span class=red>'._('*').'</span>'._('Zip').('/')._('Postal Code').'</td><td>:</td><TD>'.TextInput($this_address['ZIPCODE'],'values[address][ZIPCODE]','','class=cell_medium').'</TD></tr>';
			echo '<tr><TD>'._('School Bus Pick-up').'</td><td>:</td><td>'.CheckboxInput($this_address['BUS_PICKUP'],'values[address][BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
			echo '<TR><TD>'._('School Bus Drop-off').'</td><td>:</td><td>'.CheckboxInput($this_address['BUS_DROPOFF'],'values[address][BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
			echo '<TR><td>'._('Bus No').'</td><td>:</td><td>'.TextInput($this_address['BUS_NO'],'values[address][BUS_NO]','','class=cell_small').'</TD></tr>';
			echo '</TABLE></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; //close 3a

			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			{
				$new = true;
				$this_address['RESIDENCE'] = 'Y';
				$this_address['MAILING'] = 'Y';
				if($use_bus)
				{
					$this_address['BUS_PICKUP'] = 'Y';
					$this_address['BUS_DROPOFF'] = 'Y';
										
				}
			}
			echo '<TABLE border=0 width=100%><TR><TD>'; //open 3b
			echo '<FIELDSET><LEGEND><FONT color=gray>'._("Student")._("'s")._(' ')._("Mailing Address").'</FONT></LEGEND>';
			
/*			$query="SELECT ADDRESS_ID FROM students_join_address WHERE STUDENT_ID='".UserStudentID()."'";
			$a_ID=DBGet(DBQuery($query));
			$a_ID=$a_ID[1]['ADDRESS_ID'];
*/			//if($_REQUEST['address_id']=='new')
                        $s_mail_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM address WHERE ADDRESS_ID=\''.$a_ID.'\' AND ADDRESS=PRIM_ADDRESS AND MAIL_CITY=CITY AND MAIL_STATE=STATE AND MAIL_ZIPCODE=ZIPCODE'));
                        if($s_mail_address[1]['TOTAL']!=0)
                           $m_checked=" CHECKED=CHECKED ";
                        else
                            $m_checked=" ";
                        if($a_ID!=0)
                            echo '<div id="check_addr"><input type="checkbox" '.$m_checked.' id="same_addr" name="same_addr" value="Y">&nbsp;'._('Same as Home Address').' &nbsp;</div><br>';
			if($a_ID==0)
			echo '<table><TR><TD><span class=red>'._('*').'</span><input type="radio" id="r4" name="r4" value="Y" onClick="hidediv();" checked>&nbsp;'._('Same as Home Address').' &nbsp;&nbsp; <input type="radio" id="r4" name="r4" value="N" onClick="showdiv();">&nbsp;'._('Add New Address').'</TD></TR></TABLE>'; 
			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<div id="hideShow" style="display:none">';
			else
			echo '<div id="hideShow">';
			echo '<TABLE>';
			echo '<TR><td style=width:120px>'._('Address Line 1').'</td><td>:</td><TD>'.TextInput($this_address['MAIL_ADDRESS'],'values[address][MAIL_ADDRESS]','','class=cell_medium').'</TD>';
			echo '<TR><td>'._('Address Line 2').'</td><td>:</td><TD>'.TextInput($this_address['MAIL_STREET'],'values[address][MAIL_STREET]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('City').'</td><td>:</td><TD>'.TextInput($this_address['MAIL_CITY'],'values[address][MAIL_CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('State').'</td><td>:</td><TD>'.TextInput($this_address['MAIL_STATE'],'values[address][MAIL_STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Zip/Postal Code').'</td><td>:</td><TD>'.TextInput($this_address['MAIL_ZIPCODE'],'values[address][MAIL_ZIPCODE]','','class=cell_medium').'</TD></tr>';
			
			echo '</TABLE>';
			echo '</div>';

			echo '</FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; // close 3b
			
			
			echo '<TABLE border=0 width=100%><TR><TD>'; //open 3c
			echo '<FIELDSET><LEGEND><FONT color=gray>'._('Primary Emergency Contact').'</FONT></LEGEND><TABLE width=100%><tr><td>';
			echo '<table border=0 width=100%>';
                       
                        $prim_relation_options = _makeAutoSelect('PRIM_STUDENT_RELATION','address',$this_address['PRIM_STUDENT_RELATION'],$relation_options);
			echo '<tr><td style=width:120px><span class=red>'._('*').'</span>'._('Relationship to Student').'</TD><td>:</td><td>'._makeAutoSelectInputX($this_address['PRIM_STUDENT_RELATION'],'PRIM_STUDENT_RELATION','address','',$prim_relation_options).'</TD></tr>';
			echo '<TR><td><span class=red>'._('*').'</span>'._('First Name').'</td><td>:</td><TD>'.TextInput($this_address['PRI_FIRST_NAME'],'values[address][PRI_FIRST_NAME]','','class=cell_medium').'</TD></tr>';
			
			echo '<TR><td><span class=red>'._('*').'</span>'._('Last')._(' ')._('Name').'</td><td>:</td><TD>'.TextInput($this_address['PRI_LAST_NAME'],'values[address][PRI_LAST_NAME]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Home Phone').'</td><td>:</td><TD>'.TextInput($this_address['HOME_PHONE'],'values[address][HOME_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Work Phone').'</td><td>:</td><TD>'.TextInput($this_address['WORK_PHONE'],'values[address][WORK_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Cell/Mobile Phone').'</td><td>:</td><TD>'.TextInput($this_address['MOBILE_PHONE'],'values[address][MOBILE_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Email').'</td><td>:</td><TD>'.TextInput($this_address['EMAIL'],'values[address][EMAIL]','','class=cell_medium').'</TD></tr>';
			echo '<TR><TD>'._('Custody of Student').'</TD><td>:</td><TD>'.CheckboxInput($this_address['PRIM_CUSTODY'],'values[address][PRIM_CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR>';   
			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<tr><td colspan=3><table><TR><TD><TD><span class=red>'._('*').'</span><input type="radio" id="r5" name="r5" value="Y" onClick="prim_hidediv();" checked>&nbsp;'._('Same as Student\'s Home Address').' &nbsp;&nbsp; <input type="radio" id="r5" name="r5" value="N" onClick="prim_showdiv();">&nbsp;'._('Add New Address').'</TD></TR></TABLE></td></tr>'; 
			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<tr><td colspan=3><div id="prim_hideShow" style="display:none">';
			else
			echo '<tr><td colspan=5><div id="prim_hideShow">';
			echo '<div class=break></div>';
                        $s_prim_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM address WHERE ADDRESS_ID=\''.$a_ID.'\' AND ADDRESS=PRIM_ADDRESS AND CITY=PRIM_CITY AND STATE=PRIM_STATE AND ZIPCODE=PRIM_ZIPCODE'));
                        if($s_prim_address[1]['TOTAL']!=0)
                           $p_checked=" CHECKED=CHECKED ";
                        else
                            $p_checked=" ";
                         if($a_ID!=0)
                            echo '<div id="check_addr"><input type="checkbox" '.$p_checked.' id="prim_addr" name="prim_addr" value="Y">&nbsp;'._('Same as Home Address').' &nbsp;</div><br>';
                         
			echo '<table><TR><td style=width:120px>'._('Address Line 1').'</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($this_address['PRIM_ADDRESS'],'values[address][PRIM_ADDRESS]','','class=cell_medium').'</TD><td>';
			//if($_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='0')
			if($a_ID!=0)
			{
				$display_address = urlencode($this_address['PRIM_ADDRESS'].', '.($this_address['PRIM_CITY']?' '.$this_address['PRIM_CITY'].', ':'').$this_address['PRIM_STATE'].($this_address['PRIM_ZIPCODE']?' '.$this_address['PRIM_ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>'._('Map it').'</A>';
			}
			echo '</td></tr></table></td></tr>';
			echo '<TR><td>'._('Address Line 2').'</td><td>:</td><TD>'.TextInput($this_address['PRIM_STREET'],'values[address][PRIM_STREET]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('City').'</td><td>:</td><TD>'.TextInput($this_address['PRIM_CITY'],'values[address][PRIM_CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('State').'</td><td>:</td><TD>'.TextInput($this_address['PRIM_STATE'],'values[address][PRIM_STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Zip/Postal Code').'</td><td>:</td><TD>'.TextInput($this_address['PRIM_ZIPCODE'],'values[address][PRIM_ZIPCODE]','','class=cell_medium').'</TD>';
			echo '</table>';
			echo '</div></td></tr>';

			echo '</table></td></tr></table></FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>'; // close 3c
			
############################################################################################		
			echo '<TABLE border=0 width=100%><TR><TD>'; // open 3d
			echo '<FIELDSET><LEGEND><FONT color=gray>'._('Secondary Emergency Contact').'</FONT></LEGEND><TABLE width=100%><tr><td>';
			$sec_relation_options = _makeAutoSelect('SEC_STUDENT_RELATION','address',$this_address['SEC_STUDENT_RELATION'],$relation_options);
			echo '<table><tr><td style=width:120px><span class=red>'._('*').'</span>'._('Relationship to Student').'</td><td>:</td><TD>'._makeAutoSelectInputX($this_address['SEC_STUDENT_RELATION'],'SEC_STUDENT_RELATION','address','',$sec_relation_options).'</TD></tr>';
			echo '<TR><td><span class=red>'._('*').'</span>'._('First Name').'</td><td>:</td><TD>'.TextInput($this_address['SEC_FIRST_NAME'],'values[address][SEC_FIRST_NAME]','','class=cell_medium').'</TD></tr>';
			
			
			echo '<TR><td><span class=red>'._('*').'</span>'._('Last Name').'</td><td>:</td><TD>'.TextInput($this_address['SEC_LAST_NAME'],'values[address][SEC_LAST_NAME]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Home Phone').'</td><td>:</td><TD>'.TextInput($this_address['SEC_HOME_PHONE'],'values[address][SEC_HOME_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Work Phone').'</td><td>:</td><TD>'.TextInput($this_address['SEC_WORK_PHONE'],'values[address][SEC_WORK_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Cell/Mobile Phone').'</td><td>:</td><TD>'.TextInput($this_address['SEC_MOBILE_PHONE'],'values[address][SEC_MOBILE_PHONE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Email').'</td><td>:</td><TD>'.TextInput($this_address['SEC_EMAIL'],'values[address][SEC_EMAIL]','','class=cell_medium').'</TD></tr>';
			echo '<TR><TD>'._('Custody of Student').'</TD><td>:</td><TD>'.CheckboxInput($this_address['SEC_CUSTODY'],'values[address][SEC_CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR></table>';
			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<tr><td colspan=3><table><TR><TD><span class=red >'._('*').'</span><input type="radio" id="r6" name="r6" value="Y" onClick="sec_hidediv();" checked>&nbsp;'._('Same as Student\'s Home Address').' &nbsp;&nbsp; <input type="radio" id="r6" name="r6" value="N" onClick="sec_showdiv();">&nbsp;'._('Add New Address').'</TD></TR></TABLE></td></tr>';
			//if($_REQUEST['address_id']=='new')
			if($a_ID==0)
			echo '<tr><td colspan=3><div id="sec_hideShow" style="display:none">';
			else
			echo '<tr><td colspan=3><div id="sec_hideShow">';
			echo '<div class=break></div>';
                        $s_sec_address=DBGet(DBQuery('SELECT COUNT(1) as TOTAL FROM address WHERE ADDRESS_ID=\''.$a_ID.'\' AND ADDRESS=PRIM_ADDRESS AND CITY=SEC_CITY AND STATE=SEC_STATE AND ZIPCODE=SEC_ZIPCODE'));
                        if($s_sec_address[1]['TOTAL']!=0)
                           $s_checked=" CHECKED=CHECKED ";
                        else
                            $s_checked=" ";
                         if($a_ID!=0)
                            echo '<div id="check_addr"><input type="checkbox" '.$s_checked.' id="sec_addr" name="sec_addr" value="Y">&nbsp;'._('Same as Home Address').' &nbsp;</div><br>';
                         
			echo '<table><TR><td style=width:120px>'._('Address Line 1').'</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($this_address['SEC_ADDRESS'],'values[address][SEC_ADDRESS]','','class=cell_medium').'</TD><td>';
			//if($_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='0')
			if($a_ID!=0)
			{
				$display_address = urlencode($this_address['SEC_ADDRESS'].', '.($this_address['SEC_CITY']?' '.$this_address['SEC_CITY'].', ':'').$this_address['SEC_STATE'].($this_address['SEC_ZIPCODE']?' '.$this_address['SEC_ZIPCODE']:''));
				$link = 'http://google.com/maps?q='.$display_address;
				echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>'._('Map it').'</A>';
			}
			echo '</td></tr></table></td></tr>';
			echo '<TR><td>'._('Address Line 2').'</td><td>:</td><TD>'.TextInput($this_address['SEC_STREET'],'values[address][SEC_STREET]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('City').'</td><td>:</td><TD>'.TextInput($this_address['SEC_CITY'],'values[address][SEC_CITY]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('State').'</td><td>:</td><TD>'.TextInput($this_address['SEC_STATE'],'values[address][SEC_STATE]','','class=cell_medium').'</TD></tr>';
			echo '<TR><td>'._('Zip/Postal Code').'</td><td>:</td><TD>'.TextInput($this_address['SEC_ZIPCODE'],'values[address][SEC_ZIPCODE]','','class=cell_medium').'</TD>';
			echo '</TABLE>';
			echo '</div></td></tr></table></td></tr></table>';

			#echo '</FIELDSET>';
			echo'</TD></TR>';
			echo '</TABLE>';  // close 3d
			
			
############################################################################################			
			
		}

	}
	else
		echo '';
		
	
	$separator = '<HR>';
}


if($_REQUEST['person_id'] && $_REQUEST['con_info']=='old')
{
			echo "<INPUT type=hidden name=person_id value=$_REQUEST[person_id]>";

			if($_REQUEST['person_id']!='old')
			{
				$relation_options = _makeAutoSelect('STUDENT_RELATION','students_join_people',$this_contact['STUDENT_RELATION'],$relation_options);

				echo '<TABLE><TR><TD><FIELDSET><LEGEND><FONT color=gray>'._('Additional Contact').'</FONT></LEGEND><TABLE width=100% border=0>'; // open 3e
				if($_REQUEST['person_id']!='new' && $_REQUEST['con_info']=='old')
				{
					echo '<TR><TD colspan=3><table><tr><td>'.CheckboxInput($this_contact['EMERGENCY'],'values[students_join_people][EMERGENCY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD><TD> '._('This is an Emergency Contact').'</TD></TR></table></td></tr>';
					echo '<tr><td colspan=3 class=break></td></tr>';
					echo '<TR><TD>Name</td><td>:</td><td><DIV id=person_'.$this_contact['PERSON_ID'].'><div onclick=\'addHTML("<table><TR><TD>'.str_replace('"','\"',_makePeopleInput($this_contact['FIRST_NAME'],'FIRST_NAME','First')).'</TD><TD>'.str_replace('"','\"',_makePeopleInput($this_contact['LAST_NAME'],'LAST_NAME','Last')).'</TD></TR></TABLE>","person_'.$this_contact['PERSON_ID'].'",true);\'>'.$this_contact['FIRST_NAME'].' '.$this_contact['MIDDLE_NAME'].' '.$this_contact['LAST_NAME'].'</div></DIV></TD></TR>';
					echo '<TR><td style="width:120px">'._('Relationship to Student').'</td><td>:</td><TD>'._makeAutoSelectInputX($this_contact['STUDENT_RELATION'],'STUDENT_RELATION','students_join_people','',$relation_options).'</TD>';
					echo '<tr><TD>'._('Home Phone').'</td><td>:</td><td> '.TextInput($this_contact['ADDN_HOME_PHONE'],'values[students_join_people][ADDN_HOME_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>'._('Work Phone').'</td><td>:</td><td>'.TextInput($this_contact['ADDN_WORK_PHONE'],'values[students_join_people][ADDN_WORK_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>'._('Mobile Phone').'</td><td>:</td><td> '.TextInput($this_contact['ADDN_MOBILE_PHONE'],'values[students_join_people][ADDN_MOBILE_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>'._('Email').' </td><td>:</td><td>'.TextInput($this_contact['ADDN_EMAIL'],'values[students_join_people][ADDN_EMAIL]','','class=cell_medium').'</TD></tr>';
					echo '<TR><TD>'._('Custody').'</TD><td>:</td><TD>'.CheckboxInput($this_contact['CUSTODY'],'values[students_join_people][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></TR>';
					echo '<tr><td colspan=3 class=break></td></tr>';	
					echo '<tr><td style="width:120px">'._('Address Line 1').'</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($this_contact['ADDN_ADDRESS'],'values[students_join_people][ADDN_ADDRESS]','','class=cell_medium').'</TD><td>';
					if($_REQUEST['address_id']!='new' && $_REQUEST['address_id']!='0')
					{
						$display_address = urlencode($this_contact['ADDN_ADDRESS'].', '.($this_contact['ADDN_CITY']?' '.$this_contact['ADDN_CITY'].', ':'').$this_contact['ADDN_STATE'].($this_contact['ADDN_ZIPCODE']?' '.$this_contact['ADDN_ZIPCODE']:''));
						$link = 'http://google.com/maps?q='.$display_address;
						echo '&nbsp;<A class=red HREF=# onclick=\'window.open("'.$link.'","","scrollbars=yes,resizable=yes,width=800,height=700");\'>Map it</A>';
					}
					echo '</td></tr></table></td></tr>';
					echo '<TR><td>Address Line 2</td><td>:</td><TD>'.TextInput($this_contact['ADDN_STREET'],'values[students_join_people][ADDN_STREET]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>City</td><td>:</td><TD>'.TextInput($this_contact['ADDN_CITY'],'values[students_join_people][ADDN_CITY]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>State</td><td>:</td><TD>'.TextInput($this_contact['ADDN_STATE'],'values[students_join_people][ADDN_STATE]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>Zip/Postal Code</td><td>:</td><TD>'.TextInput($this_contact['ADDN_ZIPCODE'],'values[students_join_people][ADDN_ZIPCODE]','','class=cell_medium').'</TD></tr>';	
					echo '<TR><TD>School Bus Pick-up</TD><td>:</td><TD>'.CheckboxInput($this_contact['ADDN_BUS_PICKUP'],'values[students_join_people][ADDN_BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><TD>School Bus Drop-off</TD><td>:</td><TD>'.CheckboxInput($this_contact['ADDN_BUS_DROPOFF'],'values[students_join_people][ADDN_BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><TD>Bus No</td><td>:</td><TD>'.TextInput($this_contact['ADDN_BUSNO'],'values[students_join_people][ADDN_BUSNO]','','class=cell_small').'</TD></tr>';
					echo '</table>';
					$info_RET = DBGet(DBQuery("SELECT ID,TITLE,VALUE FROM people_join_contacts WHERE PERSON_ID='$_REQUEST[person_id]'"));
					if($info_apd)
						$info_options = _makeAutoSelect('TITLE','people_join_contacts',$info_RET,$info_options_x);

					echo '<TR><TD>';
					echo '<TABLE border=0 cellpadding=3 cellspacing=0>';
					if(!$info_apd)
					{
						echo '<TR><TD style="border-color: #BBBBBB; border: 1; border-style: none none solid none;"></TD><TD style="border-color: #BBBBBB; border: 1; border-style: none solid solid none;"><font color=gray>Description</font> &nbsp; </TD><TD style="border-color: #BBBBBB; border: 1; border-style: none none solid none;"><font color=gray>'._('Value').'</font></TD></TR>';
						if(count($info_RET))
						{
							foreach($info_RET as $info)
							{
							echo '<TR>';
							if(AllowEdit())
								echo '<TD width=20>'.button('remove','',"Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&address_id=$_REQUEST[address_id]&person_id=$_REQUEST[person_id]&contact_id=".$info['ID']).'</TD>';
							else
								echo '<TD></TD>';
							if($info_apd)
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;">'._makeAutoSelectInputX($info['TITLE'],'TITLE','people_join_contacts','',$info_options,$info['ID']).'</TD>';
							else
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;">'.TextInput($info['TITLE'],'values[people_join_contacts]['.$info['ID'].'][TITLE]','','maxlength=100').'</TD>';
							echo '<TD>'.TextInput($info['VALUE'],'values[people_join_contacts]['.$info['ID'].'][VALUE]','','maxlength=100').'</TD>';
							echo '</TR>';
							}
						}
						if(AllowEdit() && $use_contact)
						{
							echo '<TR>';
							echo '<TD width=20>'.button('add').'</TD>';
							if($info_apd)
							{
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;">'.(count($info_options)>1?SelectInput('','values[people_join_contacts][new][TITLE]','',$info_options,'N/A'):TextInput('','values[people_join_contacts][new][TITLE]','')).'</TD>';
								echo '<TD>'.TextInput('','values[people_join_contacts][new][VALUE]','').'</TD>';
							}
							else
							{
								echo '<TD style="border-color: #BBBBBB; border: 1; border-style: none solid none none;"><INPUT size=15 type=TEXT value="Example Phone" style="color: #BBBBBB;" name=values[people_join_contacts][new][TITLE] '."onfocus='if(this.value==\"Example Phone\") this.value=\"\"; this.style.color=\"000000\";' onblur='if(this.value==\"\") {this.value=\"Example Phone\"; this.style.color=\"BBBBBB\";}'></TD>";
								echo '<TD><INPUT size=15 type=TEXT value="(xxx) xxx-xxxx" style="color: #BBBBBB;" name=values[people_join_contacts][new][VALUE] '."onfocus='if(this.value==\"(xxx) xxx-xxxx\") this.value=\"\"; this.style.color=\"000000\";' onblur='if(this.value==\"\") {this.value=\"(xxx) xxx-xxxx\"; this.style.color=\"BBBBBB\";}'></TD>";
							}
							echo '</TR>';
						}
					}
					else
					{
						if(count($info_RET))
						{
							foreach($info_RET as $info)
							{
								echo '<TR>';
								if(AllowEdit())
									echo '<TD width=20>'.button('remove','',"Modules.php?modname=$_REQUEST[modname]&include=$_REQUEST[include]&modfunc=delete&address_id=$_REQUEST[address_id]&person_id=$_REQUEST[person_id]&contact_id=".$info['ID']).'</TD>';
								else
									echo '<TD></TD>';
								echo '<TD><DIV id=info_'.$info['ID'].'><div onclick=\'addHTML("<TABLE><TR><TD>'.str_replace('"','\"',TextInput($info['VALUE'],'values[people_join_contacts]['.$info['ID'].'][VALUE]','','',false).'<BR>'.str_replace("'",'&#39;',_makeAutoSelectInputX($info['TITLE'],'TITLE','people_join_contacts','',$info_options,$info['ID'],false))).'</TD></TR></TABLE>","info_'.$info['ID'].'",true);\'>'.$info['VALUE'].'<BR><small><FONT color='.($info_options_x[$info['TITLE']]?Preferences('TITLES'):'blue').'>'.$info['TITLE'].'</FONT></small></div></DIV></TD>';
								echo '</TR>';
							}
						}
						if(AllowEdit() && $use_contact)
						{
							echo '<TR>';
							echo '</TR>';
						}
					}
					echo '</TABLE>';
					echo '</TD></TR>';
					echo '</TABLE>';
					#echo '</FIELDSET>';
					echo '</TD></TR>';
					echo '</TABLE>'; // close 3e
					

				}
				else
				{
					echo '<TABLE border=0><TR><TD colspan=3><table><tr><td>'.CheckboxInput($this_contact['EMERGENCY'],'values[students_join_people][EMERGENCY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD><TD>'._('This is an Emergency Contact').'</TD></TR></table></TD></TR><tr><td colspan=3 class=break></td></tr>';	
					echo '<TR><td style="width:120px" style=white-space:nowrap><span class=red>*</span>'._('Relationship to Student').'</td><td>:</td><TD>'.SelectInput($this_contact['STUDENT_RELATION'],'values[students_join_people][STUDENT_RELATION]','',$relation_options,'N/A').'</TD></TR>';
					echo '<TR><TD><span class=red>*</span>'._('First Name').'</td><td>:</td><TD>'.str_replace('"','\"',_makePeopleInput('','FIRST_NAME','','class=cell_medium')).'</TD></tr><tr><td ><span class=red>'._('*').'</span>'._('Last Name').'</td><td>:</td><TD>'.str_replace('"','\"',_makePeopleInput($this_contact['LAST_NAME'],'LAST_NAME','','class=cell_medium')).'</TD></TR>';
					echo '<tr><TD>'._('Home Phone').'</td><td>:</td><td> '.TextInput($this_contact['ADDN_HOME_PHONE'],'values[students_join_people][ADDN_HOME_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>'._('Work Phone').'</td><td>:</td><td>'.TextInput($this_contact['ADDN_WORK_PHONE'],'values[students_join_people][ADDN_WORK_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>'._('Mobile Phone').'</td><td>:</td><td> '.TextInput($this_contact['ADDN_MOBILE_PHONE'],'values[students_join_people][ADDN_MOBILE_PHONE]','','class=cell_medium').'</TD></tr>';
					echo '<tr><TD>'._('Email').' </td><td>:</td><td>'.TextInput($this_contact['ADDN_EMAIL'],'values[students_join_people][ADDN_EMAIL]','','class=cell_medium').'</TD></tr>';
					echo '<TR><TD>'._('Custody of Student').'</td><td>:</td><TD>'.CheckboxInput($this_contact['CUSTODY'],'values[students_join_people][CUSTODY]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'<small><FONT color='.Preferences('TITLES').'></FONT></small></TD></TR>';
					echo '<TR><TD colspan=3><table><TR><TD style=white-space:nowrap><span class=red>'._('*').'</span><input type="radio" id="r7" name="r7" value="Y" onClick="addn_hidediv();" checked>&nbsp;'._('Same as Student\'s Home Address').' &nbsp;&nbsp; <input type="radio" id="r7" name="r7" value="N" onClick="addn_showdiv();">&nbsp;'._('Add New Address').'</TD></TR></TABLE></TD></TR>';
					echo '<TR><TD colspan=3><div id="addn_hideShow" style="display:none">';
					echo '<div class=break></div>';
					echo '<table><TR><td style=width:120px>'._('Address Line 1').'</td><td>:</td><TD>'.TextInput($this_address['ADDN_ADDRESS'],'values[students_join_people][ADDN_ADDRESS]','','class=cell_medium').'</TD></td>';
					
					#echo '<table><TR><td style=width:120px>Address Line 1</td><td>:</td><TD><table cellspacing=0 cellpadding=0><tr><td>'.TextInput($this_address['SEC_ADDRESS'],'values[address][SEC_ADDRESS]','','class=cell_medium').'</TD><td>';
					
					echo '<TR><td>'._('Address Line 2').'</td><td>:</td><TD>'.TextInput($this_contact['ADDN_STREET'],'values[students_join_people][ADDN_STREET]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>'._('City').'</td><td>:</td><TD>'.TextInput($this_contact['ADDN_CITY'],'values[students_join_people][ADDN_CITY]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>'._('State').'</td><td>:</td><TD>'.TextInput($this_contact['ADDN_STATE'],'values[students_join_people][ADDN_STATE]','','class=cell_medium').'</TD></tr>';
					echo '<TR><td>'._('Zip/Postal Code').'</td><td>:</td><TD>'.TextInput($this_contact['ADDN_ZIPCODE'],'values[students_join_people][ADDN_ZIPCODE]','','class=cell_medium').'</TD></tr>';
					echo '<TR><TD>'._('School Bus Pick-up').'</TD><td>:</td><TD>'.CheckboxInput($this_contact['ADDN_BUS_PICKUP'],'values[students_join_people][ADDN_BUS_PICKUP]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><TD>'._('School Bus Drop-off').'</TD><td>:</td><TD>'.CheckboxInput($this_contact['ADDN_BUS_DROPOFF'],'values[students_join_people][ADDN_BUS_DROPOFF]','','CHECKED',$new,'<IMG SRC=assets/check.gif width=15>','<IMG SRC=assets/x.gif width=15>').'</TD></tr>';
					echo '<TR><td>'._('Bus No').'</TD><td>:</td><td>'.TextInput($this_contact['ADDN_BUSNO'],'values[students_join_people][ADDN_BUSNO]','','class=cell_small').'</TD></tr>';
					echo '</table></div></td></tr></table>';
					echo '</FIELDSET>';
					echo '</TD></TR>';
					echo '</TABLE>';
				}
				
				
			}
			elseif($_REQUEST['person_id']=='old')
			{
				$people_RET = DBGet(DBQuery('SELECT PERSON_ID,FIRST_NAME,LAST_NAME FROM people WHERE PERSON_ID NOT IN (SELECT PERSON_ID FROM students_join_people WHERE STUDENT_ID=\''.UserStudentID().'\') ORDER BY LAST_NAME,FIRST_NAME'));
				foreach($people_RET as $people)
					$people_select[$people['PERSON_ID']] = $people['LAST_NAME'].', '.$people['FIRST_NAME'];
				echo SelectInput('','values[EXISTING][person_id]',$title='Select Person',$people_select);
			}
			
			if($_REQUEST['person_id']=='new') {
		echo '</TD></TR>';
		echo '</TABLE>'; // end of table 2
		}
		unset($_REQUEST['address_id']);
		unset($_REQUEST['person_id']);
		}
		
	echo '</TD></TR>';
	echo '</TABLE>'; // end of table 1

	

function _makePeopleInput($value,$column,$title='',$options='')
{	global $THIS_RET;

	if($column=='MIDDLE_NAME')
		$options = 'class=cell_medium';
	if($_REQUEST['person_id']=='new')
		$div = false;
	else
		$div = true;

	if($column=='STUDENT_RELATION')
		$table = 'students_join_people';
	else
		$table = 'people';

	return TextInput($value,"values[$table][$column]",$title,$options,false);
}

function _makeAutoSelect($column,$table,$values='',$options=array())
{
	$options_RET = DBGet(DBQuery('SELECT DISTINCT '.$column.',upper('.$column.') AS `KEY` FROM '.$table.' ORDER BY `KEY`'));

	// add the 'new' option, is also the separator
	$options['---'] = '---';
	// add values already in table
	if(count($options_RET))
		foreach($options_RET as $option)
			if($option[$column]!='' && !$options[$option[$column]])
				$options[$option[$column]] = array($option[$column],'<FONT color=blue>'.$option[$column].'</FONT>');
	// make sure values are in the list
	if(is_array($values))
	{
		foreach($values as $value)
			if($value[$column]!='' && !$options[$value[$column]])
				$options[$value[$column]] = array($value[$column],'<FONT color=blue>'.$value[$column].'</FONT>');
	}
	else
		if($values!='' && !$options[$values])
			$options[$values] = array($values,'<FONT color=blue>'.$values.'</FONT>');

	return $options;
}

function _makeAutoSelectInputX($value,$column,$table,$title,$select,$id='',$div=true)
{
	if($column=='CITY' || $column=='MAIL_CITY')
		$options = 'maxlength=60';
	if($column=='STATE' || $column=='MAIL_STATE')
		$options = 'size=3 maxlength=10';
	elseif($column=='ZIPCODE' || $column=='MAIL_ZIPCODE')
		$options = 'maxlength=10';
	else
		$options = 'maxlength=100';

	if($value!='---' && count($select)>1)
		return SelectInput($value,"values[$table]".($id?"[$id]":'')."[$column]",$title,$select,'N/A','',$div);
	else
		return TextInput($value=='---'?array('---','<FONT color=red>---</FONT>'):$value,"values[$table]".($id?"[$id]":'')."[$column]",$title,$options,$div);
}
?>