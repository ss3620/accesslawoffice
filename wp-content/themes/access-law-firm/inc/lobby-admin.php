<?php
/**
 * Receptionist role and Virtual Lobby admin console.
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current user can manage the Virtual Lobby.
 *
 * @return bool
 */
function alf_user_can_manage_lobby() {
	return current_user_can( 'alf_manage_lobby' ) || current_user_can( 'manage_options' );
}

/**
 * Register Receptionist role and grant lobby capability to Administrators.
 */
function alf_register_receptionist_role() {
	$admin = get_role( 'administrator' );
	if ( $admin && ! $admin->has_cap( 'alf_manage_lobby' ) ) {
		$admin->add_cap( 'alf_manage_lobby' );
	}

	$existing = get_role( 'alf_receptionist' );
	if ( $existing ) {
		$existing->add_cap( 'read' );
		$existing->add_cap( 'alf_manage_lobby' );
		return;
	}

	add_role(
		'alf_receptionist',
		__( 'Receptionist', 'access-law-firm' ),
		array(
			'read'             => true,
			'alf_manage_lobby' => true,
		)
	);
}
add_action( 'after_setup_theme', 'alf_register_receptionist_role' );

/**
 * Whether the current user is a Receptionist (not an Administrator).
 * Used only for UI restrictions (dashboard/menu). Capability checks stay separate.
 *
 * @return bool
 */
function alf_is_receptionist_user() {
	$user = wp_get_current_user();
	if ( ! $user || empty( $user->ID ) ) {
		return false;
	}

	// Administrators always keep the full wp-admin UI.
	if ( user_can( $user, 'manage_options' ) || in_array( 'administrator', (array) $user->roles, true ) ) {
		return false;
	}

	// Only the Receptionist role gets the stripped dashboard/menu.
	return in_array( 'alf_receptionist', (array) $user->roles, true );
}

/**
 * Receptionist Dashboard: remove every widget except Virtual Lobby.
 */
function alf_receptionist_dashboard_widgets() {
	if ( ! alf_is_receptionist_user() ) {
		return;
	}

	global $wp_meta_boxes;

	if ( empty( $wp_meta_boxes['dashboard'] ) || ! is_array( $wp_meta_boxes['dashboard'] ) ) {
		return;
	}

	foreach ( $wp_meta_boxes['dashboard'] as $context => $priorities ) {
		if ( ! is_array( $priorities ) ) {
			continue;
		}
		foreach ( $priorities as $priority => $boxes ) {
			if ( ! is_array( $boxes ) ) {
				continue;
			}
			foreach ( array_keys( $boxes ) as $box_id ) {
				if ( 'alf_lobby_widget' === $box_id ) {
					continue;
				}
				remove_meta_box( $box_id, 'dashboard', $context );
			}
		}
	}
}
add_action( 'wp_dashboard_setup', 'alf_receptionist_dashboard_widgets', 999 );

/**
 * Hide the WordPress Welcome panel for Receptionists.
 */
function alf_receptionist_hide_welcome_panel() {
	if ( alf_is_receptionist_user() ) {
		remove_action( 'welcome_panel', 'wp_welcome_panel' );
	}
}
add_action( 'admin_head-index.php', 'alf_receptionist_hide_welcome_panel' );

/**
 * Receptionist admin menu: keep only Dashboard + Virtual Lobby (+ profile via admin bar).
 */
function alf_receptionist_admin_menu() {
	if ( ! alf_is_receptionist_user() ) {
		return;
	}

	global $menu, $submenu;

	$allowed = array(
		'index.php',           // Dashboard
		'alf-virtual-lobby',   // Virtual Lobby
		'profile.php',         // Profile (if present)
	);

	if ( is_array( $menu ) ) {
		foreach ( $menu as $key => $item ) {
			$slug = isset( $item[2] ) ? $item[2] : '';
			if ( ! in_array( $slug, $allowed, true ) ) {
				remove_menu_page( $slug );
			}
		}
	}

	// Hide Screen Options / help clutter is optional; keep Profile under Users if WP added it.
	if ( isset( $submenu['alf-virtual-lobby'] ) && is_array( $submenu['alf-virtual-lobby'] ) ) {
		foreach ( $submenu['alf-virtual-lobby'] as $sub ) {
			// Settings submenu requires manage_options — already hidden for receptionist.
		}
	}
}
add_action( 'admin_menu', 'alf_receptionist_admin_menu', 999 );

