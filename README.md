# Mautic Unosend SMS Plugin

Mautic SMS transport plugin for sending SMS messages via the [Unosend](https://www.unosend.co) API.

## Features

- Send SMS via Unosend with Bearer token authentication
- Configurable sender ID (phone number or alphanumeric)
- E.164 phone number normalization
- Full logging of send results including message ID and segment count

## Requirements

- Mautic 4.x or 5.x
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

Add the GitHub repository to your Mautic project's `composer.json`:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer config repositories.radata/mautic-unosend-sms vcs \
  https://github.com/radata/mautic-unosend-sms --no-interaction
```

Install the plugin:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer require radata/mautic-unosend-sms:dev-main -W --no-interaction
```

Update to the latest version:

```bash
docker exec --user www-data --workdir /var/www/html mautic_web \
  composer update radata/mautic-unosend-sms -W --no-interaction
```

### Manual Installation

1. Copy or symlink this plugin to `plugins/UnosendSmsBundle/` in your Mautic installation.
2. Clear the Mautic cache:
   ```bash
   bin/console cache:clear
   ```

### Post-Installation

1. Navigate to **Settings > Plugins** and click **Install/Upgrade Plugins**.
2. Find **Unosend SMS** and click **Configure**.

## Configuration

In the plugin settings:

| Field | Description |
|---|---|
| **API Key** | Your Unosend API key (starts with `un_`) |
| **Sender ID** | Phone number in E.164 format (e.g. `+18773987372`) or alphanumeric ID (e.g. `Unosend`). Leave empty for default. |
| **API URL** | Unosend API endpoint (default: `https://www.unosend.co`) |

After configuring, enable the plugin and select **Unosend SMS** as your SMS transport under **Settings > Configuration > SMS Settings**.

## API Reference

This plugin uses the Unosend SMS API:

- **Send SMS**: `POST /api/v1/sms` with JSON body `{ "to": "+1...", "body": "message", "from": "sender" }`
- **Authentication**: `Authorization: Bearer <api_key>`

See [Unosend documentation](https://www.unosend.co/docs) for full API details.

## License

MIT - see [LICENSE](LICENSE) for details.
