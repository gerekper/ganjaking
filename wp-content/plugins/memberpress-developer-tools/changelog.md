# Changelog

## [1.1.41](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.41) - 2020-10-19

### Changed

- Update rules docs to show authorized access change
- Return ALL custom fields in the API

### Added

- Add access condition support for members, roles, and capabilities

## [1.1.39](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.39) - 2020-08-28

### Fixed

- Fix notice error in WP 5.5

## [1.1.38](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.38) - 2020-08-17

### Fixed

- Fix gateway API keys exposed

## [1.1.37](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.37) - 2020-08-12

### Changed

- Update MpdtCouponsApi.php

### Added

- Support coupon description field

## [1.1.36](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.36) - 2020-06-29

### Fixed

- Use correct column name

## [1.1.35](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.35) - 2020-05-12

### Fixed

- Fix setting autorized_memberships when creating and updating a Rule

### Added

- Support for setting the Rule title

## [1.1.34](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.34) - 2020-04-22

### Fixed

- Fix issue with username being an email different than the user's email

## [1.1.33](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.33) - 2020-04-09

### Fixed

- Fix preceding 0 on cc_exp_month

## [1.1.32](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.32) - 2020-03-24

### Fixed

- Fix query error when ANSI_QUOTES is enabled

### Changed

- Update get_authorization_header function

### Added

- Add use_on_upgrades coupon param

## [1.1.31](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.31) - 2020-02-12

### Changed

- Generate random coupon code if not provided
- Update built-in docs to use api key header

## [1.1.30](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.30) - 2020-01-22

### Fixed

- Added missing hook needed for autoresponders

## [1.1.29](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.29) - 2020-01-22

### Fixed

- Fix cancel subscription verbiage
- Skip sending webhooks to empty URLs

### Changed

- Update custom cancel and refund route verbiage

## [1.1.28](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.28) - 2020-01-07

### Fixed

- Handle no expires_at properly on txn
- Fix typo in webhook description

## [1.1.22](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.22) - 2019-05-13

Adds alternative HTTP header Authorization

## [1.1.21](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.21) - 2019-03-26

Adds docs for the Zapier integration.

### Added

- Documentation for API endpoints
- Only gen password on member create-NOT UPDATE

### Fixed

- Fixed notice on password not being set


## [1.1.20](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.20) - 2019-03-20

A major change that adds authorization by API key and Zapier integration.

### Added

- Authorization by API key
- Zapier integration

## [1.1.19](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.19) - 2019-02-12

Bug fix release to address duplicate signups.

### Fixed

- Fix duplicate mepr-signup action

## [1.1.18](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.18) - 2018-11-22

Minor change that adds new hooks for MPCA.

### Added

- New hooks for MP Corporate Accounts addon

### Changed

- Update to use new MP Password email type
- Update coupon importer docs

## [1.1.17](https://github.com/caseproof/memberpress-developer-tools/releases/tag/1.1.17) - 2018-09-05

A minor release that deals with a change for password reset.

### Changed

- Use MemberPress functions to send password resets rather than `wp_`
