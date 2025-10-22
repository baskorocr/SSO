# SSO Identity Provider - Documentation & Tutorial

## 📋 Overview

Sistem SSO (Single Sign-On) Identity Provider telah diimplementasikan dengan fitur lengkap untuk manajemen user dan client. Sistem ini menggunakan OAuth 2.0 Authorization Code flow yang aman dan sesuai standar industri.

## 🔄 Perubahan yang Dilakukan

### 1. **Implementasi Ulang SSO IdP**
- ✅ Hapus semua implementasi SSO lama
- ✅ Buat struktur database baru yang lebih aman
- ✅ Implementasi OAuth 2.0 Authorization Code flow
- ✅ Sistem manajemen client yang lengkap

### 2. **Database Structure**
```sql
-- SSO Clients (Aplikasi yang terdaftar)
sso_clients:
- id, name, client_id, client_secret
- redirect_uris (JSON array)
- is_active, created_at, updated_at

-- Authorization Codes (Kode otorisasi sementara)
sso_authorization_codes:
- id, code, user_id, client_id
- redirect_uri, expires_at, used
- created_at, updated_at

-- Access Tokens (Token akses)
sso_access_tokens:
- id, token, user_id, client_id
- expires_at, revoked
- created_at, updated_at

-- User-Client Assignments (Penugasan user ke client)
user_sso_client_assignments:
- id, user_id, sso_client_id
- created_at, updated_at
```

### 3. **Fitur Admin Interface**
- ✅ **CRUD SSO Clients**: Create, Read, Update, Delete
- ✅ **User Assignment**: Assign/remove users dari clients
- ✅ **User Search**: Pencarian user saat assignment
- ✅ **Token Management**: Revoke tokens, regenerate secrets
- ✅ **Monitoring**: Lihat authorization codes dan access tokens

### 4. **Fitur User Dashboard**
- ✅ **Assigned Applications**: Tampilkan aplikasi yang di-assign
- ✅ **Quick Login**: Klik langsung login ke aplikasi
- ✅ **Auto Authorization**: Tidak perlu approve manual

### 5. **Security Features**
- ✅ **Client Validation**: Validasi client_id dan client_secret
- ✅ **Redirect URI Whitelist**: Hanya URI terdaftar yang diizinkan
- ✅ **User Authorization**: Hanya user yang di-assign bisa akses
- ✅ **Token Expiration**: Authorization code (10 menit), Access token (1 jam)
- ✅ **CSRF Protection**: State parameter untuk keamanan

## 🚀 Tutorial Setup Client

### A. **Akses Admin Interface**

1. **Login sebagai Admin**
   ```
   URL: https://sinta.dharmap.com/laravel/login
   ```

2. **Buka SSO Client Management**
   ```
   URL: https://sinta.dharmap.com/laravel/sso/admin
   Menu: Management → SSO Clients
   ```

### B. **Membuat SSO Client Baru**

1. **Klik "Create New Client"**

2. **Isi Form Client:**
   ```
   Client Name: Nama Aplikasi Anda
   Redirect URIs: 
   https://yourapp.com/auth/callback
   http://localhost:3000/auth/callback
   
   Assign Users: ✓ Pilih users yang boleh akses
   ```

3. **Submit Form**
   - Client ID dan Client Secret akan di-generate otomatis
   - Catat Client ID dan Client Secret untuk konfigurasi aplikasi

### C. **Konfigurasi Client Application**

#### **1. PHP Client (Vanilla)**

```php
<?php
class SsoClient {
    private $baseUrl = 'https://sinta.dharmap.com/laravel';
    private $clientId = 'YOUR_CLIENT_ID';
    private $clientSecret = 'YOUR_CLIENT_SECRET';
    private $redirectUri = 'https://yourapp.com/auth/callback';

    public function getAuthorizationUrl($state = null) {
        $state = $state ?: bin2hex(random_bytes(16));
        session_start();
        $_SESSION['sso_state'] = $state;

        $params = [
            'client_id' => $this->clientId,
            'redirect_uri' => $this->redirectUri,
            'response_type' => 'code',
            'state' => $state
        ];

        return $this->baseUrl . '/sso/authorize?' . http_build_query($params);
    }

    public function getAccessToken($code, $state = null) {
        session_start();
        if ($state && $_SESSION['sso_state'] !== $state) {
            throw new Exception('Invalid state parameter');
        }

        $data = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
            'redirect_uri' => $this->redirectUri
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/sso/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }

    public function getUserInfo($accessToken) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->baseUrl . '/sso/userinfo');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $accessToken
        ]);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}

// Usage Example
$sso = new SsoClient();

// Step 1: Redirect to SSO
if (!isset($_GET['code'])) {
    header('Location: ' . $sso->getAuthorizationUrl());
    exit;
}

// Step 2: Handle callback
$tokenResponse = $sso->getAccessToken($_GET['code'], $_GET['state'] ?? null);
$userInfo = $sso->getUserInfo($tokenResponse['access_token']);

// Step 3: Login user
session_start();
$_SESSION['user'] = $userInfo;

echo "Welcome, " . $userInfo['name'];
```

