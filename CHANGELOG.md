# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [5.1.2] - 2024-09-07

### Fixed

- Fix password input when enforce password is enabled

### Changed

- Updated translations from transifex
- Updated dependencies

## [5.1.1] - 2024-08-03

### Changed

- Updated translations from transifex
- Updated dependencies

## [5.1.0] - 2024-05-04

### Added

- Support for Nextcloud 29

### Changed

- Updated translations from transifex
- Updated dependencies

## [5.0.0] - 2024-03-18

### Added

- Support for PHP 8.3
- Support for Nextcloud 28

### Removed

- Support for Nextcloud 27

### Changed

- Updated translations from transifex
- Updated dependencies

## [4.2.0] - 2023-10-06

### Changed

- Updated app description
- Updated translations from transifex
- Updated dependencies

## [4.1.0] - 2023-06-27

### Added

- Support for Nextcloud 27

### Changed

- Updated translations from transifex
- Updated dependencies

## [4.0.0] - 2023-06-05

### Added
- Support for PHP 8.2
- Support for Nextcloud 26

### Removed

- Support for PHP 7.4

### Fixed

- Error when moving/copying files in NC 26.0.2 ([#143](https://github.com/jimmyl0l3c/cfg_share_links/issues/143))

### Changed

- Updated translations from transifex
- Updated dependencies

## [3.0.2] - 2022-12-12

### Changed

- Downgrade [@nextcloud/vue](https://github.com/nextcloud/nextcloud-vue) to 7.0.1 (until next update)
- Use CustomListItem instead of NcListItem

### Fixed

- Creating of custom links on mobile devices

## [3.0.1] - 2022-12-11

### Changed

- Updated translations from transifex
- Updated dependencies
- Add 'Saving...' tooltip to loading icon in admin settings

## [2.1.1]

Backport from version 3.0.1

### Changed

- Updated translations from transifex
- Updated dependencies

## [3.0.0] - 2022-10-20

### Added

- Support for Nextcloud 25

### Removed

- Support for Nextcloud 22-24 (App for NC 23-24 will still be maintained for now)

### Changed

- Updated translations from transifex
- Updated to [@nextcloud/vue](https://github.com/nextcloud/nextcloud-vue) 7
- Migrated icons to [vue-material-design-icons](https://www.npmjs.com/package/vue-material-design-icons)
- NewLink: Changed `input` to `NcTextField`
- Custom label in Admin settings is visible only when label mode is set to use it
- Prevent sending another request while waiting for response

## [2.1.0] - 2022-10-20

Backport from version 3.0.0

### Removed

- Support for Nextcloud 22

### Changed

- Updated translations from transifex
- Custom label in Admin settings is visible only when label mode is set to use it
- Prevent sending another request while waiting for response

## [2.0.2] - 2022-10-17

### Changed

- Updated translations from transifex
- NewLink and RenameLink refactoring

## [2.0.1] - 2022-10-15

### Changed

- Updated translations from transifex
- Updated dependencies

### Fixed

- Ability to create custom shares with enforce password protection enabled
- Copying new link to clipboard when not using HTTPS

## [2.0.0] - 2022-05-09

### Added

- Support for PHP 8.1
- Support for Nextcloud 24

### Changed

- Updated translations from transifex
- Updated dependencies

### Removed

- Support for NC 20, 21
- Support for PHP 7.3

## [1.2.2] - 2022-04-13

### Changed

- Updated translations from transifex
- Updated dependencies


## [1.2.1] - 2022-03-29

### Changed

- Updated translations from transifex

## [1.2.0] - 2022-03-26

### Added

- cfg_shares table to store data about custom shares
- Settings option: to delete shares of deleted files during token checks (when creating/updating share)

### Changed

- Implemented IBootstrap
- Updated translations from transifex
- Updated [@nextcloud-vue](https://github.com/nextcloud/nextcloud-vue) to v5.2.1

### Fixed

- Delete custom shares when node (file/folder) is deleted

## [1.1.1] - 2022-03-22

### Changed

- Updated translations from transifex
- Updated [@nextcloud-vue](https://github.com/nextcloud/nextcloud-vue) to v5.1.1

### Fixed

- Translation text strings fixed

## [1.1.0] - 2022-03-12

### Added

- Auto-copy link when new link is created

### Changed

- Updated translation
- Updated info.xml
  - Translated description, name and summary to Czech
  - Added new thumbnail
  - Added files category

### Fixed

- Added permission check when updating tokens

## [1.0.3] - 2022-03-07

### Changed

- Updated app description (in info.xml)
- Submit NewLink input by pressing enter
- Added support for nextcloud 23

## [1.0.2] - 2022-03-03

### Changed

- Published to Nextcloud app store
- App_id and repository name changed to cfg_share_links (previously cfgsharelinks and cfg-share-links respectively)
- Updated several dev dependencies

## [1.0.1] - 2022-02-14

### Fixed

- Don't show NewLink component if user can't share the file

### Changed

- Use new forceDisplayActions property (for ListItem in NewLink component)
- Updated [@nextcloud-vue](https://github.com/nextcloud/nextcloud-vue) to v5.0.0
- Updated several dev dependencies

## [1.0.0] - 2022-01-06

### Added

- Initial release

[Unreleased]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v5.1.2...HEAD
[5.1.2]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v5.1.1...v5.1.2
[5.1.1]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v5.1.0...v5.1.1
[5.1.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v5.0.0...v5.1.0
[5.0.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v4.2.0...v5.0.0
[4.2.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v4.1.0...v4.2.0
[4.1.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v4.0.0...v4.1.0
[4.0.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v3.0.2...v4.0.0
[3.0.2]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v3.0.1...v3.0.2
[3.0.1]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v3.0.0...v3.0.1
[3.0.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v2.0.2...v3.0.0
[2.1.1]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v2.1.0...v2.1.1
[2.1.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v2.0.2...v2.1.0
[2.0.2]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v2.0.1...v2.0.2
[2.0.1]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.2.2...v2.0.0
[1.2.2]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.2.1...v1.2.2
[1.2.1]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.2.0...v1.2.1
[1.2.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.1.1...v1.2.0
[1.1.1]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.0.3...v1.1.0
[1.0.3]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/jimmyl0l3c/cfg_share_links/releases/tag/v1.0.0
