# YourCompany Laravel License — Hardened v2

Production-oriented Laravel 12 client for a remote license server.

## Security model
- Ed25519 signed license responses.
- Private signing key exists only on the license server.
- Installation-specific bearer token for refresh/deactivate requests.
- Server-side installation limit and installation revocation.
- Exact or wildcard domain binding.
- Signed response freshness and lease expiry.
- Contractual expiry is enforced immediately; offline grace only covers temporary connectivity loss.
- Local state is encrypted with the application's `APP_KEY`.
- Middleware blocks protected application routes when the cached signed lease is invalid.
- The package never deletes application files or database data.

## Install
Install from your private Composer repository, then:

```bash
php artisan vendor:publish --tag=license-config
php artisan license:activate
php artisan license:status
```

`.env`:

```dotenv
LICENSE_SERVER=https://license.example.com
LICENSE_KEY=LIC-...
LICENSE_DOMAIN=client.example.com
LICENSE_ENVIRONMENT=production
LICENSE_SIGNING_PUBLIC_KEY=BASE64_PUBLIC_KEY
LICENSE_FAIL_CLOSED=true
LICENSE_MIDDLEWARE_ENABLED=true
```

Apply `license` middleware to the application routes that require an active license. Do not put it on health checks, login/bootstrap endpoints, or the package's own license endpoints.

## Scheduler
Run `php artisan license:refresh` from the scheduler at least every 12 hours (or more frequently if your contract requires faster revocation propagation). The local signed lease is the offline grace mechanism.

## Important limitation
If the client has root/SSH access to the server, no PHP package can make its source or runtime state absolutely inaccessible to that client. This package provides licensing enforcement and distribution control; it is not a DRM system.