#### **2. Laravel Client (Socialite)**

**Install Socialite:**
```bash
composer require laravel/socialite
```

**Config (`config/services.php`):**
```php
'sso' => [
    'client_id' => env('SSO_CLIENT_ID'),
    'client_secret' => env('SSO_CLIENT_SECRET'),
    'redirect' => env('SSO_REDIRECT_URI'),
    'base_url' => env('SSO_BASE_URL', 'https://sinta.dharmap.com/laravel'),
],
```

**Environment (`.env`):**
```env
SSO_CLIENT_ID=your_client_id
SSO_CLIENT_SECRET=your_client_secret
SSO_REDIRECT_URI=https://yourapp.com/auth/callback
SSO_BASE_URL=https://sinta.dharmap.com/laravel
```

**Custom Provider (`app/Providers/SsoServiceProvider.php`):**
```php
<?php
namespace App\Providers;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\User;

class SsoProvider extends AbstractProvider
{
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase(
            config('services.sso.base_url') . '/sso/authorize', $state
        );
    }

    protected function getTokenUrl()
    {
        return config('services.sso.base_url') . '/sso/token';
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            config('services.sso.base_url') . '/sso/userinfo',
            ['headers' => ['Authorization' => 'Bearer ' . $token]]
        );

        return json_decode($response->getBody(), true);
    }

    protected function mapUserToObject(array $user)
    {
        return (new User)->setRaw($user)->map([
            'id' => $user['sub'],
            'name' => $user['name'],
            'email' => $user['email'],
        ]);
    }
}
```

**Controller (`app/Http/Controllers/AuthController.php`):**
```php
<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function redirectToSso()
    {
        return Socialite::driver('sso')->redirect();
    }

    public function handleSsoCallback()
    {
        $user = Socialite::driver('sso')->user();
        
        // Find or create user in your database
        $localUser = User::firstOrCreate(
            ['email' => $user->getEmail()],
            ['name' => $user->getName()]
        );

        auth()->login($localUser);

        return redirect('/dashboard');
    }
}
```

**Routes (`routes/web.php`):**
```php
Route::get('/auth/sso', [AuthController::class, 'redirectToSso'])->name('sso.redirect');
Route::get('/auth/sso/callback', [AuthController::class, 'handleSsoCallback'])->name('sso.callback');
```

#### **3. JavaScript/Node.js Client**

```javascript
class SsoClient {
    constructor() {
        this.baseUrl = 'https://sinta.dharmap.com/laravel';
        this.clientId = 'YOUR_CLIENT_ID';
        this.clientSecret = 'YOUR_CLIENT_SECRET';
        this.redirectUri = 'https://yourapp.com/auth/callback';
    }

    getAuthorizationUrl(state = null) {
        state = state || this.generateState();
        localStorage.setItem('sso_state', state);

        const params = new URLSearchParams({
            client_id: this.clientId,
            redirect_uri: this.redirectUri,
            response_type: 'code',
            state: state
        });

        return `${this.baseUrl}/sso/authorize?${params}`;
    }

    async getAccessToken(code, state = null) {
        const savedState = localStorage.getItem('sso_state');
        if (state && savedState !== state) {
            throw new Error('Invalid state parameter');
        }

        const response = await fetch(`${this.baseUrl}/sso/token`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({
                grant_type: 'authorization_code',
                code: code,
                client_id: this.clientId,
                client_secret: this.clientSecret,
                redirect_uri: this.redirectUri
            })
        });

        return await response.json();
    }

    async getUserInfo(accessToken) {
        const response = await fetch(`${this.baseUrl}/sso/userinfo`, {
            headers: {
                'Authorization': `Bearer ${accessToken}`
            }
        });

        return await response.json();
    }

    generateState() {
        return Math.random().toString(36).substring(2, 15) + 
               Math.random().toString(36).substring(2, 15);
    }
}

// Usage
const sso = new SsoClient();

// Redirect to SSO
window.location.href = sso.getAuthorizationUrl();

// Handle callback (in callback page)
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('code')) {
    const tokenResponse = await sso.getAccessToken(
        urlParams.get('code'), 
        urlParams.get('state')
    );
    
    const userInfo = await sso.getUserInfo(tokenResponse.access_token);
    
    // Store user info and redirect
    localStorage.setItem('user', JSON.stringify(userInfo));
    window.location.href = '/dashboard';
}
```

