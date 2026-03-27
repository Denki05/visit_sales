<!DOCTYPE html>
<html>
<head>
    <title>Sales Visit</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #e9ecef;
            margin: 0;
        }

        .app-wrapper {
            max-width: 768px; /* tablet */
            margin: auto;
            background: #fff;
            min-height: 100vh;
            position: relative;
            padding-bottom: 80px;
        }

        /* HEADER */
        .app-header {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
            border-bottom: 1px solid #eee;
            padding: 10px;
        }

        /* BOTTOM NAV */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;

            background: #fff;
            border-top: 1px solid #ddd;

            display: flex;
            justify-content: space-around;
            padding: 8px 0;

            box-shadow: 0 -2px 10px rgba(0,0,0,0.08);
            z-index: 20;
        }

        .bottom-nav a {
            text-align: center;
            font-size: 11px;
            color: #888;
            text-decoration: none;
        }

        .bottom-nav a.active {
            color: #0d6efd;
            font-weight: bold;
        }

        .fab {
            position: fixed;
            bottom: 70px;
            left: 50%;
            transform: translateX(-50%);

            width: 55px;
            height: 55px;
            background: #0d6efd;
            color: white;
            border-radius: 50%;

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 26px;
            text-decoration: none;

            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 25;
        }

        @media (min-width: 768px) {
            .bottom-nav {
                max-width: 768px;
            }

            .fab {
                left: 50%;
                transform: translateX(-50%);
            }
        }
    </style>
</head>

<body>

<div class="app-wrapper">

    <!-- HEADER -->
    <div class="app-header d-flex justify-content-between align-items-center">
        <strong>Judul Apps</strong>
    </div>

    <!-- CONTENT -->
    @yield('content')

</div>

<!-- FLOATING BUTTON -->
<a href="/prospect/create" class="fab">+</a>

<!-- BOTTOM NAV -->
<div class="bottom-nav">
    <a href="/prospect" class="{{ request()->is('prospect') ? 'active' : '' }}">
        🏠<br>Home
    </a>

    <a href="#">
        📍<br>Visit
    </a>

    <a href="/profile" class="{{ request()->is('profile') ? 'active' : '' }}">
        👤<br>Profile
    </a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

@yield('scripts')

</body>
</html>