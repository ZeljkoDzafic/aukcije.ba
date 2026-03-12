<!DOCTYPE html>
<html lang="bs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <style>
        /* Base Styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #f3f4f6;
            color: #1f2937;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        
        .email-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .email-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 32px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .email-header {
            text-align: center;
            padding-bottom: 24px;
            border-bottom: 2px solid #e5e7eb;
            margin-bottom: 24px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: 800;
            color: #2563eb;
            text-decoration: none;
        }
        
        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #111827;
            margin-bottom: 16px;
        }
        
        .content {
            font-size: 16px;
            color: #4b5563;
            margin-bottom: 24px;
        }
        
        .info-box {
            background: #f9fafb;
            border-left: 4px solid #2563eb;
            padding: 16px;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .info-row:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 500;
            color: #6b7280;
        }
        
        .info-value {
            font-weight: 600;
            color: #111827;
        }
        
        .btn {
            display: inline-block;
            background: #2563eb;
            color: #ffffff;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            font-weight: 600;
            text-align: center;
            margin: 20px 0;
        }
        
        .btn:hover {
            background: #1d4ed8;
        }
        
        .btn-secondary {
            background: #6b7280;
        }
        
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin: 20px 0;
        }
        
        .alert-warning {
            background: #fef3c7;
            border: 1px solid #f59e0b;
            color: #92400e;
        }
        
        .alert-success {
            background: #d1fae5;
            border: 1px solid #10b981;
            color: #065f46;
        }
        
        .alert-error {
            background: #fee2e2;
            border: 1px solid #ef4444;
            color: #991b1b;
        }
        
        .footer {
            text-align: center;
            padding-top: 24px;
            border-top: 2px solid #e5e7eb;
            margin-top: 24px;
            font-size: 14px;
            color: #9ca3af;
        }
        
        .social-links {
            margin: 16px 0;
        }
        
        .social-links a {
            display: inline-block;
            margin: 0 8px;
            color: #6b7280;
            text-decoration: none;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }
        
        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }
        
        .badge-danger {
            background: #fee2e2;
            color: #991b1b;
        }
        
        @media only screen and (max-width: 600px) {
            .email-container {
                padding: 10px;
            }
            
            .email-card {
                padding: 20px;
            }
            
            .info-row {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-card">
            {{-- Header with logo --}}
            <div class="email-header">
                <a href="{{ config('app.url') }}" class="logo">🔨 Aukcije.ba</a>
            </div>
            
            {{-- Email content --}}
            @yield('content')
            
            {{-- Footer --}}
            <div class="footer">
                <div class="social-links">
                    <a href="{{ config('app.url') }}/facebook">Facebook</a>
                    <a href="{{ config('app.url') }}/instagram">Instagram</a>
                    <a href="{{ config('app.url') }}/twitter">Twitter</a>
                </div>
                <p>
                    © {{ date('Y') }} Aukcije.ba. Sva prava zadržana.<br>
                    Zmaja od Bosne 1, 71000 Sarajevo, Bosna i Hercegovina
                </p>
                <p>
                    <a href="{{ config('app.url') }}/preferences">Upravljaj obavijestima</a> |
                    <a href="{{ config('app.url') }}/help">Pomoć</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
