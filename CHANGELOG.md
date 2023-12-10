# Linked Custom Fields Plugin Change Log

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/)
specification.

--------------------------------------------------------------------------------

## [2.0.1] - 2023-12-10

### Security

- XSS in linked Custom Field's values
  [#10](https://github.com/mantisbt-plugins/LinkedCustomFields/issue/10),
  [#11](https://github.com/mantisbt-plugins/LinkedCustomFields/issue/11)


## [2.0.0] - 2022-05-08

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

### Fixed

- Mapping section is not displayed
  [#4](https://github.com/mantisbt-plugins/LinkedCustomFields/pull/4)
- Linked values do not work for multiselection list custom fields
  [#5](https://github.com/mantisbt-plugins/LinkedCustomFields/pull/5)
- Linking not working for custom fields containing double-quotes
  [#6](https://github.com/mantisbt-plugins/LinkedCustomFields/pull/6)

### Security

- Sanitize field names and values
  [#7](https://github.com/mantisbt-plugins/LinkedCustomFields/pull/7)


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


[2.0.1]: https://github.com/mantisbt-plugins/LinkedCustomFields/compare/v2.0.0...v2.0.1
[2.0.0]: https://github.com/mantisbt-plugins/LinkedCustomFields/compare/v1.0.1...2.0.0
[1.0.1]: https://github.com/mantisbt-plugins/LinkedCustomFields/compare/v1.0.0...v1.0.1
[1.0]: https://github.com/mantisbt-plugins/LinkedCustomFields/compare/1b2a1482f931ae21b5cdad7e710b8c8a574b3915...v1.0.0
