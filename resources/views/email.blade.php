<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card { border-radius: 15px; box-shadow: 0 15px 35px rgba(0,0,0,0.1); width: 100%; max-width: 400px; border: none; }
        .card-header { background: linear-gradient(135deg, #667eea, #764ba2); color: white; border-radius: 15px 15px 0 0 !important; padding: 20px; text-align: center; }
        .btn-primary { background: linear-gradient(135deg, #667eea, #764ba2); border: none; padding: 12px; font-weight: 600; border-radius: 10px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Reset Password</h5>
            <small>Masukkan email terdaftar Anda</small>
        </div>
        <div class="card-body p-4">
            @if (session('status'))
                <div class="alert alert-success small">{{ session('status') }}</div>
            @endif
            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label small">Alamat Email</label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" required placeholder="example@gmail.com">
                    @error('email')
                        <span class="invalid-feedback"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
                    <a href="{{ route('login') }}" class="text-center mt-3 small text-muted">Kembali ke Login</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>