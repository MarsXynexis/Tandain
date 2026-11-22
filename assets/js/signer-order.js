function createSignerRow(person, fieldName) {
    const row = document.getElementById('signer-row-template').content.firstElementChild.cloneNode(true);
    const order = document.getElementById('signer-order-control').content.firstElementChild.cloneNode(true);
    const input = row.querySelector('[data-signer-id]');

    const pos = typeof person.position === 'string' ? person.position.trim() : '';
    const prodi = typeof person.study_program === 'string' ? person.study_program.trim() : '';
    let affiliation = person.role_display_name || '';

    if (pos !== '' && prodi !== '') {
        affiliation = pos.toLowerCase().includes(prodi.toLowerCase()) ? pos : pos + ' • ' + prodi;
    } else if (pos !== '') {
        affiliation = pos;
    } else if (prodi !== '') {
        affiliation = prodi;
    }

    row.querySelector('[data-signer-name]').textContent = person.name;
    row.querySelector('[data-signer-detail]').textContent = [affiliation, person.email, person.identity ? 'NIP/NIM ' + person.identity : ''].filter(Boolean).join(' • ');
    order.querySelector('[data-signer-grip]').setAttribute('aria-label', 'Ubah urutan ' + person.name);
    row.querySelector('[data-signer-info]').prepend(order);

    input.name = fieldName;
    input.value = person.id;

    return row;
}

function updateSignerList(list, isSequential) {
    const rows = Array.from(list.children);
    const isSearchOpen = !document.getElementById('signer-search-row').hidden;
    const canReorder = isSequential && rows.length > 1;

    document.getElementById('signer-order-hint').hidden = !canReorder;
    document.getElementById('search-order-spacer').hidden = !canReorder;
    document.getElementById('search-row-number').textContent = rows.length + 1;
    document.getElementById('signer-empty').hidden = rows.length > 0 || isSearchOpen;

    rows.forEach((row, index) => {
        row.querySelector('[data-number]').textContent = index + 1;

        const showConnector = isSequential && (index < rows.length - 1 || isSearchOpen);
        const connector = row.querySelector('[data-connector]');
        connector.classList.toggle('flex', showConnector);
        connector.classList.toggle('hidden', !showConnector);
        row.querySelector('[data-order-spacer]').hidden = !canReorder;

        const order = row.querySelector('[data-signer-order]');
        order.hidden = !canReorder;
        order.querySelector('[data-signer-step="up"]').disabled = index === 0;
        order.querySelector('[data-signer-step="down"]').disabled = index === rows.length - 1;

        if (!canReorder) {
            order.open = false;
        }
    });
}

function setupSignerOrder(list, onReorder) {
    let drag;
    let ignoredClick;
    let scrollFrame;

    function moveSigner(row, direction) {
        const sibling = direction === 'up' ? row.previousElementSibling : row.nextElementSibling;

        if (sibling) {
            list.insertBefore(row, direction === 'up' ? sibling : sibling.nextElementSibling);
            onReorder(row, true);
        }

        const order = row.querySelector('[data-signer-order]');
        order.open = false;
        order.querySelector('[data-signer-grip]').focus();
    }

    function updateDragOrder() {
        const before = Array.from(list.children).find((row) => {
            const bounds = row.getBoundingClientRect();

            return row !== drag.row && drag.y < bounds.top + bounds.height / 2;
        }) ?? null;

        if (before !== drag.row.nextElementSibling) {
            list.insertBefore(drag.row, before);
            onReorder(drag.row, false);
        }
    }

    function scrollWhileDragging() {
        const edge = 88;
        const distance = drag.y < edge ? drag.y - edge : Math.max(0, drag.y - (window.innerHeight - edge));

        if (distance) {
            const previousY = window.scrollY;
            window.scrollBy(0, Math.max(-14, Math.min(14, distance)));

            if (window.scrollY !== previousY) {
                updateDragOrder();
            }
        }

        scrollFrame = requestAnimationFrame(scrollWhileDragging);
    }

    function finishDrag(pointerId, cancelled) {
        if (drag?.pointerId === pointerId) {
            if (drag.started) {
                const moved = drag.row.nextElementSibling !== drag.originalNext;
                cancelAnimationFrame(scrollFrame);
                drag.row.classList.remove('opacity-60');

                if (moved) {
                    if (cancelled) {
                        const next = drag.originalNext?.parentElement === list ? drag.originalNext : null;
                        list.insertBefore(drag.row, next);
                    }

                    onReorder(drag.row, !cancelled);
                }

                drag.grip.focus({ preventScroll: true });
            }

            drag = null;
        }
    }

    list.addEventListener('pointerdown', (event) => {
        const grip = event.target.closest('[data-signer-grip]');

        if (grip && event.isPrimary && event.button === 0) {
            const row = grip.closest('[data-signer-row]');
            ignoredClick = null;
            drag = { grip, row, originalNext: row.nextElementSibling, pointerId: event.pointerId, startY: event.clientY, y: event.clientY, started: false };
        }
    });

    document.addEventListener('pointermove', (event) => {
        if (drag?.pointerId === event.pointerId) {
            drag.y = event.clientY;

            if (!drag.started && Math.abs(drag.y - drag.startY) >= 6) {
                drag.started = true;
                ignoredClick = drag.grip;
                drag.grip.closest('details').open = false;
                drag.row.classList.add('opacity-60');
                list.setPointerCapture(event.pointerId);
                scrollFrame = requestAnimationFrame(scrollWhileDragging);
            }

            if (drag.started) {
                updateDragOrder();
            }
        }
    });

    document.addEventListener('pointerup', (event) => finishDrag(event.pointerId, false));
    document.addEventListener('pointercancel', (event) => finishDrag(event.pointerId, true));
    list.addEventListener('lostpointercapture', (event) => {
        if (event.target === list) {
            finishDrag(event.pointerId, true);
        }
    });

    list.addEventListener('click', (event) => {
        const grip = event.target.closest('[data-signer-grip]');
        const step = event.target.closest('[data-signer-step]');

        if (grip && grip === ignoredClick && event.detail > 0) {
            event.preventDefault();
        } else if (step && !step.disabled) {
            moveSigner(step.closest('[data-signer-row]'), step.dataset.signerStep);
        }
    });

    list.addEventListener('keydown', (event) => {
        const grip = event.target.closest('[data-signer-grip]');
        const order = event.target.closest('[data-signer-order]');

        if (grip && (event.key === 'ArrowUp' || event.key === 'ArrowDown')) {
            event.preventDefault();
            moveSigner(grip.closest('[data-signer-row]'), event.key === 'ArrowUp' ? 'up' : 'down');
        } else if (event.key === 'Escape' && order?.open) {
            event.preventDefault();
            order.open = false;
            order.querySelector('[data-signer-grip]').focus();
        }
    });

    document.addEventListener('click', (event) => {
        const openOrder = list.querySelector('[data-signer-order][open]');

        if (openOrder && !openOrder.contains(event.target)) {
            openOrder.open = false;
        }
    });
}