/**
 * Redirect Receptionist away from blocked admin screens to Virtual Lobby.
 */
function alf_receptionist_block_admin_screens() {
	// Never interfere with AJAX, cron, or REST.
	if ( wp_doing_ajax() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}

	if ( ! alf_is_receptionist_user() || ! is_admin() ) {
		return;
	}

	global $pagenow;

	// Allow admin-ajax and other system endpoints.
	if ( in_array( $pagenow, array( 'admin-ajax.php', 'async-upload.php', 'admin-post.php' ), true ) ) {
		return;
	}

	$allowed_pages = array( 'alf-virtual-lobby' );
	$page          = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';

	$ok = false;
	if ( 'index.php' === $pagenow ) {
		$ok = true;
	} elseif ( 'admin.php' === $pagenow && in_array( $page, $allowed_pages, true ) ) {
		$ok = true;
	} elseif ( in_array( $pagenow, array( 'profile.php', 'user-edit.php' ), true ) ) {
		$ok = true;
	}

	if ( ! $ok ) {
		wp_safe_redirect( admin_url( 'admin.php?page=alf-virtual-lobby' ) );
		exit;
	}
}
add_action( 'admin_init', 'alf_receptionist_block_admin_screens' );

/**
 * Register Virtual Lobby admin menus.
 */
function alf_register_lobby_admin_menu() {
	if ( ! alf_user_can_manage_lobby() ) {
		return;
	}

	add_menu_page(
		__( 'Virtual Lobby', 'access-law-firm' ),
		__( 'Virtual Lobby', 'access-law-firm' ),
		'alf_manage_lobby',
		'alf-virtual-lobby',
		'alf_render_lobby_queue_page',
		'dashicons-groups',
		3
	);

	add_submenu_page(
		'alf-virtual-lobby',
		__( 'Queue', 'access-law-firm' ),
		__( 'Queue', 'access-law-firm' ),
		'alf_manage_lobby',
		'alf-virtual-lobby',
		'alf_render_lobby_queue_page'
	);

	if ( current_user_can( 'manage_options' ) ) {
		add_submenu_page(
			'alf-virtual-lobby',
			__( 'Lobby Settings', 'access-law-firm' ),
			__( 'Settings', 'access-law-firm' ),
			'manage_options',
			'alf-lobby-settings',
			'alf_render_lobby_settings_page'
		);
	}
}
add_action( 'admin_menu', 'alf_register_lobby_admin_menu' );

/**
 * Create or update the default Receptionist user.
 *
 * @return array{success:bool,message:string,username?:string,password?:string,email?:string}
 */
function alf_create_receptionist_user() {
	if ( ! get_role( 'alf_receptionist' ) ) {
		alf_register_receptionist_role();
	}

	$login = 'receptionist';
	$email = 'receptionist@accesslawoffice.com';
	$pass  = 'Reception@123';

	if ( username_exists( $login ) || email_exists( $email ) ) {
		$user = get_user_by( 'login', $login );
		if ( ! $user ) {
			$user = get_user_by( 'email', $email );
		}
		if ( ! $user ) {
			return array(
				'success' => false,
				'message' => __( 'A conflicting user exists but could not be loaded.', 'access-law-firm' ),
			);
		}
		$user->set_role( 'alf_receptionist' );
		// Reset so app/docs credentials stay usable after the account already exists.
		wp_set_password( $pass, $user->ID );
		return array(
			'success'  => true,
			'message'  => __( 'Receptionist user already existed — role updated and password reset to Reception@123.', 'access-law-firm' ),
			'username' => $user->user_login,
			'email'    => $user->user_email,
			'password' => $pass,
		);
	}

	$id = wp_create_user( $login, $pass, $email );
	if ( is_wp_error( $id ) ) {
		return array(
			'success' => false,
			'message' => $id->get_error_message(),
		);
	}

	$user = new WP_User( $id );
	$user->set_role( 'alf_receptionist' );
	wp_update_user(
		array(
			'ID'           => $id,
			'display_name' => 'Receptionist',
			'first_name'   => 'Lobby',
			'last_name'    => 'Receptionist',
		)
	);

	return array(
		'success'  => true,
		'message'  => __( 'Receptionist user created successfully.', 'access-law-firm' ),
		'username' => $login,
		'email'    => $email,
		'password' => $pass,
	);
}

