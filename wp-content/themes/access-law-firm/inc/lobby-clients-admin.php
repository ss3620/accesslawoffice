<?php
/**
 * Virtual Lobby — App Clients & activation codes admin.
 *
 * Receptionist/Admin can issue activation codes and create client accounts
 * for the mobile app (same data the staff app uses).
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Generate a unique activation code string.
 *
 * @return string
 */
function alf_generate_activation_code_string() {
	for ( $i = 0; $i < 10; $i++ ) {
		$code = 'ALF-' . strtoupper( wp_generate_password( 6, false, false ) );
		if ( function_exists( 'alf_find_activation_by_code' ) && ! alf_find_activation_by_code( $code ) ) {
			return $code;
		}
		if ( ! function_exists( 'alf_find_activation_by_code' ) ) {
			return $code;
		}
	}
	return 'ALF-' . strtoupper( wp_generate_password( 8, false, false ) );
}

/**
 * Create an activation code post.
 *
 * @param string $email Optional bound email.
 * @param string $code  Optional specific code.
 * @return array{success:bool,message:string,code?:string,id?:int}
 */
function alf_admin_create_activation_code( $email = '', $code = '' ) {
	$email = sanitize_email( (string) $email );
	$code  = strtoupper( sanitize_text_field( (string) $code ) );
	if ( '' === $code ) {
		$code = alf_generate_activation_code_string();
	}

	if ( function_exists( 'alf_find_activation_by_code' ) && alf_find_activation_by_code( $code ) ) {
		return array(
			'success' => false,
			'message' => __( 'That activation code already exists.', 'access-law-firm' ),
		);
	}

	$id = wp_insert_post(
		array(
			'post_type'   => 'alf_activation',
			'post_status' => 'publish',
			'post_title'  => $code,
		),
		true
	);
	if ( is_wp_error( $id ) || ! $id ) {
		return array(
			'success' => false,
			'message' => __( 'Could not create activation code.', 'access-law-firm' ),
		);
	}

	update_post_meta( $id, 'code', $code );
	update_post_meta( $id, 'email', $email );
	update_post_meta( $id, 'used', 0 );

	return array(
		'success' => true,
		'message' => sprintf(
			/* translators: %s: activation code */
			__( 'Activation code created: %s — send it to the client to open the app.', 'access-law-firm' ),
			$code
		),
		'code'    => $code,
		'id'      => (int) $id,
	);
}

/**
 * Create an app client and a matching used activation code.
 *
 * @param string $name  Client display name.
 * @param string $email Client email.
 * @return array{success:bool,message:string,code?:string,client_id?:int}
 */
function alf_admin_create_app_client( $name, $email ) {
	$name  = sanitize_text_field( (string) $name );
	$email = sanitize_email( (string) $email );

	if ( strlen( $name ) < 2 || ! is_email( $email ) ) {
		return array(
			'success' => false,
			'message' => __( 'Enter a valid client name and email.', 'access-law-firm' ),
		);
	}

	// Avoid duplicate active clients with the same email.
	$existing = get_posts(
		array(
			'post_type'      => 'alf_app_client',
			'post_status'    => 'publish',
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => 'email',
			'meta_value'     => strtolower( $email ),
		)
	);
	if ( ! empty( $existing ) ) {
		return array(
			'success' => false,
			'message' => __( 'A client with that email already exists.', 'access-law-firm' ),
		);
	}

	$code_result = alf_admin_create_activation_code( $email );
	if ( empty( $code_result['success'] ) ) {
		return $code_result;
	}
	$code          = $code_result['code'];
	$activation_id = (int) $code_result['id'];

	$client_id = wp_insert_post(
		array(
			'post_type'   => 'alf_app_client',
			'post_status' => 'publish',
			'post_title'  => $name,
		),
		true
	);
	if ( is_wp_error( $client_id ) || ! $client_id ) {
		return array(
			'success' => false,
			'message' => __( 'Could not create client account.', 'access-law-firm' ),
		);
	}

	update_post_meta( $client_id, 'name', $name );
	update_post_meta( $client_id, 'email', strtolower( $email ) );
	update_post_meta( $client_id, 'thread_id', (string) $client_id );
	update_post_meta( $client_id, 'active', 1 );
	update_post_meta( $client_id, 'activation_code', $code );

	update_post_meta( $activation_id, 'used', 1 );
	update_post_meta( $activation_id, 'client_id', $client_id );
	update_post_meta( $activation_id, 'email', strtolower( $email ) );

	// Welcome system message (same as app activation).
	$msg_id = wp_insert_post(
		array(
			'post_type'    => 'alf_chat_msg',
			'post_status'  => 'publish',
			'post_title'   => 'Welcome',
			'post_content' => sprintf(
				/* translators: %s: client name */
				__( 'Welcome %s. Your attorney and receptionist can see this chat. Send a message any time and we will respond during office hours.', 'access-law-firm' ),
				$name
			),
		)
	);
	if ( $msg_id && ! is_wp_error( $msg_id ) ) {
		update_post_meta( $msg_id, 'thread_id', (string) $client_id );
		update_post_meta( $msg_id, 'sender_id', 'system' );
		update_post_meta( $msg_id, 'sender_role', 'system' );
		update_post_meta( $msg_id, 'sender_name', 'Access Law Firm' );
		update_post_meta( $msg_id, 'urgent', 0 );
	}

	return array(
		'success'   => true,
		'message'   => sprintf(
			/* translators: 1: client name, 2: activation code */
			__( 'Client “%1$s” created. Activation code: %2$s — they use this code in the app to sign in.', 'access-law-firm' ),
			$name,
			$code
		),
		'code'      => $code,
		'client_id' => (int) $client_id,
	);
}

