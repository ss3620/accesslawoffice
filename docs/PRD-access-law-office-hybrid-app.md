# Product Requirements Document (PRD)
# Access Law Office — Hybrid Mobile App

| Field | Value |
|--------|--------|
| **Product** | Access Law Office Hybrid Mobile App |
| **Version** | 1.1 |
| **Status** | Draft for implementation |
| **Owner** | Access Law Office |
| **Related web product** | WordPress theme `access-law-firm` + Virtual Lobby at accesslawoffice.com |
| **Date** | 2026-08-01 |
| **Changelog** | v1.1 — First-class **Admin** and **Receptionist** app roles (RBAC, screens, auth) |

---

## 1. Executive summary

Build a **hybrid mobile app (iOS + Android)** for Access Law Office that brings the existing **Virtual Lobby** experience into a native shell, then extends it with **in-app messaging**, **voice/video calling**, and optionally **embedded Zoom meetings** (Zoom Meeting SDK) so clients do not have to leave the app for Zoom.

The app must support **three primary role experiences** in one product (role-gated after login / entry):

1. **Client** — join lobby, wait, message, join meetings  
2. **Receptionist** — operate the live queue, Ready/Transfer, chat/call clients  
3. **Admin** — configure Zoom/Twilio/hours/feature flags, manage receptionist users, oversee lobby  

The web product already supports:

- Multi-step Virtual Lobby check-in (name → phone/verify → matter → wait)
- Live receptionist queue (Ready → Transfer to attorney)
- WordPress roles: **Administrator** + custom **Receptionist** (`alf_receptionist`) with capability `alf_manage_lobby`
- Two Zoom rooms (Reception + Attorney)
- Optional SMS OTP (Twilio) + CAPTCHA
- Manual lobby open/closed + published CST hours

The app should **reuse this backend and role model** first, then add mobile-native capabilities in phases.

---

## 2. Problem statement

Today, clients use a browser:

1. Join Virtual Lobby on the website  
2. Wait for receptionist  
3. Tap a link that **opens Zoom externally**  
4. Later leave reception Zoom and open a second attorney Zoom link  

Pain points:

- Context switching (browser ↔ Zoom app)
- Weaker push notifications while waiting
- No persistent client ↔ firm messaging channel
- No branded in-app call experience
- Mobile Safari / Android Chrome friction for Zoom handoff

---

## 3. Goals

### 3.1 Primary goals (MVP)

1. Client can complete full Virtual Lobby check-in inside the app.  
2. Client receives **push notification** when receptionist is ready / transferred.  
3. Client can join Reception and Attorney meetings from the app (at minimum via deep link / Zoom URL; ideally embedded).  
4. Lobby open/closed + hours parity with website.  
5. Secure phone verification consistent with current web modes (`sms`, `captcha`, `sms_captcha`, `none`).  
6. **Receptionist** can sign in and run the live queue on mobile (Ready / Transfer / Complete / Dismiss).  
7. **Admin** can sign in and manage lobby settings + receptionist accounts from mobile (or deep-link to web admin where needed).

### 3.2 Secondary goals (Phase 2+)

8. In-app **messaging** between client and receptionist (Admin may monitor/participate).  
9. In-app **voice calling** (and optionally video) without requiring Zoom for short coordination calls.  
10. **Embedded Zoom** via Meeting SDK so video stays inside the app UI.  
11. Attorney role (optional later) for attorney-room join + post-intake chat.

### 3.3 Non-goals (v1)

- Full case management / billing / document DMS replacement  
- Replacing WordPress CMS for marketing content  
- AI chatbot replacing live receptionist  
- Multi-firm marketplace  
- Offline-only lobby (requires live queue)  
- Letting Receptionists change Zoom SDK secrets / Twilio credentials (Admin-only)

---

## 4. Users, personas & roles

### 4.1 Personas