/**
 * Render lobby settings (Zoom meeting URLs + create receptionist) — Administrators only.
 */
function alf_render_lobby_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_POST['alf_lobby_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alf_lobby_settings_nonce'] ) ), 'alf_lobby_settings' ) ) {
		$reception_raw = isset( $_POST['zoom_meeting_url'] )
			? wp_unslash( $_POST['zoom_meeting_url'] )
			: ( isset( $_POST['teams_meeting_url'] ) ? wp_unslash( $_POST['teams_meeting_url'] ) : '' );
		$attorney_raw  = isset( $_POST['zoom_attorney_url'] )
			? wp_unslash( $_POST['zoom_attorney_url'] )
			: ( isset( $_POST['teams_attorney_url'] ) ? wp_unslash( $_POST['teams_attorney_url'] ) : '' );
		$reception     = alf_zoom_meeting_url( $reception_raw );
		$attorney      = alf_zoom_attorney_url( $attorney_raw );

		$ok = true;
		if ( '' !== trim( (string) $reception_raw ) && '' === $reception ) {
			$ok = false;
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Reception Zoom link did not look valid. Paste the full https:// join URL.', 'access-law-firm' ) . '</p></div>';
		}
		if ( '' !== trim( (string) $attorney_raw ) && '' === $attorney ) {
			$ok = false;
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Attorney Zoom link did not look valid. Paste the full https:// join URL.', 'access-law-firm' ) . '</p></div>';
		}
		if ( $ok ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Lobby Zoom meeting links saved.', 'access-law-firm' ) . '</p></div>';
		}
	}

	if ( isset( $_POST['alf_create_receptionist_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alf_create_receptionist_nonce'] ) ), 'alf_create_receptionist' ) ) {
		$result = alf_create_receptionist_user();
		$class  = ! empty( $result['success'] ) ? 'notice-success' : 'notice-error';
		echo '<div class="notice ' . esc_attr( $class ) . ' is-dismissible"><p>' . esc_html( $result['message'] ) . '</p>';
		if ( ! empty( $result['success'] ) ) {
			echo '<p><strong>' . esc_html__( 'Username:', 'access-law-firm' ) . '</strong> ' . esc_html( $result['username'] ) . '<br>';
			echo '<strong>' . esc_html__( 'Email:', 'access-law-firm' ) . '</strong> ' . esc_html( $result['email'] );
			if ( ! empty( $result['password'] ) ) {
				echo '<br><strong>' . esc_html__( 'Password:', 'access-law-firm' ) . '</strong> ' . esc_html( $result['password'] );
				echo '<br><em>' . esc_html__( 'Save this password now, then ask the receptionist to change it after first login.', 'access-law-firm' ) . '</em>';
			}
			echo '</p>';
		}
		echo '</div>';
	}

	$reception_url    = alf_zoom_meeting_url();
	$attorney_url     = alf_zoom_attorney_url();
	$existing_user    = get_user_by( 'login', 'receptionist' );
	$has_receptionist = $existing_user && in_array( 'alf_receptionist', (array) $existing_user->roles, true );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Virtual Lobby Settings', 'access-law-firm' ); ?></h1>
		<form method="post" action="">
			<?php wp_nonce_field( 'alf_lobby_settings', 'alf_lobby_settings_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="zoom_meeting_url"><?php esc_html_e( 'Reception Zoom meeting URL', 'access-law-firm' ); ?></label></th>
					<td>
						<input type="text" class="large-text" id="zoom_meeting_url" name="zoom_meeting_url" value="<?php echo esc_attr( $reception_url ); ?>" placeholder="https://zoom.us/j/..." autocomplete="off" spellcheck="false">
						<p class="description"><?php esc_html_e( 'Clients join this meeting when the receptionist clicks Ready. Create a recurring Zoom meeting for reception (Waiting Room recommended) and paste the join link.', 'access-law-firm' ); ?></p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="zoom_attorney_url"><?php esc_html_e( 'Attorney Zoom meeting URL', 'access-law-firm' ); ?></label></th>
					<td>
						<input type="text" class="large-text" id="zoom_attorney_url" name="zoom_attorney_url" value="<?php echo esc_attr( $attorney_url ); ?>" placeholder="https://zoom.us/j/..." autocomplete="off" spellcheck="false">
						<p class="description"><?php esc_html_e( 'After intake, click Transfer to Attorney in the queue. The client’s screen shows Join Attorney with this link. Use a separate attorney Zoom meeting with Waiting Room so you can admit when ready.', 'access-law-firm' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Save Settings', 'access-law-firm' ) ); ?>
		</form>

		<hr>

		<h2><?php esc_html_e( 'Receptionist account', 'access-law-firm' ); ?></h2>
		<?php if ( $has_receptionist ) : ?>
			<p><?php esc_html_e( 'A Receptionist user already exists:', 'access-law-firm' ); ?>
				<strong><?php echo esc_html( $existing_user->user_login ); ?></strong>
				(<?php echo esc_html( $existing_user->user_email ); ?>)
			</p>
			<p><a class="button" href="<?php echo esc_url( get_edit_user_link( $existing_user->ID ) ); ?>"><?php esc_html_e( 'Edit user', 'access-law-firm' ); ?></a></p>
		<?php else : ?>
			<p><?php esc_html_e( 'Create a WordPress user with the Receptionist role so staff can open the lobby and manage the queue (without access to Themes, Plugins, or Twilio settings).', 'access-law-firm' ); ?></p>
		<?php endif; ?>
		<form method="post" style="margin-top:12px">
			<?php wp_nonce_field( 'alf_create_receptionist', 'alf_create_receptionist_nonce' ); ?>
			<?php
			submit_button(
				$has_receptionist
					? __( 'Re-apply Receptionist role', 'access-law-firm' )
					: __( 'Create Receptionist user', 'access-law-firm' ),
				'secondary',
				'submit',
				false
			);
			?>
		</form>

		<p class="description"><?php esc_html_e( 'Twilio SMS credentials remain under Settings → Access Law Firm.', 'access-law-firm' ); ?></p>
	</div>
	<?php
}

/**
 * Render the live queue console.
 */
function alf_render_lobby_queue_page() {
	if ( ! alf_user_can_manage_lobby() ) {
		return;
	}

	$is_open      = alf_is_lobby_open();
	$reception_url = alf_zoom_meeting_url();
	$attorney_url  = alf_zoom_attorney_url();

	if ( isset( $_GET['alf_lobby_toggled'] ) ) {
		$toggled_open = '1' === (string) wp_unslash( $_GET['alf_lobby_toggled'] );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html(
			$toggled_open
				? __( 'Virtual Lobby is now Open.', 'access-law-firm' )
				: __( 'Virtual Lobby is now Closed.', 'access-law-firm' )
		) . '</p></div>';
	}
	?>
	<div class="wrap alf-lobby-console">
		<h1><?php esc_html_e( 'Virtual Lobby Queue', 'access-law-firm' ); ?></h1>

		<div class="alf-lobby-console-bar">
			<div class="alf-lobby-console-status">
				<span class="alf-lobby-dot <?php echo $is_open ? 'is-open' : 'is-closed'; ?>" id="alf-console-dot" aria-hidden="true"></span>
				<strong id="alf-console-status-label">
					<?php
					echo $is_open
						? esc_html__( 'Virtual Lobby Open', 'access-law-firm' )
						: esc_html__( 'Virtual Lobby Closed', 'access-law-firm' );
					?>
				</strong>
			</div>
			<?php alf_render_lobby_toggle_control( 'console' ); ?>
		</div>

		<?php if ( ( ! $reception_url || ! $attorney_url ) && current_user_can( 'manage_options' ) ) : ?>
			<div class="notice notice-warning"><p>
				<?php
				printf(
					/* translators: %s: settings page URL */
					esc_html__( 'Save both Reception and Attorney Zoom meeting URLs under %s so Ready and Transfer work.', 'access-law-firm' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=alf-lobby-settings' ) ) . '">' . esc_html__( 'Virtual Lobby → Settings', 'access-law-firm' ) . '</a>'
				);
				?>
			</p></div>
		<?php elseif ( ! $reception_url || ! $attorney_url ) : ?>
			<div class="notice notice-warning"><p><?php esc_html_e( 'Reception and/or Attorney Zoom meeting URL is not configured yet. Ask an administrator to add them under Virtual Lobby → Settings.', 'access-law-firm' ); ?></p></div>
		<?php endif; ?>

		<p class="description"><?php esc_html_e( 'Queue refreshes automatically. Ready → client joins Reception Zoom. After intake, Transfer to Attorney → client sees Join Attorney.', 'access-law-firm' ); ?></p>

		<table class="wp-list-table widefat fixed striped" id="alf-queue-table">
			<thead>
				<tr>
					<th style="width:60px"><?php esc_html_e( '#', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Name', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Phone', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Matter', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Wait', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Status', 'access-law-firm' ); ?></th>
					<th style="width:220px"><?php esc_html_e( 'Actions', 'access-law-firm' ); ?></th>
				</tr>
			</thead>
			<tbody id="alf-queue-body">
				<tr><td colspan="7"><?php esc_html_e( 'Loading…', 'access-law-firm' ); ?></td></tr>
			</tbody>
		</table>
	</div>
	<style>
		.alf-lobby-console-bar{display:flex;flex-wrap:wrap;align-items:center;gap:16px;margin:16px 0 20px;padding:14px 16px;background:#fff;border:1px solid #c3c4c7;border-radius:4px}
		.alf-lobby-console-status{display:flex;align-items:center;gap:10px;font-size:15px}
		.alf-lobby-dot{width:12px;height:12px;border-radius:50%;display:inline-block;flex:none}
		.alf-lobby-dot.is-open{background:#27b05d;box-shadow:0 0 0 4px #eaf8ef}
		.alf-lobby-dot.is-closed{background:#d92d20;box-shadow:0 0 0 4px #fdeceb}
		.alf-lobby-toggle{font-weight:600;display:inline-flex;align-items:center;gap:8px}
		.alf-lobby-widget-message.is-success{color:#1a7f37}
		.alf-lobby-widget-message.is-error{color:#b42318}
		.alf-status-badge{display:inline-block;padding:2px 8px;border-radius:99px;font-size:12px;font-weight:600}
		.alf-status-waiting{background:#fff4e5;color:#9a6700}
		.alf-status-ready{background:#eaf8ef;color:#1a7f37}
		.alf-status-in_meeting{background:#e8f1ff;color:#0d4ca3}
		.alf-status-with_attorney{background:#f3e8ff;color:#6b21a8}
		.alf-queue-actions .button{margin:0 4px 4px 0}
	</style>
	<?php
}

/**
 * Enqueue lobby queue scripts (footer + DOM-ready so the table exists).
 *
 * @param string $hook Current admin page hook.
 */
function alf_enqueue_lobby_admin_assets( $hook ) {
	$on_console = ( is_string( $hook ) && false !== strpos( $hook, 'alf-virtual-lobby' ) );
	if ( ! $on_console || ! alf_user_can_manage_lobby() ) {
		return;
	}

	$cfg = wp_json_encode(
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'alf_lobby_admin' ),
		)
	);

	$js = <<<'JS'
(function () {
  function initLobbyQueue() {
    var cfg = window.alfLobbyAdmin || {};
    var bodyEl = document.getElementById('alf-queue-body');
    if (!bodyEl) return;

    function post(action, data) {
      var body = new URLSearchParams();
      body.append('action', action);
      body.append('nonce', cfg.nonce || '');
      Object.keys(data || {}).forEach(function (k) { body.append(k, data[k]); });
      return fetch(cfg.ajaxUrl, {
        method: 'POST',
        credentials: 'same-origin',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
        body: body.toString()
      }).then(function (r) {
        return r.text().then(function (text) {
          try { return JSON.parse(text); }
          catch (e) { return { success: false, data: { message: 'Unexpected response.' } }; }
        });
      });
    }

    function escapeHtml(s) {
      return String(s || '').replace(/[&<>"']/g, function (c) {
        return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c];
      });
    }

    function renderQueue(items) {
      if (!items || !items.length) {
        bodyEl.innerHTML = '<tr><td colspan="7">No visitors in the queue right now.</td></tr>';
        return;
      }
      bodyEl.innerHTML = items.map(function (row) {
        var actions = '';
        if (row.status === 'waiting') {
          actions += '<button type="button" class="button button-primary" data-action="ready" data-id="' + row.id + '">Ready</button>';
        }
        if (row.status === 'ready' || row.status === 'in_meeting') {
          actions += '<button type="button" class="button button-primary" data-action="transfer" data-id="' + row.id + '">Transfer to Attorney</button>';
        }
        if (row.status === 'ready' || row.status === 'in_meeting' || row.status === 'with_attorney') {
          actions += '<button type="button" class="button" data-action="complete" data-id="' + row.id + '">Complete</button>';
        }
        if (row.status === 'waiting' || row.status === 'ready') {
          actions += '<button type="button" class="button" data-action="dismiss" data-id="' + row.id + '">Dismiss</button>';
        }
        return '<tr>' +
          '<td>' + escapeHtml(row.position) + '</td>' +
          '<td><strong>' + escapeHtml(row.name) + '</strong></td>' +
          '<td>' + escapeHtml(row.phone) + '</td>' +
          '<td>' + escapeHtml(row.matter) + '</td>' +
          '<td>' + escapeHtml(row.wait) + '</td>' +
          '<td><span class="alf-status-badge alf-status-' + escapeHtml(row.status) + '">' + escapeHtml(row.status_label) + '</span></td>' +
          '<td class="alf-queue-actions">' + actions + '</td>' +
          '</tr>';
      }).join('');
    }

    function loadQueue() {
      post('alf_queue_list', {}).then(function (res) {
        if (res && res.success) renderQueue(res.data.items || []);
        else {
          var msg = (res && res.data && res.data.message) ? res.data.message : 'Could not load queue.';
          bodyEl.innerHTML = '<tr><td colspan="7">' + escapeHtml(msg) + '</td></tr>';
        }
      }).catch(function () {
        bodyEl.innerHTML = '<tr><td colspan="7">Network error loading queue.</td></tr>';
      });
    }

    bodyEl.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-action][data-id]');
      if (!btn) return;
      btn.disabled = true;
      post('alf_queue_update', {
        visit_id: btn.getAttribute('data-id'),
        queue_action: btn.getAttribute('data-action')
      }).then(function (res) {
        btn.disabled = false;
        if (res && res.success) loadQueue();
        else alert((res && res.data && res.data.message) || 'Update failed.');
      }).catch(function () {
        btn.disabled = false;
        alert('Network error.');
      });
    });

    loadQueue();
    setInterval(loadQueue, 6000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initLobbyQueue);
  } else {
    initLobbyQueue();
  }
})();
JS;

	// Footer handle so this runs after the queue markup exists.
	wp_register_script( 'alf-lobby-admin', false, array(), defined( 'ALF_THEME_VERSION' ) ? ALF_THEME_VERSION : null, true );
	wp_enqueue_script( 'alf-lobby-admin' );
	wp_add_inline_script( 'alf-lobby-admin', 'window.alfLobbyAdmin = ' . $cfg . ';', 'before' );
	wp_add_inline_script( 'alf-lobby-admin', $js, 'after' );
}
add_action( 'admin_enqueue_scripts', 'alf_enqueue_lobby_admin_assets' );

/**
 * AJAX: toggle Virtual Lobby open/closed (optional; form POST is primary).
 */
function alf_ajax_toggle_lobby() {
	if ( ! check_ajax_referer( 'alf_lobby_admin', 'nonce', false ) ) {
		wp_send_json_error( array( 'message' => __( 'Security check failed. Please reload the page and try again.', 'access-law-firm' ) ), 403 );
	}

	if ( ! alf_user_can_manage_lobby() ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to change this.', 'access-law-firm' ) ), 403 );
	}

	$open = ( isset( $_POST['lobby_open'] ) && (string) wp_unslash( $_POST['lobby_open'] ) === '1' ) ? 1 : 0;
	alf_set_lobby_open( $open );

	wp_send_json_success(
		array(
			'open'    => ( 1 === $open ),
			'message' => $open
				? __( 'Virtual Lobby is now Open.', 'access-law-firm' )
				: __( 'Virtual Lobby is now Closed.', 'access-law-firm' ),
		)
	);
}
add_action( 'wp_ajax_alf_toggle_lobby', 'alf_ajax_toggle_lobby' );
