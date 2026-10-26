<?php

/**
 * One-time KYC portal setup helper.
 * Delete this file from the server immediately after the setup succeeds.
 */

declare(strict_types=1);

header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate');

const SETUP_TOKEN = '3c91fad48562e7b0';

if (! isset($_GET['key']) || ! hash_equals(SETUP_TOKEN, (string) $_GET['key'])) {
    http_response_code(403);
    exit('Forbidden');
}

$candidates = [
    dirname(__DIR__),
    dirname(__DIR__, 2).'/kyc_portal',
    dirname(__DIR__, 2).'/kyc-portal',
];

$appBase = null;

foreach ($candidates as $candidate) {
    if (is_file($candidate.'/vendor/autoload.php') && is_file($candidate.'/bootstrap/app.php')) {
        $appBase = $candidate;
        break;
    }
}

if ($appBase === null) {
    http_response_code(500);
    exit('The portal application could not be found. Check the deployment path.');
}

$envPath = $appBase.'/.env';
$envExamplePath = $appBase.'/.env.example';
$defaultUrl = 'https://kijitonyamayouthchoir.co.tz/portal';
$errors = [];
$log = [];
$done = false;
$dbInfo = [];

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function envValue(string $contents, string $key): ?string
{
    if (! preg_match('/^'.preg_quote($key, '/').'=(.*)$/m', $contents, $matches)) {
        return null;
    }

    $value = trim($matches[1]);

    if ($value !== '' && ($value[0] === '"' || $value[0] === "'") && substr($value, -1) === $value[0]) {
        $quote = $value[0];
        $value = substr($value, 1, -1);
        if ($quote === '"') {
            $value = stripcslashes($value);
        }
    }

    return $value;
}

function envFormat(string $value): string
{
    if ($value === '') {
        return '';
    }

    if (preg_match('/^[A-Za-z0-9_.\-\/:@]+$/', $value)) {
        return $value;
    }

    if (strpos($value, "'") === false) {
        return "'".$value."'";
    }

    return '"'.str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $value).'"';
}

function setEnvValue(string $contents, string $key, string $value): string
{
    $line = $key.'='.envFormat($value);
    $pattern = '/^'.preg_quote($key, '/').'=.*$/m';

    if (preg_match($pattern, $contents)) {
        return preg_replace_callback($pattern, static fn (): string => $line, $contents, 1);
    }

    return rtrim($contents, "\r\n")."\r\n".$line."\r\n";
}

function writeEnvFile(string $path, string $contents): void
{
    if (file_put_contents($path, $contents) === false) {
        throw new RuntimeException('Could not write .env. Check file permissions on the app folder.');
    }

    @chmod($path, 0600);
}

// Make sure .env exists before we try to read or write settings.
if (! is_file($envPath) && is_file($envExamplePath)) {
    if (! copy($envExamplePath, $envPath)) {
        http_response_code(500);
        exit('Could not create .env from .env.example.');
    }
    @chmod($envPath, 0600);
}

$envContents = is_file($envPath) ? (string) file_get_contents($envPath) : '';

$existingKey = envValue($envContents, 'APP_KEY') ?? '';
$existingDbPassword = envValue($envContents, 'DB_PASSWORD') ?? '';