| Persona | App entry | Needs |
|---------|-----------|--------|
| **Prospective client** | No staff login — “Join Virtual Lobby” | Check in, wait, talk to a human, then attorney |
| **Existing client** | Optional later account | Message office, join video, status updates |
| **Receptionist** | Staff login → Receptionist home | Operate queue all day; Ready/Transfer; chat/call |
| **Admin** | Staff login → Admin home | Configure systems; manage users; oversee lobby |
| **Attorney** (optional later) | Staff or Zoom-host path | Join attorney room; optional chat after intake |

### 4.2 Canonical app roles (RBAC)

Map 1:1 to the existing WordPress model where possible.

| App role | WordPress mapping | Capability gate | Description |
|----------|-------------------|-----------------|-------------|
| **`client`** | No WP user (visit token) | Public lobby APIs + visit `token` | Guest journey through Virtual Lobby |
| **`receptionist`** | Role `alf_receptionist` | `alf_manage_lobby` | Day-to-day queue operations only |
| **`admin`** | Role `administrator` (or any user with `manage_options` + `alf_manage_lobby`) | `manage_options` (+ lobby caps) | Full configuration and oversight |
| **`attorney`** (Phase 5+) | TBD (custom role or Admin-assigned) | TBD | Attorney meeting + limited chat — **out of MVP** |

**Rules**

1. One binary app; after launch screen, user chooses **Join as client** or **Staff sign in**.  
2. Staff sign-in returns `role: receptionist | admin` (and capabilities list).  
3. UI is **role-gated** — Receptionist must not see Admin settings; Client must not see queue controls.  
4. Admins inherit all Receptionist queue powers.  
5. Backend enforces the same checks as WordPress today (`alf_user_can_manage_lobby`, `manage_options`).

### 4.3 Permission matrix

| Capability | Client | Receptionist | Admin |
|------------|:------:|:------------:|:-----:|
| Join / check into Virtual Lobby | ✓ | — | — |
| View own wait status / join meetings | ✓ | — | — |
| Client ↔ staff messaging (own thread) | ✓ | ✓ | ✓ |
| View live queue (all active visits) | — | ✓ | ✓ |
| Ready / Transfer / Complete / Dismiss | — | ✓ | ✓ |
| Toggle lobby open / closed | — | ✓ | ✓ |
| Place VoIP / coordination call to visitor | — | ✓ | ✓ |
| View visit phone / matter details | — | ✓ | ✓ |
| Configure Reception / Attorney Zoom URLs & meeting IDs | — | — | ✓ |
| Configure Twilio / CAPTCHA / verify mode | — | — | ✓ |
| Manage feature flags (messaging, VoIP, Meeting SDK) | — | — | ✓ |
| Create / reset **Receptionist** users | — | — | ✓ |
| View analytics / visit history reports | — | limited (today’s queue) | ✓ |
| Change branding / hours copy source | — | — | ✓ |
| Access WordPress site settings / plugins | — | — | Web only (not in app) |

### 4.4 Existing web behavior to preserve

From `inc/lobby-admin.php`:

- Capability **`alf_manage_lobby`** granted to Administrators and role **`alf_receptionist`**.  
- Receptionists see a **stripped WP admin** (dashboard + Virtual Lobby menus only).  
- Admins use **Virtual Lobby → Settings** (Zoom URLs, create receptionist user) and full WP.  
- Queue actions: `ready`, `transfer`, `complete`, `dismiss` via `alf_queue_update`.  

The mobile app must not weaken these boundaries.

---

## 5. Product principles

1. **Live human first** — lobby is receptionist-assisted, not a chatbot.  
2. **One continuous journey** — check-in → wait → reception → attorney without losing place.  
3. **Mobile-native reliability** — push + reconnect after backgrounding.  
4. **Privacy by default** — immigration matters are sensitive; minimize retention; encrypt in transit.  
5. **Parity then power** — match web lobby first; then messaging/calling/SDK.  
6. **Brand** — Access Law Office / Experience × Affordability = Access; Houston; immigration focus.

---

## 6. Recommended technical approach

### 6.1 Hybrid stack (recommended)

