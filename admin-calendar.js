/* ========================================
   Admin Multi-Calendar (Airbnb-style)
   Residence I Mari — Gestione Disponibilità + Prezzi
   ======================================== */
(function () {
  'use strict';

  /* ── State ── */
  var now   = new Date();
  var state = {
    year : now.getFullYear(),
    month: now.getMonth(),
    apts : window.rimCalData || [],
    sel  : null   // {aptId, from} or {aptId, from, to}
  };

  var wrap = document.getElementById('rim-cal-wrap');

  var MONTHS = [
    'Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno',
    'Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'
  ];
  var DAYS = ['L','M','M','G','V','S','D'];

  /* ── Helpers ── */
  function pad (n) { return n < 10 ? '0' + n : '' + n; }
  function ymd (y, m, d) { return y + '-' + pad(m + 1) + '-' + pad(d); }
  function todayYmd () { return ymd(now.getFullYear(), now.getMonth(), now.getDate()); }
  function dim (y, m) { return new Date(y, m + 1, 0).getDate(); }

  function dow (y, m, d) {
    var w = new Date(y, m, d).getDay();
    return w === 0 ? 6 : w - 1;
  }

  function addDays (ds, n) {
    var d = new Date(ds + 'T12:00:00');
    d.setDate(d.getDate() + n);
    return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
  }

  function nights (a, b) {
    return Math.round((new Date(b + 'T12:00:00') - new Date(a + 'T12:00:00')) / 86400000);
  }

  function fmtIT (ds) {
    var p = ds.split('-');
    return p[2] + '/' + p[1] + '/' + p[0];
  }

  function esc (s) { return s.replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;'); }

  /* ── Price helpers ── */
  function getPrice (apt, ds) {
    if (apt.prices && apt.prices[ds] !== undefined) return parseFloat(apt.prices[ds]);
    return apt.base || 0;
  }

  function fmtPrice (n) {
    if (!n || n <= 0) return '';
    return n % 1 === 0 ? n + '€' : n.toFixed(0) + '€';
  }

  /* ── Booking lookups ── */
  function findBooking (apt, ds) {
    for (var i = 0; i < apt.booked.length; i++) {
      var b = apt.booked[i];
      if (ds >= b.from && ds < b.to) return { idx: i, b: b };
    }
    return null;
  }

  function bookingPos (apt, ds) {
    var info = findBooking(apt, ds);
    if (!info) return null;
    var b       = info.b;
    var isStart = (ds === b.from);
    var isEnd   = (addDays(ds, 1) >= b.to);
    var type;
    if (isStart && isEnd)  type = 'single';
    else if (isStart)      type = 'start';
    else if (isEnd)        type = 'end';
    else                   type = 'mid';
    return { type: type, idx: info.idx, b: b };
  }

  /* ── Selection helpers ── */
  function inSelection (aptId, ds) {
    var s = state.sel;
    if (!s || s.aptId !== aptId) return false;
    if (!s.to) return ds === s.from;
    var a = s.from < s.to ? s.from : s.to;
    var b = s.from < s.to ? s.to   : s.from;
    return ds >= a && ds <= b;
  }

  function selPos (ds) {
    var s = state.sel;
    if (!s || !s.to) return 'single';
    var a = s.from < s.to ? s.from : s.to;
    var b = s.from < s.to ? s.to   : s.from;
    if (ds === a && ds === b) return 'single';
    if (ds === a) return 'start';
    if (ds === b) return 'end';
    return 'mid';
  }

  function aptById (id) {
    for (var i = 0; i < state.apts.length; i++) {
      if (state.apts[i].id === id) return state.apts[i];
    }
    return null;
  }

  /* ── Render ── */
  function render () {
    var days = dim(state.year, state.month);
    var ts   = todayYmd();
    var h    = '';

    /* Header */
    h += '<div class="rc-header">';
    h += '<button class="rc-nav" id="rc-prev">&larr;</button>';
    h += '<h2 class="rc-month-title">' + MONTHS[state.month] + ' ' + state.year + '</h2>';
    h += '<button class="rc-nav" id="rc-next">&rarr;</button>';
    h += '<button class="rc-btn-today" id="rc-go-today">Oggi</button>';
    h += '</div>';

    /* Legend */
    h += '<div class="rc-legend">';
    h += '<span class="rc-leg"><span class="rc-dot rc-dot-free"></span> Disponibile</span>';
    h += '<span class="rc-leg"><span class="rc-dot rc-dot-booked"></span> Occupato</span>';
    h += '<span class="rc-leg"><span class="rc-dot rc-dot-sel"></span> Selezione</span>';
    h += '<span class="rc-leg-hint">Click = seleziona periodo &middot; Modifica prezzo + disponibilit&agrave;</span>';
    h += '</div>';

    /* Status bar */
    var statusCls = 'rc-status';
    var statusTxt = 'Clicca su una cella per selezionare un periodo. Potrai modificare prezzo e disponibilit&agrave;.';
    if (state.sel && !state.sel.to) {
      var sa = aptById(state.sel.aptId);
      statusCls += ' rc-status-sel';
      statusTxt = 'Selezionando: <strong>' + (sa ? sa.title : '') + '</strong> dal <strong>' +
                  fmtIT(state.sel.from) + '</strong> &mdash; clicca la data di fine.';
    }
    h += '<div class="' + statusCls + '" id="rc-status">' + statusTxt + '</div>';

    /* Grid */
    h += '<div class="rc-grid-wrap"><table class="rc-grid"><thead><tr>';
    h += '<th class="rc-apt-header">Appartamento</th>';

    for (var d = 1; d <= days; d++) {
      var w   = dow(state.year, state.month, d);
      var ds  = ymd(state.year, state.month, d);
      var isW = (w >= 5);
      var isT = (ds === ts);
      h += '<th class="rc-day-h' + (isW ? ' rc-we' : '') + (isT ? ' rc-today-h' : '') + '">';
      h += DAYS[w] + '<br>' + d + '</th>';
    }
    h += '</tr></thead><tbody>';

    /* Apartment rows */
    for (var a = 0; a < state.apts.length; a++) {
      var apt = state.apts[a];
      h += '<tr><td class="rc-apt-name">' + esc(apt.title) + '</td>';

      for (var d = 1; d <= days; d++) {
        var ds  = ymd(state.year, state.month, d);
        var bp  = bookingPos(apt, ds);
        var w   = dow(state.year, state.month, d);
        var isW = (w >= 5);
        var isT = (ds === ts);
        var iS  = inSelection(apt.id, ds);
        var price = getPrice(apt, ds);

        var cls = 'rc-cell';
        if (isT) cls += ' rc-today-c';
        if (isW) cls += ' rc-we-c';

        if (bp) {
          cls += ' rc-bk rc-bk-' + bp.type;
          var tt = (bp.b.guest || 'Occupato');
          if (bp.b.note) tt += ' — ' + bp.b.note;
          tt += '\n' + fmtIT(bp.b.from) + ' \u2192 ' + fmtIT(bp.b.to);
          tt += ' (' + nights(bp.b.from, bp.b.to) + ' notti)';
          h += '<td class="' + cls + '" data-apt="' + apt.id + '" data-date="' + ds +
               '" data-bidx="' + bp.idx + '" title="' + esc(tt) + '">';
          if (bp.type === 'start' || bp.type === 'single') {
            h += '<span class="rc-bk-label">' + esc(bp.b.guest || '\u2022') + '</span>';
          }
          h += '</td>';
        } else if (iS) {
          var sp = selPos(ds);
          cls += ' rc-sel rc-sel-' + sp;
          h += '<td class="' + cls + '" data-apt="' + apt.id + '" data-date="' + ds + '">';
          h += '<span class="rc-price">' + fmtPrice(price) + '</span>';
          h += '</td>';
        } else {
          h += '<td class="' + cls + '" data-apt="' + apt.id + '" data-date="' + ds + '">';
          h += '<span class="rc-price">' + fmtPrice(price) + '</span>';
          h += '</td>';
        }
      }
      h += '</tr>';
    }

    h += '</tbody></table></div>';

    /* Popup overlay */
    h += '<div class="rc-overlay" id="rc-overlay" style="display:none">';
    h += '<div class="rc-popup"><button class="rc-popup-close" id="rc-popup-close">&times;</button>';
    h += '<div id="rc-popup-content"></div></div></div>';

    wrap.innerHTML = h;
    bindEvents();
  }

  /* ── Popup: Edit Price & Availability ── */
  function showPopupEdit (aptId, from, toInc) {
    var apt = aptById(aptId);
    if (!apt) return;
    var nn = nights(from, addDays(toInc, 1));

    // Calculate current price for first day of selection
    var currentPrice = getPrice(apt, from);

    // Check if any day in range is booked
    var hasBookings = false;
    var cur = from;
    while (cur <= toInc) {
      if (findBooking(apt, cur)) { hasBookings = true; break; }
      cur = addDays(cur, 1);
    }

    var c = document.getElementById('rc-popup-content');
    var html =
      '<h3>Modifica Periodo</h3>' +
      '<p class="rc-popup-apt">' + esc(apt.title) + '</p>' +
      '<p class="rc-popup-dates">' + fmtIT(from) + ' &rarr; ' + fmtIT(addDays(toInc, 1)) +
      ' <small>(' + nn + ' nott' + (nn === 1 ? 'e' : 'i') + ')</small></p>';

    // Tabs
    html += '<div class="rc-tabs">' +
      '<button class="rc-tab rc-tab-active" data-tab="price">Prezzo</button>' +
      '<button class="rc-tab" data-tab="avail">Disponibilit&agrave;</button>' +
      '</div>';

    // Tab: Price
    html += '<div class="rc-tab-content" id="rc-tab-price">';
    html += '<div class="rc-popup-field"><label>Prezzo giornaliero (&euro;)</label>' +
      '<input type="number" id="rc-p-price" value="' + currentPrice + '" min="0" step="1" placeholder="es. 148"></div>';
    html += '<p class="rc-popup-hint">Prezzo base: <strong>' + (apt.base || 0) + '&euro;</strong></p>';
    html += '<div class="rc-popup-btns">' +
      '<button class="rc-pbtn rc-pbtn-save" id="rc-p-save-price">Salva Prezzo</button>' +
      '<button class="rc-pbtn rc-pbtn-cancel" id="rc-p-cancel-price">Annulla</button></div>';
    html += '<div class="rc-popup-msg" id="rc-p-msg-price"></div>';
    html += '</div>';

    // Tab: Availability
    html += '<div class="rc-tab-content" id="rc-tab-avail" style="display:none">';
    if (hasBookings) {
      html += '<p class="rc-popup-warn">Alcune date nel range sono gi&agrave; occupate.</p>';
    }
    html += '<div class="rc-popup-field"><label>Ospite</label>' +
      '<input type="text" id="rc-p-guest" placeholder="Nome (opzionale)"></div>';
    html += '<div class="rc-popup-field"><label>Note</label>' +
      '<input type="text" id="rc-p-note" placeholder="Note (opzionale)"></div>';
    html += '<div class="rc-popup-btns">' +
      '<button class="rc-pbtn rc-pbtn-save" id="rc-p-block">Blocca Periodo</button>' +
      '<button class="rc-pbtn rc-pbtn-cancel" id="rc-p-cancel-avail">Annulla</button></div>';
    html += '<div class="rc-popup-msg" id="rc-p-msg-avail"></div>';
    html += '</div>';

    c.innerHTML = html;
    openPopup();

    // Focus price input
    document.getElementById('rc-p-price').focus();
    document.getElementById('rc-p-price').select();

    // Tab switching
    var tabs = c.querySelectorAll('.rc-tab');
    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        tabs.forEach(function (t) { t.classList.remove('rc-tab-active'); });
        tab.classList.add('rc-tab-active');
        document.getElementById('rc-tab-price').style.display = tab.dataset.tab === 'price' ? '' : 'none';
        document.getElementById('rc-tab-avail').style.display = tab.dataset.tab === 'avail' ? '' : 'none';
      });
    });

    // Save Price
    document.getElementById('rc-p-save-price').onclick = function () {
      var btn = this;
      var newPrice = parseFloat(document.getElementById('rc-p-price').value);
      if (isNaN(newPrice) || newPrice < 0) {
        showMsg('rc-p-msg-price', 'Inserisci un prezzo valido.', 'err');
        return;
      }
      btn.disabled = true;
      btn.textContent = 'Salvataggio\u2026';
      ajaxPost('rim_save_prices', {
        post_id: aptId,
        from:    from,
        to:      toInc,
        price:   newPrice
      }, function (res) {
        if (res.success) {
          // Update local state
          apt.prices = res.data.prices;
          apt.base = res.data.base;
          state.sel = null;
          closePopup();
          render();
        } else {
          btn.disabled = false;
          btn.textContent = 'Salva Prezzo';
          showMsg('rc-p-msg-price', res.data, 'err');
        }
      });
    };

    // Block Availability
    document.getElementById('rc-p-block').onclick = function () {
      if (hasBookings) {
        if (!confirm('Il range include date già occupate. Continuare?')) return;
      }
      var btn = this;
      var toExc = addDays(toInc, 1);
      btn.disabled = true;
      btn.textContent = 'Salvataggio\u2026';
      ajaxPost('rim_add_blocked_dates', {
        post_id: aptId,
        from:    from,
        to:      toExc,
        guest:   document.getElementById('rc-p-guest').value,
        note:    document.getElementById('rc-p-note').value
      }, function (res) {
        if (res.success) {
          apt.booked = res.data.booked;
          state.sel = null;
          closePopup();
          render();
        } else {
          btn.disabled = false;
          btn.textContent = 'Blocca Periodo';
          showMsg('rc-p-msg-avail', res.data, 'err');
        }
      });
    };

    // Cancel buttons
    document.getElementById('rc-p-cancel-price').onclick = function () {
      state.sel = null; closePopup(); render();
    };
    document.getElementById('rc-p-cancel-avail').onclick = function () {
      state.sel = null; closePopup(); render();
    };
  }

  /* ── Popup: Booking Info ── */
  function showPopupInfo (aptId, idx) {
    var apt = aptById(aptId);
    if (!apt || !apt.booked[idx]) return;
    var b  = apt.booked[idx];
    var nn = nights(b.from, b.to);

    // Calculate total price for this booking
    var totalPrice = 0;
    var cur = b.from;
    while (cur < b.to) {
      totalPrice += getPrice(apt, cur);
      cur = addDays(cur, 1);
    }

    var c = document.getElementById('rc-popup-content');
    c.innerHTML =
      '<h3>Dettaglio Prenotazione</h3>' +
      '<p class="rc-popup-apt">' + esc(apt.title) + '</p>' +
      '<table class="rc-info-table">' +
      '<tr><td><strong>Check-in</strong></td><td>' + fmtIT(b.from) + '</td></tr>' +
      '<tr><td><strong>Check-out</strong></td><td>' + fmtIT(b.to) + '</td></tr>' +
      '<tr><td><strong>Notti</strong></td><td>' + nn + '</td></tr>' +
      '<tr><td><strong>Ospite</strong></td><td>' + esc(b.guest || '\u2014') + '</td></tr>' +
      '<tr><td><strong>Note</strong></td><td>' + esc(b.note || '\u2014') + '</td></tr>' +
      (totalPrice > 0 ? '<tr><td><strong>Totale</strong></td><td><strong>' + totalPrice + ' &euro;</strong></td></tr>' : '') +
      '</table>' +
      '<div class="rc-popup-btns">' +
      '<button class="rc-pbtn rc-pbtn-del" id="rc-p-delete">Rimuovi Blocco</button>' +
      '<button class="rc-pbtn rc-pbtn-cancel" id="rc-p-close">Chiudi</button></div>' +
      '<div class="rc-popup-msg" id="rc-p-msg"></div>';

    openPopup();

    document.getElementById('rc-p-delete').onclick = function () {
      if (!confirm('Rimuovere questo periodo occupato?')) return;
      var btn = this;
      btn.disabled    = true;
      btn.textContent = 'Rimozione\u2026';
      ajaxPost('rim_remove_blocked_dates', {
        post_id: aptId,
        index:   idx
      }, function (res) {
        if (res.success) {
          apt.booked = res.data.booked;
          closePopup();
          render();
        } else {
          btn.disabled    = false;
          btn.textContent = 'Rimuovi Blocco';
          showMsg('rc-p-msg', res.data, 'err');
        }
      });
    };

    document.getElementById('rc-p-close').onclick = function () { closePopup(); };
  }

  /* ── Popup helpers ── */
  function openPopup ()  { document.getElementById('rc-overlay').style.display = 'flex'; }
  function closePopup () { document.getElementById('rc-overlay').style.display = 'none'; }
  function showMsg (id, txt, type) {
    var el = document.getElementById(id);
    if (!el) return;
    el.textContent = txt;
    el.className   = 'rc-popup-msg ' + (type === 'err' ? 'rc-msg-err' : 'rc-msg-ok');
  }

  /* ── AJAX ── */
  function ajaxPost (action, data, cb) {
    var fd = new FormData();
    fd.append('action', action);
    fd.append('nonce',  rimCal.nonce);
    for (var k in data) {
      if (data.hasOwnProperty(k)) fd.append(k, data[k]);
    }
    fetch(rimCal.ajaxUrl, { method: 'POST', body: fd, credentials: 'same-origin' })
      .then(function (r) { return r.json(); })
      .then(cb)
      .catch(function (e) { alert('Errore di connessione: ' + e.message); });
  }

  /* ── Events ── */
  function bindEvents () {
    /* Navigation */
    document.getElementById('rc-prev').onclick = function () {
      state.month--;
      if (state.month < 0) { state.month = 11; state.year--; }
      state.sel = null;
      render();
    };
    document.getElementById('rc-next').onclick = function () {
      state.month++;
      if (state.month > 11) { state.month = 0; state.year++; }
      state.sel = null;
      render();
    };
    document.getElementById('rc-go-today').onclick = function () {
      state.year  = now.getFullYear();
      state.month = now.getMonth();
      state.sel   = null;
      render();
    };

    /* Cell clicks (event delegation on the grid) */
    var grid = document.querySelector('.rc-grid');
    if (grid) {
      grid.addEventListener('click', function (e) {
        var cell = e.target.closest('.rc-cell');
        if (!cell) return;
        handleCellClick(cell);
      });

      /* Hover preview during selection */
      grid.addEventListener('mouseover', function (e) {
        var cell = e.target.closest('.rc-cell');
        if (!cell) return;
        handleCellHover(cell);
      });
      grid.addEventListener('mouseleave', function () {
        clearHover();
      });
    }

    /* Popup close */
    document.getElementById('rc-popup-close').onclick = function () {
      state.sel = null;
      closePopup();
      render();
    };
    document.getElementById('rc-overlay').onclick = function (e) {
      if (e.target === this) {
        state.sel = null;
        closePopup();
        render();
      }
    };

    /* Keyboard */
    document.onkeydown = function (e) {
      if (e.key === 'Escape') {
        if (document.getElementById('rc-overlay').style.display === 'flex') {
          state.sel = null;
          closePopup();
          render();
        } else if (state.sel) {
          state.sel = null;
          render();
        }
      }
    };
  }

  /* ── Cell click handler ── */
  function handleCellClick (cell) {
    var aptId = parseInt(cell.dataset.apt, 10);
    var ds    = cell.dataset.date;
    var bidx  = cell.dataset.bidx;

    /* Booked cell → show info */
    if (bidx !== undefined && bidx !== '') {
      state.sel = null;
      showPopupInfo(aptId, parseInt(bidx, 10));
      return;
    }

    /* Free cell */
    if (!state.sel) {
      state.sel = { aptId: aptId, from: ds, to: null };
      render();
    } else if (state.sel.aptId !== aptId) {
      state.sel = { aptId: aptId, from: ds, to: null };
      render();
    } else if (!state.sel.to) {
      var from = state.sel.from < ds ? state.sel.from : ds;
      var to   = state.sel.from < ds ? ds : state.sel.from;

      state.sel.from = from;
      state.sel.to   = to;
      render();
      showPopupEdit(aptId, from, to);
    }
  }

  /* ── Hover preview ── */
  function handleCellHover (cell) {
    if (!state.sel || state.sel.to) return;

    var aptId = parseInt(cell.dataset.apt, 10);
    if (aptId !== state.sel.aptId) return;

    var ds   = cell.dataset.date;
    var from = state.sel.from < ds ? state.sel.from : ds;
    var to   = state.sel.from < ds ? ds : state.sel.from;

    clearHover();

    var cells = document.querySelectorAll('.rc-cell[data-apt="' + aptId + '"]');
    for (var i = 0; i < cells.length; i++) {
      var cd = cells[i].dataset.date;
      if (cd >= from && cd <= to) {
        cells[i].classList.add('rc-sel-hover');
      }
    }
  }

  function clearHover () {
    var hovered = document.querySelectorAll('.rc-sel-hover');
    for (var i = 0; i < hovered.length; i++) {
      hovered[i].classList.remove('rc-sel-hover');
    }
  }

  /* ── Init ── */
  render();

})();
