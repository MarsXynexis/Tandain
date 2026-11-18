function setupSignerPicker(addSignerBtn, { signerIds, onSelect, onVisibilityChange }) {
    const searchRow = document.getElementById('signer-search-row');
    const searchInput = document.getElementById('signer-search-input');
    const searchClose = document.getElementById('signer-search-close');
    const searchDropdown = document.getElementById('signer-dropdown');
    const searchMessage = document.getElementById('signer-search-message');
    const searchResults = document.getElementById('signer-results');
    const signerAnnouncement = document.getElementById('signer-announcement');

    let searchTimer;
    let searchRequest;

    function openSearch() {
      if (searchRow.hidden) {
        searchRow.hidden = false;
        searchInput.value = '';
        onVisibilityChange();
        searchSigners();
      }

      searchInput.focus();
    }

    function closeSearch(restoreFocus = false) {
      if (!searchRow.hidden) {
        clearTimeout(searchTimer);
        searchRequest?.abort();
        searchRow.hidden = true;
        searchDropdown.hidden = true;
        searchResults.replaceChildren();
        signerAnnouncement.textContent = '';
        onVisibilityChange();

        if (restoreFocus) {
          addSignerBtn.focus();
        }
      }
    }

    function showResultMessage(text) {
      searchMessage.textContent = text;
      searchMessage.hidden = !text;
      signerAnnouncement.textContent = text;
    }

    function createSignerResult(person) {
      const item = document.createElement('li');
      const hasCertificate = Number(person.has_active_cert) === 1;
      const option = document.createElement('button');
      option.type = 'button';
      option.disabled = !hasCertificate;
      option.className = 'flex w-full flex-wrap items-center justify-between gap-x-3 gap-y-1 px-3 py-2.5 text-left enabled:hover:bg-brand-soft focus-visible:bg-brand-soft focus-visible:outline focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-brand disabled:bg-surface-container-low';

      const info = document.createElement('span');
      info.className = 'min-w-0';

      const name = document.createElement('span');
      name.className = 'block break-words text-sm font-semibold text-primary';
      name.textContent = person.full_name;

      const detail = document.createElement('span');
      detail.className = 'mt-0.5 block break-words text-xs text-on-surface-variant';
      detail.textContent = [person.email, person.identity_number ? 'NIP/NIM ' + person.identity_number : ''].filter(Boolean).join(' • ');

      info.append(name, detail);
      const badge = document.getElementById(hasCertificate ? 'active-signer-certificate' : 'inactive-signer-certificate');
      option.append(info, badge.content.cloneNode(true));
      item.append(option);

      option.addEventListener('click', () => {
        onSelect(person);
        closeSearch(true);
        signerAnnouncement.textContent = person.full_name + ' ditambahkan.';
      });

      return item;
    }

    async function runSearch(query) {
      const request = new AbortController();
      searchRequest = request;

      try {
        const response = await fetch('/Tandain/triage/searchSigners?q=' + encodeURIComponent(query), {
          headers: { Accept: 'application/json' },
          signal: request.signal
        });

        if (!response.ok) {
          throw new Error('Pencarian gagal.');
        }

        const people = await response.json();

        if (!request.signal.aborted) {
          const taken = signerIds();
          const results = people.filter((person) => !taken.includes(String(person.id)));
          searchResults.replaceChildren(...results.map(createSignerResult));

          if (!results.length) {
            showResultMessage('Tidak ada penandatangan yang cocok.');
          } else {
            showResultMessage('');
            signerAnnouncement.textContent = results.length + ' hasil pencarian.';
          }
        }
      } catch (error) {
        if (!request.signal.aborted) {
          searchResults.replaceChildren();
          showResultMessage('Pencarian gagal. Coba lagi.');
        }
      }
    }

    function searchSigners(delay = 0) {
      clearTimeout(searchTimer);
      searchRequest?.abort();
      searchResults.replaceChildren();
      searchDropdown.hidden = false;

      const query = searchInput.value.trim();

      if (query.length < 2) {
        showResultMessage('Ketik minimal 2 karakter.');
      } else {
        showResultMessage('Mencari penandatangan…');
        searchTimer = setTimeout(() => runSearch(query), delay);
      }
    }

    addSignerBtn.addEventListener('click', openSearch);
    searchClose.addEventListener('click', () => closeSearch(true));
    searchInput.addEventListener('input', () => searchSigners(300));

    searchRow.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        event.preventDefault();
        closeSearch(true);
      } else {
        const options = Array.from(searchResults.querySelectorAll('button:enabled'));
        const index = options.indexOf(event.target);

        if (event.key === 'Enter' && event.target === searchInput) {
          event.preventDefault();

          if (options.length === 1) {
            options[0].click();
          } else if (options.length > 1) {
            options[0].focus();
          } else {
            searchSigners();
          }
        } else if (event.key === 'ArrowDown' && (event.target === searchInput || index >= 0)) {
          event.preventDefault();
          options[index + 1]?.focus();
        } else if (event.key === 'ArrowUp' && index >= 0) {
          event.preventDefault();
          (index > 0 ? options[index - 1] : searchInput).focus();
        }
      }
    });

    searchRow.addEventListener('focusout', (event) => {
      if (event.relatedTarget && !searchRow.contains(event.relatedTarget)) {
        closeSearch();
      }
    });

    document.addEventListener('click', (event) => {
      if (!searchRow.contains(event.target) && !addSignerBtn.contains(event.target)) {
        closeSearch();
      }
    });

    return closeSearch;
}
