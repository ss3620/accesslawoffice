<?php
/**
 * Virtual Lobby — Appointments admin (unified app + website).
 *
 * @package Access_Law_Firm
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Human-readable appointment status label.
 *
 * @param string $status Status slug.
 * @return string
 */
function alf_appointment_status_label( $status ) {
	$labels = array(
		'requested' => __( 'Requested', 'access-law-firm' ),
		'confirmed' => __( 'Confirmed', 'access-law-firm' ),
		'declined'  => __( 'Declined', 'access-law-firm' ),
	);
	return isset( $labels[ $status ] ) ? $labels[ $status ] : (string) $status;
}

/**
 * Serialize an appointment for the wp-admin table (includes linked lobby status).
 *
 * @param int $post_id Appointment post ID.
 * @return array|null
 */
function alf_serialize_appointment_admin( $post_id ) {
	$row = function_exists( 'alf_serialize_appointment' ) ? alf_serialize_appointment( $post_id ) : null;
	if ( ! $row ) {
		return null;
	}

	$visit_id     = ! empty( $row['visitId'] ) ? (int) $row['visitId'] : 0;
	$queue_status = '';
	$queue_label  = '';
	if ( $visit_id > 0 ) {
		$queue_status = (string) get_post_meta( $visit_id, 'queue_status', true );
		if ( $queue_status && function_exists( 'alf_queue_status_label' ) ) {
			$queue_label = alf_queue_status_label( $queue_status );
		} elseif ( $queue_status ) {
			$queue_label = $queue_status;
		}
	}

	$source = (string) ( $row['source'] ?? 'app' );
	return array(
		'id'              => (string) $post_id,
		'clientName'      => (string) ( $row['clientName'] ?? '' ),
		'email'           => (string) ( $row['email'] ?? '' ),
		'phone'           => (string) ( $row['phone'] ?? '' ),
		'preferredWindow' => (string) ( $row['preferredWindow'] ?? '' ),
		'note'            => (string) ( $row['note'] ?? '' ),
		'status'          => (string) ( $row['status'] ?? 'requested' ),
		'statusLabel'     => alf_appointment_status_label( (string) ( $row['status'] ?? 'requested' ) ),
		'source'          => $source,
		'sourceLabel'     => 'website' === $source ? __( 'Website', 'access-law-firm' ) : __( 'App', 'access-law-firm' ),
		'visitId'         => $visit_id ? (string) $visit_id : '',
		'queueStatus'     => $queue_status,
		'queueLabel'      => $queue_label,
		'createdAt'       => (string) ( $row['createdAt'] ?? '' ),
		'createdDisplay'  => get_the_date( 'M j, Y g:i a', $post_id ),
	);
}

/**
 * Render unified appointments list (app + website).
 */
