<?php
/**
*
* License:
*
* Copyright (c) 2003-2006 ossim.net
* Copyright (c) 2007-2013 AlienVault
* All rights reserved.
*
* This package is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; version 2 dated June, 1991.
* You may not use, modify or distribute this program under any other version
* of the GNU General Public License.
*
* This package is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* You should have received a copy of the GNU General Public License
* along with this package; if not, write to the Free Software
* Foundation, Inc., 51 Franklin St, Fifth Floor, Boston,
* MA  02110-1301  USA
*
*
* On Debian GNU/Linux systems, the complete text of the GNU General
* Public License can be found in `/usr/share/common-licenses/GPL-2'.
*
* Otherwise you can read it here: http://www.gnu.org/licenses/gpl-2.0.txt
*
*/


//Format user data
function format_user($user, $html = TRUE, $show_email = FALSE) 
{
    if (is_a($user, 'Session')) 
	{
        $login   = $user->get_login();
        $name    = $user->get_name();
        $depto   = $user->get_department();
        $company = $user->get_company();
        $mail    = $user->get_email();
    } 
	elseif (is_array($user))
	{
        $login   = $user['login'];
        $name    = $user['name'];
        $depto   = $user['department'];
        $company = $user['company'];
        $mail    = $user['email'];
    } 
	else
	{
        return '';
    }
	
    $ret = $name;
    
	if ($depto && $company)   
	{
        $ret.= " / $depto / $company";
    }
    
    if ($mail && $show_email) 
    {
        $ret = "$ret &lt;$mail&gt;";
    }
    
    if ($login)               
    {
        $ret = "<label title=\"Login: $login\">$ret</label>";
    }
    
	if ($mail)
	{
        $ret = '<a href="mailto:' . $mail . '">' . $ret . '</a>';
    }
    else
    { 
        $ret = "$ret <font size='small' color='red'><i>(No email)</i></font>";
    }
    
    return $html ? $ret : strip_tags($ret);
}


function get_params_field($field, $map_key = NULL)
{	
	$unique_id = md5( uniqid() );
	$fld       = "custom_".$unique_id;
	$name      = "custom_".base64_encode($field['name']."_####_".$field['type']);
	
	switch ($field['type'])
	{
		case "Asset":
			
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_TEXT';
															
			$params = array("name"       => $name, 
							"id"         => $fld, 
							"class"      => "vfield ct_assets_sel",
							"validation" => $validation
			);
		
		break;
		
		case "Check Yes/No":
			
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_LETTER';			
			
			$params = array("name"       => $name, 
							"id"         => $fld, 
							"class"      => "vfield",
							"validation" => $validation
			);
			
		break;
		
		case "Check True/False":
				
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_LETTER';	
			
			$params = array("name"       => $name, 
							"id"         => $fld, 
							"class"      => "vfield",
							"validation" => $validation
			);
		
		break;
		
		case "Checkbox":
		
			$options = ($field["options"] != '') ? explode("\n", $field["options"]) : '';
			$num_opt = count($options);
			$num_chk = ($options[$num_opt-1] == '') ? $num_opt-1 : $num_opt;
			
			for ($i=0; $i<$num_chk; $i++)
			{
				$ids[] = $fld."_".($i+1);
			}
									
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_PUNC_EXT, OSS_ALPHA';	
			
			
			$params = array("name"       => $name.'[]', 
							"id"         => $ids, 
							"class"      => "vfield",
							"values"     => $options,
							"validation" => $validation
			);
			
		break;
		
		case "Date":			
			
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_DIGIT, OSS_COLON, OSS_SPACE, OSS_SCORE';	
						
			$params = array("name"       => $name, 
							"id"         => $fld, 
							"class"      => "vfield date_filter",
							"validation" => $validation
			);
		
		break;
		
		case "Date Range":
			
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_ALPHA, OSS_PUNC_EXT';
			
			$params = array("name"       => $name, 
							"id"         => $fld, 
							"class"      => "vfield",
							"validation" => $validation
			);
		
		break;
		
		case "Map":
					
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_ALPHA, OSS_PUNC_EXT';
			
			$params = array("name"       => $name,
							"id"         => $fld, 
							"class"      => "vfield",
							"values"     => array($map_key),
							"validation" => $validation
			);
					
		break;
			
		case "Radio button":
			
			$options   = ($field["options"] != '') ? explode("\n", $field["options"]) : '';		
			$num_opt   = count($options);
			$num_radio = ($options[$num_opt-1] == '' ) ? $num_opt-1 : $num_opt;
			
			for ($i = 0; $i < $num_radio; $i++)
			{
				$ids[] = $fld."_".$i;
			}			
						
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_ALPHA, OSS_PUNC_EXT';

			
			$params = array("name"       => $name,
							"id"         => $ids, 
							"class"      => "vfield",
							"values"     => $options,
							"validation" => $validation
			);
		
		break;
		
		case "Select box":
			
			$options    = ($field["options"] != '') ? explode("\n", $field["options"]) : '';	
			
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_ALPHA, OSS_PUNC_EXT';
			
			$params = array("name"       => $name,
							"id"         => $fld, 
							"class"      => "vfield",
							"values"     => $options,
							"validation" => $validation
			);
					
		break;
		
		case "Slider":
			
			$options    = ($field["options"] != '') ? explode(",", $field["options"]) : '';
		
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_ALPHA, OSS_PUNC_EXT';
			
			$params = array("name"       => $name,
							"id"         => $fld, 
							"class"      => "vfield",
							"values"     => $options,
							"validation" => $validation
			);
			
		break;
					
		case "Textarea":
			
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_ALL';
					
			$params = array("name"       => $name,
							"id"         => $fld, 
							"class"      => "vfield",
							"rows"       => "3", 
							"cols"       => "80",
							"validation" => $validation
			);
				
		break;
		
		case "Textbox":
					
			$validation  = ($field['required'] != 1) ? 'OSS_NULLABLE, ' : '';
			$validation .= 'OSS_ALPHA, OSS_PUNC_EXT';
			
			$params = array("name"       => $name,
							"id"         => $fld, 
							"class"      => "vfield",
							"validation" => $validation
			);
			
		break;
		
		case "File":
						
			$params = array("name"       => $name,
							"id"         => $fld,
							"size"       => "50",							
							"class"      => ""
			);
			
		break;
	}
	
	return $params;
}


