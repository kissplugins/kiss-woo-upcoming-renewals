<?php
/**
 * Plugin Name: KISS Upcoming Subscription Renewals
 * Description: Simple admin page showing upcoming WooCommerce subscription renewals sorted by next payment date.
 * Version: 1.0.0
 * Author: BinoidCBD
 * Requires Plugins: woocommerce, woocommerce-subscriptions
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', function () {
	add_submenu_page(
		'woocommerce',
		'Upcoming Renewals',
		'Upcoming Renewals',
		'manage_woocommerce',
		'upcoming-renewals',
		'kiss_render_upcoming_renewals'
	);
} );

function kiss_render_upcoming_renewals() {
	$per_page = isset( $_GET['per_page'] ) ? absint( $_GET['per_page'] ) : 10;
	$per_page = min( max( $per_page, 5 ), 100 );

	$subscriptions = wcs_get_subscriptions( [
		'subscriptions_per_page' => -1,
		'subscription_status'    => 'active',
	] );

	$upcoming = [];
	$now      = current_time( 'timestamp', true );

	foreach ( $subscriptions as $sub ) {
		$next_utc = $sub->get_date( 'next_payment' );
		if ( ! $next_utc ) {
			continue;
		}
		$ts = strtotime( $next_utc );
		if ( $ts <= $now ) {
			continue;
		}
		$upcoming[] = [
			'id'      => $sub->get_id(),
			'ts'      => $ts,
			'next'    => $next_utc,
			'name'    => trim( $sub->get_billing_first_name() . ' ' . $sub->get_billing_last_name() ),
			'email'   => $sub->get_billing_email(),
			'total'   => $sub->get_total(),
			'gateway' => $sub->get_payment_method_title(),
			'period'  => $sub->get_billing_interval() . ' ' . $sub->get_billing_period(),
			'status'  => $sub->get_status(),
		];
	}

	usort( $upcoming, function ( $a, $b ) {
		return $a['ts'] - $b['ts'];
	} );

	$display   = array_slice( $upcoming, 0, $per_page );
	$total     = count( $upcoming );
	$wp_tz     = wp_timezone();
	$page_url  = admin_url( 'admin.php?page=upcoming-renewals' );

	?>
	<div class="wrap">
		<h1>Upcoming Subscription Renewals</h1>
		<p><?php echo esc_html( $total ); ?> active subscriptions with upcoming renewals.
			Showing next <?php echo esc_html( count( $display ) ); ?>.
			<?php
			$links = [];
			foreach ( [ 10, 25, 50 ] as $n ) {
				if ( $n === $per_page ) {
					$links[] = '<strong>' . $n . '</strong>';
				} else {
					$links[] = '<a href="' . esc_url( add_query_arg( 'per_page', $n, $page_url ) ) . '">' . $n . '</a>';
				}
			}
			echo 'Show: ' . implode( ' | ', $links );
			?>
		</p>

		<table class="widefat striped fixed">
			<thead>
				<tr>
					<th style="width:90px;">Sub ID</th>
					<th style="width:200px;">Next Renewal</th>
					<th style="width:120px;">Time Until</th>
					<th>Customer</th>
					<th style="width:90px;">Total</th>
					<th style="width:100px;">Gateway</th>
					<th style="width:100px;">Billing Cycle</th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $display ) ) : ?>
					<tr><td colspan="7">No upcoming renewals found.</td></tr>
				<?php else : ?>
					<?php foreach ( $display as $row ) :
						$dt    = new DateTime( $row['next'], new DateTimeZone( 'UTC' ) );
						$dt->setTimezone( $wp_tz );
						$local = $dt->format( 'M j, Y g:i A T' );

						$diff    = $row['ts'] - $now;
						$days    = floor( $diff / 86400 );
						$hours   = floor( ( $diff % 86400 ) / 3600 );
						$minutes = floor( ( $diff % 3600 ) / 60 );
						if ( $days > 0 ) {
							$until = $days . 'd ' . $hours . 'h';
						} elseif ( $hours > 0 ) {
							$until = $hours . 'h ' . $minutes . 'm';
						} else {
							$until = $minutes . 'm';
						}

						$sub_url = admin_url( 'admin.php?page=wc-orders&action=edit&id=' . $row['id'] );
					?>
					<tr>
						<td><a href="<?php echo esc_url( $sub_url ); ?>">#<?php echo esc_html( $row['id'] ); ?></a></td>
						<td><?php echo esc_html( $local ); ?></td>
						<td><?php echo esc_html( $until ); ?></td>
						<td>
							<?php echo esc_html( $row['name'] ); ?><br>
							<small><?php echo esc_html( $row['email'] ); ?></small>
						</td>
						<td>$<?php echo esc_html( number_format( (float) $row['total'], 2 ) ); ?></td>
						<td><?php echo esc_html( $row['gateway'] ); ?></td>
						<td>Every <?php echo esc_html( $row['period'] ); ?></td>
					</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<?php
}
