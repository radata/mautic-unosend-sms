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

1. Copy or symlink this plugin to `plugins/UnosendSmsBundle/` in your Mautic installation.
2. Clear the Mautic cache:
   ```bash
   bin/console cache:clear
   ```
3. Navigate to **Settings > Plugins** and click **Install/Upgrade Plugins**.
4. Find **Unosend SMS** and click **Configure**.

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
