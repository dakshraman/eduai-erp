<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>EduAI - License Bypass</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; background: #f8fafc; }
        .card { background: white; padding: 3rem; border-radius: 12px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); text-align: center; }
        .spinner { width: 40px; height: 40px; border: 4px solid #e2e8f0; border-top: 4px solid #6366f1; border-radius: 50%; animation: spin 0.8s linear infinite; margin: 0 auto 1.5rem; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="card">
        <div class="spinner"></div>
        <p>Bypassing license verification...</p>
    </div>
    <form id="bypassForm" method="POST" action="{{ url('/install/license') }}">
        @csrf
        <input type="hidden" name="access_code" value="eduai-bypass-{{ md5(time()) }}">
        <input type="hidden" name="envato_email" value="admin@eduai.com">
        <input type="hidden" name="installed_domain" value="{{ request()->getHost() }}">
    </form>
    <script>document.getElementById('bypassForm').submit();</script>
</body>
</html>
