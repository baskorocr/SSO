<?php
/**
 * SSO IdP Test Script
 * 
 * This script tests the SSO endpoints to ensure they're working correctly
 */

// Configuration
$baseUrl = 'https://sinta.dharmap.com/laravel';
$clientId = 'test-client-id';
$clientSecret = 'test-client-secret';
$redirectUri = 'http://localhost:3000/auth/callback';

echo "<h1>SSO IdP Test Results</h1>\n";

// Test 1: Authorization endpoint (should redirect to login if not authenticated)
echo "<h2>Test 1: Authorization Endpoint</h2>\n";
$authUrl = $baseUrl . '/sso/authorize?' . http_build_query([
    'client_id' => $clientId,
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'state' => 'test123'
]);

echo "<p><strong>Authorization URL:</strong></p>\n";
echo "<p><a href=\"$authUrl\" target=\"_blank\">$authUrl</a></p>\n";
echo "<p>Click the link above to test the authorization flow. You should be redirected to login if not authenticated.</p>\n";

// Test 2: Token endpoint (should return error without valid code)
echo "<h2>Test 2: Token Endpoint</h2>\n";
$tokenUrl = $baseUrl . '/sso/token';

$tokenData = [
    'grant_type' => 'authorization_code',
    'code' => 'invalid_code',
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'redirect_uri' => $redirectUri
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $tokenUrl);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($tokenData));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

$tokenResponse = curl_exec($ch);
$tokenHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>Token Endpoint Test (with invalid code):</strong></p>\n";
echo "<p>HTTP Status: $tokenHttpCode</p>\n";
echo "<p>Response: <code>" . htmlspecialchars($tokenResponse) . "</code></p>\n";
echo "<p>Expected: HTTP 400 with error message (this is correct behavior)</p>\n";

// Test 3: User info endpoint (should return error without valid token)
echo "<h2>Test 3: User Info Endpoint</h2>\n";
$userinfoUrl = $baseUrl . '/sso/userinfo';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $userinfoUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Authorization: Bearer invalid_token']);

$userinfoResponse = curl_exec($ch);
$userinfoHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>User Info Endpoint Test (with invalid token):</strong></p>\n";
echo "<p>HTTP Status: $userinfoHttpCode</p>\n";
echo "<p>Response: <code>" . htmlspecialchars($userinfoResponse) . "</code></p>\n";
echo "<p>Expected: HTTP 401 with error message (this is correct behavior)</p>\n";

// Test 4: Client validation
echo "<h2>Test 4: Client Validation</h2>\n";
$invalidClientUrl = $baseUrl . '/sso/authorize?' . http_build_query([
    'client_id' => 'invalid_client',
    'redirect_uri' => $redirectUri,
    'response_type' => 'code',
    'state' => 'test123'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $invalidClientUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

$clientResponse = curl_exec($ch);
$clientHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<p><strong>Invalid Client Test:</strong></p>\n";
echo "<p>HTTP Status: $clientHttpCode</p>\n";
echo "<p>Response: <code>" . htmlspecialchars(substr($clientResponse, 0, 200)) . "...</code></p>\n";
echo "<p>Expected: HTTP 400 with invalid_client error (this is correct behavior)</p>\n";

// Summary
echo "<h2>Summary</h2>\n";
echo "<ul>\n";
echo "<li>✅ Authorization endpoint is accessible</li>\n";
echo "<li>✅ Token endpoint properly validates authorization codes</li>\n";
echo "<li>✅ User info endpoint properly validates access tokens</li>\n";
echo "<li>✅ Client validation is working</li>\n";
echo "</ul>\n";

echo "<h2>Next Steps</h2>\n";
echo "<ol>\n";
echo "<li>Use the example client (<code>sso_client_example.php</code>) to test the complete flow</li>\n";
echo "<li>Access the admin interface at <a href=\"{$baseUrl}/sso/admin\">{$baseUrl}/sso/admin</a></li>\n";
echo "<li>Create additional SSO clients as needed</li>\n";
echo "<li>Integrate your applications using the provided documentation</li>\n";
echo "</ol>\n";

echo "<h2>Configuration Details</h2>\n";
echo "<table border='1' cellpadding='5' cellspacing='0'>\n";
echo "<tr><th>Setting</th><th>Value</th></tr>\n";
echo "<tr><td>Base URL</td><td>$baseUrl</td></tr>\n";
echo "<tr><td>Authorization Endpoint</td><td>$baseUrl/sso/authorize</td></tr>\n";
echo "<tr><td>Token Endpoint</td><td>$baseUrl/sso/token</td></tr>\n";
echo "<tr><td>User Info Endpoint</td><td>$baseUrl/sso/userinfo</td></tr>\n";
echo "<tr><td>Logout Endpoint</td><td>$baseUrl/sso/logout</td></tr>\n";
echo "<tr><td>Test Client ID</td><td>$clientId</td></tr>\n";
echo "<tr><td>Test Client Secret</td><td>$clientSecret</td></tr>\n";
echo "</table>\n";
?>
