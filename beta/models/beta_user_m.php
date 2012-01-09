<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Beta Signups Module
 *
 * @author 		Adam Fairholm - Parse19
 * @website		http://parse19.com
 * @package 	PyroCMS
 * @subpackage 	Beta Module
 */
class Beta_user_m extends MY_Model {

	public function __construct()
	{		
		parent::__construct();
		
		$this->_table = 'beta_users';

		$this->validation = array(
			array(
				'field' => 'email',
				'label' => 'Email',
				'rules' => 'trim|max_length[150]|valid_email|required'
			)
		);
	}

	// --------------------------------------------------------------------------

	/**
	 * Invite a user
	 *
	 * @access	public
	 * @param	string - email
	 * @return 	bool
	 */
	public function invite_user($email)
	{
		// -----------------------
		// Insert Invite into DB
		// -----------------------

		$insert = array(
			'email' 	=> $email,
			'created'	=> time(),
			'type'		=> 'invite',
			'hash'		=> $this->create_hash()
		);

		if( ! $this->db->insert($this->_table, $insert) ) return false;
	
		// -----------------------
		// Send Invite Email
		// -----------------------
	
		$beta_user_id = $this->db->insert_id();
		
		// Get the template
		$template = $this->db
							->limit(1)
							->where('slug', 'beta_invite')
							->get('email_templates')
							->row();
							
		if( ! $template ) return false;
		
		$data = array('hash' => $insert['hash']);
		
		// Parse subject & body
		$template->body = html_entity_decode($this->parser->parse_string(str_replace(array('&quot;', '&#39;'), array('"', "'"), $template->body), $data, true));
		$template->subject = html_entity_decode($this->parser->parse_string(str_replace(array('&quot;', '&#39;'), array('"', "'"), $template->subject), $data, true));
		
		$this->load->library('Email');
		
		$this->email->from($this->settings->item('server_email'));
		$this->email->reply_to($this->settings->item('server_email'));
		$this->email->to($email);
		$this->email->subject($template->subject);
		$this->email->message($template->body);
		
		if( ! $this->email->send() ) return false;
		
		$this->email->clear();
		
		return true;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Create a hash
	 *
	 * Keep going until it is unique
	 *
	 * @access	public
	 * @return	string - the hash
	 */
	public function create_hash()
	{
		$hash = md5(sha1(time()));
		
		$obj = $this->db->where('hash', $hash)->get('beta_users');
		
		if($obj->num_rows() == 0):
		
			return $hash;
		
		else:
		
			return $this->create_hash();
		
		endif;
	}

	// --------------------------------------------------------------------------
	
	/**
	 * Add a user to the beta (IE: mark them
	 * as accepted and send our email with
	 * registeration URL.
	 *
	 * @access	public
	 * @param	obj - beta user
	 * @return	bool
	 */
	public function add_user_to_beta($beta_user)
	{
		// -----------------------
		// Set status to accepted
		// -----------------------
		
		if( ! $this->db->limit(1)->where('id', $beta_user->id)->update('beta_users', array('status' => 'a')) ) return false;
		
		// -----------------------
		// Send Email
		// -----------------------

		// Get the template
		$template = $this->db
							->limit(1)
							->where('slug', 'beta_accepted')
							->get('email_templates')
							->row();
							
		if( ! $template ) return false;
		
		$data = array('hash' => $beta_user->hash);
		
		// Parse subject & body
		$template->body = html_entity_decode($this->parser->parse_string(str_replace(array('&quot;', '&#39;'), array('"', "'"), $template->body), $data, true));
		$template->subject = html_entity_decode($this->parser->parse_string(str_replace(array('&quot;', '&#39;'), array('"', "'"), $template->subject), $data, true));
		
		$this->load->library('Email');
		
		$this->email->from($this->settings->item('server_email'));
		$this->email->reply_to($this->settings->item('server_email'));
		$this->email->to($beta_user->email);
		$this->email->subject($template->subject);
		$this->email->message($template->body);
		
		if( ! $this->email->send() ) return false;
		
		$this->email->clear();
		
		return true;
	}

}