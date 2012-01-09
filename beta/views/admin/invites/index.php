<section class="title">
	<h4><?php echo lang('beta:invites'); ?></h4>
</section>

<section class="item">
	<?php echo form_open('admin/sample/delete');?>
	
	<?php if (!empty($invites)): ?>
	
		<table border="0" class="table-list">
			<thead>
				<tr>
					<th><?php echo lang('email_label'); ?></th>
					<th><?php echo lang('beta:invite_sent'); ?></th>
					<th><?php echo lang('beta:converted'); ?></th>
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
				<?php foreach( $invites as $invite ): ?>
				<tr>
					<td><?php echo $invite->email; ?></td>
					<td><?php echo date($this->settings->item('date_format').' g:i a', $invite->created); ?></td>
					<td><?php if($invite->converted == 'y'): echo date($this->settings->item('date_format').' g:i a', $invite->when_converted); endif; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		
	<?php else: ?>
		<div class="no_data"><?php echo lang('beta:no_invites'); ?></div>
	<?php endif;?>
	
	<?php echo form_close(); ?>
</section>