| Layer | Recommendation | Why |
|-------|----------------|-----|
| **Client app** | **React Native CLI** (not Expo-managed for Zoom SDK path) | Zoom Meeting SDK for RN officially supports RN (docs historically cap around **0.75.x**); **Expo is not supported** for Meeting SDK |
| **Alt (Phase 1 only)** | Capacitor wrapping the existing site | Fastest ship for lobby-in-webview; weak for deep Zoom SDK / rich native calling |
| **Backend** | Extend current WordPress AJAX → formal **REST/JSON API** | App needs stable auth, push tokens, messaging; keep lobby CPT as source of truth initially |
| **Push** | FCM (Android) + APNs (iOS) via backend | Waiting room is useless without background alerts |
| **Realtime** | Polling (reuse current ~2s visit poll) → later WebSocket/Pusher | Match web MVP; upgrade when messaging ships |

**Decision for implementers:**  
- If **embedded Zoom is a hard requirement in v1**, choose **React Native CLI + Meeting SDK**.  
- If **speed-to-market** matters more, ship **Capacitor/WebView MVP** with Zoom deep links, then migrate critical screens to RN + SDK.

### 6.2 Suggested monorepo layout (for new app window)

```
access-law-office-app/
  apps/
    mobile/          # Single React Native app (client + receptionist + admin shells)
  packages/
    api-client/      # Typed API SDK (role-aware)
    ui/              # Shared design tokens (navy/gold)
    rbac/            # Role → route/capability helpers
  docs/
    PRD.md           # This document
```

WordPress remains system of record for users/roles (`administrator`, `alf_receptionist`) and lobby visits until a later migration.

---

## 7. Feature requirements

### 7.1 Phase 0 — Foundations

| ID | Requirement | Priority |
|----|-------------|----------|
| F0.1 | App branding, splash, intro gate (optional, match web intro modal) | P1 |
| F0.2 | Environment config (API base URL, Zoom SDK keys via secure backend) | P0 |
| F0.3 | Secure storage for visit token / session | P0 |
| F0.4 | Analytics events (check-in, ready, join, transfer) — privacy-safe | P2 |
| F0.5 | Crash reporting | P1 |

### 7.2 Phase 1 — Virtual Lobby parity (MVP)

Mirror web flow:

| Step | App screen | Behavior |
|------|------------|----------|
| Welcome | Lobby home | Show open/closed, hours (CST), waiting count, Enter Lobby |
| Name | Form | `full_name` validation |
| Phone | Form | Country `+1` / `+91`, normalize E.164; skip if SMS off (match web) |
| Verify | CAPTCHA / OTP | Modes: `sms_captcha`, `sms`, `captcha`, `none` |
| Matter | Select | Same matter list as web |
| Waiting | Queue wait | Poll `visit_status`; show position; **do not close** guidance |
| Reception ready | Join CTA | Join reception meeting |
| Attorney ready | Join CTA | Instruct leave reception if needed; join attorney |

**Functional requirements**

| ID | Requirement | Priority |
|----|-------------|----------|
| F1.1 | Check-in creates lobby visit; returns `visit_id` + `token` | P0 |
| F1.2 | Poll status; handle `waiting`, `ready`, `in_meeting`, `with_attorney`, `completed`, `dismissed` | P0 |
| F1.3 | When `ready` → show Join Reception; call `visit_joined` on join | P0 |
| F1.4 | When `with_attorney` → show Join Attorney | P0 |
| F1.5 | Lobby closed blocks check-in with clear copy | P0 |
| F1.6 | Push: “Your receptionist is ready” / “Your attorney is ready” | P0 |
| F1.7 | Restore session if app killed during wait (token in secure storage) | P0 |
| F1.8 | Deep link from website “Open in app” (optional) | P2 |

**Join meeting strategies (choose per phase)**

| Mode | Description | Phase |
|------|-------------|-------|
| **A. External Zoom** | Open Zoom URL / universal link (current web behavior) | Phase 1 default |
| **B. Embedded Meeting SDK** | Join inside app UI with meeting number + password + SDK JWT | Phase 1.5 / 2 |
| **C. Hybrid** | Prefer SDK; fall back to external Zoom if SDK fails | Recommended |

