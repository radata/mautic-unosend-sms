# Mautic Unosend SMS Plugin

Mautic SMS transport plugin for sending SMS messages via the [Unosend](https://www.unosend.co) API.

## Features

- Send SMS via Unosend with Bearer token authentication
- Configurable sender ID (phone number or alphanumeric)
- E.164 phone number normalization
- Full logging of send results including message ID and segment count

## Requirements

- Mautic 4.x, 5.x, 6.x or 7.x
- PHP 8.0+
- An Unosend account with API access

## Installation

### Via Composer (Docker)

Ensure the composer directories exist with correct permissions:

```bash
docker exec --user root mautic_web mkdir -p /var/www/.composer/cache
docker exec --user root mautic_web chown -R www-data:www-data /var/www/.composer
docker exec --user root mautic_web mkdir -p /var/www/.npm
docker exec --user root mautic_web chown -R www-data:www-data /var/www/.npm

```

Allow dev packages (only needed once per Mautic installation):

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config minimum-stability dev
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config prefer-stable true
```

Add the GitHub repository and install the plugin:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config repositories.mautic-unosend-sms vcs \
  https://github.com/radata/mautic-unosend-sms --no-interaction
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer require radata/mautic-unosend-sms:dev-main -W --no-interaction
```

Update to the latest version:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer update radata/mautic-unosend-sms -W --no-interaction
```

If the npm post-install hook fails after composer require, fix it:

```bash
docker exec --user root mautic_web rm -rf /var/www/html/node_modules
docker exec --user root mautic_web mkdir -p /var/www/.npm
docker exec --user root mautic_web chown -R www-data:www-data /var/www/.npm
docker exec --user www-data --workdir /var/www/html mautic_web npm ci --no-audit
```

### Manual Installation (docker cp)

```bash
docker cp plugins/UnosendSmsBundle mautic_web:/var/www/html/plugins/UnosendSmsBundle
docker exec --user root mautic_web chown -R www-data:www-data /var/www/html/plugins/UnosendSmsBundle
```

### Post-Installation

Clear cache (hard delete required), reload plugins, then enable in UI:

```bash
docker exec --user www-data mautic_web rm -rf /var/www/html/var/cache/prod
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console cache:warmup --env=prod
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console mautic:plugins:reload
```

1. Go to **Settings > Plugins > Unosend SMS**
2. Set **Published** to **Yes**
3. Enter your API Key and Sender ID in the settings
4. Go to **Settings > Configuration > SMS Settings** and select **Unosend SMS** as transport

## Configuration

In the plugin settings:

| Field | Description |
|---|---|
| **API Key** | Your Unosend API key (starts with `un_`) |
| **Sender ID** | Phone number in E.164 format (e.g. `+18773987372`) or alphanumeric ID (e.g. `Unosend`). Leave empty for default. |
| **API URL** | Unosend API endpoint (default: `https://www.unosend.co`) |

After configuring, enable the plugin and select **Unosend SMS** as your SMS transport under **Settings > Configuration > SMS Settings**.

## Plugin Structure

```
plugins/UnosendSmsBundle/
├── Config/config.php              # Service registration
├── Integration/
│   └── UnosendSmsIntegration.php  # Settings UI (API key, sender ID, API URL)
├── Transport/
│   ├── Configuration.php          # Reads credentials from integration settings
│   ├── ConfigurationException.php
│   └── UnosendTransport.php       # Sends SMS via POST /api/v1/sms
├── Translations/en_US/messages.ini
└── UnosendSmsBundle.php           # Bundle class
```

## API Reference

This plugin uses the Unosend SMS API:

- **Send SMS**: `POST /api/v1/sms` with JSON body `{ "to": "+1...", "body": "message", "from": "sender" }`
- **Authentication**: `Authorization: Bearer <api_key>`

See [Unosend documentation](https://www.unosend.co/docs) for full API details.

## Uninstall

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer remove radata/mautic-unosend-sms -W --no-interaction
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config --unset repositories.mautic-unosend-sms
docker exec --user www-data mautic_web rm -rf /var/www/html/var/cache/prod
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console cache:warmup --env=prod
docker exec --user www-data --workdir /var/www/html mautic_web php bin/console mautic:plugins:reload
```

## License

MIT - see [LICENSE](LICENSE) for details.
