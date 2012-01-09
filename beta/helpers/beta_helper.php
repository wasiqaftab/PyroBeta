<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Signup Status
 *
 * Converts signup status shorthand
 * into a readable status
 *
 * @access	public
 * @param	string - status shortcode
 * @return	string - full, readable status
 */
function signup_status($code)
{
	if($code == 'p'):
	
		return lang('beta:pending');
		
	elseif($code == 'd'):
	
		return lang('beta:denied');
	
	elseif($code == 'a'):
	
		return lang('beta:accepted');
	
	endif;
}