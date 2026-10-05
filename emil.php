<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift Invoice Studio</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- AUTHENTICATION CONTAINER (Shown by default on first load) -->
    <div id="auth-container" class="auth-wrapper" style="display: flex;">
        <div class="auth-card">
            <div class="logo-area center">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <h2>Swift Invoice Studio</h2>
            </div>

            <!-- LOGIN FORM -->
            <form id="login-form" class="auth-form">
                <h3>Sign In</h3>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="login-email" required placeholder="name@example.com">
                </div>
                <div class="form-group">
                    <label for="login-password">Password</label>
                    <div class="password-field">
                        <input type="password" id="login-password" required placeholder="••••••••">
                        <button type="button" class="password-toggle" data-target="login-password" aria-label="Show password" aria-pressed="false">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary full-width">Sign In</button>
                <p class="auth-switch">Don't have an account? <a href="#" id="show-register">Create account</a></p>
                <p class="auth-switch"><a href="#" id="show-forgot">Forgot password?</a></p>
            </form>

            <!-- REGISTER FORM -->
            <form id="register-form" class="auth-form" style="display:none;">
                <h3>Create Account</h3>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" id="reg-email" required placeholder="name@example.com">
                </div>
                <div class="form-group">
                    <label for="reg-password">Password (Min 8 chars, 1 upper, 1 lower, 1 number, 1 symbol)</label>
                    <div class="password-field">
                        <input type="password" id="reg-password" required placeholder="••••••••">
                        <button type="button" class="password-toggle" data-target="reg-password" aria-label="Show password" aria-pressed="false">
                            <i class="fa fa-eye"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary full-width">Register</button>
                <p class="auth-switch">Already have an account? <a href="#" id="show-login">Sign In</a></p>
            </form>

            <!-- FORGOT PASSWORD FORM -->
            <form id="forgot-form" class="auth-form" style="display:none;">
                <h3>Reset Password</h3>
                <div class="form-group">
                    <label>Enter your account email</label>
                    <input type="email" id="forgot-email" required placeholder="name@example.com">
                </div>
                <button type="submit" class="btn btn-primary full-width">Send Reset Link</button>
                <p class="auth-switch"><a href="#" id="back-to-login">Back to Sign In</a></p>
            </form>

            <div id="auth-message" class="auth-message"></div>
        </div>
    </div>

    <!-- MAIN APP WORKSPACE (Hidden until successful login) -->
    <div id="app-workspace" class="app-main-layout" style="display: none;">
        <header class="app-header">
            <div class="logo-area">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <h1>Swift Invoice Studio</h1>
            </div>
            <div class="actions">
                <button id="save-invoice-btn" class="btn btn-secondary"><i class="fa-solid fa-floppy-disk"></i> Save Invoice</button>
                <button id="history-btn" class="btn btn-secondary"><i class="fa-solid fa-clock-rotate-left"></i> Invoices</button>
                <button id="print-btn" class="btn btn-primary"><i class="fa-solid fa-download"></i> Download PDF</button>
                <button id="logout-btn" class="btn btn-danger"><i class="fa-solid fa-right-from-bracket"></i></button>
            </div>
        </header>

        <main class="workspace">
            <!-- LEFT PANEL: EDITOR FORM -->
            <section class="editor-panel">
                <h2>Invoice Editor</h2>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Invoice Template</label>
                        <select id="invTemplate" class="form-control">
                            <option value="template-modern">Modern Minimalist</option>
                            <option value="template-classic">Classic Corporate</option>
                            <option value="template-creative">Creative Studio</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Invoice Status</label>
                        <select id="invStatus" class="form-control">
                            <option value="Draft">Draft</option>
                            <option value="Sent">Sent</option>
                            <option value="Paid">Paid</option>
                            <option value="Overdue">Overdue</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Business Name</label>
                    <input type="text" id="busName" value="Northstar Studio">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Business Logo</label>
                        <input type="file" id="busLogo" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Stamp / Signature</label>
                        <input type="file" id="stampInput" accept="image/*">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Owner / Sender</label>
                        <input type="text" id="busOwner" value="Alex Morgan">
                    </div>
                    <div class="form-group">
                        <label>Invoice Number</label>
                        <input type="text" id="invNum" value="INV-2026-001">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" id="busEmail" value="hello@northstar.studio">
                    </div>
                    <div class="form-group">
                        <label>Phone</label>
                        <input type="text" id="busPhone" value="+256 700 000000">
                    </div>
                </div>

                <div class="form-group">
                    <label>Business Address</label>
                    <textarea id="busAddress">Kakoba, Mbarara, Uganda</textarea>
                </div>

                <hr class="divider">

                <h3>Client Details</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Client Name</label>
                        <input type="text" id="clientName" value="Jamie Rivera">
                    </div>
                    <div class="form-group">
                        <label>Company</label>
                        <input type="text" id="clientCompany" value="Aperture Labs">
                    </div>
                </div>
                <div class="form-group">
                    <label>Client Email & Address</label>
                    <input type="text" id="clientEmail" value="jamie@aperturelabs.co">
                    <textarea id="clientAddress" style="margin-top: 8px;">Mbarara City, Uganda</textarea>
                </div>

                <hr class="divider">

                <h3>Line Items</h3>
                <div id="itemsContainer"></div>
                <button type="button" id="addItemBtn" class="btn btn-outline"><i class="fa-solid fa-plus"></i> Add Item</button>
            </section>

            <!-- RIGHT PANEL: LIVE PREVIEW -->
            <section class="preview-panel">
                <div class="preview-badge"><span class="dot"></span> LIVE PREVIEW</div>
                
                <div class="invoice-paper template-modern" id="invoicePaper">
                    <div class="invoice-top-bar">
                        <div class="logo-wrap" id="renderLogoContainer"></div>
                        <div id="renderStatusBadge" class="status-badge status-draft">DRAFT</div>
                    </div>

                    <div class="inv-header">
                        <div class="sender-info">
                            <h3 id="renderBusName">Northstar Studio</h3>
                            <p id="renderBusMeta">hello@northstar.studio &bull; +256 700 000000</p>
                        </div>
                        <div class="inv-meta">
                            <h2>INVOICE</h2>
                            <p id="renderInvNumber" class="invoice-num">INV-2026-001</p>
                        </div>
                    </div>

                    <div class="inv-parties">
                        <div class="billed-to">
                            <span class="label">Billed To</span>
                            <h4 id="renderClientName">Jamie Rivera</h4>
                            <p id="renderClientAddress">Mbarara City, Uganda</p>
                        </div>
                        <div class="dates">
                            <p><strong>Issue Date:</strong> <span id="renderInvDate">Sep 21, 2026</span></p>
                            <p><strong>Due Date:</strong> <span id="renderInvDueDate">Oct 21, 2026</span></p>
                        </div>
                    </div>

                    <table class="inv-table">
                        <thead>
                            <tr>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th style="text-align: right;">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="renderItemsTableBody"></tbody>
                    </table>

                    <div class="inv-footer-section">
                        <div class="stamp-wrap" id="renderStampContainer"></div>
                        <div class="inv-totals">
                            <div class="total-row sub">
                                <span>Subtotal</span>
                                <span id="renderSubtotal">$0.00</span>
                            </div>
                            <div class="total-row" id="renderTaxRow" style="display:none;">
                                <span>Tax (<span id="renderTaxRateDisplay">0</span>%)</span>
                                <span id="renderTaxAmount">$0.00</span>
                            </div>
                            <div class="total-row" id="renderDiscountRow" style="display:none;">
                                <span>Discount</span>
                                <span id="renderDiscountAmount">-$0.00</span>
                            </div>
                            <div class="total-row final">
                                <span>Total Due</span>
                                <span id="renderTotal">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <!-- History Drawer / Modal -->
    <div id="historyOverlay" class="history-overlay" style="display:none;">
        <div class="history-drawer">
            <div class="history-header">
                <h3>Saved Invoices & Payloads</h3>
                <button id="closeHistoryBtn" class="btn btn-outline">Close</button>
            </div>
            <div id="historyList" class="history-list"></div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>