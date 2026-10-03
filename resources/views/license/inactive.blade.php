<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>License Inactive</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: #f5f7fa;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto,
                Helvetica, Arial, sans-serif;
            color: #212529;
        }

        .license-card {
            width: 100%;
            max-width: 520px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
        }

        .icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 24px;
            border-radius: 50%;
            background: #fff3cd;
            color: #856404;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        h1 {
            margin: 0 0 12px;
            font-size: 26px;
            font-weight: 600;
        }

        .message {
            margin: 0 auto 28px;
            max-width: 420px;
            color: #6c757d;
            line-height: 1.6;
        }

        .details {
            text-align: left;
            background: #f8f9fa;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 24px;
        }

        .detail {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 8px 0;
            font-size: 14px;
        }

        .detail + .detail {
            border-top: 1px solid #e9ecef;
        }

        .label {
            color: #6c757d;
        }

        .value {
            font-weight: 500;
            text-align: right;
            word-break: break-word;
        }

        .status {
            color: #dc3545;
            font-weight: 600;
        }

        .help {
            margin: 0;
            font-size: 13px;
            color: #6c757d;
            line-height: 1.6;
        }
    </style>
</head>

<body>

<div class="license-card">

    <div class="icon">
        !
    </div>

    <h1>License Inactive</h1>

    <p class="message">
        This application is currently unavailable because its license
        is not active.
    </p>

    <div class="details">

        <div class="detail">
            <span class="label">Application</span>
            <span class="value">
                {{ config('app.name', 'Application') }}
            </span>
        </div>

        <div class="detail">
            <span class="label">Status</span>
            <span class="value status">
                {{ $license['status'] ?? 'Inactive' }}
            </span>
        </div>

        @if (!empty($license['expires_at']))
            <div class="detail">
                <span class="label">License Expiry</span>
                <span class="value">
                    {{ $license['expires_at'] }}
                </span>
            </div>
        @endif

    </div>

    <p class="help">
        If you believe this is an error, please contact your
        application administrator or license provider.
    </p>

</div>

</body>
</html>