function alf_render_appointments_admin_page() {
	if ( ! alf_user_can_manage_lobby() ) {
		return;
	}
	?>
	<div class="wrap alf-appointments-console">
		<h1><?php esc_html_e( 'Appointments', 'access-law-firm' ); ?></h1>
		<p class="description">
			<?php esc_html_e( 'One list for appointment requests from the mobile app and the website Virtual Lobby. Lobby check-ins are linked to the same entry when possible.', 'access-law-firm' ); ?>
		</p>

		<table class="wp-list-table widefat fixed striped" id="alf-appointments-table">
			<thead>
				<tr>
					<th style="width:140px"><?php esc_html_e( 'Requested', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Client', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Contact', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Preferred time', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Source', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Lobby', 'access-law-firm' ); ?></th>
					<th><?php esc_html_e( 'Status', 'access-law-firm' ); ?></th>
					<th style="width:180px"><?php esc_html_e( 'Actions', 'access-law-firm' ); ?></th>
				</tr>
			</thead>
			<tbody id="alf-appointments-body">
				<tr><td colspan="8"><?php esc_html_e( 'Loading…', 'access-law-firm' ); ?></td></tr>
			</tbody>
		</table>
	</div>
	<style>
		.alf-appt-status{display:inline-block;padding:2px 8px;border-radius:99px;font-size:12px;font-weight:600}
		.alf-appt-requested{background:#fff4e5;color:#9a6700}
		.alf-appt-confirmed{background:#eaf8ef;color:#1a7f37}
		.alf-appt-declined{background:#fdeceb;color:#b42318}
		.alf-appt-note{display:block;margin-top:4px;color:#646970;font-size:12px}
		.alf-appt-actions .button{margin:0 4px 4px 0}
	</style>
	<?php
}

/**
 * Admin AJAX: list all appointments.
 */
function alf_ajax_appointments_list() {
	check_ajax_referer( 'alf_lobby_admin', 'nonce' );

	if ( ! function_exists( 'alf_user_can_manage_lobby' ) || ! alf_user_can_manage_lobby() ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'access-law-firm' ) ), 403 );
	}

	$posts = get_posts(
		array(
			'post_type'      => 'alf_appointment',
			'post_status'    => 'publish',
			'posts_per_page' => 200,
			'orderby'        => 'date',
			'order'          => 'DESC',
		)
	);

	$items = array();
	foreach ( $posts as $post ) {
		$row = alf_serialize_appointment_admin( $post->ID );
		if ( $row ) {
			$items[] = $row;
		}
	}

	wp_send_json_success( array( 'items' => $items ) );
}
add_action( 'wp_ajax_alf_appointments_list', 'alf_ajax_appointments_list' );

/**
 * Admin AJAX: confirm or decline an appointment.
 */
function alf_ajax_appointments_update() {
	check_ajax_referer( 'alf_lobby_admin', 'nonce' );

	if ( ! function_exists( 'alf_user_can_manage_lobby' ) || ! alf_user_can_manage_lobby() ) {
		wp_send_json_error( array( 'message' => __( 'Permission denied.', 'access-law-firm' ) ), 403 );
	}

	$id     = isset( $_POST['appointment_id'] ) ? absint( $_POST['appointment_id'] ) : 0;
	$status = isset( $_POST['status'] ) ? sanitize_key( wp_unslash( $_POST['status'] ) ) : '';

	$post = get_post( $id );
	if ( ! $post || 'alf_appointment' !== $post->post_type ) {
		wp_send_json_error( array( 'message' => __( 'Appointment not found.', 'access-law-firm' ) ), 404 );
	}

	if ( ! in_array( $status, array( 'confirmed', 'declined', 'requested' ), true ) ) {
		wp_send_json_error( array( 'message' => __( 'Invalid status.', 'access-law-firm' ) ), 400 );
	}

	update_post_meta( $id, 'status', $status );

	$row = alf_serialize_appointment_admin( $id );
	wp_send_json_success( array( 'item' => $row ) );
}
add_action( 'wp_ajax_alf_appointments_update', 'alf_ajax_appointments_update' );

/**
 * Inline script for the appointments admin table.
 *
 * @return string
 */
function alf_appointments_admin_js() {
	return <<<'JS'
(function () {
  function initAppointmentsAdmin() {
    var cfg = window.alfLobbyAdmin || {};
    var bodyEl = document.getElementById('alf-appointments-body');
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

    function contactCell(row) {
      var parts = [];
      if (row.email) parts.push(escapeHtml(row.email));
      if (row.phone) parts.push(escapeHtml(row.phone));
      return parts.length ? parts.join('<br>') : '—';
    }

    function lobbyCell(row) {
      if (!row.visitId) return '—';
      if (row.queueLabel) {
        return '<span class="alf-status-badge alf-status-' + escapeHtml(row.queueStatus) + '">' +
          escapeHtml(row.queueLabel) + '</span>';
      }
      return escapeHtml(row.visitId ? ('Visit #' + row.visitId) : '—');
    }

    function render(items) {
      if (!items || !items.length) {
        bodyEl.innerHTML = '<tr><td colspan="8">No appointments yet.</td></tr>';
        return;
      }
      bodyEl.innerHTML = items.map(function (row) {
        var actions = '';
        if (row.status === 'requested') {
          actions += '<button type="button" class="button button-primary" data-status="confirmed" data-id="' + row.id + '">Confirm</button>';
          actions += '<button type="button" class="button" data-status="declined" data-id="' + row.id + '">Decline</button>';
        } else if (row.status === 'declined') {
          actions += '<button type="button" class="button" data-status="requested" data-id="' + row.id + '">Re-open</button>';
        } else if (row.status === 'confirmed') {
          actions += '<button type="button" class="button" data-status="declined" data-id="' + row.id + '">Decline</button>';
        }
        var note = row.note ? '<span class="alf-appt-note">' + escapeHtml(row.note) + '</span>' : '';
        return '<tr>' +
          '<td>' + escapeHtml(row.createdDisplay) + '</td>' +
          '<td><strong>' + escapeHtml(row.clientName) + '</strong>' + note + '</td>' +
          '<td>' + contactCell(row) + '</td>' +
          '<td>' + escapeHtml(row.preferredWindow) + '</td>' +
          '<td>' + escapeHtml(row.sourceLabel) + '</td>' +
          '<td>' + lobbyCell(row) + '</td>' +
          '<td><span class="alf-appt-status alf-appt-' + escapeHtml(row.status) + '">' + escapeHtml(row.statusLabel) + '</span></td>' +
          '<td class="alf-appt-actions">' + actions + '</td>' +
          '</tr>';
      }).join('');
    }

    function load() {
      post('alf_appointments_list', {}).then(function (res) {
        if (res && res.success) render(res.data.items || []);
        else {
          var msg = (res && res.data && res.data.message) ? res.data.message : 'Could not load appointments.';
          bodyEl.innerHTML = '<tr><td colspan="8">' + escapeHtml(msg) + '</td></tr>';
        }
      }).catch(function () {
        bodyEl.innerHTML = '<tr><td colspan="8">Network error loading appointments.</td></tr>';
      });
    }

    bodyEl.addEventListener('click', function (e) {
      var btn = e.target.closest('[data-status][data-id]');
      if (!btn) return;
      btn.disabled = true;
      post('alf_appointments_update', {
        appointment_id: btn.getAttribute('data-id'),
        status: btn.getAttribute('data-status')
      }).then(function (res) {
        btn.disabled = false;
        if (res && res.success) load();
        else alert((res && res.data && res.data.message) || 'Update failed.');
      }).catch(function () {
        btn.disabled = false;
        alert('Network error.');
      });
    });

    load();
    setInterval(load, 15000);
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initAppointmentsAdmin);
  } else {
    initAppointmentsAdmin();
  }
})();
JS;
}