### 7.3 Phase 2 — Messaging

| ID | Requirement | Priority |
|----|-------------|----------|
| F2.1 | Thread per lobby visit (and later per client account) | P0 |
| F2.2 | Client ↔ receptionist text chat during wait and after ready | P0 |
| F2.3 | System messages: “You’re next”, “Ready”, “Transferred” | P1 |
| F2.4 | Push on new message | P0 |
| F2.5 | Staff can message from queue UI / staff app | P0 |
| F2.6 | Media attachments (images/PDF) with size limits | P2 |
| F2.7 | Message retention policy + admin delete | P1 |
| F2.8 | No PHI in push payload bodies (generic: “New message from Access Law Office”) | P0 |

**Suggested data model**

- `conversation_id`, `visit_id`, `participants[]`, `messages[]` (`sender_role`, `body`, `created_at`, `read_at`)  
- Transport: REST send/list + realtime (WebSocket/FCM data messages)

### 7.4 Phase 3 — Calling (non-Zoom)

Purpose: short coordination calls (receptionist clarifying documents, “please rejoin lobby”, etc.) without starting a full Zoom meeting.

| ID | Requirement | Priority |
|----|-------------|----------|
| F3.1 | Receptionist can place **in-app VoIP audio call** to waiting/ready client | P0 |
| F3.2 | Client can accept/decline; missed-call message in thread | P0 |
| F3.3 | Optional in-app video call (1:1 WebRTC) | P2 |
| F3.4 | Call quality fallback: “We’ll call your mobile number” (Twilio Voice PSTN) | P1 |
| F3.5 | Call logs linked to visit (duration, outcome) | P1 |
| F3.6 | Recording **off by default**; if enabled, explicit consent + legal review | P0 (compliance) |

**Technology options**

| Option | Pros | Cons |
|--------|------|------|
| **Twilio Voice SDK** | Fits existing Twilio stack; PSTN fallback | Cost per minute |
| **WebRTC (custom / Daily / Agora)** | Strong in-app UX | Another vendor; complexity |
| **Zoom Phone / Zoom Meeting for all calls** | One vendor | Heavier; Meeting SDK licensing |

**Recommendation:** Twilio Voice for Phase 3 audio; keep Zoom for formal video consultations.

### 7.5 Phase 4 — Virtual Lobby **inside** the app (Zoom Meeting SDK)

#### 7.5.1 Concept (your idea — validated)

Yes: Zoom provides a **Meeting SDK** that embeds meeting audio/video/UI (or custom UI depending on platform) **inside your app**, so clients do not need to open the Zoom mobile app or a browser tab.

Official path relevant to hybrid:

- **Zoom Meeting SDK for React Native** (`@zoom/meetingsdk-react-native`)  
- Requires native iOS/Android Meeting SDK install  
- Auth via **SDK JWT generated on your backend** (never ship SDK secret in the app)  
- Join with `meetingNumber`, `password`, `userName`  
- **Expo not supported**; pin RN version to Zoom’s supported range (verify at build time against current Zoom docs)

#### 7.5.2 Product requirements

| ID | Requirement | Priority |
|----|-------------|----------|
| F4.1 | Backend stores Zoom **meeting number + passcode** (not only join URL) for Reception + Attorney rooms | P0 |
| F4.2 | Backend endpoint issues short-lived **Meeting SDK JWT** for client join | P0 |
| F4.3 | App joins Reception meeting in-process when status = `ready` | P0 |
| F4.4 | On Transfer, app leaves/ends reception session UX, then joins Attorney meeting in-process | P0 |
| F4.5 | Waiting Room supported (attorney admits client) | P1 |
| F4.6 | Fallback: if SDK join fails, open external Zoom URL | P0 |
| F4.7 | Host/co-host remains receptionist/attorney in Zoom web/desktop (app is attendee join) | P0 |
| F4.8 | Do not require client Zoom account login | P0 |
| F4.9 | Document Zoom Marketplace app + Meeting SDK plan / fees | P0 |

