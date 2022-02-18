# Linked Custom Fields Plugin Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/)
specification.

--------------------------------------------------------------------------------

## [Unreleased]

### Added

- Support for MantisBT 2.x
- In Edit Links page, add buttons to 
  - clear all selected target values
  - revert changes to selected values

### Changed

- Plugin files moved to repository root
- Moved JavaScript code to separate files
- Use MantisBT REST API to retrieve data from JavaScript
- Updated French translation

### Removed

- Support for MantisBT 1.x


## [1.0.1] - 2015-07-16

### Added

- French translation
- Russian translation

### Changed

- Only display custom fields of supported types
  [#1](https://github.com/mantisbt-plugins/LinkedCustomFields/pull/1)
- Increase size of custom_field_value and target_field_values columns 
  [#15549](https://mantisbt.org/bugs/view.php?id=15549)
- Bump jQuery requirement to 1.8


## [1.0] - 2011-09-22

### Added

- Initial release


[Unreleased]: https://github.com/mantisbt-plugins/LinkedCustomFields/compare/v1.0.1...master

[1.0.1]: https://github.com/mantisbt-plugins/LinkedCustomFields/compare/v1.0.0...v1.0.1
[1.0]: https://github.com/mantisbt-plugins/LinkedCustomFields/compare/1b2a1482f931ae21b5cdad7e710b8c8a574b3915...v1.0.0
