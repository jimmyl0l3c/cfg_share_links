# ⚠️ Archiving repo ⚠️

Specifying custom share tokens was added in Nextcloud 31 (Hub 10) ([issue details](https://github.com/nextcloud/server/issues/45440)) so this app is no longer needed.
There will be no future updates to this app.

# Configurable Share Links

App for Nextcloud that adds the ability to specify share tokens when creating new public links and change tokens of existing links.

This app was built using [tutorial notes app](https://github.com/nextcloud/app-tutorial) as a skeleton.

[<img src="https://img.shields.io/github/downloads/jimmyl0l3c/cfg_share_links/total?color=blue&style=flat-square">](https://github.com/jimmyl0l3c/cfg_share_links/releases)
[<img src="https://img.shields.io/github/v/release/jimmyl0l3c/cfg_share_links?color=c7ee00&style=flat-square">](https://github.com/jimmyl0l3c/cfg_share_links/releases/latest)
[<img src="https://img.shields.io/github/release-date/jimmyl0l3c/cfg_share_links?color=c7ee00&style=flat-square">](https://github.com/jimmyl0l3c/cfg_share_links/releases/latest)

# Usage

You can get this app directly from [nextcloud app store](https://apps.nextcloud.com/apps/cfg_share_links).

## Manual

- Download the [gzip archive from the latest release](https://github.com/jimmyl0l3c/cfg_share_links/releases/latest/download/cfg_share_links.tar.gz)
- Extract the folder **cfg_share_links** into your NC **apps** folder
- Enable **Configurable Share Links** in apps (in Nextcloud)

# Build frontend from source

- Run `make dev-setup` to install frontend dependencies
- Run `make build-js` or `make build-js-production` to build

# Preview

Sharing sidebar is expanded by new section (Custom public link) that allows you to create public links with custom share tokens as can be seen in the first screenshot.

![Sidebar preview](screens/nc02.png "Sidebar preview")

You can also customize share tokens of existing public links as can be seen in the second screenshot.

![Sidebar preview](screens/nc03.png "Sidebar preview")

Admins can set default labels for custom links and minimal token length as can be seen in the next screenshot.
Default label can be: none, same as token or custom (the same custom label for all custom links).

![Admin settings preview](screens/nc01.png "Admin settings preview")

Since version [2.0.1](https://github.com/jimmyl0l3c/cfg_share_links/releases/tag/v2.0.1) you can create custom links when **Enforce password protection** is enabled as can be seen in the screenshot below.

![Sidebar with enforce password preview](screens/nc04.png "Sidebar with enforce password preview")