#### 7.5.3 Architecture sketch

```
[Client App]
    |  check-in / poll status
    v
[WordPress/API] ---- visit status: ready | with_attorney
    |                      |
    |                      +--> zoom_url (fallback)
    |                      +--> meeting_number, passcode, phase
    v
[JWT service] -- SDK JWT --> [Client App] -- Meeting SDK.joinMeeting()
                                               |
                                               v
                                         In-app Zoom session
```

#### 7.5.4 Important constraints / risks

| Risk | Mitigation |
|------|------------|
| RN version pinning for Meeting SDK | Lock RN early; avoid Expo-managed workflow |
| Dual-room handoff UX | Explicit “Leave reception → Join attorney” confirmation screen |
| Zoom license cost (Meeting SDK is paid product) | Confirm Marketplace pricing before committing Phase 4 as MVP |
| Meeting URL-only config today | Extend settings: parse URL → number/pwd or add explicit fields |
| App store review (VoIP + camera) | Clear purpose strings; immigration consult use case |

#### 7.5.5 Alternative: Zoom Video SDK

- **Video SDK** = fully custom UI sessions (not “Zoom Meeting” rooms).  
- Better for greenfield custom rooms; **worse** if you want to keep existing scheduled Zoom Meetings + Waiting Room + staff already using Zoom client.  
- **Recommendation for this product:** prefer **Meeting SDK** to preserve current dual-room Zoom workflow.

---

## 8. Receptionist & Admin app experience

Staff use the **same app binary** with a **Staff sign-in** path. After auth, navigate by role.

### 8.1 Shared staff requirements

| ID | Requirement | Priority |
|----|-------------|----------|
| S0.1 | Staff login with WordPress username/email + password (or app password / OAuth later) | P0 |
| S0.2 | API returns `role`, `capabilities[]`, `display_name` | P0 |
| S0.3 | Session JWT/refresh; auto-logout on capability revoke | P0 |
| S0.4 | Biometric unlock optional after first login | P2 |
| S0.5 | Push: new visitor checked in; chat from client | P0 |
| S0.6 | Deep link to open specific `visit_id` from notification | P1 |

### 8.2 Receptionist app (role `receptionist`)

**Home:** Live queue dashboard (parity with Virtual Lobby → Queue).

| ID | Requirement | Priority |
|----|-------------|----------|
| R1.1 | List active visits: Waiting / Ready / In reception / With attorney | P0 |
| R1.2 | Show name, matter, phone (masked until expand), wait time, position | P0 |
| R1.3 | Actions: **Ready**, **Transfer to Attorney**, **Complete**, **Dismiss** | P0 |
| R1.4 | Block Ready/Transfer when Zoom meeting not configured (show Admin contact message) | P0 |
| R1.5 | Toggle **Lobby Open / Closed** | P0 |
| R1.6 | Auto-refresh queue (~6s) + pull-to-refresh | P0 |
| R1.7 | Open visit thread (messaging) | P0 (with Phase 2) |
| R1.8 | Place coordination call to visitor | P0 (with Phase 3) |
| R1.9 | Join Reception Zoom as host/co-host from app (optional; may stay on desktop Zoom) | P2 |
| R1.10 | **No access** to Zoom secrets, Twilio keys, or create-user screens | P0 |

**Receptionist IA**

1. Queue  
2. Visit detail (actions + chat + call)  
3. Lobby status toggle  
4. Profile / sign out  

### 8.3 Admin app (role `admin`)

Admins get **everything Receptionist has**, plus configuration.

