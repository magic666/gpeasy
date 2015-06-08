<?php defined('is_running') or die('Not an entry point...'); ?>

<?php global $linkPrefix, $page; ?>

<h3><?php echo gpOutput::SelectText('Newsletter'); ?></h3>

<p><?php echo gpOutput::SelectText('If you\'d like to get our newsletter, please enter your e-mail-address here:'); ?></p>

<form action="<?php echo $linkPrefix; ?>/EasyNewsLetter" id="EasyNewsLetter_Subscribe" class="EasyNewsLetter_Subscribe" method="post">

<div>
<input class="input text" type="text" maxlength="254" id="nl_email" name="nl_email" value="" size="15"/>
</div>

<div style="display: none">
<input type="hidden" name="cmd" value="subscribe" />
<input type="hidden" name="newsletter_nonce" value="<?php echo htmlspecialchars(common::new_nonce('newsletter_post',true)); ?>" />
</div>

<div>
<input type="submit" class="submit" name="aaa" value="<?php echo gpOutput::SelectText('Subscribe'); ?>" />
</div>

</form>

