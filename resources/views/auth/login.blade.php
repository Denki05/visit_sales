<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background:#f5f5f5;">

<div class="container d-flex align-items-center justify-content-center vh-100">

    <div class="card shadow w-100" style="max-width:400px;">
        <div class="card-body">

            <!-- <h4 class="text-center mb-4">Login Sales</h4> -->

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="/login">
                @csrf

                <div class="mb-3">
                    <input type="text" name="username" class="form-control" placeholder="Username" required autofocus>
                </div>

                <div class="mb-3">
                    <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>

                <button class="btn btn-primary w-100">
                    Login
                </button>

            </form>

        </div>
    </div>

</div>

</body>
</html>