| ID | Requirement | Priority |
|----|-------------|----------|
| A1.1 | All Receptionist queue capabilities | P0 |
| A1.2 | Settings: Reception Zoom URL / meeting number / passcode | P0 |
| A1.3 | Settings: Attorney Zoom URL / meeting number / passcode | P0 |
| A1.4 | Settings: Twilio + CAPTCHA / verify mode (or “Open in web admin” link if too heavy for v1) | P1 |
| A1.5 | Feature flags: messaging, VoIP, Meeting SDK | P1 |
| A1.6 | **Create / reset Receptionist user** (username, email, temporary password) | P0 |
| A1.7 | List staff users with roles; deactivate receptionist | P1 |
| A1.8 | Hours copy / timezone display source of truth | P1 |
| A1.9 | Basic reports: visits today, avg wait, completed/dismissed | P2 |
| A1.10 | Sensitive fields never logged to crash analytics | P0 |

**Admin IA**

1. Queue (same as Receptionist)  
2. Settings  
3. Staff users  
4. Reports (later)  
5. Profile / sign out  

### 8.4 Phase timing for staff mobile

| Phase | Staff scope |
|-------|-------------|
| **P0** | Auth + role routing stubs |
| **P1** | Receptionist queue + Admin settings (Zoom + create receptionist) — **in MVP**, not deferred forever |
| **P2+** | Messaging/calling on staff screens |
| **Web fallback** | Full WP admin remains for plugins, theme files, billing, etc. |

**MVP clarification:** Client lobby **and** Receptionist queue are both P1. Admin mobile settings are P1 for Zoom + receptionist user management; advanced Twilio/CAPTCHA may deep-link to web initially.

### 8.5 Auth API (staff)

| Endpoint | Purpose |
|----------|---------|
| `POST /auth/login` | Username/password → access token + `role` + `capabilities` |
| `POST /auth/refresh` | Refresh token |
| `POST /auth/logout` | Invalidate |
| `GET /auth/me` | Current user profile + role |

Unauthorized users without `alf_manage_lobby` must not receive staff tokens.

---

## 9. Backend & API requirements

### 9.1 Reuse existing AJAX (bootstrap)

Public (nonce `alf_lobby`):

- `alf_lobby_status`
- `alf_lobby_queue_snapshot`
- `alf_send_otp` / `alf_verify_otp`
- `alf_verify_captcha` / `alf_skip_verify`
- `alf_check_in`
- `alf_visit_status`
- `alf_visit_joined`

Admin:

- `alf_queue_list` / `alf_queue_update` / `alf_toggle_lobby`

### 9.2 New API surface (required for real app)

Create versioned REST under e.g. `/wp-json/alf/v1/`:

| Endpoint | Purpose | Roles |
|----------|---------|-------|
| `POST /lobby/check-in` | Create visit | Client (public) |
| `GET /lobby/visits/{id}` | Status + meeting credentials | Client (visit token) |
| `POST /lobby/visits/{id}/joined` | Mark in meeting | Client (visit token) |
| `POST /device/register` | Push token + platform | Client or Staff |
| `GET /queue` | Active visits | Receptionist, Admin |
| `POST /queue/{id}/actions` | `ready` \| `transfer` \| `complete` \| `dismiss` | Receptionist, Admin |
| `POST /lobby/toggle` | Open/closed | Receptionist, Admin |
| `GET /admin/settings` | Zoom / verify / flags | Admin |
| `PUT /admin/settings` | Update settings | Admin |
| `GET /admin/staff` | List receptionist users | Admin |
| `POST /admin/staff` | Create/reset receptionist | Admin |
| `POST /messaging/threads/{id}/messages` | Send message | Client (own), Receptionist, Admin |
| `GET /messaging/threads/{id}/messages` | List messages | Client (own), Receptionist, Admin |
| `POST /calls/sessions` | Start VoIP/PSTN call | Receptionist, Admin |
| `POST /zoom/sdk-jwt` | Meeting SDK JWT | Client (authorized phase) or Staff |
| `GET /config` | Public: verifyMode, hours, flags | Public |

**Security**

- Visit-scoped tokens for **clients**  
- Staff JWT scoped by **role + capabilities** (`alf_manage_lobby`, `manage_options`)  
- Admin-only routes reject Receptionist tokens  
- Rate limit OTP and check-in  
- SDK secret / Twilio token only on server  
- Audit log Admin setting changes (who/when)

