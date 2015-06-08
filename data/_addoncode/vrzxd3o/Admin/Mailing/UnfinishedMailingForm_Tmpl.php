<?php defined('is_running') or die('Not an entry point.'); ?>

<?php global $linkPrefix; ?>

<form class="EasyNewsLetter_Unfinished" id="EasyNewsLetter_Sent" action="<?php echo $linkPrefix; ?>/Admin_EasyNewsLetter_Mailing" method="post">

<fieldset>
<legend>Newsletter Sending Interrupted</legend>
<p><?php echo sprintf('The newsletter has not been sent to the following subscribers (%s) :', count($this->email_list)); ?></p>
<ul>
<?php foreach ($this->email_list as $email) : ?>
<li><?php echo $email; ?></li>
<?php endforeach; ?>
</ul>
</fieldset>

<input type="hidden" name="subject" value="<?php echo $this->subject; ?>" />
<input type="hidden" name="message" value="<?php echo $this->message; ?>" />
<input type="hidden" name="cmd" value="send_mailing" />
<?php foreach ($this->email_list as $email) : ?>
<input type="hidden" name="email_list[]" value="<?php echo $email; ?>" />
<?php endforeach; ?>
<input type="submit" class="submit" value="Send Newsletter" />
</form>


