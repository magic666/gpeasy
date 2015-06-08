<?php defined('is_running') or die('Not an entry point.'); ?>

<?php global $linkPrefix; ?>

<?php $this->adminTopNav(); ?>

<div id="drafting_wrapper">
Draft:&nbsp;
<a href="#" id="save_draft">Save</a>
&nbsp;|&nbsp;
<a href="#" id="load_draft">Load</a>
&nbsp;|&nbsp;
<a href="#" id="send_draft"><?php echo sprintf('Send (%s)', $this->config['from_email']); ?></a>
</div>

<div id="ticker_wrapper"><span id="ticker"></span></div>

<form class="EasyNewsLetter_Form" id="EasyNewsLetter_Form" action="<?php echo $linkPrefix; ?>/Admin_EasyNewsLetter_Mailing" method="post">

<fieldset>

<legend>Write the newsletter</legend>

</p>
<label for="subject"><span class="title">Subject</span></label>
<input class="input" type="text" id="EasyNewsLetter_Form_Subject" name="subject" value="" />
</p>

<p>
<label for="message"><span class="title">Message</span></label>
<textarea name="message" id="EasyNewsLetter_Form_Message" rows="10" cols="40"></textarea>
</p>

</fieldset>

<input type="hidden" name="cmd" value="send_mailing" />
<input type="submit" class="submit" value="Send newsletter" />

</form>



