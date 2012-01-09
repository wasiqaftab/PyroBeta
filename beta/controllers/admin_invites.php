<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * This is a sample module for PyroCMS
 *
 * @author 		Jerel Unruh - PyroCMS Dev Team
 * @website		http://unruhdesigns.com
 * @package 	PyroCMS
 * @subpackage 	Sample Module
 */
class Admin_invites extends Admin_Controller
{
	protected $section = 'invites';

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
		$this->db->where('type', 'invite');
		$this->data->invites = $this->beta_user_m->get_all();

		$this->template->title($this->module_details['name'])
							->build('admin/invites/index', $this->data);
	}

	// --------------------------------------------------------------------------

	public function invite()
	{
		$this->load->library('Form_validation');
	
		$this->form_validation->set_rules($this->beta_user_m->validation);

		if($this->form_validation->run()):
		
			if($this->beta_user_m->invite_user($this->input->post('email'))):
				
				$this->session->set_flashdata('success', lang('beta:user_invite_success'));
				redirect('admin/beta/invites');
			
			else:
			
				$this->session->set_flashdata('error', lang('beta:user_invite_error'));
				redirect('admin/beta/invites/invite');
			
			endif;
		
		endif;
		
		foreach($this->beta_user_m->validation as $rule):

			$this->data->{$rule['field']} = $this->input->post($rule['field']);
		
		endforeach;

		// Build the view using sample/views/admin/form.php
		$this->template->title($this->module_details['name'], lang('beta:invite_user'))
						->build('admin/invites/form', $this->data);
	}

}