document.addEventListener('DOMContentLoaded', () => {
    const authContainer = document.getElementById('auth-container');
    const appWorkspace = document.getElementById('app-workspace');
    
    // Auth Forms
    const loginForm = document.getElementById('login-form');
    const registerForm = document.getElementById('register-form');
    const forgotForm = document.getElementById('forgot-form');

    // Navigation Switch Links
    const showRegister = document.getElementById('show-register');
    const showForgot = document.getElementById('show-forgot');
    const showLoginFromReg = document.getElementById('show-login-from-reg');
    const showLoginFromForgot = document.getElementById('show-login-from-forgot');

    // Error & Success Elements
    const loginError = document.getElementById('login-error');
    const registerError = document.getElementById('register-error');
    const forgotError = document.getElementById('forgot-error');
    const forgotSuccess = document.getElementById('forgot-success');

    const logoutBtn = document.getElementById('logout-btn');
    const editorPanel = document.getElementById('editor-panel');
    const editorBackdrop = document.getElementById('editor-backdrop');
    const mobileEditorToggle = document.getElementById('mobile-editor-toggle');
    const mobileEditorClose = document.getElementById('mobile-editor-close');

    function toggleMobileEditor(forceState) {
        if (!editorPanel) return;

        if (window.innerWidth > 768) {
            editorPanel.classList.remove('is-open');
            editorBackdrop?.classList.remove('is-open');
            document.body.classList.remove('editor-panel-open');
            if (mobileEditorToggle) mobileEditorToggle.setAttribute('aria-expanded', 'false');
            return;
        }

        const shouldOpen = typeof forceState === 'boolean' ? forceState : !editorPanel.classList.contains('is-open');
        editorPanel.classList.toggle('is-open', shouldOpen);
        editorBackdrop?.classList.toggle('is-open', shouldOpen);
        document.body.classList.toggle('editor-panel-open', shouldOpen);

        if (mobileEditorToggle) {
            mobileEditorToggle.setAttribute('aria-expanded', String(shouldOpen));
        }
    }

    if (mobileEditorToggle) {
        mobileEditorToggle.addEventListener('click', () => toggleMobileEditor());
    }

    if (mobileEditorClose) {
        mobileEditorClose.addEventListener('click', () => toggleMobileEditor(false));
    }

    if (editorBackdrop) {
        editorBackdrop.addEventListener('click', () => toggleMobileEditor(false));
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && window.innerWidth <= 768 && editorPanel && editorPanel.classList.contains('is-open')) {
            toggleMobileEditor(false);
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            toggleMobileEditor(false);
        } else {
            editorPanel?.classList.remove('is-open');
            editorBackdrop?.classList.remove('is-open');
            document.body.classList.remove('editor-panel-open');
            if (mobileEditorToggle) mobileEditorToggle.setAttribute('aria-expanded', 'false');
        }
    });

    // On page load, ensure auth container is shown and workspace hidden
    if (authContainer) authContainer.style.display = 'flex';
    if (appWorkspace) appWorkspace.style.display = 'none';

    // Helper to switch active auth view and reset alerts
    function switchAuthView(viewName) {
        [loginForm, registerForm, forgotForm].forEach(form => {
            if (form) {
                form.style.display = 'none';
                form.reset();
            }
        });

        // Clear all feedback boxes
        [loginError, registerError, forgotError, forgotSuccess].forEach(el => {
            if (el) {
                el.style.display = 'none';
                el.textContent = '';
                if (el === forgotSuccess) {
                    el.className = 'auth-success';
                } else {
                    el.className = 'auth-error';
                }
            }
        });

        if (viewName === 'login' && loginForm) loginForm.style.display = 'block';
        if (viewName === 'register' && registerForm) registerForm.style.display = 'block';
        if (viewName === 'forgot' && forgotForm) forgotForm.style.display = 'block';
    }

    function setupPasswordToggles() {
        document.querySelectorAll('.password-toggle').forEach((button) => {
            const targetId = button.dataset.target;
            const input = targetId ? document.getElementById(targetId) : button.parentElement.querySelector('input');
            if (!input) return;

            button.addEventListener('click', () => {
                const shouldShow = input.type === 'password';
                input.type = shouldShow ? 'text' : 'password';
                button.setAttribute('aria-pressed', String(shouldShow));
                button.setAttribute('aria-label', shouldShow ? 'Hide password' : 'Show password');

                const icon = button.querySelector('i');
                if (icon) {
                    icon.classList.toggle('fa-eye', !shouldShow);
                    icon.classList.toggle('fa-eye-slash', shouldShow);
                }
            });
        });
    }

    setupPasswordToggles();

    // Event Listeners for switching views
    if (showRegister) {
        showRegister.addEventListener('click', (e) => {
            e.preventDefault();
            switchAuthView('register');
        });
    }

    if (showForgot) {
        showForgot.addEventListener('click', (e) => {
            e.preventDefault();
            switchAuthView('forgot');
        });
    }

    if (showLoginFromReg) {
        showLoginFromReg.addEventListener('click', (e) => {
            e.preventDefault();
            switchAuthView('login');
        });
    }

    if (showLoginFromForgot) {
        showLoginFromForgot.addEventListener('click', (e) => {
            e.preventDefault();
            switchAuthView('login');
        });
    }

    // Login Form Submission (Connected to login.php)
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('login-email').value.trim();
            const password = document.getElementById('login-password').value;

            loginError.style.display = 'none';
            loginError.textContent = '';
            loginError.className = 'auth-error';

            try {
                const response = await fetch('login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (data.status === 'success') {
                    if (authContainer) authContainer.style.display = 'none';
                    if (appWorkspace) appWorkspace.style.display = 'flex';
                } else {
                    loginError.className = 'auth-error';
                    loginError.textContent = data.message === 'Invalid email or password.'
                        ? 'You must have an account to login.'
                        : data.message;
                    loginError.style.display = 'block';
                }
            } catch (err) {
                loginError.textContent = 'Unable to connect to the server. Please check your connection.';
                loginError.style.display = 'block';
            }
        });
    }

    function showAuthMessage(element, message, isError = true) {
        if (!element) return;
        element.textContent = message;
        element.className = isError ? 'auth-error' : 'auth-success';
        element.style.display = 'block';
    }

    // Register Form Submission (Stub for backend integration)
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('reg-name').value.trim();
            const email = document.getElementById('reg-email').value.trim();
            const password = document.getElementById('reg-password').value;

            registerError.style.display = 'none';
            registerError.textContent = '';
            registerError.className = 'auth-error';

            const namePattern = /^[A-Za-z\s'-]+$/;
            if (!name || !namePattern.test(name)) {
                registerError.textContent = 'Name can only contain letters and spaces.';
                registerError.style.display = 'block';
                return;
            }

            const passwordRule = 'Password must be at least 8 characters and include an uppercase letter, a number, and a special character.';
            const strongPasswordPattern = /^(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

            if (!strongPasswordPattern.test(password)) {
                registerError.textContent = passwordRule;
                registerError.style.display = 'block';
                return;
            }

            try {
                const response = await fetch('register.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ name, email, password })
                });
                const data = await response.json();
                if (data.status === 'success') {
                    switchAuthView('login');
                    showAuthMessage(loginError, 'Registration successful. Please sign in.', false);
                } else {
                    registerError.className = 'auth-error';
                    registerError.textContent = data.message;
                    registerError.style.display = 'block';
                }
            } catch (err) {
                registerError.textContent = 'Registration service endpoint not found yet.';
                registerError.style.display = 'block';
            }
        });
    }

    // Forgot Password Form Submission
    if (forgotForm) {
        forgotForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('forgot-email').value.trim();
            forgotError.style.display = 'none';
            forgotSuccess.style.display = 'none';

            try {
                const response = await fetch('forgot-password.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email })
                });
                const data = await response.json();
                if (data.status === 'success') {
                    forgotSuccess.textContent = data.message || 'Password reset link sent to your email.';
                    forgotSuccess.style.display = 'block';
                } else {
                    forgotError.textContent = data.message;
                    forgotError.style.display = 'block';
                }
            } catch (err) {
                forgotSuccess.textContent = 'If an account exists with this email, reset instructions have been sent.';
                forgotSuccess.style.display = 'block';
            }
        });
    }

    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            if (appWorkspace) appWorkspace.style.display = 'none';
            if (authContainer) authContainer.style.display = 'flex';
            switchAuthView('login');
        });
    }

    // --- Invoice State Management & Live Preview ---
    function generateInvoiceNumber() {
        const now = new Date();
        const year = now.getFullYear();
        const stamp = String(Date.now()).slice(-6);
        return `INV-${year}-${stamp}`;
    }

    let invoiceData = {
        meta: { status: 'Draft', template: 'template-modern' },
        business: {
            name: 'Northstar Studio',
            owner: 'Alex Morgan',
            email: 'hello@northstar.studio',
            phone: '+256 700 000000',
            address: 'Kakoba, Mbarara, Uganda',
            logo: ''
        },
        client: {
            name: 'Jamie Rivera',
            company: 'Aperture Labs',
            email: 'jamie@aperturelabs.co',
            phone: '+256 700 111111',
            address: 'Mbarara City, Uganda'
        },
        invoice: {
            number: 'INV-2026-001',
            date: new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
            dueDate: new Date(Date.now() + 30*24*60*60*1000).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
            items: [
                { description: 'Brand identity & visual', qty: 1, price: 1200 },
                { description: 'UI/UX consultation & wireframes', qty: 3, price: 150 }
            ],
            subtotal: 0,
            total: 0
        },
        stamp: ''
    };

    // DOM Elements for Editor
    const busNameInput = document.getElementById('bus-name');
    const busOwnerInput = document.getElementById('bus-owner');
    const busEmailInput = document.getElementById('bus-email');
    const busPhoneInput = document.getElementById('bus-phone');
    const busAddressInput = document.getElementById('bus-address');
    const logoInput = document.getElementById('logo-input');

    const clientNameInput = document.getElementById('client-name');
    const clientCompanyInput = document.getElementById('client-company');
    const clientEmailInput = document.getElementById('client-email');
    const clientPhoneInput = document.getElementById('client-phone');
    const clientAddressInput = document.getElementById('client-address');

    const invNumberInput = document.getElementById('inv-number');
    const invoiceStatusSelect = document.getElementById('invoice-status');
    const templateSelector = document.getElementById('template-selector');
    const stampInput = document.getElementById('stamp-input');

    const invoicePreview = document.getElementById('invoice-preview');
    const prevLogo = document.getElementById('prev-logo');
    const prevStatusBadge = document.getElementById('prev-status-badge');
    const prevDocTitle = document.getElementById('prev-doc-title');
    const prevReceiptNote = document.getElementById('prev-receipt-note');
    const prevBusName = document.getElementById('prev-bus-name');
    const prevBusOwner = document.getElementById('prev-bus-owner');
    const prevBusEmail = document.getElementById('prev-bus-email');
    const prevBusPhone = document.getElementById('prev-bus-phone');
    const prevBusAddress = document.getElementById('prev-bus-address');
    const prevInvNumber = document.getElementById('prev-inv-number');
    const prevClientName = document.getElementById('prev-client-name');
    const prevClientCompany = document.getElementById('prev-client-company');
    const prevClientEmail = document.getElementById('prev-client-email');
    const prevClientPhone = document.getElementById('prev-client-phone');
    const prevClientAddress = document.getElementById('prev-client-address');
    const prevDate = document.getElementById('prev-date');
    const prevDueDate = document.getElementById('prev-due-date');
    const prevItemsList = document.getElementById('prev-items-list');
    const prevSubtotal = document.getElementById('prev-subtotal');
    const prevTotal = document.getElementById('prev-total');
    const prevStamp = document.getElementById('prev-stamp');
    const itemsContainer = document.getElementById('items-container');

    const addItemBtn = document.getElementById('add-item-btn');
    const saveInvoiceBtn = document.getElementById('save-btn');
    const viewHistoryBtn = document.getElementById('view-history-btn');
    const printBtn = document.getElementById('print-btn');
    const historyOverlay = document.getElementById('history-overlay');
    const historyClose = document.getElementById('history-close');

    function initFormValues() {
        if (busNameInput) busNameInput.value = invoiceData.business.name;
        if (busOwnerInput) busOwnerInput.value = invoiceData.business.owner;
        if (busEmailInput) busEmailInput.value = invoiceData.business.email;
        if (busPhoneInput) busPhoneInput.value = invoiceData.business.phone;
        if (busAddressInput) busAddressInput.value = invoiceData.business.address;

        if (clientNameInput) clientNameInput.value = invoiceData.client.name;
        if (clientCompanyInput) clientCompanyInput.value = invoiceData.client.company;
        if (clientEmailInput) clientEmailInput.value = invoiceData.client.email;
        if (clientPhoneInput) clientPhoneInput.value = invoiceData.client.phone;
        if (clientAddressInput) clientAddressInput.value = invoiceData.client.address;

        invoiceData.invoice.number = invoiceData.invoice.number || generateInvoiceNumber();
        if (invNumberInput) invNumberInput.value = invoiceData.invoice.number;
        if (invoiceStatusSelect) invoiceStatusSelect.value = invoiceData.meta.status;
        if (templateSelector) templateSelector.value = invoiceData.meta.template;
    }

    function renderLineItemsEditor() {
        if (!itemsContainer) return;
        itemsContainer.innerHTML = '';
        invoiceData.invoice.items.forEach((item, index) => {
            const row = document.createElement('div');
            row.className = 'item-row';
            row.innerHTML = `
                <input type="text" placeholder="Description" value="${item.description}" data-index="${index}" data-field="description" style="flex: 2.8;">
                <input type="number" placeholder="Qty" value="${item.qty}" data-index="${index}" data-field="qty" style="flex: 0.9;">
                <input type="number" placeholder="Rate" value="${item.price}" data-index="${index}" data-field="price" style="flex: 1.1;">
                <button type="button" class="delete-btn" data-index="${index}" title="Remove item">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                </button>
            `;
            itemsContainer.appendChild(row);
        });
    }

    function calculateTotals() {
        let subtotal = 0;
        invoiceData.invoice.items.forEach(item => {
            subtotal += (parseFloat(item.qty) || 0) * (parseFloat(item.price) || 0);
        });
        invoiceData.invoice.subtotal = subtotal;
        invoiceData.invoice.total = subtotal;
    }

    function renderPreview() {
        calculateTotals();

        if (prevBusName) prevBusName.textContent = invoiceData.business.name;
        if (prevBusOwner) prevBusOwner.textContent = invoiceData.business.owner;
        if (prevBusEmail) prevBusEmail.textContent = invoiceData.business.email;
        if (prevBusPhone) prevBusPhone.textContent = invoiceData.business.phone;
        if (prevBusAddress) {
            prevBusAddress.innerHTML = (invoiceData.business.address || '').replace(/\n/g, '<br>');
        }

        if (prevLogo) {
            if (invoiceData.business.logo) {
                prevLogo.src = invoiceData.business.logo;
                prevLogo.style.display = 'block';
            } else {
                prevLogo.style.display = 'none';
            }
        }

        if (prevClientName) prevClientName.textContent = invoiceData.client.name;
        if (prevClientCompany) prevClientCompany.textContent = invoiceData.client.company;
        if (prevClientEmail) prevClientEmail.textContent = invoiceData.client.email;
        if (prevClientPhone) prevClientPhone.textContent = invoiceData.client.phone;
        if (prevClientAddress) {
            prevClientAddress.innerHTML = (invoiceData.client.address || '').replace(/\n/g, '<br>');
        }

        if (prevInvNumber) prevInvNumber.textContent = invoiceData.invoice.number;
        if (prevDate) prevDate.textContent = invoiceData.invoice.date;
        if (prevDueDate) prevDueDate.textContent = invoiceData.invoice.dueDate;

        if (prevStatusBadge) {
            const status = invoiceData.meta.status;
            prevStatusBadge.className = `status-badge status-${status.toLowerCase()}`;
            prevStatusBadge.textContent = status === 'Paid' ? 'PAID' : status;
        }

        if (prevDocTitle) {
            prevDocTitle.textContent = invoiceData.meta.status === 'Paid' ? 'RECEIPT' : 'INVOICE';
        }

        if (prevReceiptNote) {
            const isReceipt = invoiceData.meta.status === 'Paid';
            prevReceiptNote.style.display = isReceipt ? 'block' : 'none';
            prevReceiptNote.textContent = isReceipt ? 'Payment Receipt' : '';
        }

        if (invoicePreview) {
            const isReceipt = invoiceData.meta.status === 'Paid';
            invoicePreview.className = `invoice-paper ${invoiceData.meta.template}${isReceipt ? ' is-payment-receipt' : ''}`;
        }

        if (prevItemsList) {
            prevItemsList.innerHTML = '';
            invoiceData.invoice.items.forEach(item => {
                const tr = document.createElement('tr');
                const lineTotal = (parseFloat(item.qty) || 0) * (parseFloat(item.price) || 0);
                tr.innerHTML = `
                    <td>${item.description}</td>
                    <td>${item.qty}</td>
                    <td>UGX${parseFloat(item.price || 0).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                    <td style="text-align: right;">UGX${lineTotal.toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
                `;
                prevItemsList.appendChild(tr);
            });
        }

        if (prevSubtotal) prevSubtotal.textContent = `UGX${invoiceData.invoice.subtotal.toLocaleString(undefined, {minimumFractionDigits: 2})}`;
        if (prevTotal) prevTotal.textContent = `UGX${invoiceData.invoice.total.toLocaleString(undefined, {minimumFractionDigits: 2})}`;

        if (prevStamp) {
            if (invoiceData.stamp) {
                prevStamp.src = invoiceData.stamp;
                prevStamp.style.display = 'block';
            } else {
                prevStamp.style.display = 'none';
            }
        }
    }

    function bindEvents() {
        const bindInput = (el, objKey1, objKey2) => {
            if (!el) return;
            el.addEventListener('input', (e) => {
                if (objKey2) {
                    invoiceData[objKey1][objKey2] = e.target.value;
                } else {
                    invoiceData[objKey1] = e.target.value;
                }
                renderPreview();
            });
        };

        bindInput(busNameInput, 'business', 'name');
        bindInput(busOwnerInput, 'business', 'owner');
        bindInput(busEmailInput, 'business', 'email');
        bindInput(busPhoneInput, 'business', 'phone');
        bindInput(busAddressInput, 'business', 'address');

        bindInput(clientNameInput, 'client', 'name');
        bindInput(clientCompanyInput, 'client', 'company');
        bindInput(clientEmailInput, 'client', 'email');
        bindInput(clientPhoneInput, 'client', 'phone');
        bindInput(clientAddressInput, 'client', 'address');

        bindInput(invNumberInput, 'invoice', 'number');

        if (invoiceStatusSelect) {
            invoiceStatusSelect.addEventListener('change', (e) => {
                invoiceData.meta.status = e.target.value;
                renderPreview();
            });
        }

        const applyTemplate = (templateName) => {
            invoiceData.meta.template = templateName;
            if (templateSelector) templateSelector.value = templateName;
            document.querySelectorAll('.template-tab').forEach((tab) => {
                tab.classList.toggle('active', tab.dataset.template === templateName);
            });
            renderPreview();
        };

        if (templateSelector) {
            templateSelector.addEventListener('change', (e) => {
                applyTemplate(e.target.value);
            });
        }

        document.querySelectorAll('.template-tab').forEach((tab) => {
            tab.addEventListener('click', () => {
                applyTemplate(tab.dataset.template);
            });
        });

        if (logoInput) {
            logoInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        invoiceData.business.logo = ev.target.result;
                        renderPreview();
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (stampInput) {
            stampInput.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (ev) => {
                        invoiceData.stamp = ev.target.result;
                        renderPreview();
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        if (itemsContainer) {
            itemsContainer.addEventListener('input', (e) => {
                const index = e.target.getAttribute('data-index');
                const field = e.target.getAttribute('data-field');
                if (index !== null && field !== null) {
                    invoiceData.invoice.items[index][field] = e.target.value;
                    renderPreview();
                }
            });

            itemsContainer.addEventListener('click', (e) => {
                const deleteBtn = e.target.closest('.delete-btn');
                if (deleteBtn) {
                    const index = deleteBtn.getAttribute('data-index');
                    invoiceData.invoice.items.splice(index, 1);
                    renderLineItemsEditor();
                    renderPreview();
                }
            });
        }

        if (addItemBtn) {
            addItemBtn.addEventListener('click', (e) => {
                e.preventDefault();
                invoiceData.invoice.items.push({ description: 'New Service Item', qty: 1, price: 50000 });
                renderLineItemsEditor();
                renderPreview();
            });
        }

        if (saveInvoiceBtn) {
            saveInvoiceBtn.addEventListener('click', () => {
                if (!invoiceData.invoice.number || invoiceData.invoice.number.trim() === '') {
                    invoiceData.invoice.number = generateInvoiceNumber();
                    if (invNumberInput) invNumberInput.value = invoiceData.invoice.number;
                }
                alert('Invoice payload successfully saved!');
            });
        }

        if (viewHistoryBtn && historyOverlay) {
            viewHistoryBtn.addEventListener('click', () => {
                historyOverlay.style.display = 'flex';
            });
        }

        if (historyClose && historyOverlay) {
            historyClose.addEventListener('click', () => {
                historyOverlay.style.display = 'none';
            });
        }

        if (printBtn) {
            printBtn.addEventListener('click', () => {
                window.print();
            });
        }
    }

    initFormValues();
    renderLineItemsEditor();
    renderPreview();
    bindEvents();
});