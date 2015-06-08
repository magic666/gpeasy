<?php defined('is_running') or die('Not an entry point.'); ?>

<?php global $linkPrefix, $NewsletterText; ?>

<?php if (empty($this->failed)) : ?>

<div class="EasyNewsLetter_Sent">

<fieldset>
<legend>Newsletter Sending Completed.</legend>
<p>The newsletter has been sent to all subscribers.</p>
</fieldset>

</div>

<?php else : ?>

<form class="EasyNewsLetter_Sent" id="EasyNewsLetter_Sent" action="<?php echo $linkPrefix; ?>/Admin_EasyNewsLetter_Mailing" method="post">

<fieldset>
<legend>Newsletter Sending Incomplete.</legend>
<p><?php echo sprintf('The newsletter has not been sent to the following subscribers (%s) :', count($this->failed)); ?></p>
<ul>
<?php foreach ($this->failed as $email) : ?>
<li><?php echo $email; ?></li>
<?php endforeach; ?>
</ul>
</fieldset>

<input type="hidden" name="subject" value="<?php echo $this->subject; ?>" />
<input type="hidden" name="message" value="<?php echo $this->message; ?>" />
<input type="hidden" name="cmd" value="send_mailing" />
<?php foreach ($this->failed as $email) : ?>
<input type="hidden" name="email_list[]" value="<?php echo $email; ?>" />
<?php endforeach; ?>
<input type="submit" class="submit" value="Re-send the newsletter to these subscribers" />
</form>

<?php endif;?>
