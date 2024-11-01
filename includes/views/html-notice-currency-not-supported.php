<?php if ( ! defined( 'ABSPATH' ) ) exit;

$is_valid_currency = wc_z4money()->environment()->valid_woocommerce_currency();

?>

<?php if( ! $is_valid_currency ) : ?>
<div class="notice notice-info is-dismissible">
	<p><strong><?php _e( 'Z4Money Disabled', 'wc-z4money' ); ?></strong>: <?php __( 'Currency not supported. Works only with Brazilian Real.', 'wc-z4money' ); ?>
	</p>
</div>
<?php endif; ?>