### 9.3 Settings extensions

| Setting | Notes |
|---------|-------|
| Reception Zoom URL (existing) | Keep |
| Attorney Zoom URL (existing) | Keep |
| Reception meeting number + passcode | Required for SDK |
| Attorney meeting number + passcode | Required for SDK |
| Feature flags: `messaging`, `voip`, `meeting_sdk` | Gradual rollout |
| Push credentials | FCM/APNs via server |

---

## 10. UX requirements

### 10.1 Launch & role routing

1. Splash / optional intro gate  
2. **Choose path:** “Join Virtual Lobby” (client) **or** “Staff sign in”  
3. Client → lobby wizard IA below  
4. Staff → role-based home (Receptionist Queue vs Admin Queue+Settings)  

### 10.2 Client information architecture

1. Home (Lobby status + Enter)  
2. Lobby flow wizard  
3. Waiting room (position, tips, chat entry)  
4. Meeting screens (external or embedded)  
5. Messages (visit thread)  
6. Settings (notifications permission, legal links)  

### 10.3 Waiting room copy (parity)

- Do not close or force-quit the app while waiting.  
- You will get a notification when the receptionist is ready.  
- Meetings are 100% virtual; no in-person appointments.

### 10.4 Hours copy (keep consistent)

- Mon–Fri: 9:00 AM – 5:00 PM CST  
- Sat–Sun: 10:00 AM – 3:30 PM CST  

(Single source of truth on backend; do not hardcode conflicting times in multiple clients.)

### 10.5 Accessibility

- Dynamic type, VoiceOver/TalkBack labels, sufficient contrast (navy/gold brand).  
- Large tap targets for Join CTAs and queue actions.

---

## 11. Compliance, privacy, legal

| Topic | Requirement |
|-------|-------------|
| Immigration sensitivity | Minimize data; encrypt TLS; restrict staff access |
| Push content | No case details in notifications |
| OTP | Existing Twilio throttles; same for app |
| Call recording | Off unless counsel approves + consent UX |
| Chat retention | Document retention window (e.g. 90 days) |
| App Store / Play | Privacy nutrition labels; camera/mic purpose strings |
| Attorney advertising rules | Marketing claims reviewed by firm |
| Zoom BAA / vendor DPA | Review if required for firm’s compliance posture |

---

## 12. Success metrics

| Metric | Target (initial) |
|--------|------------------|
| Lobby check-in completion rate | ≥ web baseline |
| Time from Ready → Join | ↓ vs web (fewer drop-offs) |
| Push open rate on Ready | ≥ 60% |
| % meetings joined in-app (SDK) | Track after Phase 4 |
| Chat response time (staff) | < 2 min during open hours |
| Crash-free sessions | ≥ 99.5% |

---

## 13. Phased delivery plan

| Phase | Name | Scope | Outcome |
|-------|------|-------|---------|
| **P0** | Foundations | RN shell, API client, **role routing** (client / receptionist / admin), push plumbing | Installable TestFlight/Internal APK |
| **P1** | Lobby + Staff MVP | Client check-in + wait + Zoom join + push; **Receptionist queue**; **Admin** Zoom settings + create receptionist | Mobile replaces browser for lobby ops |
| **P1.5** | Meeting SDK | In-app Zoom join + fallback | No Zoom app hop for most users |
| **P2** | Messaging | Visit threads; Receptionist/Admin replies + push | Continuous contact during wait |
| **P3** | Calling | Twilio Voice / VoIP from Receptionist/Admin | Coordination without Zoom |
| **P4** | Admin depth | Full Twilio/CAPTCHA in-app; reports; staff deactivate | Less dependency on WP admin |
| **P5** | Client accounts / Attorney role | Returning clients; optional attorney login | Retention + attorney mobile |

**Suggested first store release:** P0 + P1 (+ P1.5 if Zoom SDK licensing approved).  
Receptionist + Admin are **not optional** for the product vision — they ship in P1 alongside the client lobby.

---

## 14. Open questions (resolve before/during build)

