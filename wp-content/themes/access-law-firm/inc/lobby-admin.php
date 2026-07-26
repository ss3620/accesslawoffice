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
 * Render lobby settings (Teams URL) — Administrators only.
 */
function alf_render_lobby_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if ( isset( $_POST['alf_lobby_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['alf_lobby_settings_nonce'] ) ), 'alf_lobby_settings' ) ) {
		$url = isset( $_POST['teams_meeting_url'] ) ? esc_url_raw( wp_unslash( $_POST['teams_meeting_url'] ) ) : '';
		alf_update_setting( 'teams_meeting_url', $url );
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Lobby settings saved.', 'access-law-firm' ) . '</p></div>';
	}

	$teams_url = alf_get_setting( 'teams_meeting_url', '' );
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Virtual Lobby Settings', 'access-law-firm' ); ?></h1>
		<form method="post">
			<?php wp_nonce_field( 'alf_lobby_settings', 'alf_lobby_settings_nonce' ); ?>
			<table class="form-table" role="presentation">
				<tr>
					<th scope="row"><label for="teams_meeting_url"><?php esc_html_e( 'Microsoft Teams meeting URL', 'access-law-firm' ); ?></label></th>
					<td>
						<input type="url" class="large-text" id="teams_meeting_url" name="teams_meeting_url" value="<?php echo esc_attr( $teams_url ); ?>" placeholder="https://teams.microsoft.com/l/meetup-join/...">
						<p class="description"><?php esc_html_e( 'Clients open this link when the receptionist marks them Ready and they click “Join Reception”. Create a meeting in Teams and paste the join URL here.', 'access-law-firm' ); ?></p>
					</td>
				</tr>
			</table>
			<?php submit_button( __( 'Save Settings', 'access-law-firm' ) ); ?>
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

	$is_open   = alf_is_lobby_open();
	$teams_url = alf_get_setting( 'teams_meeting_url', '' );
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
			<label class="alf-lobby-toggle">
				<input type="checkbox" id="alf-lobby-open-toggle" value="1" <?php checked( $is_open, true ); ?>>
				<?php esc_html_e( 'Virtual Lobby Open', 'access-law-firm' ); ?>
			</label>
			<span id="alf-lobby-widget-message" class="alf-lobby-widget-message" hidden></span>
		</div>

		<?php if ( ! $teams_url && current_user_can( 'manage_options' ) ) : ?>
			<div class="notice notice-warning"><p>
				<?php
				printf(
					/* translators: %s: settings page URL */
					esc_html__( 'No Teams meeting URL is set. Add one under %s so clients can join reception.', 'access-law-firm' ),
					'<a href="' . esc_url( admin_url( 'admin.php?page=alf-lobby-settings' ) ) . '">' . esc_html__( 'Virtual Lobby → Settings', 'access-law-firm' ) . '</a>'
				);
				?>
			</p></div>
		<?php elseif ( ! $teams_url ) : ?>
			<div class="notice notice-warning"><p><?php esc_html_e( 'No Teams meeting URL is configured yet. Ask an administrator to add it under Virtual Lobby → Settings.', 'access-law-firm' ); ?></p></div>
		<?php endif; ?>

		<p class="description"><?php esc_html_e( 'Queue refreshes automatically. Click Ready when you can greet the visitor; they will see “Join Reception” with the Teams link.', 'access-law-firm' ); ?></p>

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
		.alf-queue-actions .button{margin:0 4px 4px 0}
	</style>
	<?php
}

/**
 * Enqueue lobby console + dashboard widget scripts.
 *
 * @param string $hook Current admin page hook.
 */
function alf_enqueue_lobby_admin_assets( $hook ) {
	$on_dashboard = ( 'index.php' === $hook );
	$on_console   = ( false !== strpos( $hook, 'alf-virtual-lobby' ) );

	if ( ! $on_dashboard && ! $on_console ) {
		return;
	}
	if ( ! alf_user_can_manage_lobby() ) {
		return;
	}

	$js = <<<'JS'
(function () {
  var cfg = window.alfLobbyAdmin || {};
  var toggle = document.getElementById('alf-lobby-open-toggle');
  var label = document.getElementById('alf-console-status-label') || document.getElementById('alf-lobby-status-label');
  var dot = document.getElementById('alf-console-dot') || document.querySelector('.alf-lobby-dot');
  var message = document.getElementById('alf-lobby-widget-message');

  function setMessage(text, ok) {
    if (!message) return;
    message.hidden = !text;
    message.textContent = text || '';
    message.className = 'alf-lobby-widget-message ' + (ok ? 'is-success' : 'is-error');
  }

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
    }).then(function (r) { return r.json(); });
  }

  if (toggle) {
    toggle.addEventListener('change', function () {
      var open = toggle.checked ? 1 : 0;
      toggle.disabled = true;
      setMessage('Saving…', true);
      post('alf_toggle_lobby', { lobby_open: String(open) }).then(function (res) {
        toggle.disabled = false;
        if (res && res.success) {
          var isOpen = !!res.data.open;
          toggle.checked = isOpen;
          if (label) label.textContent = isOpen ? 'Virtual Lobby Open' : 'Virtual Lobby Closed';
          if (dot) {
            dot.classList.toggle('is-open', isOpen);
            dot.classList.toggle('is-closed', !isOpen);
          }
          setMessage(res.data.message || 'Saved.', true);
        } else {
          toggle.checked = !toggle.checked;
          setMessage((res && res.data && res.data.message) || 'Could not save.', false);
        }
      }).catch(function () {
        toggle.disabled = false;
        toggle.checked = !toggle.checked;
        setMessage('Network error.', false);
      });
    });
  }

  var bodyEl = document.getElementById('alf-queue-body');
  if (!bodyEl) return;

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
      if (res && res.success) {
        renderQueue(res.data.items || []);
      } else {
        bodyEl.innerHTML = '<tr><td colspan="7">Could not load queue.</td></tr>';
      }
    }).catch(function () {
      bodyEl.innerHTML = '<tr><td colspan="7">Network error loading queue.</td></tr>';
    });
  }

  bodyEl.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-action][data-id]');
    if (!btn) return;
    btn.disabled = true;
    post('alf_queue_update', { visit_id: btn.getAttribute('data-id'), queue_action: btn.getAttribute('data-action') }).then(function (res) {
      btn.disabled = false;
      if (res && res.success) {
        loadQueue();
      } else {
        alert((res && res.data && res.data.message) || 'Update failed.');
      }
    }).catch(function () {
      btn.disabled = false;
      alert('Network error.');
    });
  });

  loadQueue();
  setInterval(loadQueue, 6000);
})();
JS;

	wp_register_script( 'alf-lobby-admin', false, array(), ALF_THEME_VERSION, true );
	wp_enqueue_script( 'alf-lobby-admin' );
	wp_add_inline_script( 'alf-lobby-admin', $js );
	wp_localize_script(
		'alf-lobby-admin',
		'alfLobbyAdmin',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'alf_lobby_admin' ),
		)
	);
}
add_action( 'admin_enqueue_scripts', 'alf_enqueue_lobby_admin_assets' );

/**
 * AJAX: toggle Virtual Lobby open/closed.
 */
function alf_ajax_toggle_lobby() {
	check_ajax_referer( 'alf_lobby_admin', 'nonce' );

	if ( ! alf_user_can_manage_lobby() ) {
		wp_send_json_error( array( 'message' => __( 'You do not have permission to change this.', 'access-law-firm' ) ), 403 );
	}

	$open = ! empty( $_POST['lobby_open'] ) ? 1 : 0;
	alf_update_setting( 'lobby_open', $open );

	wp_send_json_success(
		array(
			'open'    => (bool) $open,
			'message' => $open
				? __( 'Virtual Lobby is now Open.', 'access-law-firm' )
				: __( 'Virtual Lobby is now Closed.', 'access-law-firm' ),
		)
	);
}
add_action( 'wp_ajax_alf_toggle_lobby', 'alf_ajax_toggle_lobby' );
