<section class="title">
	<h4><?php echo lang('beta:signups'); ?></h4>
</section>

<section class="item">
	<?php echo form_open('admin/sample/delete');?>
	
	<?php if (!empty($signups)): ?>
	
		<table border="0" class="table-list">
			<thead>
				<tr>
					<th><?php echo lang('email_label'); ?></th>
					<th><?php echo lang('beta:signed_up'); ?></th>
					<th><?php echo lang('beta:status'); ?></th>
					<th></th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<td colspan="5">
						<div class="inner"><?php $this->load->view('admin/partials/pagination'); ?></div>
					</td>
				</tr>
			</tfoot>
			<tbody>
				<?php foreach( $signups as $signups ): ?>
				<tr>
					<td><?php echo $signups->email; ?></td>
					<td><?php echo date($this->settings->item('date_format').' g:i a', $signups->created); ?></td>
					<td><?php echo signup_status($signups->status); ?></td>
					<td class="actions">
						<?php if($signups->status == 'p'): ?>
						<a href="<?php echo site_url('admin/beta/add_user/'.$signups->id); ?>" class="button"><?php echo lang('beta:add_to_beta'); ?></a>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
	<?php else: ?>
		<div class="no_data"><?php echo lang('beta:no_signups'); ?></div>
	<?php endif;?>
	
	<?php echo form_close(); ?>
</section>