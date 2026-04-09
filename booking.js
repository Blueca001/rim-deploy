/* ========================================
   BOOKING MODAL — Availability Check System
   ======================================== */
document.addEventListener('DOMContentLoaded', function() {
  var overlay = document.getElementById('bookingOverlay');
  var closeBtn = document.getElementById('closeBooking');
  if (!overlay) return;

  var step1 = document.getElementById('bookingStep1');
  var step2 = document.getElementById('bookingStep2');
  var step3 = document.getElementById('bookingStep3');
  var step4 = document.getElementById('bookingStep4');
  var allSteps = [step1, step2, step3, step4];
  var indicators = overlay.querySelectorAll('.booking-step-indicator');
  var selectedApt = null;

  function showStep(n) {
    allSteps.forEach(function(s, i) { if (s) s.style.display = (i === n - 1) ? '' : 'none'; });
    indicators.forEach(function(ind, i) {
      ind.classList.toggle('active', i < n);
      ind.classList.toggle('current', i === n - 1);
    });
  }

  function openModal() {
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
    showStep(1);
    selectedApt = null;
  }

  function closeModal() {
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  }

  function formatDateIT(dateStr) {
    var d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('it-IT', { day: '2-digit', month: 'short', year: 'numeric' });
  }

  // Open triggers
  document.querySelectorAll('.js-open-booking, #openBooking').forEach(function(btn) {
    btn.addEventListener('click', function(e) { e.preventDefault(); openModal(); });
  });

  // Close triggers
  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  overlay.addEventListener('click', function(e) { if (e.target === overlay) closeModal(); });
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape' && overlay.classList.contains('open')) closeModal();
  });

  // Date setup
  var today = new Date().toISOString().split('T')[0];
  var ciInput = document.getElementById('bk-checkin');
  var coInput = document.getElementById('bk-checkout');
  if (ciInput) ciInput.min = today;
  if (coInput) coInput.min = today;

  if (ciInput && coInput) {
    ciInput.addEventListener('change', function() {
      var ci = new Date(ciInput.value);
      ci.setDate(ci.getDate() + 1);
      var minCo = ci.toISOString().split('T')[0];
      coInput.min = minCo;
      if (!coInput.value || coInput.value <= ciInput.value) coInput.value = minCo;
    });
  }

  // STEP 1: Search availability
  var searchBtn = document.getElementById('bk-search-btn');
  if (searchBtn) {
    searchBtn.addEventListener('click', function() {
      var checkin = ciInput.value;
      var checkout = coInput.value;
      if (!checkin || !checkout) { alert('Seleziona le date di check-in e check-out.'); return; }
      if (checkin >= checkout) { alert('Il check-out deve essere successivo al check-in.'); return; }

      searchBtn.disabled = true;
      searchBtn.innerHTML = '<span class="bk-spinner"></span> Ricerca in corso...';

      fetch(rimAjax.ajaxUrl + '?action=rim_check_availability&checkin=' + checkin + '&checkout=' + checkout)
        .then(function(r) { return r.json(); })
        .then(function(data) {
          searchBtn.disabled = false;
          searchBtn.innerHTML = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="20" height="20" style="vertical-align:middle;margin-right:8px;"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg> Cerca Disponibilit\u00e0';
          if (data.success) {
            renderResults(data.data.apartments, checkin, checkout);
            showStep(2);
          } else {
            alert(data.data || 'Errore nella ricerca.');
          }
        })
        .catch(function() {
          searchBtn.disabled = false;
          searchBtn.textContent = 'Cerca Disponibilit\u00e0';
          alert('Errore di connessione. Riprova.');
        });
    });
  }

  // STEP 2: Render results
  function renderResults(apartments, checkin, checkout) {
    var container = document.getElementById('bk-results');
    if (!container) return;

    var adults = document.getElementById('bk-adults').value;
    var children = document.getElementById('bk-children').value;
    var ci = new Date(checkin + 'T00:00:00');
    var co = new Date(checkout + 'T00:00:00');
    var nights = Math.round((co - ci) / 86400000);

    var summary = document.getElementById('bk-search-summary');
    if (summary) {
      summary.textContent = formatDateIT(checkin) + ' \u2192 ' + formatDateIT(checkout) +
        ' (' + nights + ' notti, ' + adults + ' adulti' +
        (parseInt(children) > 0 ? ', ' + children + ' bambini' : '') + ')';
    }

    var available = apartments.filter(function(a) { return a.available; });
    var unavailable = apartments.filter(function(a) { return !a.available; });
    var html = '';

    if (available.length === 0) {
      html = '<div class="bk-no-results">' +
        '<svg viewBox="0 0 24 24" fill="none" stroke="#b71c1c" stroke-width="2" width="48" height="48"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>' +
        '<h4>Nessun appartamento disponibile</h4>' +
        '<p>Prova con date diverse o contattaci telefonicamente.</p>' +
        '</div>';
    } else {
      html += '<div class="bk-available-count">' + available.length + ' appartament' + (available.length === 1 ? 'o' : 'i') + ' disponibil' + (available.length === 1 ? 'e' : 'i') + '</div>';
      available.forEach(function(apt) {
        var priceHtml = '';
        if (apt.total_price && apt.total_price > 0) {
          var avgNight = Math.round(apt.total_price / nights);
          priceHtml = '<div class="bk-apt-card__price">' +
            '<span class="bk-apt-card__total">' + apt.total_price + ' &euro;</span>' +
            '<span class="bk-apt-card__avg">' + avgNight + ' &euro;/notte &middot; ' + nights + ' notti</span>' +
            '</div>';
        }
        html += '<div class="bk-apt-card bk-apt-card--available">' +
          (apt.thumb ? '<img src="' + apt.thumb + '" class="bk-apt-card__img" alt="' + apt.name + '">' : '<div class="bk-apt-card__img bk-apt-card__img--placeholder"></div>') +
          '<div class="bk-apt-card__info">' +
          '<h4 class="bk-apt-card__name">' + apt.name + '</h4>' +
          '<p class="bk-apt-card__desc">' + (apt.short_desc || (apt.sqm + ' mq \u00b7 ' + apt.guests + ' ospiti \u00b7 ' + apt.rooms + ' camere')) + '</p>' +
          priceHtml +
          '</div>' +
          '<button type="button" class="bk-btn bk-btn--primary bk-select-apt" data-id="' + apt.id + '" data-name="' + apt.name + '" data-total="' + (apt.total_price || 0) + '">Seleziona</button>' +
          '</div>';
      });

      if (unavailable.length > 0) {
        html += '<div class="bk-unavailable-divider">Non disponibili per queste date</div>';
        unavailable.forEach(function(apt) {
          html += '<div class="bk-apt-card bk-apt-card--unavailable">' +
            '<div class="bk-apt-card__info">' +
            '<h4 class="bk-apt-card__name">' + apt.name + '</h4>' +
            '<p class="bk-apt-card__desc">' + (apt.short_desc || '') + '</p>' +
            '</div>' +
            '<span class="bk-badge--unavailable">Occupato</span>' +
            '</div>';
        });
      }
    }

    container.innerHTML = html;

    // Select buttons
    container.querySelectorAll('.bk-select-apt').forEach(function(btn) {
      btn.addEventListener('click', function() {
        selectedApt = { id: btn.dataset.id, name: btn.dataset.name, total: parseFloat(btn.dataset.total) || 0 };
        prepareStep3(checkin, checkout, nights, adults, children);
        showStep(3);
      });
    });
  }

  // Back buttons
  var backToDates = document.getElementById('bk-back-to-dates');
  if (backToDates) backToDates.addEventListener('click', function() { showStep(1); });

  var backToResults = document.getElementById('bk-back-to-results');
  if (backToResults) backToResults.addEventListener('click', function() { showStep(2); });

  // STEP 3: Prepare contact form
  function prepareStep3(checkin, checkout, nights, adults, children) {
    var el = document.getElementById('bk-selected-summary');
    if (el && selectedApt) {
      var priceStr = selectedApt.total > 0 ? ' &mdash; <strong>' + selectedApt.total + ' &euro;</strong>' : '';
      el.innerHTML = '<strong>' + selectedApt.name + '</strong> &mdash; ' +
        formatDateIT(checkin) + ' \u2192 ' + formatDateIT(checkout) + ' (' + nights + ' notti)' + priceStr;
    }
    document.getElementById('bk-h-apartment').value = selectedApt.name;
    document.getElementById('bk-h-apartment-id').value = selectedApt.id;
    document.getElementById('bk-h-checkin').value = checkin;
    document.getElementById('bk-h-checkout').value = checkout;
    document.getElementById('bk-h-adults').value = adults;
    document.getElementById('bk-h-children').value = children;
  }

  // STEP 3: Submit booking
  var bookingForm = document.getElementById('bk-contact-form');
  if (bookingForm) {
    bookingForm.addEventListener('submit', function(e) {
      e.preventDefault();
      var submitBtn = bookingForm.querySelector('button[type="submit"]');
      submitBtn.disabled = true;
      submitBtn.innerHTML = '<span class="bk-spinner"></span> Invio in corso...';

      var formData = new FormData(bookingForm);
      formData.append('action', 'rim_send_booking');
      formData.append('nonce', rimAjax.nonce);

      fetch(rimAjax.ajaxUrl, { method: 'POST', body: formData })
        .then(function(r) { return r.json(); })
        .then(function(data) {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Invia Richiesta';
          if (data.success) {
            showStep(4);
            bookingForm.reset();
          } else {
            alert(data.data || 'Errore nell\'invio. Riprova.');
          }
        })
        .catch(function() {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Invia Richiesta';
          alert('Errore di connessione.');
        });
    });
  }

  // STEP 4: Actions
  var newSearchBtn = document.getElementById('bk-new-search');
  if (newSearchBtn) newSearchBtn.addEventListener('click', function() { showStep(1); });

  var closeFinalBtn = document.getElementById('bk-close-final');
  if (closeFinalBtn) closeFinalBtn.addEventListener('click', closeModal);
});
