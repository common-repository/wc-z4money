<?php if ( ! defined( 'ABSPATH' ) ) exit;

$settings_z4money_creditcard = get_option( 'woocommerce_wc_z4money_creditcard_settings' );
$settings_z4money_boleto = get_option( 'woocommerce_wc_z4money_boleto_settings' );

?>

<?php if( 'yes' !== $settings_z4money_creditcard['testmode'] && empty( $settings_z4money_creditcard['token_secret'] ) ) : ?>
<div class="notice notice-info is-dismissible">
	<p><strong><?php _e( 'Z4Money - Credit Card', 'wc-z4money' ); ?></strong>: <?php _e( 'To receive payments by Credit Card you must enter your token secret in the settings.', 'wc-z4money' ); ?>
	</p>
</div>
<?php endif; ?>

<?php if( 'yes' === $settings_z4money_creditcard['testmode'] && empty( $settings_z4money_creditcard['token_secret_sandbox'] ) ) : ?>
<div class="notice notice-info is-dismissible">
	<p><strong><?php _e( 'Z4Money - Credit Card', 'wc-z4money' ); ?></strong>: <?php _e( 'To receive payments by Credit Card you must enter your token secret (sandbox) in the settings.', 'wc-z4money' ); ?>
	</p>
</div>
<?php endif; ?>

<?php if( 'yes' !== $settings_z4money_boleto['testmode'] && empty( $settings_z4money_boleto['token_secret'] ) ) : ?>
<div class="notice notice-info is-dismissible">
	<p><strong><?php _e( 'Z4Money - Boleto', 'wc-z4money' ); ?></strong>: <?php _e( 'To receive payments by Boleto you must enter your token secret in the settings.', 'wc-z4money' ); ?>
	</p>
</div>
<?php endif; ?>

<?php if( 'yes' === $settings_z4money_boleto['testmode'] && empty( $settings_z4money_boleto['token_secret_sandbox'] ) ) : ?>
<div class="notice notice-info is-dismissible">
	<p><strong><?php _e( 'Z4Money - Boleto', 'wc-z4money' ); ?></strong>: <?php _e( 'To receive payments by Boleto you must enter your token secret (sandbox) in the settings.', 'wc-z4money' ); ?>
	</p>
</div>
<?php endif; ?>