$form = [
    'name' => trim((string) ($_POST['name'] ?? '')),
    'email' => trim((string) ($_POST['email'] ?? '')),
    'phone' => trim((string) ($_POST['phone'] ?? '')),
    'password' => (string) ($_POST['password'] ?? ''),
    'app_url' => trim((string) ($_POST['app_url'] ?? (envValue($envContents, 'APP_URL') ?: $defaultUrl))),
    'db_host' => trim((string) ($_POST['db_host'] ?? (envValue($envContents, 'DB_HOST') ?: 'localhost'))),
    'db_port' => trim((string) ($_POST['db_port'] ?? (envValue($envContents, 'DB_PORT') ?: '3306'))),
    'db_database' => trim((string) ($_POST['db_database'] ?? (envValue($envContents, 'DB_DATABASE') ?: 'kyc_portal'))),
    'db_username' => trim((string) ($_POST['db_username'] ?? (envValue($envContents, 'DB_USERNAME') ?? ''))),
    'db_password' => (string) ($_POST['db_password'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($form['name'] === '') {
        $errors[] = 'Administrator name is required.';
    }

    if ($form['email'] === '' && $form['phone'] === '') {
        $errors[] = 'Provide an email address or a phone number for the administrator.';
    }

    if ($form['email'] !== '' && ! filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'The administrator email address is not valid.';
    }

    if (strlen($form['password']) < 12 || ! preg_match('/[a-z]/', $form['password']) || ! preg_match('/[A-Z]/', $form['password']) || ! preg_match('/[0-9]/', $form['password']) || ! preg_match('/[^A-Za-z0-9]/', $form['password'])) {
        $errors[] = 'The administrator password must be at least 12 characters and include upper case, lower case, a number and a symbol.';
    }

    if ($form['app_url'] === '' || ! filter_var($form['app_url'], FILTER_VALIDATE_URL)) {
        $errors[] = 'The portal URL is not valid. Example: https://kijitonyamayouthchoir.co.tz/portal';
    }

    if ($form['db_host'] === '') {
        $errors[] = 'Database host is required.';
    }

    if (! ctype_digit($form['db_port']) || (int) $form['db_port'] < 1 || (int) $form['db_port'] > 65535) {
        $errors[] = 'Database port must be a number between 1 and 65535.';
    }

    if ($form['db_database'] === '') {
        $errors[] = 'Database name is required.';
    }

    if ($form['db_username'] === '') {
        $errors[] = 'Database username is required.';
    }

    $dbPassword = $form['db_password'] !== '' ? $form['db_password'] : $existingDbPassword;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $errors === []) {
    try {
        $appKey = $existingKey !== '' ? $existingKey : 'base64:'.base64_encode(random_bytes(32));
        $publicPath = str_replace('\\', '/', __DIR__);
        $appUrl = rtrim($form['app_url'], '/');

        $envContents = setEnvValue($envContents, 'APP_KEY', $appKey);
        $envContents = setEnvValue($envContents, 'PUBLIC_PATH', $publicPath);
        $envContents = setEnvValue($envContents, 'APP_URL', $appUrl);
        $envContents = setEnvValue($envContents, 'ASSET_URL', $appUrl);
        $envContents = setEnvValue($envContents, 'DB_CONNECTION', 'mysql');
        $envContents = setEnvValue($envContents, 'DB_HOST', $form['db_host']);
        $envContents = setEnvValue($envContents, 'DB_PORT', $form['db_port']);
        $envContents = setEnvValue($envContents, 'DB_DATABASE', $form['db_database']);
        $envContents = setEnvValue($envContents, 'DB_USERNAME', $form['db_username']);
        $envContents = setEnvValue($envContents, 'DB_PASSWORD', $dbPassword);

        writeEnvFile($envPath, $envContents);

        // A stale cached config would keep the old database settings.
        @unlink($appBase.'/bootstrap/cache/config.php');

        require $appBase.'/vendor/autoload.php';

        /** @var Illuminate\Foundation\Application $app */
        $app = require $appBase.'/bootstrap/app.php';
        $app->usePublicPath(__DIR__);

        $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
        $kernel->bootstrap();

        $dbConnection = (string) config('database.default');
        $dbConfig = (array) config('database.connections.'.$dbConnection, []);
        $dbInfo = [
            'driver' => (string) ($dbConfig['driver'] ?? $dbConnection),
            'host' => (string) ($dbConfig['host'] ?? ''),
            'database' => (string) ($dbConfig['database'] ?? ''),
            'username' => (string) ($dbConfig['username'] ?? ''),
        ];

        // Fail early with a clear message if the database is not ready.
        $app->make('db')->connection()->getPdo();

        $run = static function (string $label, callable $callback) use (&$log): void {
            try {
                $result = $callback();
                $log[] = ['label' => $label, 'ok' => true, 'output' => trim((string) $result)];
            } catch (Throwable $e) {
                $log[] = ['label' => $label, 'ok' => false, 'output' => $e->getMessage()];
                throw $e;
            }
        };

        $run('Create storage link', function () use ($kernel): string {
            $link = __DIR__.'/storage';
            $target = storage_path('app/public');

            if (! is_dir($target) && ! mkdir($target, 0755, true) && ! is_dir($target)) {
                throw new RuntimeException('Could not create storage/app/public.');
            }

            $isCorrectLink = static function () use ($link, $target): bool {
                clearstatcache(true, $link);

                return is_link($link) || (file_exists($link) && realpath($link) === realpath($target));
            };

            if ($isCorrectLink()) {
                return 'Already present.';
            }

            if (is_dir($link) && ! is_link($link)) {
                $entries = array_values(array_diff(scandir($link) ?: [], ['.', '..']));
                if ($entries !== []) {
                    throw new RuntimeException('public/storage exists as a real folder and is not empty. Remove or rename it, then run setup again so the symlink can be created.');
                }
                rmdir($link);
            } elseif (file_exists($link)) {
                @unlink($link);
            }

            $kernel->call('storage:link');
            $output = trim($kernel->output());

            if (! $isCorrectLink()) {
                throw new RuntimeException(($output !== '' ? $output.' ' : '').'The storage link could not be created. Create it manually in cPanel with target storage/app/public and link public/storage.');
            }

            return $output !== '' ? $output : 'Created.';
        });

        $run('Run database migrations', function () use ($kernel): string {
            $kernel->call('migrate', ['--force' => true]);

            return $kernel->output();
        });

        $run('Seed roles, settings and sample events', function () use ($kernel): string {
            $kernel->call('db:seed', ['--force' => true]);

            return $kernel->output();
        });

        $run('Create System Admin account', function () use ($app, $form): string {
            $normalizedEmail = $form['email'] !== '' ? mb_strtolower($form['email']) : null;
            $normalizedPhone = $form['phone'] !== '' ? $form['phone'] : null;

            $existing = App\Models\User::withTrashed()
                ->when($normalizedEmail, fn ($query) => $query->where('email', $normalizedEmail))
                ->when($normalizedPhone, fn ($query) => $query->where('phone', $normalizedPhone))
                ->first();

            if ($existing) {
                return 'An account with that email or phone already exists. Skipped.';
            }

            $users = $app->make(App\Services\UserManager::class);
            $user = $users->create([
                'name' => $form['name'],
                'email' => $normalizedEmail,
                'phone' => $normalizedPhone,
                'password' => $form['password'],
                'is_active' => true,
                'must_change_password' => true,
            ], [App\Enums\RoleName::SystemAdmin->value]);

            $app->make(App\Services\AuditLogger::class)->log(
                App\Enums\AuditEvent::AdminBootstrapped,
                $user,
                "Bootstrapped System Admin account for {$user->name}.",
                [],
                ['email' => $user->email, 'phone' => $user->phone],
                $user,
            );

            $login = $user->email ?: $user->phone;

            return 'Created System Admin "'.$user->name.'" with login '.$login.'. Password change is required at first sign-in.';
        });

        $run('Clear application caches', function () use ($kernel): string {
            $output = '';
            foreach (['config:clear', 'route:clear', 'view:clear', 'cache:clear'] as $command) {
                $kernel->call($command);
                $output .= $kernel->output();
            }

            return $output;
        });

        $done = true;
    } catch (Throwable $e) {
        $errors[] = $e->getMessage();
    }
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>KYC Portal Setup</title>
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, sans-serif; background: #f4f6f8; color: #14213d; margin: 0; padding: 32px 16px; }
        .wrap { max-width: 820px; margin: 0 auto; }
        .card { background: #fff; border-radius: 12px; padding: 28px; box-shadow: 0 10px 30px rgba(20, 33, 61, .08); }
        h1 { margin: 0 0 8px; font-size: 24px; }
        h2 { margin: 28px 0 4px; font-size: 17px; }
        p { line-height: 1.55; }
        .muted { color: #5c677d; }
        label { display: block; font-weight: 600; margin: 16px 0 6px; }
        input { width: 100%; box-sizing: border-box; padding: 12px 14px; border: 1px solid #c8d0dc; border-radius: 8px; font-size: 15px; }
        button { margin-top: 22px; background: #14213d; color: #fff; border: 0; border-radius: 8px; padding: 13px 20px; font-size: 15px; font-weight: 600; cursor: pointer; }
        .alert { padding: 14px 16px; border-radius: 8px; margin: 16px 0; }
        .alert--error { background: #fdecec; color: #8a1c1c; }
        .alert--ok { background: #e8f7ee; color: #1b6b3a; }
        .steps { margin: 16px 0 0; padding: 0; list-style: none; }
        .steps li { padding: 10px 0; border-bottom: 1px solid #eef1f5; }
        .steps li:last-child { border-bottom: 0; }
        .ok { color: #1b6b3a; font-weight: 700; }
        .fail { color: #8a1c1c; font-weight: 700; }
        pre { white-space: pre-wrap; background: #f7f9fb; padding: 10px; border-radius: 6px; font-size: 12px; }
        code { background: #f1f3f7; padding: 2px 5px; border-radius: 4px; }
        .grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0 18px; }
        @media (max-width: 640px) { .grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<div class="wrap">
    <div class="card">
        <h1>KYC Portal Setup</h1>

        <?php if ($done): ?>
            <div class="alert alert--ok">
                <strong>Setup completed.</strong><br>
                Delete <code><?= h(basename(__FILE__)) ?></code> from the server now.
            </div>
            <ul class="steps">
                <?php foreach ($log as $item): ?>
                    <li>
                        <span class="<?= $item['ok'] ? 'ok' : 'fail' ?>"><?= $item['ok'] ? 'OK' : 'FAIL' ?></span>
                        &mdash; <?= h($item['label']) ?>
                        <?php if ($item['output'] !== ''): ?>
                            <pre><?= h($item['output']) ?></pre>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            <p class="muted">Sign in at <a href="login"><?= h($form['app_url']) ?>/login</a> using the administrator account you just created. You will be asked to change the temporary password.</p>
            <p class="muted">Email is currently set to <code>log</code>. Configure SMTP in <code>.env</code> when you want password-reset emails to be delivered.</p>
        <?php else: ?>
            <?php if ($errors !== []): ?>
                <div class="alert alert--error">
                    <strong>Setup could not complete.</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= h($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if ($log !== []): ?>
                <ul class="steps">
                    <?php foreach ($log as $item): ?>
                        <li>
                            <span class="<?= $item['ok'] ? 'ok' : 'fail' ?>"><?= $item['ok'] ? 'OK' : 'FAIL' ?></span>
                            &mdash; <?= h($item['label']) ?>
                            <?php if ($item['output'] !== ''): ?>
                                <pre><?= h($item['output']) ?></pre>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <p class="muted">Create the MySQL database and database user in cPanel first, then enter the details below. This tool writes <code>.env</code>, runs migrations, seeds roles/settings, creates the storage link and creates the first System Admin account.</p>

            <p class="muted">App folder: <code><?= h($appBase) ?></code> &middot; Public folder: <code><?= h(__DIR__) ?></code></p>

            <form method="post" action="">
                <h2>1. Portal</h2>
                <label for="app_url">Portal URL</label>
                <input id="app_url" name="app_url" type="url" required value="<?= h($form['app_url']) ?>">

                <h2>2. Database</h2>
                <div class="grid">
                    <div>
                        <label for="db_host">Host</label>
                        <input id="db_host" name="db_host" type="text" required value="<?= h($form['db_host']) ?>">
                    </div>
                    <div>
                        <label for="db_port">Port</label>
                        <input id="db_port" name="db_port" type="number" min="1" max="65535" required value="<?= h($form['db_port']) ?>">
                    </div>
                    <div>
                        <label for="db_database">Database name</label>
                        <input id="db_database" name="db_database" type="text" required value="<?= h($form['db_database']) ?>">
                    </div>
                    <div>
                        <label for="db_username">Database username</label>
                        <input id="db_username" name="db_username" type="text" required value="<?= h($form['db_username']) ?>">
                    </div>
                </div>
                <label for="db_password">Database password</label>
                <input id="db_password" name="db_password" type="password" autocomplete="new-password" placeholder="<?= $existingDbPassword !== '' ? 'Leave blank to keep the current password' : '' ?>">

                <h2>3. First System Admin</h2>
                <label for="name">Full name</label>
                <input id="name" name="name" type="text" required value="<?= h($form['name']) ?>">

                <div class="grid">
                    <div>
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" value="<?= h($form['email']) ?>">
                    </div>
                    <div>
                        <label for="phone">Phone number</label>
                        <input id="phone" name="phone" type="text" value="<?= h($form['phone']) ?>">
                    </div>
                </div>
                <p class="muted">Provide at least an email address or a phone number.</p>

                <label for="password">Temporary password</label>
                <input id="password" name="password" type="password" required minlength="12" autocomplete="new-password">
                <p class="muted">At least 12 characters with upper case, lower case, a number and a symbol.</p>

                <button type="submit">Run setup</button>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