### D. **Testing & Debugging**

#### **1. Test Endpoints**

**Authorization Endpoint:**
```
GET https://sinta.dharmap.com/laravel/sso/authorize?client_id=YOUR_CLIENT_ID&redirect_uri=YOUR_CALLBACK&response_type=code&state=test123
```

**Token Endpoint:**
```bash
curl -X POST https://sinta.dharmap.com/laravel/sso/token \
  -d "grant_type=authorization_code" \
  -d "code=AUTHORIZATION_CODE" \
  -d "client_id=YOUR_CLIENT_ID" \
  -d "client_secret=YOUR_CLIENT_SECRET" \
  -d "redirect_uri=YOUR_CALLBACK"
```

**User Info Endpoint:**
```bash
curl -H "Authorization: Bearer ACCESS_TOKEN" \
  https://sinta.dharmap.com/laravel/sso/userinfo
```

#### **2. Common Issues & Solutions**

| Error | Cause | Solution |
|-------|-------|----------|
| `invalid_client` | Client ID/Secret salah | Cek credentials di admin panel |
| `invalid_redirect_uri` | URI tidak terdaftar | Tambahkan URI di client settings |
| `access_denied` | User tidak di-assign | Assign user di admin panel |
| `invalid_grant` | Code expired/used | Generate code baru |
| `invalid_token` | Token expired/revoked | Request token baru |

#### **3. Monitoring & Logs**

**Admin Panel:**
- Monitor authorization codes dan access tokens
- Lihat aktivitas user per client
- Revoke tokens jika diperlukan

**Laravel Logs:**
```bash
tail -f storage/logs/laravel.log | grep SSO
```

## 📊 API Endpoints Reference

### **Authorization Flow**

1. **Authorization Request**
   ```
   GET /sso/authorize
   Parameters: client_id, redirect_uri, response_type=code, state
   ```

2. **Token Exchange**
   ```
   POST /sso/token
   Body: grant_type=authorization_code, code, client_id, client_secret, redirect_uri
   ```

3. **User Information**
   ```
   GET /sso/userinfo
   Headers: Authorization: Bearer {access_token}
   ```

4. **Logout**
   ```
   POST /sso/logout
   Body: post_logout_redirect_uri (optional)
   ```

### **Quick Login (Dashboard)**
```
GET /sso/quick-login/{client_id}
Requires: Authentication
```

## 🔐 Security Best Practices

### **Production Checklist**
- [ ] Gunakan HTTPS untuk semua komunikasi
- [ ] Set redirect URI yang spesifik (hindari wildcard)
- [ ] Implement rate limiting pada token endpoint
- [ ] Monitor failed authentication attempts
- [ ] Regular cleanup expired codes/tokens
- [ ] Backup database SSO tables
- [ ] Use strong client secrets (64+ characters)

### **Client Security**
- [ ] Validate state parameter untuk CSRF protection
- [ ] Store client secret securely (environment variables)
- [ ] Implement proper session management
- [ ] Handle token expiration gracefully
- [ ] Use secure HTTP-only cookies untuk session

## 📞 Support & Maintenance

### **Admin Tasks**
- **User Management**: Assign/remove users via admin panel
- **Client Management**: Create/edit/delete SSO clients
- **Token Management**: Monitor dan revoke tokens
- **Monitoring**: Check logs untuk suspicious activities

### **Database Maintenance**
```sql
-- Clean expired authorization codes
DELETE FROM sso_authorization_codes WHERE expires_at < NOW();

-- Clean expired access tokens
DELETE FROM sso_access_tokens WHERE expires_at < NOW();

-- Check client usage
SELECT c.name, COUNT(t.id) as token_count 
FROM sso_clients c 
LEFT JOIN sso_access_tokens t ON c.id = t.client_id 
GROUP BY c.id;
```

### **Troubleshooting**
1. **Check client configuration** di admin panel
2. **Verify user assignments** untuk client
3. **Monitor Laravel logs** untuk error details
4. **Test endpoints** menggunakan curl/Postman
5. **Check database** untuk expired tokens/codes

---

## 🎯 Summary

SSO Identity Provider telah berhasil diimplementasikan dengan fitur lengkap:

- ✅ **OAuth 2.0 compliant** authorization server
- ✅ **Admin interface** untuk manajemen clients dan users
- ✅ **User dashboard** dengan quick login
- ✅ **Security features** yang komprehensif
- ✅ **Multiple client support** (PHP, Laravel, JavaScript)
- ✅ **Production ready** dengan monitoring dan logging

Sistem siap digunakan untuk mengintegrasikan multiple aplikasi dengan single sign-on yang aman dan mudah dikelola.
