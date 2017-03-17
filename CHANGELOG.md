# Changelog

All Notable changes to `kevintweber/gauges` will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [1.0] - 2017-03-16

### Updated
- Minimum PHP version is 7.0.  If you need a lower version
  of PHP, please use a previous version of this library.
- Changed all methods from snake_case to camelCase.
  Ex: create_gauge -> createGauge
- Date parameters will now accept both string and \DateTime objects.

## [0.4] - 2017-03-16

### Added
- group parameter to `content` API call. (Thanks to David Clarke)
- `browserstats` API call.

### Updated
- Minimum PHP version is 5.6
- Updated dependencies to allow PHP7.
- Updated unit tests to use new PHPUnit class structure.

## [0.3.1] - 2016-06-18

### Updated
- Guzzle MessageFormatter default format should use Apache Common Log Format (CLF) instead of derpy format I put in.

## [0.3] - 2016-06-17

NOTE: CONTAINS BC CHANGES.

### Updated
- Updated guzzle to ~6.0
