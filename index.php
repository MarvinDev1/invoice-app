<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Swift Invoice Studio</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Identity Services SDK -->
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>

    <!-- AUTHENTICATION CONTAINER -->
    <div id="auth-container" class="auth-wrapper" style="display: <?php echo $isLoggedIn ? 'none' : 'flex'; ?>;">
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
                
                <!-- Google Sign-In Divider & Button Container -->
                <div class="auth-divider" style="text-align: center; margin: 15px 0; color: #888; font-size: 13px;">OR</div>
                <div id="g_id_onload"
                     data-client_id="359034216459-3vpmcmt0vv23j4rng4uc6b6pt62jsfjf.apps.googleusercontent.com"
                     data-callback="handleGoogleSignIn"
                     data-auto_prompt="false">
                </div>
                <div class="g_id_signin"
                     data-type="standard"
                     data-shape="rectangular"
                     data-theme="outline"
                     data-text="signin_with"
                     data-size="large"
                     data-logo_alignment="left"
                     style="display: flex; justify-content: center; width: 100%;">
                </div>

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
                
                <!-- Google Sign-Up Divider & Button Container -->
                <div class="auth-divider" style="text-align: center; margin: 15px 0; color: #888; font-size: 13px;">OR</div>
                <div class="g_id_signin"
                     data-type="standard"
                     data-shape="rectangular"
                     data-theme="outline"
                     data-text="signup_with"
                     data-size="large"
                     data-logo_alignment="left"
                     style="display: flex; justify-content: center; width: 100%;">
                </div>

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
    <div id="app-workspace" class="app-main-layout" style="display: <?php echo $isLoggedIn ? 'block' : 'none'; ?>;">
        <header class="app-header">
            <div class="app-header-title-bar">
                <div class="logo-area">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    <h1>Swift Invoice Studio</h1>
                </div>
            </div>
            <div class="app-header-top">
                <button type="button" id="mobile-editor-toggle" class="mobile-editor-toggle" aria-label="Toggle invoice editor" aria-controls="mobile-editor-drawer" aria-expanded="false">
                    <span class="hamburger-icon" aria-hidden="true"><span></span><span></span><span></span></span>
                </button>
                <div class="actions">
                    <button id="save-btn" class="btn btn-secondary">Save Invoice</button>
                    <button id="view-history-btn" class="btn btn-secondary">Invoices</button>
                    <button id="print-btn" class="btn btn-primary">Download PDF</button>
                    <button id="logout-btn" class="btn btn-danger" title="Logout">Logout</button>
                </div>
            </div>
        </header>

        <main class="workspace">
            <div class="drawer-backdrop" id="drawer-backdrop" aria-hidden="true"></div>
            <aside class="editor-panel desktop-editor-sidebar" id="editor-panel" aria-label="Invoice editor">
                <div class="editor-content">
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

                    <div class="mobile-form-section" data-mobile-section="business">
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
                    </div>

                    <div class="mobile-form-section" data-mobile-section="client">
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
                    </div>

                    <div class="mobile-form-section" data-mobile-section="items">
                        <hr class="divider">
                        <h3>Line Items</h3>
                        <div id="items-container"></div>
                        <button type="button" id="add-item-btn" class="btn btn-outline">Add Item</button>
                    </div>
                </div>
            </aside>

            <aside class="mobile-editor-drawer" id="mobile-editor-drawer" aria-label="Mobile invoice editor drawer" aria-hidden="true">
                <div class="editor-panel-header">
                    <h2>Invoice Editor</h2>
                    <button type="button" class="mobile-editor-close" id="mobile-editor-close" aria-label="Close editor">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="mobile-editor-nav" id="mobile-editor-nav">
                    <div class="mobile-editor-toolbar">
                        <div class="mobile-template-picker" aria-label="Mobile Invoice Template">
                            <button type="button" class="mobile-template-tab active" data-template="template-modern">Modern</button>
                            <button type="button" class="mobile-template-tab" data-template="template-classic">Classic</button>
                            <button type="button" class="mobile-template-tab" data-template="template-creative">Creative</button>
                        </div>
                        <label class="mobile-status-label">
                            <span>Status</span>
                            <select id="mobile-invoice-status" class="form-control mobile-status-select">
                                <option value="Draft">Draft</option>
                                <option value="Sent">Sent</option>
                                <option value="Paid">Paid</option>
                                <option value="Overdue">Overdue</option>
                            </select>
                        </label>
                    </div>

                    <nav class="mobile-editor-menu" aria-label="Invoice sections">
                        <button type="button" class="mobile-nav-item" data-section="business">
                            <span class="mobile-nav-icon">🏢</span>
                            <span>Business Details</span>
                        </button>
                        <button type="button" class="mobile-nav-item" data-section="client">
                            <span class="mobile-nav-icon">👤</span>
                            <span>Client Details</span>
                        </button>
                        <button type="button" class="mobile-nav-item" data-section="items">
                            <span class="mobile-nav-icon">📝</span>
                            <span>Line Items</span>
                        </button>
                    </nav>

                    <button type="button" id="mobile-logout-btn" class="btn btn-danger mobile-logout-btn">Logout</button>
                </div>

                <div class="mobile-subview-overlay" id="mobile-subview-overlay" aria-hidden="true">
                    <div class="mobile-subview-header">
                        <button type="button" class="mobile-subview-nav" id="mobile-subview-back" aria-label="Back">←</button>
                        <span id="mobile-subview-title">Business Details</span>
                        <button type="button" class="mobile-subview-done" id="mobile-subview-done" aria-label="Done">✓</button>
                    </div>
                    <div class="mobile-subview-body" id="mobile-subview-body"></div>
                </div>
            </aside>

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

    <script>
        // Google Sign-In Callback Handler
        function handleGoogleSignIn(response) {
            fetch('login.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ credential: response.credential })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload(); // Reload workspace on successful authentication
                } else {
                    alert(data.message || 'Google authentication failed.');
                }
            })
            .catch(err => console.error('Error:', err));
        }
    </script>
    <script src="script.js"></script>
</body>
</html>