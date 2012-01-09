<section class="title">
	<h4><?php echo lang('beta:invite'); ?></h4>
</section>

<section class="item">

	<?php echo form_open_multipart($this->uri->uri_string(), 'class="crud"'); ?>
		
		<div class="form_inputs">
		
		<ul>
			<li>
				<label for="name"><?php echo lang('email_label'); ?> <span>*</span></label>
				<div class="input"><?php echo form_input('email', set_value('email', $email)); ?></div>
			</li>
		</ul>
		
		</div><!--.form_inputs-->
		
		<div class="buttons">
			<?php $this->load->view('admin/partials/buttons', array('buttons' => array('save', 'cancel') )); ?>
		</div>
		
	<?php echo form_close(); ?>

</section>