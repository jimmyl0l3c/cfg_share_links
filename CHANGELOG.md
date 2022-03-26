# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- cfg_shares table to store data about custom shares

### Changed

- Implemented IBootstrap
- Updated translations from transifex

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

[Unreleased]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.1.1...HEAD
[1.1.1]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.0.3...v1.1.0
[1.0.3]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/jimmyl0l3c/cfg_share_links/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/jimmyl0l3c/cfg_share_links/releases/tag/v1.0.0