1. Is **Meeting SDK licensing cost** approved for launch, or is external Zoom OK for v1?  
2. ~~One app vs separate staff app?~~ → **Decided: one app**, role-gated (`client` / `receptionist` / `admin`).  
3. Must messaging work **only during an active lobby visit**, or also for general intake?  
4. Preferred call stack: **Twilio Voice** vs Zoom-only?  
5. India (`+91`) support required in production or US-only?  
6. Will WordPress remain system of record for 12+ months?  
7. Brand string in app: **Access Law Office** vs **Access Law Firm** (standardize).  
8. Auto-open lobby hours by clock (CST) vs keep **manual** open/closed toggle?  
9. May **Admin** also act as on-duty receptionist on the same device/shift? (Recommended: **yes** — Admin inherits queue.)  
10. Should multiple Receptionists be supported concurrently (claim/lock visit)?

---

## 15. Implementation notes for the next Cursor window

Use this PRD as the source of truth. Suggested first implementation prompts:

1. Scaffold React Native CLI app with navy/gold theme; launch screen: **Join Lobby** vs **Staff sign in**.  
2. Implement auth + RBAC (`client` / `receptionist` / `admin`) mapped to WP `alf_receptionist` + `administrator`.  
3. Implement API client wrapping existing `admin-ajax.php` actions; then add `/wp-json/alf/v1/` routes.  
4. Port client lobby wizard screens 0–7 from web UX.  
5. Build Receptionist queue (Ready/Transfer/Complete/Dismiss + lobby toggle).  
6. Build Admin settings (Zoom config + create receptionist user).  
7. Add push registration + server hooks on check-in / ready / with_attorney.  
8. Spike Zoom Meeting SDK join; document RN version pin.  
9. Design messaging schema with `sender_role`: `client` | `receptionist` | `admin`.  

### Key existing references in this repo

- Lobby UI: `wp-content/themes/access-law-firm/footer.php`  
- Lobby JS: `wp-content/themes/access-law-firm/assets/js/main.js`  
- Visits/queue + **Receptionist role**: `inc/lobby-visits.php`, `inc/lobby-admin.php`  
- Settings/Zoom helpers: `inc/settings.php`  
- OTP/CAPTCHA: `inc/twilio-otp.php`, `inc/captcha.php`  

**WP role keys to reuse**

- Role: `alf_receptionist`  
- Capability: `alf_manage_lobby`  
- Admin: `administrator` / `manage_options`  
- Helper: `alf_user_can_manage_lobby()`, `alf_is_receptionist_user()`

### Zoom SDK docs to follow at build time

- Meeting SDK React Native integrate guide (Zoom Developer Docs)  
- `@zoom/meetingsdk-react-native` package + JWT generation (server-side only)  
- Confirm current supported React Native version before scaffolding  

---

## 16. Summary decision matrix

| Capability | In web today? | In app MVP? | How |
|------------|---------------|-------------|-----|
| Virtual Lobby check-in (Client) | Yes | **Yes** | Reuse visit APIs |
| Queue wait + status (Client) | Yes | **Yes** | Poll + push |
| Join Zoom (Client) | External URL | **Yes** | Deep link first |
| Zoom **inside** app | No | **Recommended Phase 1.5** | Meeting SDK |
| **Receptionist** live queue | WP admin | **Yes (P1)** | Role `receptionist` / `alf_receptionist` |
| **Admin** Zoom + user mgmt | WP Settings | **Yes (P1)** | Role `admin` / `administrator` |
| Messaging | No | Phase 2 | Roles: client ↔ receptionist/admin |
| Calling | No | Phase 3 | Receptionist/Admin → client |
| Attorney mobile role | No | Phase 5 | Optional |

**Bottom line:** The app is a **three-role product** — **Client**, **Receptionist**, and **Admin** — aligned with today’s WordPress lobby permissions. Messaging and calling extend staff roles in later phases. **In-app Zoom via Meeting SDK** remains a planned phase with licensing and server-side JWT requirements.
