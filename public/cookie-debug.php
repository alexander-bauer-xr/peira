<?php
/**
 * Simple cookie debugging page
 * Shows all cookies received by the server
 */
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Cookie Debug - Peira</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .section { background: white; padding: 20px; margin: 20px 0; border-radius: 5px; }
        h2 { color: #ff3901; }
        pre { background: #f9f9f9; padding: 10px; border-left: 3px solid #ff3901; overflow-x: auto; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #ff3901; color: white; }
    </style>
</head>
<body>
    <h1>🍪 Cookie Debug Page</h1>
    
    <div class="section">
        <h2>Server-Side Cookies ($_COOKIE)</h2>
        <?php if (empty($_COOKIE)): ?>
            <p class="error">No cookies received by server</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Cookie Name</th>
                        <th>Value (truncated)</th>
                        <th>Length</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($_COOKIE as $name => $value): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($name); ?></strong></td>
                        <td><code><?php echo htmlspecialchars(substr($value, 0, 100)); ?><?php echo strlen($value) > 100 ? '...' : ''; ?></code></td>
                        <td><?php echo strlen($value); ?> bytes</td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Cookie Consent Check</h2>
        <?php
        $cookieConsentName = 'laravel_cookie_consent';
        if (isset($_COOKIE[$cookieConsentName])):
            $consent = json_decode($_COOKIE[$cookieConsentName], true);
        ?>
            <p class="success">✓ Cookie consent cookie found!</p>
            <pre><?php echo htmlspecialchars(json_encode($consent, JSON_PRETTY_PRINT)); ?></pre>
        <?php else: ?>
            <p class="error">✗ Cookie consent cookie NOT found on server</p>
            <p>The browser is not sending the cookie to the server.</p>
        <?php endif; ?>
    </div>

    <div class="section">
        <h2>Request Information</h2>
        <table>
            <tr>
                <th>Protocol</th>
                <td><?php echo isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'HTTPS ✓' : 'HTTP'; ?></td>
            </tr>
            <tr>
                <th>Host</th>
                <td><?php echo htmlspecialchars($_SERVER['HTTP_HOST'] ?? 'N/A'); ?></td>
            </tr>
            <tr>
                <th>X-Forwarded-Proto</th>
                <td><?php echo htmlspecialchars($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? 'Not set'); ?></td>
            </tr>
            <tr>
                <th>Cookie Header</th>
                <td><?php echo isset($_SERVER['HTTP_COOKIE']) ? '✓ Present (' . strlen($_SERVER['HTTP_COOKIE']) . ' bytes)' : '✗ Missing'; ?></td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h2>JavaScript Cookie Check</h2>
        <div id="js-cookies"></div>
        <script>
            const allCookies = document.cookie;
            const cookieDiv = document.getElementById('js-cookies');
            
            if (!allCookies) {
                cookieDiv.innerHTML = '<p class="error">✗ No cookies accessible via JavaScript</p>';
            } else {
                const cookies = allCookies.split(';').map(c => c.trim());
                const hasConsent = cookies.some(c => c.startsWith('laravel_cookie_consent='));
                
                let html = hasConsent 
                    ? '<p class="success">✓ Cookie consent cookie accessible via JavaScript</p>' 
                    : '<p class="error">✗ Cookie consent cookie NOT accessible via JavaScript</p>';
                
                html += '<h3>All JavaScript-Accessible Cookies:</h3><ul>';
                cookies.forEach(cookie => {
                    const [name, value] = cookie.split('=');
                    html += `<li><strong>${name}</strong> = ${value ? value.substring(0, 50) + '...' : '(empty)'}</li>`;
                });
                html += '</ul>';
                
                cookieDiv.innerHTML = html;
            }
        </script>
    </div>

    <div class="section">
        <h2>Test Actions</h2>
        <p><a href="/de/cookie-einstellungen" style="color: #ff3901;">→ Go to Cookie Preferences Page</a></p>
        <p><a href="javascript:location.reload();" style="color: #ff3901;">→ Reload This Page</a></p>
    </div>
</body>
</html>
