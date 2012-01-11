<h2 class="page-title" id="page_title"><?php echo $this->module_details['name'].' '.lang('beta:sign_up') ?></h2>

<?php if ( ! empty($error_string)):?>
<div class="error-box">
	<?php echo $error_string;?>
</div>
<?php endif;?>

<?php echo form_open('beta/signup', array('id' => 'beta_signup')); ?>

<ul>
	<li>
		<label for="email"><?php echo lang('email_label') ?></label>
		<?php echo form_input('email', set_value('email', $email)); ?>
	</li>
</ul>

<p><?php echo form_submit('btnSubmit', lang('user_register_btn')) ?></p>

<?php echo form_close(); ?>