/**
 * Render App Clients admin page.
 */
function alf_render_app_clients_admin_page() {
	if ( ! alf_user_can_manage_lobby() ) {
		return;
	}

	$notice_html = '';

	if ( isset( $_POST['alf_issue_code_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alf_issue_code_nonce'] ) ), 'alf_issue_code' ) ) {
		$email  = isset( $_POST['code_email'] ) ? sanitize_email( wp_unslash( $_POST['code_email'] ) ) : '';
		$result = alf_admin_create_activation_code( $email );
		$class  = ! empty( $result['success'] ) ? 'notice-success' : 'notice-error';
		$notice_html = '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>' . esc_html( $result['message'] ) . '</p>';
		if ( ! empty( $result['success'] ) && ! empty( $result['code'] ) ) {
			$notice_html .= '<p><code style="font-size:16px;padding:4px 8px;background:#f0f0f1">' . esc_html( $result['code'] ) . '</code></p>';
		}
		$notice_html .= '</div>';
	}

	if ( isset( $_POST['alf_create_client_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alf_create_client_nonce'] ) ), 'alf_create_client' ) ) {
		$name   = isset( $_POST['client_name'] ) ? sanitize_text_field( wp_unslash( $_POST['client_name'] ) ) : '';
		$email  = isset( $_POST['client_email'] ) ? sanitize_email( wp_unslash( $_POST['client_email'] ) ) : '';
		$result = alf_admin_create_app_client( $name, $email );
		$class  = ! empty( $result['success'] ) ? 'notice-success' : 'notice-error';
		$notice_html = '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>' . esc_html( $result['message'] ) . '</p>';
		if ( ! empty( $result['success'] ) && ! empty( $result['code'] ) ) {
			$notice_html .= '<p><strong>' . esc_html__( 'Give this code to the client:', 'access-law-firm' ) . '</strong> ';
			$notice_html .= '<code style="font-size:16px;padding:4px 8px;background:#f0f0f1">' . esc_html( $result['code'] ) . '</code></p>';
		}
		$notice_html .= '</div>';
	}

	$clients = get_posts(
		array(
			'post_type'      => 'alf_app_client',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$codes = get_posts(
		array(
			'post_type'      => 'alf_activation',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);
	?>
	<div class="wrap alf-app-clients-admin">
		<h1><?php esc_html_e( 'App Clients', 'access-law-firm' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'Create mobile app clients or issue activation codes. Clients open the app with their name, email, and code — no password.', 'access-law-firm' ); ?>
		</p>

		<?php echo $notice_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_* above. ?>

		<div class="alf-client-admin-grid">
			<div class="alf-client-admin-card">
				<h2><?php esc_html_e( 'Create client', 'access-law-firm' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Creates the client account now and an activation code they use to sign into the app.', 'access-law-firm' ); ?></p>
				<form method="post" action="">
					<?php wp_nonce_field( 'alf_create_client', 'alf_create_client_nonce' ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="client_name"><?php esc_html_e( 'Client name', 'access-law-firm' ); ?></label></th>
							<td><input type="text" class="regular-text" id="client_name" name="client_name" required autocomplete="off"></td>
						</tr>
						<tr>
							<th scope="row"><label for="client_email"><?php esc_html_e( 'Client email', 'access-law-firm' ); ?></label></th>
							<td><input type="email" class="regular-text" id="client_email" name="client_email" required autocomplete="off"></td>
						</tr>
					</table>
					<?php submit_button( __( 'Create client', 'access-law-firm' ), 'primary', 'submit', false ); ?>
				</form>
			</div>

			<div class="alf-client-admin-card">
				<h2><?php esc_html_e( 'Issue activation code only', 'access-law-firm' ); ?></h2>
				<p class="description"><?php esc_html_e( 'Same as the mobile staff app. Client completes signup themselves when they redeem the code.', 'access-law-firm' ); ?></p>
				<form method="post" action="">
					<?php wp_nonce_field( 'alf_issue_code', 'alf_issue_code_nonce' ); ?>
					<table class="form-table" role="presentation">
						<tr>
							<th scope="row"><label for="code_email"><?php esc_html_e( 'Client email (optional)', 'access-law-firm' ); ?></label></th>
							<td>
								<input type="email" class="regular-text" id="code_email" name="code_email" autocomplete="off">
								<p class="description"><?php esc_html_e( 'If set, only that email can redeem the code.', 'access-law-firm' ); ?></p>
							</td>
						</tr>
					</table>
					<?php submit_button( __( 'Create activation code', 'access-law-firm' ), 'secondary', 'submit', false ); ?>
				</form>
			</div>
		</div>

		<hr>

		<h2><?php esc_html_e( 'Clients', 'access-law-firm' ); ?></h2>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Name', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Email', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Activation code', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Created', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Status', 'access-law-firm' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $clients ) ) : ?>
					<tr><td colspan="5"><?php esc_html_e( 'No app clients yet.', 'access-law-firm' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $clients as $client ) : ?>
						<?php
						$c_name  = (string) get_post_meta( $client->ID, 'name', true ) ?: $client->post_title;
						$c_email = (string) get_post_meta( $client->ID, 'email', true );
						$c_code  = (string) get_post_meta( $client->ID, 'activation_code', true );
						$active  = (bool) get_post_meta( $client->ID, 'active', true );
						?>
						<tr>
							<td><strong><?php echo esc_html( $c_name ); ?></strong></td>
							<td><?php echo esc_html( $c_email ?: '—' ); ?></td>
							<td><code><?php echo esc_html( $c_code ?: '—' ); ?></code></td>
							<td><?php echo esc_html( get_the_date( 'M j, Y g:i a', $client ) ); ?></td>
							<td><?php echo $active ? esc_html__( 'Active', 'access-law-firm' ) : esc_html__( 'Inactive', 'access-law-firm' ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>

		<h2 style="margin-top:28px"><?php esc_html_e( 'Activation codes', 'access-law-firm' ); ?></h2>
		<table class="wp-list-table widefat fixed striped">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Code', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Bound email', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Status', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Created', 'access-law-firm' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if ( empty( $codes ) ) : ?>
					<tr><td colspan="4"><?php esc_html_e( 'No activation codes yet.', 'access-law-firm' ); ?></td></tr>
				<?php else : ?>
					<?php foreach ( $codes as $code_post ) : ?>
						<?php
						$code_val = (string) get_post_meta( $code_post->ID, 'code', true ) ?: $code_post->post_title;
						$email_v  = (string) get_post_meta( $code_post->ID, 'email', true );
						$used     = (bool) get_post_meta( $code_post->ID, 'used', true );
						?>
						<tr>
							<td><code><?php echo esc_html( $code_val ); ?></code></td>
							<td><?php echo esc_html( $email_v ?: '—' ); ?></td>
							<td>
								<?php
								echo $used
									? esc_html__( 'Used', 'access-law-firm' )
									: esc_html__( 'Available', 'access-law-firm' );
								?>
							</td>
							<td><?php echo esc_html( get_the_date( 'M j, Y g:i a', $code_post ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
	<style>
		.alf-client-admin-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin:20px 0 28px}
		.alf-client-admin-card{background:#fff;border:1px solid #c3c4c7;border-radius:4px;padding:16px 20px 20px}
		.alf-client-admin-card h2{margin-top:0;font-size:1.15em}
		.alf-client-admin-card .form-table th{width:160px;padding-left:0}
		@media (max-width:900px){.alf-client-admin-grid{grid-template-columns:1fr}}
	</style>
	<?php
}
