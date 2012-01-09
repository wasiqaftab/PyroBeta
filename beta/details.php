<?php defined('BASEPATH') or exit('No direct script access allowed');

class Module_Beta extends Module {

	public $version = '0.8';

	public function info()
	{
		return array(
			'name' => array(
				'en' => 'Beta'
			),
			'description' => array(
				'en' => 'Accept and manage beta signups.'
			),
			'frontend' => true,
			'backend' => true,
			'menu' => 'users',

			'sections' => array(
				'signups' => array(
					'name' 	=> 'beta:signups',
					'uri' 	=> 'admin/beta',
				),
				'invites' => array(
					'name' 	=> 'beta:invites',
					'uri' 	=> 'admin/beta/invites',
						'shortcuts' => array(
							'create' => array(
								'name' 	=> 'beta:invite',
								'uri' 	=> 'admin/beta/invites/invite',
								'class' => 'add'
								)
							)
						)
				)
		);
	}

	// --------------------------------------------------------------------------

	/**
	 * Install
	 *
	 * @access	public
	 * @return 	bool
	 */
	public function install()
	{
		// -----------------------
		// Add Beta Signup Table
		// -----------------------
	
		$this->dbforge->drop_table('beta_users');

		$fields = array(
	                'id' => array(
								'type' 				=> 'INT',
								'constraint' 		=> '11',
								'auto_increment' 	=> TRUE
								),
	                'created' => array(
								'type' 				=> 'INT',
								'constraint' 		=> '11'
								),
					'type' => array(
								'type' 				=> 'ENUM',
								'constraint' 		=> "'invite','signup'",
								),
					'email' => array(
								'type' 				=> 'VARCHAR',
								'constraint' 		=> '150'
								),
					'status' => array(
								'type' 				=> 'CHAR',
								'constraint' 		=> '1',
								'null'				=> true
								),
					'converted' => array(
								'type' 				=> 'CHAR',
								'constraint' 		=> '1',
								'default'			=> 'n'
								),
	                'when_converted' => array(
								'type' 				=> 'INT',
								'constraint' 		=> '11',
								'null'				=> true
								),
					'hash' => array(
								'type' 				=> 'VARCHAR',
								'constraint' 		=> '255'
								),
				);

		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', true);

		if( ! $this->dbforge->create_table('beta_users') ) return false;

		// -----------------------
		// Add Email Templates
		// -----------------------

		$this->db->limit(1)->where('slug', 'beta_accepted')->delete('email_templates');
		$this->db->limit(1)->where('slug', 'beta_invite')->delete('email_templates');
	
		// Beta Signup Accepted Template
	
		$accepted_template = array(
			'slug'			=> 'beta_accepted',
			'name'			=> 'Beta: Invite Accepted',
			'description'	=> 'Email sent to user when a beta has been approved.',
			'subject'		=> 'Your beta request has been approved',
			'body'			=> $this->accepted_template(),
			'lang'			=> 'en',
			'is_default'	=> '0'
		);
		
		if( ! $this->db->limit(1)->insert('email_templates', $accepted_template) ) return false;

		// Beta Invite Template

		$invite_template = array(
			'slug'			=> 'beta_invite',
			'name'			=> 'Beta: Invite Email',
			'description'	=> 'Email sent to user when invited to beta.',
			'subject'		=> 'Beta Invitation to  {{ settings:site_name }}',
			'body'			=> $this->invite_template(),
			'lang'			=> 'en',
			'is_default'	=> '0'
		);
		
		if( ! $this->db->limit(1)->insert('email_templates', $invite_template) ) return false;
		
		return true;
	}

	// --------------------------------------------------------------------------

	/**
	 * Uninstall
	 *
	 * @access	public
	 * @return	bool
	 */
	public function uninstall()
	{
		if( ! $this->dbforge->drop_table('beta_users') ) return false;
		
		if( ! $this->db->limit(1)->where('slug', 'beta_accepted')->delete('email_templates') ) return false;
		if( ! $this->db->limit(1)->where('slug', 'beta_invite')->delete('email_templates') ) return false;
		
		return true;
	}

	// --------------------------------------------------------------------------

	public function upgrade($old_version)
	{
		return true;
	}
	
	// --------------------------------------------------------------------------

	/**
	 * Beta Invite Email Template
	 *
	 * @access	private
	 * @return	string
	 */
	private function invite_template()
	{
		return '<p>You have been invited to the beta for {{ settings:site_name }}.</p>
		
<p>You can register your account at the following url:</p>

<p><a href="{{ url:site }}beta/register/{{ hash }}">{{ url:site }}beta/register/{{ hash }}</a></p>';
	
	}

	// --------------------------------------------------------------------------

	/**
	 * Beta Signup Accepted Template
	 *
	 * @access	private
	 * @return	string
	 */
	private function accepted_template()
	{
		return '<p>Good news! Your beta for {{ settings:site_name }} has been approved.</p>
		
<p>You can register your account at the following url:</p>

<p><a href="{{ url:site }}beta/register/{{ hash }}">{{ url:site }}beta/register/{{ hash }}</a></p>';
	
	}

	// --------------------------------------------------------------------------

	public function help()
	{
		return "No documentation has been added for this module.<br />Contact the module developer for assistance.";
	}

}