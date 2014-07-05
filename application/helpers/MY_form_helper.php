<?php

function showerror($field='')
{
    $ci =& get_instance();
    if (isset($ci->data['errors'][$field])) {
        echo '<div class="error">'.$ci->data['errors'][$field].'</div>';
    } else {
        echo '';
    }
}

function form_dropdown_contract_requests_status($name='', $selected='', $extra='', $search = false)
{
    $label = array('Pending','Approved','Rejected','Old Version');
    $values = array('Pending','Approved','Rejected','OldVersion');	
    if ($search) {
        $options = array('' => 'All Status');
    }
    foreach ( $values as $index=>$val )
    {
        $options[$val] = $label[$index];
    }
    return form_dropdown($name, $options, $selected, $extra);
}

function form_dropdown_lead_status($name='', $selected='', $extra='', $search=false)
{
	$label = array('Hot', 'Warm', 'Active', 'New', 'Cold', 'Subscriber', 'Lost');
	$values = array('1-Hot', '2-Warm', '3-Active', '4-New', '5-Cold', '6-Subscriber', '7-Lost');
	if ($search) {
		$option = array('' => 'View All');
	}
	foreach ($values AS $index=>$val)
	{
		$option[$val] = $label[$index];
	}
	return form_dropdown($name, $option, $selected, $extra);
}

function form_dropdown_investor_status($name='', $selected='', $extra='', $search = false)
{
    $labels = array('Hot','Warm','Active','New','Cold','Client','Subscriber','Lost');
    if ($search) {
        $options = array('' => 'View All');
    } else {
        $options = array('' => '- Status');
    }
    foreach ( $labels as $label )
    {
        $options[$label] = $label;
    }
    return form_dropdown($name, $options, $selected, $extra);
}

function form_dropdown_investors($user_id, $name='', $selected='', $extra='')
{
    $ci =& get_instance();
    $ci->load->model('Users_model');
    $filters = array();
    $filters["created_by_user_id"] = $user_id;
    $filters["owner_id"] = $user_id;
    $filters["deleted"] = 0;
    $filters["order_by"] = "u.first_name ASC";        
    $extra_sql = ", get_investor_status_count(u.user_id, 'sold') as num_sold, get_investor_last_status_date(u.user_id, 'sold') as last_sold_date ";
    $count_all = 0;
    $users = $ci->Users_model->get_list(1, '', $page_no = '', $count_all, "", $user_type = array(USER_TYPE_INVESTOR, USER_TYPE_LEAD), $filters, $extra_sql);
    $options = array(
        '' => '- Please select '
    );
    if($users)
    {
        foreach ($users->result() as $user)
        {
            $options[$user->user_id] = trim("$user->first_name $user->last_name");
        }
    }
    return form_dropdown($name, $options, $selected, $extra);
}

function form_dropdown_owner($user_id, $name='', $selected='', $extra='')
{
    $ci =& get_instance();
    $ci->load->model('Users_model');
    
    $filters = array();
    $filters["deleted"] = 0;
    $filters["order_by"] = "u.first_name ASC";   
    $filters["created_by_user_id"] = $user_id;   
    
    $extra_sql = "";
    $count_all = 0;
    
    $users = $ci->Users_model->get_list(1, '', $page_no = '', $count_all, "", $user_type = array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER), $filters, $extra_sql);
    
    $options = array(
        '' => '- Please select '
    );
    
    $creator = $ci->Users_model->get_details($user_id);
    if($creator) {
        $options[$creator->user_id] = trim("$creator->first_name $creator->last_name");    
    }
    
    if($users)
    {
        foreach ($users->result() as $user)
        {
            $options[$user->user_id] = trim("$user->first_name $user->last_name");
        }
    }
    
    return form_dropdown($name, $options, $selected, $extra);
}

function form_dropdown_advisors($user_id, $name='', $selected='', $extra='', $default_text = "- Please select ")
{
    $ci =& get_instance();
    $ci->load->model('Users_model');
    
    $filters = array();
    $filters["deleted"] = 0;
    $filters["order_by"] = "u.first_name ASC";        
    
    $extra_sql = "";
    $count_all = 0;
    
    $users = $ci->Users_model->get_list(1, '', $page_no = '', $count_all, "", $user_type = array(USER_TYPE_ADVISOR), $filters, $extra_sql);
                    
    $options = array();
    
    if($default_text != "") {
        $options[""] = $default_text;
    }
    
    if($users)
    {
        foreach ($users->result() as $user)
        {
            $options[$user->user_id] = trim("$user->first_name $user->last_name");
        }
    }
        
    return form_dropdown($name, $options, $selected, $extra);
}

function form_dropdown_states($countryID, $name='', $selected='', $extra='')
{
    $ci =& get_instance();
    $rs = $ci->db->where('country_id',$countryID)
                ->order_by('name','ASC')
                ->get('states')
                ->result();
    $options = array('' => 'All States');
    foreach ($rs as $row)
    {
        $options[$row->state_id] = !empty($row->preferredName) ? $row->preferredName : $row->name;
    }
    return form_dropdown($name, $options, $selected, $extra);
}

function form_dropdown_partners($user_id, $name='', $selected='', $extra='')
{
    $ci =& get_instance();
    $ci->load->model('Users_model');
    $filters = array();
    $filters["created_by_user_id"] = $user_id;
    $filters["owner_id"] = $user_id;
    $filters["deleted"] = 0;
    $filters["order_by"] = "u.first_name ASC";        
    $count_all = 0;
    $users = $ci->Users_model->get_list(-1, '', $page_no = '', $count_all, "", $user_type = array(USER_TYPE_PARTNER), $filters);
    $options = array(
        '' => '- Please select '
    );
    if($users)
    {
        foreach ($users->result() as $user)
        {
            $options[$user->user_id] = trim("$user->first_name $user->last_name");
        }
    }
    return form_dropdown($name, $options, $selected, $extra);
}