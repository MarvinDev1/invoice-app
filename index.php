<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift Invoice Studio</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- AUTHENTICATION CONTAINER -->
    <div id="auth-container" class="auth-wrapper" style="display: flex;">
        <div class="auth-card">
            <div class="logo-area center">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <h2>Swift Invoice Studio</h2>
            </div>

            <!-- LOGIN FORM -->
            <form id="login-form" class="auth-form view-section">
                <h3>Sign In to your workspace</h3>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="login-email" placeholder="name@example.com" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <div class="password-field">
                        <input type="password" id="login-password" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" data-target="login-password" aria-label="Show password" aria-pressed="false">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary full-width">Sign In</button>
                <p class="auth-switch">Don't have an account? <a href="#" id="show-register">Create account</a></p>
                <div id="login-error" class="auth-error" style="display:none;"></div>
                <p class="auth-switch"><a href="#" id="show-forgot" data-target="forgot">Forgot password?</a></p>
            </form>

            <!-- REGISTER FORM -->
            <form id="register-form" class="auth-form view-section" style="display:none;">
                <h3>Create a New Account</h3>
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" id="reg-name" placeholder="Alex Morgan" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="reg-email" placeholder="name@example.com" required>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="password-field">
                        <input type="password" id="reg-password" placeholder="••••••••" required>
                        <button type="button" class="password-toggle" data-target="reg-password" aria-label="Show password" aria-pressed="false">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary full-width">Register Account</button>
                <p class="auth-switch">Already have an account? <a href="#" id="show-login-from-reg">Sign in</a></p>
                <div id="register-error" class="auth-error" style="display:none;"></div>
            </form>

            <!-- FORGOT PASSWORD FORM -->
            <form id="forgot-form" class="auth-form view-section" style="display:none;">
                <h3>Reset Password</h3>
                <div id="forgot-error" class="auth-error" style="display:none;"></div>
                <div id="forgot-success" class="auth-success" style="display:none;"></div>
                <p class="auth-desc">Enter your registered email and we'll send you password reset instructions.</p>
                <div class="form-group" style="margin-top: 1rem;">
                    <label>Email Address</label>
                    <input type="email" id="forgot-email" placeholder="name@example.com" required>
                </div>
                <button type="submit" class="btn btn-primary full-width">Send Reset Link</button>
                <p class="auth-switch"><a href="#" id="show-login-from-forgot" data-target="login">Back to Sign In</a></p>
            </form>
        </div>
    </div>

    <!-- MAIN APP WORKSPACE -->
    <div id="app-workspace" class="app-main-layout" style="display: none;">
        <header class="app-header">
            <div class="logo-area">
                <!-- Bootstrap Hamburger Toggle Button for Mobile Offcanvas -->
                <button class="btn btn-outline-secondary d-md-none me-2 p-1 px-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#editor-panel" aria-controls="editor-panel" aria-label="Toggle invoice editor">
                    <i class="fas fa-bars"></i>
                </button>
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <h1>Swift Invoice Studio</h1>
            </div>
            <div class="actions">
                <button id="save-btn" class="btn btn-secondary">Save Invoice</button>
                <button id="view-history-btn" class="btn btn-secondary">Invoices</button>
                <button id="print-btn" class="btn btn-primary">Download PDF</button>
                <button id="logout-btn" class="btn btn-danger" title="Logout">Logout</button>
            </div>
        </header>

        <main class="workspace">
            <!-- Bootstrap offcanvas-md transforms the editor panel into a sliding drawer on mobile and static sidebar on desktop -->
            <section class="offcanvas-md offcanvas-start editor-panel" tabindex="-1" id="editor-panel" aria-labelledby="editorPanelLabel">
                <div class="offcanvas-header editor-panel-header border-bottom d-md-none">
                    <h2 class="offcanvas-title fs-5" id="editorPanelLabel">Invoice Editor</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#editor-panel" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body editor-content d-flex flex-column p-3 p-md-0">
                    <div class="form-row">
                        <div class="form-group">
                            <label>Invoice Template</label>
                            <div class="template-picker" aria-label="Invoice Template">
                                <button type="button" class="template-tab active" data-template="template-modern">Modern</button>
                                <button type="button" class="template-tab" data-template="template-classic">Classic</button>
                                <button type="button" class="template-tab" data-template="template-creative">Creative</button>
                            </div>
                            <select id="template-selector" class="form-control" style="display:none;">
                                <option value="template-modern">Modern Minimalist</option>
                                <option value="template-classic">Classic Corporate</option>
                                <option value="template-creative">Creative Studio</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Invoice Status</label>
                            <select id="invoice-status" class="form-control">
                                <option value="Draft">Draft</option>
                                <option value="Sent">Sent</option>
                                <option value="Paid">Paid</option>
                                <option value="Overdue">Overdue</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Business Name</label>
                        <input type="text" id="bus-name" value="Northstar Studio">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Business Logo</label>
                            <input type="file" id="logo-input" accept="image/*">
                        </div>
                        <div class="form-group">
                            <label>Stamp / Signature</label>
                            <input type="file" id="stamp-input" accept="image/*">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Owner / Sender</label>
                            <input type="text" id="bus-owner" value="Alex Morgan">
                        </div>
                        <div class="form-group">
                            <label>Invoice Number</label>
                            <input type="text" id="inv-number" value="INV-2026-001">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" id="bus-email" value="hello@northstar.studio">
                        </div>
                        <div class="form-group">
                            <label>Phone</label>
                            <input type="text" id="bus-phone" value="+256 700 000000">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Business Address</label>
                        <textarea id="bus-address">Kakoba, Mbarara, Uganda</textarea>
                    </div>
                    <hr class="divider">
                    <h3>Client Details</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Client Name</label>
                            <input type="text" id="client-name" value="Jamie Rivera">
                        </div>
                        <div class="form-group">
                            <label>Company</label>
                            <input type="text" id="client-company" value="Aperture Labs">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Client Email</label>
                        <input type="text" id="client-email" value="jamie@aperturelabs.co">
                    </div>
                    <div class="form-group">
                        <label>Client Phone</label>
                        <input type="text" id="client-phone" value="+256 700 111111">
                    </div>
                    <div class="form-group">
                        <label>Client Address</label>
                        <textarea id="client-address">Mbarara City, Uganda</textarea>
                    </div>
                    <hr class="divider">
                    <h3>Line Items</h3>
                    <div id="items-container"></div>
                    <button type="button" id="add-item-btn" class="btn btn-outline">Add Item</button>
                </div>
            </section>

            <section class="preview-panel">
                <div class="preview-badge"><span class="dot"></span> LIVE PREVIEW</div>
                <div class="invoice-paper template-modern" id="invoice-preview">
                    <div class="invoice-top-bar">
                        <div class="logo-wrap">
                            <img id="prev-logo" src="" alt="Logo" style="display:none;">
                        </div>
                        <div id="prev-status-badge" class="status-badge status-draft">DRAFT</div>
                    </div>
                    <div class="inv-header">
                        <div class="sender-info">
                            <h3 id="prev-bus-name">Northstar Studio</h3>
                            <p id="prev-bus-owner">Alex Morgan</p>
                            <p id="prev-bus-email">hello@northstar.studio</p>
                            <p id="prev-bus-phone">+256 700 000000</p>
                            <p id="prev-bus-address">Kakoba, Mbarara, Uganda</p>
                        </div>
                        <div class="inv-meta">
                            <h2 id="prev-doc-title">INVOICE</h2>
                            <div id="prev-receipt-note" class="payment-receipt-note" style="display:none;">Payment Receipt</div>
                            <p id="prev-inv-number" class="invoice-num">INV-2026-001</p>
                        </div>
                    </div>
                    <div class="inv-parties">
                        <div class="billed-to">
                            <span class="label">Billed To</span>
                            <h4 id="prev-client-name">Jamie Rivera</h4>
                            <p id="prev-client-company">Aperture Labs</p>
                            <p id="prev-client-email">jamie@aperturelabs.co</p>
                            <p id="prev-client-phone">+256 700 111111</p>
                            <p id="prev-client-address">Mbarara City, Uganda</p>
                        </div>
                        <div class="dates">
                            <p><strong>Issue Date:</strong> <span id="prev-date">Sep 21, 2026</span></p>
                            <p style="margin-top: 4px;"><strong>Due Date:</strong> <span id="prev-due-date">Oct 21, 2026</span></p>
                        </div>
                    </div>
                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody id="prev-items-list"></tbody>
                    </table>
                    <div class="inv-footer-section">
                        <div class="stamp-wrap">
                            <img id="prev-stamp" src="" alt="Stamp" style="display:none;">
                        </div>
                        <div class="inv-totals">
                            <div class="total-row sub">
                                <span>Subtotal</span>
                                <span id="prev-subtotal">UGX0.00</span>
                            </div>
                            <div class="total-row final">
                                <span>Total Due</span>
                                <span id="prev-total">UGX0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <div id="history-overlay" class="history-overlay" style="display:none;">
        <div class="history-drawer">
            <div class="history-header">
                <h3>Saved Invoices</h3>
                <button id="history-close" class="btn btn-outline">Close</button>
            </div>
            <div id="history-list" class="history-list"></div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN (Required for Offcanvas toggle functionality) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>