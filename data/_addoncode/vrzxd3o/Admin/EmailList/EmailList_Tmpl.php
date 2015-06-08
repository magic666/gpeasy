<?php defined('is_running') or die('Not an entry point.'); ?>

<?php global $linkPrefix, $config, $langmessage; ?>

<?php $this->adminTopNav(); ?>

<?php if(!count($this->addresses)) : ?>

<div id="EasyNewsLetter_EmptyAddressFile">

<fieldset>
<legend>Warning</legend>
<p>The Address file is empty (no subscribers).</p>
</fieldset>

</div>

<?php else: ?>

<?php $admin_unsubscribe = sprintf('http://%1$s%2$s/Admin_EasyNewsLetter_EmailList?cmd=unsubscribe&nl_email=%3$s', $_SERVER['SERVER_NAME'], $linkPrefix, '%s'); ?>

<p>The Newsletter will be sent to the following addresses:</p>

<p class="search_nav search_nav_top">
<?php echo sprintf('Subscribers %1$s - %2$s out of %3$s',($this->start+1),$this->end,$this->total); ?>
</p>

<table class="bordered" id="EmailList">

<tr>
<th>Email</th>
<th>Inscription Date</th>
<th>Sent</th>
<th>Activated</th>
<th></th>
</tr>

<?php foreach($this->addresses as $email => $info) : ?>
<tr>
<td><?php echo $email; ?></td>
<td><?php echo strftime($config['dateformat'], $info['datetime']); ?></td>
<td><?php echo $info['sent']; ?></td>
<td>
	<a href="#" onclick="document.forms['ENL_Activation_Switcher'].email.value='<?php echo $email; ?>'; document.forms['ENL_Activation_Switcher'].submit();return false;">
		<?php echo ($info['activated'] ? 'Yes' : 'No'); ?>
	</a>
</td>
<td>
	<a href="<?php echo sprintf($admin_unsubscribe, urlencode($email)); ?>">
		<?php echo 'Delete'; ?>
	</a>
</td>
</tr>
<?php endforeach; ?>

<?php if( $this->total_pages > 1 ) : ?>
<tr><th colspan="5">
	<?php for($i=0;$i<$this->total_pages;$i++) : ?>
		<?php if( $i == $this->current_page ) : ?>
			<span><?php echo ($i+1); ?></span>
			<?php continue; ?>
		<?php endif; ?>
		<?php if( $i > 0 ) : ?>
			<?php $query = 'pg='.$i; ?>
		<?php endif; ?>
		<?php $attr = ''; ?>
		<?php $query = (isset($query) ? $query : ''); ?>
		<?php echo common::Link('Admin_EasyNewsLetter_EmailList',$i+1,$query); ?>
	<?php endfor; ?>
</th></tr>
<?php endif; ?>

</table>

<div style="diplay: none; !important">
<form name="ENL_Activation_Switcher" method="post" action="Admin_EasyNewsLetter_EmailList"> 
<input type="hidden" name="cmd" value="switcher" /> 
<input type="hidden" name="email" value="" />
<input type="hidden" name="verified" value="<?php echo common::new_nonce('post',true); ?>"/>
</form>
</div>

<hr style="clear: both; visibility: hidden;" />

<?php endif; ?>

<?php if ($this->import_show) : ?>
<form name="EasyNewsLetter_ImportForm" id="EasyNewsLetter_ImportForm" method="post" action="Admin_EasyNewsLetter_EmailList"> 
<fieldset>
<legend>Import Subscribers from the Newsletter Plugin</legend>
<p>The Newsletter plugin is installed on your website.</p>
<p>Would you like to import the Newsletter's subscribers to your EasyNewsLetter install?</p>

<input type="hidden" name="cmd" value="import_subscribers" /> 
<input type="hidden" name="verified" value="<?php echo common::new_nonce('post',true); ?>"/>

</fieldset>
<input type="submit" name="import_subscribers" value="Import Existing Subscribers" class="gpsubmit" style="float:right" />
<p>
<input type="hidden" name="hide_import_form" value="0" <?php echo ($this->config['hide_import_form'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="hide_import_form" value="1" <?php echo ($this->config['hide_import_form'] == 1 ? 'checked="checked"' : ''); ?> />
<label for="show_import_form">Hide this form after the import is completed</label>
</p>
</form>
<?php endif; ?>
