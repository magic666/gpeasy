<?php defined('is_running') or die('Not an entry point...'); ?>

<?php global $config, $addonFolderName, $langmessage; ?>

<div class="EasyNewsLetter_EditConfig">

<?php $this->adminTopNav(); ?>

<div>
<?php echo common::Link('Admin_Theme_Content',$langmessage['editable_text'],'cmd=addontext&amp;addon='.$addonFolderName,'name="gpabox"'); ?>
</div>

<form action="<?php echo common::GetUrl('Admin_EasyNewsLetter_EditConfig'); ?>" method="post" name="SkypeStatus_EasyNewsLetter">

<fieldset>

<legend>General</legend>

<label for="from_name">(Sender) From - Name</label>
<input type="text" name="from_name" value="<?php echo $this->config['from_name']; ?>" /> 
<br />

<label for="from_email">(Sender) From - Email Address</label>
<input type="text" name="from_email" value="<?php echo $this->config['from_email']; ?>" /> 
<br />

<hr />

<label for="double_optin_validate">Subscription - Double Opt-in Validation</label>
<input type="hidden" name="double_optin_validate" value="0" <?php echo ($this->config['double_optin_validate'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="double_optin_validate" value="1" <?php echo ($this->config['double_optin_validate'] == 1 ? 'checked="checked"' : ''); ?> />
<br />

<hr />

<label for="send_to_email_from">Mailing - Copy to Sender</label>
<input type="hidden" name="send_to_email_from" value="0" <?php echo ($this->config['send_to_email_from'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="send_to_email_from" value="1" <?php echo ($this->config['send_to_email_from'] == 1 ? 'checked="checked"' : ''); ?> />
<br />

<label for="on_mailing_draft">Mailing - Draft Action</label>
<select name="on_mailing_draft">
	<?php $on_mailing_draft_list = array('', 'delete', 'update'); ?>
	<?php foreach ($on_mailing_draft_list as $on_mailing_draft) : ?>
		<option value="<?php echo $on_mailing_draft; ?>" 
			<?php echo ($this->config['on_mailing_draft'] == $on_mailing_draft ? 'selected="selected"' : ''); ?>>
			<?php echo ucfirst($on_mailing_draft); ?>
		</option>
	<?php endforeach; ?>
</select>
<br />

<hr />

<label for="form_css">Gadget Form - Css</label>
<textarea name="form_css"><?php echo $this->config['form_css']; ?></textarea>
<br />

<label for="form_js">Gadget Form - Js</label>
<textarea name="form_js"><?php echo $this->config['form_js']; ?></textarea>
<br />

<hr />

<label for="email_list_paginate">Email List - Pagination</label>
<select name="email_list_paginate">
	<?php $paginate_list = array(1, 5, 10, 15, 20, 25, 50, 100); ?>
	<?php foreach ($paginate_list as $email_list_paginate) : ?>
		<option value="<?php echo $email_list_paginate; ?>" 
			<?php echo ($this->config['email_list_paginate'] == $email_list_paginate ? 'selected="selected"' : ''); ?>>
			<?php echo $email_list_paginate; ?>
		</option>
	<?php endforeach; ?>
</select>
<br />

<label for="hide_import_form">Email List - Hide Import Form</label>
<input type="hidden" name="hide_import_form" value="0" <?php echo ($this->config['hide_import_form'] == 0 ? 'checked="checked"' : ''); ?> />
<input type="checkbox" name="hide_import_form" value="1" <?php echo ($this->config['hide_import_form'] == 1 ? 'checked="checked"' : ''); ?> />
<br />

</fieldset>

<input type="submit" name="save_config" value="<?php echo $langmessage['save']; ?>" class="gpsubmit" style="float:left" />

</form>

</div>
