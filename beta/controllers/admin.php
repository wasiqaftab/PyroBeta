<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a sample module for PyroCMS
 *
 * @author 		Jerel Unruh - PyroCMS Dev Team
 * @website		http://unruhdesigns.com
 * @package 	PyroCMS
 * @subpackage 	Sample Module
 */
class Admin extends Admin_Controller
{
	protected $section = 'signups';

	public function __construct()
	{
		parent::__construct();

		$this->load->model('beta_user_m');
		$this->lang->load('beta');
		$this->load->helper('beta');
	}

	// --------------------------------------------------------------------------

	/**
	 * List all beta sign ups
	 */
	public function index()
	{
		$this->db->where('type', 'signup');
		$this->data->signups = $this->beta_user_m->get_all();

		$this->template->title($this->module_details['name'])
							->build('admin/signups/index', $this->data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Add user to beta
	 */
	public function add_user()
	{
		// Get the beta user
		if( ! $id = $this->uri->segment(4) ) show_error('No ID found.');
		if( ! $beta_user = $this->db->limit(1)->where('id', $id)->get('beta_users')->row() ) show_error('Invalid User.');
	
		if( $beta_user->converted == 'y' ) show_error('User already added to beta.');
		
		if( ! $this->beta_user_m->add_user_to_beta($beta_user) ):
		
			$this->session->set_flashdata('error', lang('beta:added_to_beta_error'));
		
		else:
		
			$this->session->set_flashdata('success', lang('beta:added_to_beta_success'));
		
		endif;
	
		redirect('admin/beta');
	}

}