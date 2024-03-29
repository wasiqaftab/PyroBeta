<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Beta Register
 *
 * @author 		Adam Fairholm - Parse19
 * @website		http://parse19.com
 * @package 	PyroCMS
 * @subpackage 	Beta Module
 */
class Beta extends Public_Controller
{

	public function __construct()
	{
		parent::__construct();

		$this->load->model('beta_user_m');
		$this->lang->load('beta');		
		$this->load->model('users/user_m');
		$this->load->helper('users/user');
		$this->lang->load('users/user');
		$this->load->library('form_validation');
	}

	// --------------------------------------------------------------------------

	/**
	 * Beta Signup
	 *
	 * Once a user is approved or is invited to a
	 * beta, they can register using this function.
	 *
	 * @access	public
	 * @return	void
	 */
	public function signup()
	{
		// Can't be logged in and sign up for the beta.
		if(isset($this->current_user->id)):
		
			$this->session->set_flashdata('notice', lang('beta:cannot_signup_logged_in'));
			redirect();
	
		endif;
	
		$this->load->library('Form_validation');
	
		$this->form_validation->set_rules($this->beta_user_m->validation);

		if( $this->form_validation->run() ):
		
			if( $this->beta_user_m->queue_user($this->input->post('email')) ):
				
				redirect('beta/complete');
			
			else:
			
				// Add to validation errors
			
			endif;
		
		endif;
		
		foreach($this->beta_user_m->validation as $rule):

			$this->data->{$rule['field']} = $this->input->post($rule['field']);
		
		endforeach;

		$this->template->error_string = $this->form_validation->error_string();

		$this->template->title($this->module_details['name'], lang('beta:sign_up'))
					   ->build('signup', $this->data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Beta Signup
	 *
	 * Once a user is approved or is invited to a
	 * beta, they can register using this function.
	 *
	 * @access	public
	 * @return	void
	 */
	public function complete()
	{
		$this->template->title($this->module_details['name'], lang('beta:thank_you'))
					   ->build('signup_complete', $this->data);
	}

	// --------------------------------------------------------------------------

	/**
	 * Beta Register
	 *
	 * Once a user is approved or is invited to a
	 * beta, they can register using this function.
	 *
	 * @access	public
	 * @return	void
	 */
	public function register()
	{
		// Logged in users can't get into this.
		if( isset($this->current_user->id) ) show_404();
	
		// Get and validate the user hash
		if( ! $hash = $this->uri->segment(3) ) show_404();
		if( ! $beta_user = $this->db->limit(1)->where('hash', $hash)->get('beta_users')->row() ) show_error('Unable to find user');
		
		// Make sure this user is not converted.
		if( $beta_user->converted == 'y' or ($beta_user->type == 'signup' and $beta_user->status != 'a') ) show_error('You have already registered.');

		// Validation rules
		$validation = array(
			array(
				'field' => 'first_name',
				'label' => lang('user_first_name'),
				'rules' => 'required'
			),
			array(
				'field' => 'last_name',
				'label' => lang('user_last_name'),
				'rules' => (Settings::get('require_lastname') ? 'required' : '')
			),
			array(
				'field' => 'password',
				'label' => lang('user_password'),
				'rules' => 'required|min_length[6]|max_length[20]'
			),
			array(
				'field' => 'username',
				'label' => lang('user_username'),
				'rules' => Settings::get('auto_username') ? '' : 'required|alpha_dot_dash|min_length[3]|max_length[20]|callback__username_check',
			),
		);

		$this->form_validation->set_rules($validation);

		if ($this->form_validation->run()):

			$email				= $beta_user->email;
			$password			= $this->input->post('password');	

			// Auto-username generation from the PyroCMS users module
			if (Settings::get('auto_username')):
			
				$i = 1;
				
				do
				{
					$username = url_title($this->input->post('first_name').'.'.$this->input->post('last_name'), '-', true);

					// Add 2, 3, 4 etc to the end
					$i > 1 and $username .= $i;

					++$i;
				}

				// Keep trying until it is unique
				while ($this->db->where('username', $username)->count_all_results('users') > 0);

			else:
			
				$username = $this->input->post('username');
			
			endif;
						
			// Register our user and auto-activate him.
			$id = $this->ion_auth->register($username, $password, $email, array(
				'first_name'		=> $this->input->post('first_name'),
				'last_name'			=> $this->input->post('last_name'),
				'display_name'		=> $username,
			));

			// Try to create the user
			if ($id > 0):

				Events::trigger('post_user_register', $id);
				
				// Set a converted
				$convert_data = array(
						'converted'			=> 'y',
						'when_converted'	=> time()
				);
				$this->db->limit(1)->update('beta_users', $convert_data);

				$this->session->set_flashdata('success', lang('user_activation_by_admin_notice'));
				redirect('users/login');
			
			else:
			
				// Can't create the user, show why
				$this->template->error_string = $this->ion_auth->errors();
			
			endif;
		
		else:
		
			// Return the validation error
			$this->template->error_string = $this->form_validation->error_string();
		
		endif;

		// Form repopulation
		foreach($validation as $rule):
		
			$user->{$rule['field']} = set_value($rule['field']);
		
		endforeach;
	
		// Load up page
		$this->template->set('_user', $user)
					   ->title($this->module_details['name'], lang('user_register_header'))
					   ->build('register', array());
	}

}