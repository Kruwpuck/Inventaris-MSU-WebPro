<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Sedang Istirahat - Inventaris MSU</title>
    <link rel="icon" href="{{ asset('aset/logo.png') }}" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    
    <style>
        :root {
            --green-1: #eaf6ee;
            --green-2: #9bd0a5;
            --green-3: #2e8b57;
            --green-4: #236e47;
            --teal: #0f6f6b;
            --teal-2: #0b5c58;
            --gold: #d6b15d;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--green-1);
            color: #121816;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background-image: radial-gradient(circle at 10% 20%, rgba(46, 139, 87, 0.05) 0%, transparent 20%),
                              radial-gradient(circle at 90% 80%, rgba(15, 111, 107, 0.05) 0%, transparent 20%);
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 600px;
        }
        .error-code {
            font-size: 8rem;
            font-weight: 800;
            line-height: 1;
            color: var(--green-1);
            position: relative;
            z-index: 1;
             /* Text stroke for the "transparent" look */
            -webkit-text-stroke: 4px var(--gold);
            opacity: 0.6;
        }
        .error-code span {
            color: var(--teal);
            text-shadow: 4px 4px 0px rgba(15, 111, 107, 0.2);
            -webkit-text-stroke: 0;
            opacity: 1;
        }
        .error-message {
            position: relative;
            z-index: 2;
            margin-top: -2rem;
        }
        .illustration {
            font-size: 5rem;
            margin-bottom: 1rem;
            color: var(--gold);
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
        .btn-home {
            padding: 0.75rem 2rem;
            font-weight: 700;
            border-radius: 50px;
            background: linear-gradient(135deg, var(--teal), var(--green-3));
            border: none;
            color: #fff;
            box-shadow: 0 8px 16px rgba(15, 111, 107, 0.2);
            transition: all 0.3s ease;
        }
        .btn-home:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(15, 111, 107, 0.3);
            color: #fff;
        }
        .btn-refresh {
            padding: 0.75rem 2rem;
            font-weight: 700;
            border-radius: 50px;
            border: 2px solid var(--teal);
            color: var(--teal);
            background: transparent;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }
        .btn-refresh:hover {
            background: var(--green-1);
            color: var(--teal-2);
            border-color: var(--teal-2);
        }
        .text-masjid {
            color: var(--teal-2);
        }
    </style>
</head>
<body>
    <div class="container error-container">
        <div class="mb-4">
            <img src="{{ asset('fe-guest/loogoo.png') }}" alt="Logo MSU" style="height: 70px; width: auto;">
        </div>
        <div class="illustration">
            <i class="bi bi-cone-striped"></i>
        </div>
        <div class="error-code">
            5<span>0</span>0
        </div>
        <div class="error-message">
            <h2 class="fw-bold mb-3 display-6 text-masjid">Sistem Sedang Istirahat</h2>
            <p class="text-secondary fs-5 mb-4">
                Server kami sedang lelah menangani inventaris. <br>
                Mohon tunggu sebentar, kami sedang membereskannya.
            </p>
            <button onclick="window.location.reload()" class="btn btn-refresh">
                <i class="bi bi-arrow-clockwise me-2"></i> Coba Lagi
            </button>
            <a href="{{ url('/') }}" class="btn btn-home">
                <i class="bi bi-house-door-fill me-2"></i> Kembali
            </a>
        </div>
    </div>
</body>
</html>
