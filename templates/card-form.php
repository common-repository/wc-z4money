<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<fieldset id="z4money-card-payment-form" class="z4money-payment-form">
	<p class="form-row form-row-first">
		<label for="z4money-card-number"><?php _e( 'Card Number', 'wc-z4money' ); ?> <span class="required">*</span></label>
		<input value="" id="z4money-card-number" name="<?php echo $card_number ?>" class="input-text wc-credit-card-form-card-number" type="tel" maxlength="22" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<p class="form-row form-row-last">
		<label for="z4money-card-holder-name"><?php _e( 'Name Printed on the Card', 'wc-z4money' ); ?> <span class="required">*</span></label>
		<input value="" id="z4money-card-holder-name" name="<?php echo $card_name ?>" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<div class="clear"></div>
	<p class="form-row form-row-first">
		<label for="z4money-card-expiry"><?php _e( 'Expiry (MM/YYYY)', 'wc-z4money' ); ?> <span class="required">*</span></label>
		<input value="" id="z4money-card-expiry" name="<?php echo $card_expiry ?>" class="input-text wc-credit-card-form-card-expiry" type="tel" autocomplete="off" placeholder="<?php _e( 'MM / YYYY', 'wc-z4money' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<p class="form-row form-row-last">
		<label for="z4money-card-cvc"><?php _e( 'Security Code', 'wc-z4money' ); ?> <span class="required">*</span></label>
		<input value="" id="z4money-card-cvc" name="<?php echo $card_cvc ?>" class="input-text wc-credit-card-form-card-cvc" type="tel" autocomplete="off" placeholder="<?php _e( 'CVC', 'wc-z4money' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<?php if ( ! empty( $installments ) ) : ?>
		<p class="form-row form-row-wide">
			<label for="z4money-installments"><?php _e( 'Installments', 'wc-z4money' ); ?> <span class="required">*</span></label>
			<?php echo $installments; ?>
		</p>
	<?php endif; ?>
	<div class="clear"></div>
</fieldset>
