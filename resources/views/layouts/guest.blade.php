<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — DIPRODA</title>

    <!-- Poppins Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #0F172B;
            --primary-dark: #0B1121;
            --accent: #FFA000;
            --accent-hover: #FF8F00;
            --bg: #FFFFFF;
            --border: #E2E8F0;
            --text-dark: #0F172B;
            --text-muted: #64748B;
        }
        * { box-sizing: border-box; font-family: 'Poppins', sans-serif; margin: 0; padding: 0; }
        body { background: #F8FAFC; color: var(--text-dark); display: flex; min-height: 100vh; align-items: center; justify-content: center; }
        
        .login-wrapper { display: flex; width: 900px; max-width: 100%; background: #fff; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid var(--border); overflow: hidden; }
        .login-branding { flex: 1; background: var(--primary); padding: 48px; color: #fff; display: flex; flex-direction: column; justify-content: space-between; }
        .login-branding h1 { font-size: 28px; font-weight: 700; letter-spacing: 0.02em; margin-bottom: 8px; }
        .login-branding h1 span { color: var(--accent); }
        .login-branding p { font-size: 13px; color: rgba(255,255,255,0.6); line-height: 1.6; }
        .login-branding .sop-text { font-size: 11px; color: rgba(255,255,255,0.3); margin-top: auto; letter-spacing: 0.05em; text-transform: uppercase; }
        
        .login-form-container { flex: 1; padding: 48px; display: flex; flex-direction: column; justify-content: center; }
        .login-form-container h2 { font-size: 20px; font-weight: 600; margin-bottom: 24px; color: var(--primary); }
        
        .form-group { margin-bottom: 16px; }
        .form-label { display: block; font-size: 12px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.04em; }
        .form-control { width: 100%; border: 1px solid var(--border); border-radius: 2px; padding: 10px 12px; font-size: 13px; background: #fff; transition: all 0.15s; }
        .form-control:focus { outline: none; border-color: var(--accent); box-shadow: 0 0 0 2px rgba(255,160,0,0.1); }
        
        .btn-primary { background: var(--accent); color: #fff; border: none; padding: 12px 20px; font-size: 14px; font-weight: 600; border-radius: 2px; cursor: pointer; transition: background 0.15s; width: 100%; margin-top: 12px; }
        .btn-primary:hover { background: var(--accent-hover); }
        
        .checkbox-container { display: flex; align-items: center; gap: 8px; margin-top: 12px; margin-bottom: 24px; }
        .checkbox-container input[type="checkbox"] { accent-color: var(--accent); width: 16px; height: 16px; }
        .checkbox-container label { font-size: 12px; color: var(--text-muted); cursor: pointer; }
        
        .error-message { color: #C62828; font-size: 11px; margin-top: 4px; font-weight: 500; }
        
        @media (max-width: 768px) {
            .login-wrapper { flex-direction: column; width: 100%; margin: 20px; }
            .login-branding { padding: 32px; }
            .login-form-container { padding: 32px; }
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-branding">
            <div>
                <h1>DIPRO<span>DA</span></h1>
                <p>Digitalisasi Proyek Daerah. Silakan masuk untuk mengakses modul pelaporan dan pengawasan.</p>
            </div>
            <div class="sop-text">SOP/UPM/DJBM-113 Rev:01</div>
        </div>
        <div class="login-form-container">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
