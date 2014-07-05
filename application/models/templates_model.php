<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc	This class will help us to get, insert, update the templates for broadcasts in database
 * @author 	Zoltan Jozsa
 *
 */

class Templates_model extends CI_Model {
	function Templates_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	/**
	 * @method	get_details
	 * @access	public
	 * @desc	this method gets a details of a template by template id
	 * @author	Zoltan Jozsa
	 * @param 	int						$template_id				- the id of the template
	 * 
	 * @return  template object or FALSE
	 */
	public function get_details( $template_id = '' )
	{
		if( empty( $template_id ) )
			return FALSE;
			
		$this->db->where( 'broadcast_template_id', $template_id );
		$query = $this->db->get( 'broadcast_templates' );
		
		if( $query->num_rows() > 0 )
		{
			return $query->row();
		}
			
		return FALSE;
	}
}