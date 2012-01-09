<h2 class="page-title" id="page_title"><?php echo $this->module_details['name'].' '.lang('user_register_header') ?></h2>

<?php if ( ! empty($error_string)):?>
<div class="error-box">
	<?php echo $error_string;?>
</div>
<?php endif;?>

<?php echo form_open('beta/register/'.$this->uri->segment(3), array('id' => 'register')); ?>

<ul>
	<li>
		<label for="first_name"><?php echo lang('user_first_name') ?></label>
		<input type="text" name="first_name" maxlength="40" value="<?php echo $_user->first_name; ?>" />
	</li>
	
	<li>
		<label for="last_name"><?php echo lang('user_last_name') ?></label>
		<input type="text" name="last_name" maxlength="40" value="<?php echo $_user->last_name; ?>" />
	</li>
	
	<?php if ( ! Settings::get('auto_username')): ?>
	<li>
		<label for="username"><?php echo lang('user_username') ?></label>
		<input type="text" name="username" maxlength="100" value="<?php echo $_user->username; ?>" />
	</li>
	<?php endif; ?>
		
	<li>
		<label for="password"><?php echo lang('user_password') ?></label>
		<input type="password" name="password" maxlength="100" />
	</li>
	
	<li>
		<?php echo form_submit('btnSubmit', lang('user_register_btn')) ?>
	</li>

</ul>

<?php echo form_close(); ?>