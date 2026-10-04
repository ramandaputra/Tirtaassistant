/**
 * TirtAssistant — Embeddable Chat Widget
 *
 * Usage:
 *   <script src="https://tirtassistant.tirtakepri.co.id/widget.js" defer></script>
 *
 * The script auto-detects its own origin so the API base URL is always correct,
 * regardless of where the widget is embedded.
 */
(function () {
    'use strict';

    /* ------------------------------------------------------------------ */
    /*  Configuration                                                      */
    /* ------------------------------------------------------------------ */

    // Auto-detect the origin of this script so it works from any host
    const currentScript = document.currentScript;
    const scriptSrc = currentScript ? currentScript.src : '';
    const BASE_URL = scriptSrc
        ? new URL(scriptSrc).origin
        : window.location.origin;
    const API_BASE = BASE_URL + '/api/widget';
    const ICON_URL = BASE_URL + '/img/icon.png';

    /* ------------------------------------------------------------------ */
    /*  State                                                              */
    /* ------------------------------------------------------------------ */

    let sessionId = null;
    let isOpen = false;
    let isTyping = false;
    let messages = [];

    /* ------------------------------------------------------------------ */
    /*  Helpers                                                            */
    /* ------------------------------------------------------------------ */

    function formatTime() {
        const now = new Date();
        return String(now.getHours()).padStart(2, '0') + ':' +
               String(now.getMinutes()).padStart(2, '0');
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function nl2br(text) {
        return escapeHtml(text).replace(/\n/g, '<br>');
    }

    /* ------------------------------------------------------------------ */
    /*  CSS (injected once)                                                */
    /* ------------------------------------------------------------------ */

    function injectStyles() {
        if (document.getElementById('tirtassistant-styles')) return;
        const style = document.createElement('style');
        style.id = 'tirtassistant-styles';
        style.textContent = `
/* ---- Reset & Container ---- */
#tirtassistant-widget *,
#tirtassistant-widget *::before,
#tirtassistant-widget *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}
#tirtassistant-widget {
    font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Helvetica Neue', Arial, sans-serif;
    font-size: 14px;
    line-height: 1.5;
    color: #1e293b;
    -webkit-font-smoothing: antialiased;
}

/* ---- FAB Button ---- */
#tirtassistant-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 999999;
    width: 64px;
    height: 64px;
    border: none;
    border-radius: 50%;
    background: linear-gradient(135deg, #1976D2 0%, #1565C0 50%, #0D47A1 100%);
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 28px rgba(21, 101, 192, 0.5), 0 2px 8px rgba(0,0,0,0.12);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    outline: none;
}
#tirtassistant-fab:hover {
    transform: scale(1.08);
    box-shadow: 0 8px 36px rgba(21, 101, 192, 0.65), 0 4px 12px rgba(0,0,0,0.15);
}
#tirtassistant-fab:active { transform: scale(0.95); }
#tirtassistant-fab svg { width: 30px; height: 30px; transition: transform 0.3s; }
#tirtassistant-fab .ta-fab-close { display: none; }
#tirtassistant-fab.ta-open .ta-fab-chat { display: none; }
#tirtassistant-fab.ta-open .ta-fab-close { display: block; }

/* Pulse ring */
#tirtassistant-fab::before {
    content: '';
    position: absolute;
    inset: -4px;
    border-radius: 50%;
    background: rgba(21, 101, 192, 0.35);
    animation: taPulse 2s ease-in-out infinite;
    pointer-events: none;
}
#tirtassistant-fab.ta-open::before { animation: none; opacity: 0; }
@keyframes taPulse {
    0%, 100% { transform: scale(1); opacity: 0.4; }
    50%      { transform: scale(1.25); opacity: 0; }
}

/* ---- Chat Window ---- */
#tirtassistant-window {
    position: fixed;
    bottom: 100px;
    right: 24px;
    z-index: 999998;
    width: 400px;
    max-width: calc(100vw - 32px);
    height: 580px;
    max-height: calc(100vh - 120px);
    background: #fff;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 64px rgba(0,0,0,0.18), 0 4px 16px rgba(0,0,0,0.08);
    display: flex;
    flex-direction: column;
    opacity: 0;
    transform: translateY(20px) scale(0.95);
    pointer-events: none;
    transition: opacity 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
#tirtassistant-window.ta-visible {
    opacity: 1;
    transform: translateY(0) scale(1);
    pointer-events: auto;
}

/* ---- Header ---- */
.ta-header {
    background: linear-gradient(135deg, #1976D2 0%, #1565C0 50%, #0D47A1 100%);
    color: #fff;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
    position: relative;
    overflow: hidden;
}
.ta-header::after {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: rgba(255,255,255,0.06);
    pointer-events: none;
}
.ta-header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}
.ta-avatar {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    background: #fff;
    padding: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.12);
    flex-shrink: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.ta-avatar img {
    width: 28px;
    height: 28px;
    object-fit: contain;
    border-radius: 50%;
}
.ta-avatar-dot {
    position: absolute;
    bottom: 1px;
    right: 1px;
    width: 10px;
    height: 10px;
    background: #4CAF50;
    border: 2px solid #fff;
    border-radius: 50%;
}
.ta-header-info h2 {
    font-size: 16px;
    font-weight: 700;
    letter-spacing: 0.3px;
    line-height: 1.2;
}
.ta-header-info p {
    font-size: 11px;
    color: rgba(255,255,255,0.8);
    font-weight: 500;
    letter-spacing: 0.3px;
}
.ta-header-actions {
    display: flex;
    gap: 4px;
}
.ta-header-btn {
    width: 36px;
    height: 36px;
    border: none;
    background: rgba(255,255,255,0.12);
    border-radius: 50%;
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s;
}
.ta-header-btn:hover { background: rgba(255,255,255,0.25); }
.ta-header-btn svg { width: 18px; height: 18px; }

/* ---- Messages Container ---- */
.ta-messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px 16px;
    background: linear-gradient(180deg, #F0F4F8 0%, #F8FAFC 100%);
    display: flex;
    flex-direction: column;
    gap: 16px;
    scroll-behavior: smooth;
    scrollbar-width: thin;
    scrollbar-color: #cbd5e1 transparent;
}
.ta-messages::-webkit-scrollbar { width: 5px; }
.ta-messages::-webkit-scrollbar-track { background: transparent; }
.ta-messages::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

/* ---- Message Bubbles ---- */
.ta-msg-row {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    animation: taFadeIn 0.35s ease;
}
.ta-msg-row.ta-user { justify-content: flex-end; }
.ta-msg-row.ta-bot  { justify-content: flex-start; }

@keyframes taFadeIn {
    from { opacity: 0; transform: translateY(8px); }
    to   { opacity: 1; transform: translateY(0); }
}

.ta-msg-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #fff;
    border: 1px solid #e2e8f0;
    padding: 3px;
    flex-shrink: 0;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 1px 4px rgba(0,0,0,0.06);
}
.ta-msg-avatar img { width: 100%; height: 100%; object-fit: contain; border-radius: 50%; }

.ta-msg-content { max-width: 80%; display: flex; flex-direction: column; }

.ta-bubble {
    padding: 12px 16px;
    border-radius: 20px;
    font-size: 13px;
    line-height: 1.55;
    word-break: break-word;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.ta-bot .ta-bubble {
    background: #fff;
    color: #334155;
    border: 1px solid rgba(226,232,240,0.6);
    border-bottom-left-radius: 6px;
}
.ta-user .ta-bubble {
    background: linear-gradient(135deg, #1976D2, #1565C0);
    color: #fff;
    border-bottom-right-radius: 6px;
}

.ta-msg-time {
    font-size: 10px;
    color: #94a3b8;
    margin-top: 3px;
    font-weight: 500;
}
.ta-user .ta-msg-time { text-align: right; margin-right: 4px; }
.ta-bot .ta-msg-time  { margin-left: 4px; }

/* Sources */
.ta-sources {
    margin-top: 10px;
    padding-top: 8px;
    border-top: 1px solid rgba(226,232,240,0.5);
}
.ta-sources-label {
    font-size: 10px;
    font-weight: 700;
    color: #94a3b8;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.ta-sources-list { display: flex; flex-wrap: wrap; gap: 4px; }
.ta-source-tag {
    font-size: 10px;
    background: #f1f5f9;
    color: #64748b;
    padding: 3px 8px;
    border-radius: 6px;
    border: 1px solid #e2e8f0;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.ta-source-tag svg { width: 10px; height: 10px; color: #3b82f6; }

/* ---- Quick Actions ---- */
.ta-quick-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    padding-left: 40px;
    animation: taFadeIn 0.5s ease 0.3s both;
}
.ta-quick-btn {
    font-size: 11px;
    font-weight: 600;
    padding: 8px 14px;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    cursor: pointer;
    transition: all 0.2s;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.ta-quick-btn:hover {
    border-color: #1565C0;
    color: #1565C0;
    background: #f0f7ff;
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(21,101,192,0.12);
}

/* ---- Typing Indicator ---- */
.ta-typing {
    display: flex;
    align-items: flex-end;
    gap: 8px;
    animation: taFadeIn 0.3s ease;
}
.ta-typing-dots {
    background: #fff;
    border: 1px solid rgba(226,232,240,0.6);
    padding: 14px 20px;
    border-radius: 20px;
    border-bottom-left-radius: 6px;
    display: flex;
    align-items: center;
    gap: 5px;
    box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}
.ta-typing-dot {
    width: 8px;
    height: 8px;
    background: rgba(21, 101, 192, 0.5);
    border-radius: 50%;
    animation: taBounce 1.4s ease-in-out infinite;
}
.ta-typing-dot:nth-child(2) { animation-delay: 0.2s; }
.ta-typing-dot:nth-child(3) { animation-delay: 0.4s; }
@keyframes taBounce {
    0%, 60%, 100% { transform: translateY(0); }
    30%           { transform: translateY(-6px); }
}

/* ---- Input Area ---- */
.ta-input-area {
    padding: 12px 14px;
    background: #fff;
    border-top: 1px solid #f1f5f9;
    flex-shrink: 0;
}
.ta-input-form {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f8fafc;
    border: 2px solid #e2e8f0;
    border-radius: 28px;
    padding: 4px 6px 4px 16px;
    transition: border-color 0.2s;
}
.ta-input-form:focus-within { border-color: #1976D2; }
.ta-input-field {
    flex: 1;
    border: none;
    background: transparent;
    font-size: 13px;
    color: #334155;
    outline: none;
    font-family: inherit;
    line-height: 1.4;
    padding: 6px 0;
}
.ta-input-field::placeholder { color: #94a3b8; }
.ta-send-btn {
    width: 38px;
    height: 38px;
    border: none;
    border-radius: 50%;
    background: linear-gradient(135deg, #1976D2, #1565C0);
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.2s;
    box-shadow: 0 2px 8px rgba(21,101,192,0.3);
}
.ta-send-btn:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(21,101,192,0.4); }
.ta-send-btn:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }
.ta-send-btn svg { width: 16px; height: 16px; }

/* ---- Footer ---- */
.ta-footer {
    text-align: center;
    padding: 6px;
    background: #fff;
    flex-shrink: 0;
}
.ta-footer p {
    font-size: 10px;
    color: #94a3b8;
    font-weight: 500;
}
.ta-footer span { color: #1565C0; font-weight: 700; }

/* ---- Mobile ---- */
@media (max-width: 480px) {
    #tirtassistant-window {
        bottom: 0;
        right: 0;
        width: 100vw;
        height: 100vh;
        max-height: 100vh;
        border-radius: 0;
    }
    #tirtassistant-fab { bottom: 16px; right: 16px; width: 56px; height: 56px; }
    #tirtassistant-fab svg { width: 26px; height: 26px; }
}
`;
        document.head.appendChild(style);
    }

    /* ------------------------------------------------------------------ */
    /*  DOM builders                                                       */
    /* ------------------------------------------------------------------ */

    function createFab() {
        const fab = document.createElement('button');
        fab.id = 'tirtassistant-fab';
        fab.setAttribute('aria-label', 'Buka Chat TirtAssistant');
        fab.innerHTML = `
            <svg class="ta-fab-chat" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <svg class="ta-fab-close" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M6 18L18 6M6 6l12 12"/>
            </svg>`;
        fab.addEventListener('click', function (e) {
            e.stopPropagation();
            toggleChat();
        });
        return fab;
    }

    function createWindow() {
        const win = document.createElement('div');
        win.id = 'tirtassistant-window';

        win.innerHTML = `
        <!-- Header -->
        <div class="ta-header">
            <div class="ta-header-left">
                <div class="ta-avatar">
                    <img src="${ICON_URL}" alt="TirtAssistant">
                    <span class="ta-avatar-dot"></span>
                </div>
                <div class="ta-header-info">
                    <h2>TirtAssistant</h2>
                    <p>Asisten Virtual Tirta Kepri</p>
                </div>
            </div>
            <div class="ta-header-actions">
                <button class="ta-header-btn" id="ta-close-btn" title="Tutup" aria-label="Tutup chat">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div class="ta-messages" id="ta-messages"></div>

        <!-- Input -->
        <div class="ta-input-area">
            <div class="ta-input-form" id="ta-form">
                <input class="ta-input-field" id="ta-input" type="text"
                       placeholder="Tulis pesan Anda..." autocomplete="off">
                <button class="ta-send-btn" type="button" id="ta-send" aria-label="Kirim pesan">
                    <svg fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Footer -->
        <div class="ta-footer">
            <p>Powered by <span>Tirta Kepri</span></p>
        </div>`;

        // Event listeners — use stopPropagation to prevent bubbling
        win.querySelector('#ta-close-btn').addEventListener('click', function (e) {
            e.stopPropagation();
            toggleChat();
        });

        // Send button click
        win.querySelector('#ta-send').addEventListener('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            handleSend();
        });

        // Enter key on input
        win.querySelector('#ta-input').addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                e.stopPropagation();
                handleSend();
            }
        });

        return win;
    }

    /* ------------------------------------------------------------------ */
    /*  Rendering                                                          */
    /* ------------------------------------------------------------------ */

    function getMessagesEl() {
        return document.getElementById('ta-messages');
    }

    function renderBotMessage(content, sources, time) {
        const container = getMessagesEl();
        const row = document.createElement('div');
        row.className = 'ta-msg-row ta-bot';

        let sourcesHtml = '';
        if (sources && sources.length > 0) {
            const tags = sources.map(function (s) {
                return '<span class="ta-source-tag">' +
                    '<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>' +
                    escapeHtml(s.document) + '</span>';
            }).join('');
            sourcesHtml = '<div class="ta-sources"><div class="ta-sources-label">Sumber:</div><div class="ta-sources-list">' + tags + '</div></div>';
        }

        row.innerHTML =
            '<div class="ta-msg-avatar"><img src="' + ICON_URL + '" alt="Bot"></div>' +
            '<div class="ta-msg-content">' +
            '<div class="ta-bubble">' + nl2br(content) + sourcesHtml + '</div>' +
            '<span class="ta-msg-time">' + escapeHtml(time || formatTime()) + '</span>' +
            '</div>';
        container.appendChild(row);
        scrollToBottom();
    }

    function renderUserMessage(content, time) {
        const container = getMessagesEl();
        const row = document.createElement('div');
        row.className = 'ta-msg-row ta-user';
        row.innerHTML =
            '<div class="ta-msg-content">' +
            '<div class="ta-bubble">' + escapeHtml(content) + '</div>' +
            '<span class="ta-msg-time">' + escapeHtml(time || formatTime()) + '</span>' +
            '</div>';
        container.appendChild(row);
        scrollToBottom();
    }

    function renderQuickActions() {
        const container = getMessagesEl();
        const actions = [
            { emoji: '📄', label: 'Cek Tagihan' },
            { emoji: '📊', label: 'Info Tarif' },
            { emoji: '💳', label: 'Pembayaran' },
            { emoji: '⚙️', label: 'Gangguan' },
            { emoji: '💬', label: 'Pengaduan' },
        ];
        const div = document.createElement('div');
        div.className = 'ta-quick-actions';
        actions.forEach(function (action) {
            const btn = document.createElement('button');
            btn.className = 'ta-quick-btn';
            btn.textContent = action.emoji + ' ' + action.label;
            btn.addEventListener('click', function () {
                sendMessageToApi(action.label);
                // Remove quick actions after first click
                if (div.parentNode) div.parentNode.removeChild(div);
            });
            div.appendChild(btn);
        });
        container.appendChild(div);
        scrollToBottom();
    }

    function showTyping() {
        const container = getMessagesEl();
        // Remove any existing typing indicator
        const existing = container.querySelector('.ta-typing');
        if (existing) existing.remove();

        const div = document.createElement('div');
        div.className = 'ta-typing';
        div.innerHTML =
            '<div class="ta-msg-avatar"><img src="' + ICON_URL + '" alt="Bot"></div>' +
            '<div class="ta-typing-dots">' +
            '<span class="ta-typing-dot"></span>' +
            '<span class="ta-typing-dot"></span>' +
            '<span class="ta-typing-dot"></span>' +
            '</div>';
        container.appendChild(div);
        scrollToBottom();
    }

    function hideTyping() {
        const container = getMessagesEl();
        const el = container.querySelector('.ta-typing');
        if (el) el.remove();
    }

    function scrollToBottom() {
        const container = getMessagesEl();
        if (container) {
            requestAnimationFrame(function () {
                container.scrollTop = container.scrollHeight;
            });
        }
    }

    function setInputEnabled(enabled) {
        const input = document.getElementById('ta-input');
        const send = document.getElementById('ta-send');
        if (input) input.disabled = !enabled;
        if (send) send.disabled = !enabled;
    }

    /* ------------------------------------------------------------------ */
    /*  API calls                                                          */
    /* ------------------------------------------------------------------ */

    function startSession() {
        return fetch(API_BASE + '/session', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({}),
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            sessionId = data.session_id;
            // Render greeting
            if (data.greeting) {
                renderBotMessage(data.greeting.content, [], data.greeting.time);
            }
            renderQuickActions();
        })
        .catch(function (err) {
            console.error('[TirtAssistant] Session error:', err);
            renderBotMessage('Maaf, tidak dapat terhubung ke server. Silakan coba lagi nanti.', [], formatTime());
        });
    }

    function sendMessageToApi(text) {
        if (!text.trim() || isTyping) return;

        // Remove quick actions if still present
        const qa = getMessagesEl().querySelector('.ta-quick-actions');
        if (qa) qa.remove();

        renderUserMessage(text);
        isTyping = true;
        setInputEnabled(false);
        showTyping();

        if (!sessionId) {
            // If no session yet, start one then send
            startSession().then(function () {
                doSend(text);
            });
            return;
        }

        doSend(text);
    }

    function doSend(text) {
        fetch(API_BASE + '/message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                session_id: sessionId,
                message: text,
            }),
        })
        .then(function (res) {
            if (!res.ok) throw new Error('HTTP ' + res.status);
            return res.json();
        })
        .then(function (data) {
            hideTyping();
            isTyping = false;
            setInputEnabled(true);
            if (data.reply) {
                renderBotMessage(
                    data.reply.content,
                    data.reply.sources || [],
                    data.reply.time
                );
            }
            document.getElementById('ta-input').focus();
        })
        .catch(function (err) {
            console.error('[TirtAssistant] Send error:', err);
            hideTyping();
            isTyping = false;
            setInputEnabled(true);
            renderBotMessage('Maaf, terjadi kendala. Silakan coba lagi.', [], formatTime());
        });
    }

    /* ------------------------------------------------------------------ */
    /*  Toggle Chat                                                        */
    /* ------------------------------------------------------------------ */

    function toggleChat() {
        isOpen = !isOpen;

        const fab = document.getElementById('tirtassistant-fab');
        const win = document.getElementById('tirtassistant-window');

        if (isOpen) {
            fab.classList.add('ta-open');
            win.classList.add('ta-visible');
            // Start session on first open
            if (!sessionId) {
                startSession();
            }
            // Focus input
            setTimeout(function () {
                var input = document.getElementById('ta-input');
                if (input) input.focus();
            }, 350);
        } else {
            fab.classList.remove('ta-open');
            win.classList.remove('ta-visible');
        }
    }

    function handleSend() {
        const input = document.getElementById('ta-input');
        if (!input) return;
        const text = input.value.trim();
        if (!text) return;
        input.value = '';
        sendMessageToApi(text);
    }

    /* ------------------------------------------------------------------ */
    /*  Init                                                               */
    /* ------------------------------------------------------------------ */

    function init() {
        // Don't double-init
        if (document.getElementById('tirtassistant-widget')) return;

        injectStyles();

        const wrapper = document.createElement('div');
        wrapper.id = 'tirtassistant-widget';
        wrapper.appendChild(createWindow());
        wrapper.appendChild(createFab());
        document.body.appendChild(wrapper);
    }

    // Run init when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();