function order_img($subject) 
{
    global $order_by, $order_mode;
    
    if ($order_by != $subject)
    {
        return '';
    }
	
    $img = $order_mode == 'DESC' ? 'abajo.gif' : 'arriba.gif';
    
    return '&nbsp;<img src="../pixmaps/top/' . $img . '" border="0"/>';
}


//Create a tag box 
function show_tag_box($tag_list)
{
    ?>
    <div id="tag_box">
        <table id='theader_tag_box' cellpadding='0' cellspacing='0'>
            <tr>
                <th>
                    <div id='theader_left'><?php echo _("Tags")?></div>
                    <div id='theader_right'>
                        <a style="text-align: right;" id='link_tbox'><img src="../pixmaps/cross-circle-frame.png" alt="<?php echo _("Close"); ?>" title="<?php echo _("Close"); ?>" border="0" align='absmiddle'/></a>
                    </div>
                </th>
            </tr>
            <?php
            if (is_array($tag_list) && count($tag_list) >= 1) 
            { 
                $cont = 0;
                foreach ($tag_list as $tg) 
                { 
                    $background = ( $cont%2 == 0 ) ? "background: #F2F2F2;" :  "background: #FFFFFF;";
                    ?>
                    <tr><td class='td_tags' style='<?php echo $background?>' id='tag_<?php echo $tg['id']?>'><label title="<?php echo $tg['descr']?>"><?php echo $tg['name']?></label></td></tr>
                    <?php 
                    $cont++;
                }
                ?>
                <tr>        
                    <td class="td_rm_tags noborder">
                        <a id='link_rm_tags'>[<?php echo _("Remove selected") ?>]</a>
                    </td>
                </tr>
                <?php
            } 
            else
            {
                ?>
                <tr><td><div id='notags'><?php echo _("No tags found") ?></div></td></tr>
                <?php 
            }
            ?>
        </table>
    </div>
 
    <?php
}


function get_json_entities($conn)
{
    require_once 'av_init.php';
    
    $json_entities = NULL;
    
    $conf = $GLOBALS["CONF"];
		
    if (!$conf) 
    {
        $conf = new Ossim_conf();
    }
    
    $version  = $conf->get_conf("ossim_server_version", FALSE);
	$pro      = ( preg_match("/pro|demo/i",$version) ) ? TRUE : FALSE;
    
    if ($pro)
    {
        list($entities_all,$num_entities) = Acl::get_entities($conn, '', '', FALSE, FALSE);
        
        if (is_array($entities_all) && !empty($entities_all))
        {
            foreach ($entities_all as $entity_id)
            {                 
                $entity_text = $entity["name"];
                $entity_id   = $entity["id"];
                
                $json_entities .= '{ txt:"'.$entity_text.'", id:"'.$entity_id.'"},';
            }
        }
    }

    return $json_entities;
}

	
function get_json_users($conn)
{
    require_once 'av_init.php';
    
    $json_users   = NULL;
    
    $users_list = Session::get_list($conn, "ORDER BY login");
							
    if (is_array($users_list) && !empty($users_list))
    {
        foreach($users_list as $user)
        {
            $json_users .= '{ txt:"'.$user->get_name().' ['._("User").']", id: "'.$user->get_login().'" },';
        }
    }
        
    return $json_users;
}


function show_incident_error($msg, $style='', $type='nf_error')
{	
	$config_nt = array(
		'content' => $msg,
		'options' => array (
			'type'          => $type,
			'cancel_button' => FALSE
		),
		'style'   => $style
	); 
	
	$nt = new Notification('nt_1', $config_nt);
	$nt->show();
}


function clean_inc_ic()
{	
    static $cons = FALSE;
    
	$parms = func_get_args();
	
	if (func_num_args() < 2)
	{
    	return $parms[0];
	}
    
	if (!$cons)
	{ 
		$cons = get_defined_constants();
    }
	
	   	
	if (!is_string($parms[0]))
	{
        return FALSE;
    }
	
	//Variable to be validated
	$v_var = (mb_detect_encoding($parms[0]." ",'UTF-8,ISO-8859-1') == 'UTF-8' ) ? mb_convert_encoding($parms[0], 'ISO-8859-1', 'UTF-8') : $parms[0]; 
		
	array_shift($parms);	
	
	$val_str = '';
	
	foreach($parms as $p) 
	{
		$val_str .= $p;
	}
	
	$pattern  = '/[^'.$val_str.']/';
			
	$v = preg_replace($pattern, "", $v_var);

	return $v;
}


function print_incident_fields($title, $val)
{
    echo '<div class="ticket_section_title">'. $title .':</div>';
    echo '<div class="ticket_section_val">'. $val .'</div>';
    echo '<div class="clear_layer"></div>';
}
