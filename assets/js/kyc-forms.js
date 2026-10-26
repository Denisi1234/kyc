/* KYC Choir — form handling
   Client-side validation + success states for the application, contact
   and newsletter forms. No backend: submissions are acknowledged
   locally so the front-end can be demoed offline. */
(function () {
  'use strict';

  var KYC = window.KYC || (window.KYC = {});

  /* ------------------------------------------------------------ helpers */
  function fieldOf(input) {
    return input.closest('.field') || input.parentElement;
  }

  function markError(input, message) {
    var wrap = fieldOf(input);
    var note = wrap ? wrap.querySelector('.field__error') : null;
    input.setAttribute('aria-invalid', 'true');
    input.classList.add('is-invalid');
    if (!note) {
      note = document.createElement('p');
      note.className = 'field__error';
      if (wrap) wrap.appendChild(note);
    }
    note.textContent = message;
  }

  function clearError(input) {
    var wrap = fieldOf(input);
    var note = wrap ? wrap.querySelector('.field__error') : null;
    input.removeAttribute('aria-invalid');
    input.classList.remove('is-invalid');
    if (note) note.remove();
  }

  function validate(form) {
    var ok = true;
    var firstBad = null;
    var required = Array.prototype.slice.call(form.querySelectorAll('[required]'));

    required.forEach(function (input) {
      var value = (input.value || '').trim();
      var message = '';

      if (input.type === 'checkbox' || input.type === 'radio') {
        if (!input.checked) message = input.getAttribute('data-error') || 'Please complete this field.';
      } else if (!value) {
        message = input.getAttribute('data-error') || 'This field is required.';
      } else if (input.type === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
        message = 'Enter a valid email address.';
      } else if (input.type === 'tel' && !/^[0-9+\-\s()]{7,}$/.test(value)) {
        message = 'Enter a valid phone number.';
      }

      if (message) {
        markError(input, message);
        ok = false;
        if (!firstBad) firstBad = input;
      } else {
        clearError(input);
      }
    });

    if (firstBad) {
      firstBad.focus();
      if (firstBad.scrollIntoView) {
        firstBad.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    }
    return ok;
  }

  function showNote(form, html) {
    var note = form.querySelector('.form-note');
    if (!note) {
      note = document.createElement('div');
      note.className = 'form-note';
      form.appendChild(note);
    }
    note.innerHTML = html;
    note.hidden = false;
    note.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }

  function serialise(form) {
    var data = {};
    Array.prototype.slice.call(form.elements).forEach(function (el) {
      if (!el.name) return;
      if (el.type === 'checkbox' || el.type === 'radio') {
        if (el.checked) data[el.name] = el.value || 'yes';
      } else if (el.type !== 'submit' && el.type !== 'button') {
        data[el.name] = (el.value || '').trim();
      }
    });
    return data;
  }

  /* ------------------------------------------------ spam prevention
     A hidden field only a robot would fill, plus a minimum time spent on
     the form. Bots get the same acknowledgement as a human so they learn
     nothing from the response. */
  function isBot(form) {
    var trap = form.querySelector('.hp input, input.hp');
    if (trap && (trap.value || '').trim() !== '') return true;
    var opened = Number(form.getAttribute('data-opened-at') || 0);
    if (opened && (Date.now() - opened) < 4000) return true;
    return false;
  }

  function stampForms() {
    Array.prototype.slice.call(document.querySelectorAll('form[data-spam="1"]')).forEach(function (form) {
      if (!form.getAttribute('data-opened-at')) {
        form.setAttribute('data-opened-at', String(Date.now()));
      }
    });
  }

  /* --------------------------------------------------- application form */
  function initApplication(form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      if (isBot(form)) {
        form.reset();
        Array.prototype.slice.call(form.querySelectorAll('[required]')).forEach(clearError);
        showNote(form,
          '<strong>Application received.</strong> Thank you. Our secretary will ' +
          'contact you within five working days to arrange your first rehearsal.');
        return;
      }

      if (!validate(form)) return;

      var data = serialise(form);
      var ref = 'KYC-' + new Date().getFullYear() + '-' +
        String(Math.floor(1000 + Math.random() * 9000));
      var name = (data.fullName || 'Member').split(' ')[0];

      form.reset();
      Array.prototype.slice.call(form.querySelectorAll('[required]')).forEach(clearError);

      showNote(form,
        '<strong>Application received.</strong> Thank you, ' + name + '. ' +
        'Your reference is <strong>' + ref + '</strong> &mdash; our secretary will ' +
        'contact you within five working days to arrange your first rehearsal.');
    });

    form.addEventListener('input', function (e) {
      if (e.target.hasAttribute('required')) clearError(e.target);
    });
  }

  /* ------------------------------------------------------ contact form */
  function initContact(form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();

      if (isBot(form)) {
        form.reset();
        Array.prototype.slice.call(form.querySelectorAll('[required]')).forEach(clearError);
        showNote(form,
          '<strong>Message sent.</strong> Thank you. We read every message and ' +
          'usually reply within two working days.');
        return;
      }

      if (!validate(form)) return;
      var data = serialise(form);
      form.reset();
      Array.prototype.slice.call(form.querySelectorAll('[required]')).forEach(clearError);
      showNote(form,
        '<strong>Message sent.</strong> Thank you, ' + (data.name || 'friend') +
        '. We read every message and usually reply within two working days.');
    });
    form.addEventListener('input', function (e) {
      if (e.target.hasAttribute('required')) clearError(e.target);
    });
  }

  /* --------------------------------------------------------- newsletter */
  function initNewsletter(form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var input = form.querySelector('input[type="email"]');
      var value = (input && input.value ? input.value : '').trim();
      var note = form.parentElement ? form.parentElement.querySelector('.footer__note') : null;

      if (!value || !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value)) {
        if (input) { markError(input, 'Enter a valid email address.'); input.focus(); }
        return;
      }
      clearError(input);
      form.reset();
      if (note) {
        note.textContent = 'You are on the list. See you at rehearsal.';
        note.hidden = false;
      }
    });
  }

  /* -------------------------------------------------------- portal login */
  function initPortal(form) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (!validate(form)) return;
      var note = form.querySelector('.form-note');
      if (note) {
        note.innerHTML = '<strong>Demo mode.</strong> Member portal sign-in is ' +
          'reserved for registered members. Contact the choir office for access.';
        note.hidden = false;
      }
    });
    form.addEventListener('input', function (e) {
      if (e.target.hasAttribute('required')) clearError(e.target);
    });
  }

  /* -------------------------------------------------------------- init */
  function initForms() {
    Array.prototype.slice.call(document.querySelectorAll('[data-form="application"]')).forEach(initApplication);
    Array.prototype.slice.call(document.querySelectorAll('[data-form="contact"]')).forEach(initContact);
    Array.prototype.slice.call(document.querySelectorAll('[data-form="newsletter"]')).forEach(initNewsletter);
    Array.prototype.slice.call(document.querySelectorAll('[data-form="portal"]')).forEach(initPortal);

    /* A link inside a <label> toggles the checkbox as well as navigating.
       Navigate deliberately instead, and leave the checkbox alone. */
    document.addEventListener('click', function (e) {
      var link = e.target.closest('.check label a[href]');
      if (!link) return;
      e.preventDefault();
      window.location.href = link.href;
    });

    stampForms();
  }

  KYC.forms = { init: initForms };
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initForms);
  } else {
    initForms();
  }
})();
