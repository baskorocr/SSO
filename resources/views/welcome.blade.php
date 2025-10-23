<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>SINTA - Sistem Integrasi Terpadu Akses</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }
            
            body {
                font-family: 'Inter', sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            
            .container {
                background: rgba(255, 255, 255, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
                padding: 40px 30px;
                max-width: 800px;
                width: 100%;
                border: 1px solid rgba(255, 255, 255, 0.2);
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
            
            .content-section {
                width: 100%;
                max-width: 600px;
            }
            
            .hero-section {
                width: 100%;
                display: flex;
                justify-content: center;
                align-items: center;
                margin: 30px 0;
            }
            
            .logo {
                width: 60px;
                height: 60px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                border-radius: 15px;
                margin: 0 auto 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
            }
            
            .logo svg {
                width: 30px;
                height: 30px;
                fill: white;
            }
            
            h1 {
                font-size: 3rem;
                font-weight: 700;
                color: #2d3748;
                margin-bottom: 10px;
                letter-spacing: -0.025em;
            }
            
            .subtitle {
                font-size: 1.2rem;
                color: #667eea;
                font-weight: 600;
                margin-bottom: 20px;
            }
            
            .description {
                font-size: 1.1rem;
                color: #718096;
                line-height: 1.6;
                margin-bottom: 30px;
            }
            
            .sinta-illustration {
                width: 100%;
                max-width: 250px;
                height: auto;
            }
            
            .sinta-illustration svg {
                width: 100%;
                height: auto;
            }
            
            .auth-buttons {
                display: flex;
                gap: 15px;
                margin: 30px 0 40px 0;
                flex-wrap: wrap;
                justify-content: center;
            }
            
            .btn {
                padding: 14px 32px;
                border-radius: 12px;
                text-decoration: none;
                font-weight: 600;
                font-size: 1rem;
                transition: all 0.3s ease;
                border: none;
                cursor: pointer;
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }
            
            .btn-primary {
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
            }
            
            .btn-primary:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
            }
            
            .btn-secondary {
                background: rgba(102, 126, 234, 0.1);
                color: #667eea;
                border: 2px solid rgba(102, 126, 234, 0.2);
            }
            
            .btn-secondary:hover {
                background: rgba(102, 126, 234, 0.15);
                border-color: rgba(102, 126, 234, 0.3);
                transform: translateY(-1px);
            }
            
            .features {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 20px;
                max-width: 100%;
                margin: 0 auto;
            }
            
            .feature {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 10px;
                padding: 20px 15px;
                background: rgba(102, 126, 234, 0.05);
                border-radius: 12px;
                border: 1px solid rgba(102, 126, 234, 0.1);
                text-align: center;
            }
            
            .feature-icon {
                width: 40px;
                height: 40px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                border-radius: 10px;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
            }
            
            .feature-icon svg {
                width: 20px;
                height: 20px;
                fill: white;
            }
            
            .feature-content h3 {
                font-size: 1rem;
                font-weight: 600;
                color: #2d3748;
                margin-bottom: 5px;
            }
            
            .feature-content p {
                font-size: 0.9rem;
                color: #718096;
                line-height: 1.4;
            }
            
            @media (min-width: 768px) {
                .container {
                    padding: 60px 40px;
                }
                
                .sinta-illustration {
                    max-width: 280px;
                }
            }
            
            @media (max-width: 480px) {
                .container {
                    padding: 30px 20px;
                }
                
                h1 {
                    font-size: 2.2rem;
                }
                
                .subtitle {
                    font-size: 1rem;
                }
                
                .description {
                    font-size: 1rem;
                
                .btn {
                    padding: 12px 24px;
                    font-size: 0.9rem;
                }
                
                .sinta-illustration {
                    max-width: 200px;
                }
                
                .features {
                    grid-template-columns: 1fr;
                    gap: 15px;
                }
                
                .feature {
                    flex-direction: row;
                    text-align: left;
                    gap: 15px;
                }
            }
        </style>
        
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const container = document.getElementById('sintaContainer');
                const leftEye = document.getElementById('leftEye');
                const rightEye = document.getElementById('rightEye');
                
                document.addEventListener('mousemove', function(e) {
                    if (!container || !leftEye || !rightEye) return;
                    
                    const rect = container.getBoundingClientRect();
                    const containerCenterX = rect.left + rect.width / 2;
                    const containerCenterY = rect.top + rect.height / 2;
                    
                    const mouseX = e.clientX;
                    const mouseY = e.clientY;
                    
                    const deltaX = mouseX - containerCenterX;
                    const deltaY = mouseY - containerCenterY;
                    
                    const distance = Math.sqrt(deltaX * deltaX + deltaY * deltaY);
                    const maxDistance = 300;
                    const eyeMovement = Math.min(distance / maxDistance, 1) * 3;
                    
                    const angle = Math.atan2(deltaY, deltaX);
                    const eyeX = Math.cos(angle) * eyeMovement;
                    const eyeY = Math.sin(angle) * eyeMovement;
                    
                    leftEye.style.transform = `translate(${eyeX}px, ${eyeY}px)`;
                    rightEye.style.transform = `translate(${eyeX}px, ${eyeY}px)`;
                });
            });
        </script>
    </head>
    <body>
        <div class="container">
            <!-- Content Section -->
            <div class="content-section">
                <!-- Logo -->
                <div class="logo">
                    <svg viewBox="0 0 24 24">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                    </svg>
                </div>
                
                <!-- Header -->
                <h1>SINTA</h1>
                <div class="subtitle">Sistem Integrasi Terpadu Akses</div>
                <p class="description">
                    Platform Single Sign-On (SSO) yang memungkinkan akses terpadu ke berbagai sistem dengan satu akun yang aman dan terpercaya.
                </p>
                
                <!-- Auth Buttons -->
                @if (Route::has('login'))
                    <div class="auth-buttons">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 13h8V3H3v10zm0 8h8v-6H3v6zm10 0h8V11h-8v10zm0-18v6h8V3h-8z"/>
                                </svg>
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-primary">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M11 7L9.6 8.4l2.6 2.6H2v2h10.2l-2.6 2.6L11 17l5-5-5-5zm9 12h-8v2h8c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2h-8v2h8v14z"/>
                                </svg>
                                Masuk
                            </a>
                            
                            @if (Route::has('register'))
                                <a href="https://requestit.dharmap.com/" class="btn btn-secondary">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M15 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm-9-2V7H4v3H1v2h3v3h2v-3h3v-2H6zm9 4c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                                    </svg>
                                    Requested Access
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
            
            <!-- Features in One Row -->
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>Keamanan Terjamin</h3>
                        <p>Sistem keamanan berlapis dengan enkripsi tingkat enterprise</p>
                    </div>
                </div>
                
                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>Akses Terpadu</h3>
                        <p>Akses semua aplikasi dengan satu kali login</p>
                    </div>
                </div>
                
                <div class="feature">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24">
                            <path d="M13 3c-4.97 0-9 4.03-9 9H1l3.89 3.89.07.14L9 12H6c0-3.87 3.13-7 7-7s7 3.13 7 7-3.13 7-7 7c-1.93 0-3.68-.79-4.94-2.06l-1.42 1.42C8.27 19.99 10.51 21 13 21c4.97 0 9-4.03 9-9s-4.03-9-9-9zm-1 5v5l4.28 2.54.72-1.21-3.5-2.08V8H12z"/>
                        </svg>
                    </div>
                    <div class="feature-content">
                        <h3>Proses Efisien</h3>
                        <p>Hemat waktu dengan proses otentikasi yang cepat</p>
                    </div>
                </div>
            </div>
            
            <!-- Developer Credit -->
            <div style="margin-top: 30px; text-align: center; font-size: 0.85rem; color: #718096; opacity: 0.8;">
                Develop by IT Development Dharma Group
            </div>
        </div>
    </body>
</html>
