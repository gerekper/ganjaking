# Changelog

## [1.5.15](https://github.com/caseproof/memberpress-corporate/releases/tag/1.5.15) - 2020-10-21

### Changed

- Replace error_log calls with MeprUtils

## [1.5.14](https://github.com/caseproof/memberpress-corporate/releases/tag/1.5.14) - 2020-10-07

### Added

- Add 'mpca-find-by-uui' hook

## [1.5.12](https://github.com/caseproof/memberpress-corporate/releases/tag/1.5.12) - 2020-09-10

### Fixed

- Prevent xss attack

## [1.5.11](https://github.com/caseproof/memberpress-corporate/releases/tag/1.5.11) - 2020-08-28

### Fixed

- Do not send disabled emails
- Block parent Corporate user from being able to add themselves as a sub account

## [1.5.10](https://github.com/caseproof/memberpress-corporate/releases/tag/1.5.10) - 2020-08-12

### Added

- Add corporate name in welcome email

### Fixed

- Correct logic to get corporate user

## [1.5.9](https://github.com/caseproof/memberpress-corporate/releases/tag/1.5.9) - 2020-04-22

### Fixed

- Fix a display issue with the sub accounts table

## [1.5.8](https://github.com/caseproof/memberpress-corporate/releases/tag/1.5.8) - 2020-03-24

### Added

- Add ability to hide email address in error message
- Make confirming message translatable

## [1.5.7](https://github.com/caseproof/memberpress-corporate/releases/tag/1.5.7) - 2020-01-22

### Fixed

- Block parent from adding child account

### Changed

- Hide corporate account option if sub-account
- Remove MeprHooks calls

## [1.5.6](https://github.com/caseproof/memberpress-corporate/releases/tag/1.5.6) - 2019-10-19

### Fixed

- Fix notice errors on the manage sub accounts page

### Added

- Support Loco Translate

## [1.4.9](https://github.com/caseproof/memberpress-corporate/releases/tag/1.4.9) - 2018-10-09

Updates the copy-to-clipboard functionality and removes dependency from
MemberPress.

### Changes

- Removes dependency from MemberPress clipboard functionality
- Upgrades copy-to-clipboard feature

## [1.4.8](https://github.com/caseproof/memberpress-corporate/releases/tag/1.4.8) - 2018-09-11

This release fixes a bug related to the import of users with existing usernames.

### Fixed

- Restrict the update of existing users to the current corporation

## [1.4.7](https://github.com/caseproof/memberpress-corporate/compare/1.4.6...1.4.7) - 2018-08-15

This release fixes a bug with the signup URL

### Fixed

-  Users were being redirected to the home page when registering instead of the thank you page

## [1.4.6](https://github.com/caseproof/memberpress-corporate/compare/1.4.5...1.4.6) - 2018-05-01

This release fixes an error experienced by users with older versions of PHP and
improves signup email sending.

### Fixed

- Older versions of PHP were getting an error that is now fixed by this release.

### Changed

- We're now smarter about when we send signup emails to sub-accounts.

## [1.4.5](https://github.com/caseproof/memberpress-corporate/compare/1.4.4...1.4.5) - 2018-04-11

### Fixed

- Reverted change showing sub account active status

## [1.4.4](https://github.com/caseproof/memberpress-corporate/compare/1.4.3...1.4.4) - 2018-04-03

### Added

- New signup email
- Update the Account page for the corporate account user to be more friendly

### Fixed

- Don't associate corporate account with subscription when a 100% coupon is used
- Display corporate account owner's status properly
- Fix `full_name` method
- Problems syncing sub accounts when upgrading and downgrading


## [1.4.3](https://github.com/caseproof/memberpress-corporate/compare/1.4.2...1.4.3) - 2018-03-20

Minor bug fix release.

### Fixed

- Sub account status was being set to "confirmed" when it shouldn't
- A missing argument was causing WP Cron to fail to run and cause sub accounts
  to not sync after a renewal payment
- A warning message was being generated when accessing a expiring transaction

## [1.4.2](https://github.com/caseproof/memberpress-corporate/compare/1.4.1...1.4.2) - 2018-03-09

Another minor release that addresses two semi-urgent bugs.

### Fixed

- Older versions of PHP 5.x were causing a fatal error
- Remove an extra superfluous div causing problems with some users theme

## [1.4.1](https://github.com/caseproof/memberpress-corporate/compare/1.4.0...1.4.1) - 2018-03-06

This is a minor release that adds event tracking for changes to sub-accounts and
fixes a few bugs.

### Added

- Developers can now see events for when a sub-account is added or removed

### Fixed

- Prevent a parent account from editing the admin email via import
- All non-sub-accounts were showing as "parent"; now show as "non"
- Removed plugin's dependency on importer plugin. Now you won't get an error if
  it isn't installed
- Imported users now also get the "new welcome email" sent to them

## [1.4.0](https://github.com/caseproof/memberpress-corporate/compare/1.3.1...1.4.0) - 2018-02-13

This release adds additional functionality for dealing with sub-accounts.
Locating their parent and associating them.

### Added

- Ability to find corporate parent account via the sub-account
- Corporate account can now add existing sub-users

## [1.3.1](https://github.com/caseproof/memberpress-corporate/compare/1.3.0...1.3.1) - 2018-02-02

This is a minor fix dealing with when the welcome email is triggered.

### Fixed

- Send welcome email to sub accounts when they are associated with a parent

## [1.3.0](https://github.com/caseproof/memberpress-corporate/compare/1.2.0...1.3.0) - 2018-01-30

This release adds gives admins access to the Welcome email template so they can
now customize it however they like. It also fixes a couple of minor issues.

### Added

- Welcome email template

### Fixed

- Fixed an error when adding/removing sub account users
- Fixed an issue with user validation

## [1.2.0](https://github.com/caseproof/memberpress-corporate/compare/1.1.7...1.2.0) - 2018-01-24
### Added

- Existing users can be associated with a corporate/parent account. Useful when
  importing
- Sub users now get redirected to a Thank You page after signing up
- Enabled button text to be translated
- Limited scripts to only load on pages where they are needed to improve
  performance
- One-time renewals will now migrate all sub-accounts to the renewed
  subscription
- Admins can display first and last name fields in the sign up form
- Support signing in with email as username
- Update import error handling to be more robust and continue when it encounters
  an error
- Hide "Add Sub Account" and "Import" buttons when sub account limit is reached
- Add column in members list to indicate if member is a corporate user or a sub
  account user

### Fixed

- Missing jQuery warning in browser console
- Removed an errant `div` the caused display bugs for some themes
- Fixed translation issues
- Prevent empty value for sub account limit; default to zero (0)

## 1.1.7 - 2017-11-02

### Changed

- Bumped version

## 1.1.6 - 2017-11-02
### Fixed

- Minor syntax issue

## 1.1.5 - 2017-11-02
### Fixed

- Recurring payments did not have a corporate account id set before being stored

