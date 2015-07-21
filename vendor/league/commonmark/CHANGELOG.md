# Change Log
All notable changes to this project will be documented in this file.
Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## [Unreleased][unreleased]
### Added
 - Added public $data array to block elements (#95)

### Changed
 - Renamed ListBlock::$data and ListItem::$data to $listData

## [0.8.0] - 2015-04-29
### Added
 - Allow swapping built-in renderers without using their fully qualified names (#84)
 - Lots of unit tests (for existing code)
 - Ability to include arbitrary functional tests in addition to spec-based tests

### Changed
 - Dropped support for PHP 5.3 (#64 and #76)
 - Bumped spec target version to 0.19
 - Made the AbstractInlineContainer be abstract
 - Moved environment config. logic into separate class

### Fixed
 - Fixed underscore emphasis to conform to spec changes (jgm/CommonMark#317)

### Removed
 - Removed PHP 5.3 workaround (see commit 5747822)
 - Removed unused AbstractWebResource::setUrl() method
 - Removed unnecessary check for hrule when parsing lists (#85)

## [0.7.2] - 2015-03-08
### Changed
 - Bumped spec target version to 0.18

### Fixed
 - Fixed broken parsing of emphasized text ending with a '0' character (#81)

## [0.7.1] - 2015-03-01
### Added
 - All references can now be obtained from the `ReferenceMap` via `listReferences()` (#73)
 - Test against PHP 7.0 (nightly) but allow failures

### Changed
 - ListData::$start now defaults to null instead of 0 (#74)
 - Replace references to HtmlRenderer with new HtmlRendererInterface

### Fixed
 - Fixed 0-based ordered lists starting at 1 instead of 0 (#74)
 - Fixed errors parsing multi-byte characters (#78 and #79)

## [0.7.0] - 2015-02-16
### Added
 - More unit tests to increase code coverage

### Changed
 - Enabled the InlineParserEngine to parse several non-special characters at once (performance boost)
 - NewlineParser no longer attempts to parse spaces; look-behind is used instead (major performance boost)
 - Moved closeUnmatchedBlocks into its own class
 - Image and link elements now extend AbstractInlineContainer; label data is stored via $inlineContents instead
 - Renamed AbstractInlineContainer::$inlineContents and its getter/setter

### Removed
 - Removed the InlineCollection class
 - Removed the unused ArrayCollection::splice() method
 - Removed impossible-to-reach code in Cursor::advanceToFirstNonSpace
 - Removed unnecessary test from the InlineParserEngine
 - Removed unnecessary/unused RegexHelper::getMainRegex() method

## [0.6.1] - 2015-01-25
### Changed
 - Bumped spec target version to 0.17
 - Updated emphasis parsing for underscores to prevent intra-word emphasis
 - Defered closing of fenced code blocks

## [0.6.0] - 2015-01-09
### Added
 - Bulk registration of parsers/renderers via extensions (#45)
 - Proper UTF-8 support, especially in the Cursor; mbstring extension is now required (#49)
 - Environment is now configurable; options can be accessed in its parsers/renderers (#56)
 - Added some unit tests 

### Changed
 - Bumped spec target version to 0.15 (#50)
 - Parsers/renderers are now lazy-initialized (#52)
 - Some private elements are now protected for easier extending, especially on Element classes (#53)
 - Renderer option keys changed from camelCase to underscore_case (#56) 
 - Moved CommonMark parser/render definitions into CommonMarkCoreExtension

### Fixed
 - Improved parsing of emphasis around punctuation
 - Improved regexes for CDATA and HTML comments
 - Fixed issue with HTML content that is considered false in loose comparisons, like `'0'` (#55)
 - Fixed DocParser trying to add empty strings to closed containers (#58)
 - Fixed incorrect use of a null parameter value in the HtmlElementTest

### Removed
 - Removed unused ReferenceDefinition* classes (#51)
 - Removed UnicodeCaseFolder in favor of mb_strtoupper

## [0.5.1] - 2014-12-27
### Fixed
 - Fixed infinite loop and link-in-link-in-image parsing (#37)

### Removed
 - Removed hard dependency on mbstring extension; workaround used if not installed (#38)

## [0.5.0] - 2014-12-24
### Added
 - Support for custom directives, parsers, and renderers

### Changed
 - Major refactoring to de-couple directives from the parser, support custom directive functionality, and reduce complexity
 - Updated references to stmd.js in README and docblocks
 - Modified CHANGELOG formatting
 - Improved travis configuration
 - Put tests in autoload-dev

### Fixed
 - Fixed CommonMarkConverter re-creating object each time new text is converted (#26)

### Removed
 - Removed HtmlRenderer::render() (use the renderBlock method instead)
 - Removed dependency on symfony/options-resolver (fixes #20)

## [0.4.0] - 2014-12-15
### Added
 - Added some missing copyright info

### Changed
 - Changed namespace to League\CommonMark
 - Made compatible with spec version 0.13
 - Moved delimiter stack functionality into separate class

### Fixed
 - Fixed regex which caused HHVM tests to fail

## [0.3.0] - 2014-11-28
### Added
 - Made renderer options configurable (issue #7)

### Changed
 - Made compatible with spec version 0.12
 - Stack-based parsing now used for emphasis, links and images
 - Protected some of the internal renderer methods which shouldn't have been `public`
 - Minor code clean-up (including PSR-2 compliance)

### Removed
 - Removed unnecessary distinction between ATX and Setext headers

## [0.2.1] - 2014-11-09
### Added
 - Added simpler string replacement to a method

### Changed
 - Removed "is" prefix from boolean methods
 * Updated to latest version of PHPUnit
 * Target specific spec version

## [0.2.0] - 2014-11-09
### Changed
 - Mirrored significant changes and improvements from stmd.js
 - Made compatible with spec version 0.10
 - Updated location of JGM's repository
 - Allowed HHVM tests to fail without affecting overall build success

### Removed
 - Removed composer.lock
 - Removed fixed reference to jgm/stmd@0275f34

## [0.1.2] - 2014-09-28
### Added
 - Added performance benchmarking tool (issue #2)
 - Added more badges to the README

### Changed
 - Fix JS -> PHP null judgement (issue #4)
 - Updated phpunit dependency

## [0.1.1] - 2014-09-08
### Added
 - Add anchors to regexes

### Changed
 - Updated target spec (now compatible with jgm/stmd:spec.txt @ 2cf0750)
 - Adjust HTML output for fenced code
 - Adjust block-level tag regex (remove "br", add "iframe")
 - Fix incorrect handling of nested emphasis

## 0.1.0
### Added
 - Initial commit (compatible with jgm/stmd:spec.txt @ 0275f34)

[unreleased]: https://github.com/thephpleague/commonmark/compare/0.8.0...HEAD
[0.8.0]: https://github.com/thephpleague/commonmark/compare/0.7.2...0.8.0
[0.7.2]: https://github.com/thephpleague/commonmark/compare/0.7.1...0.7.2
[0.7.1]: https://github.com/thephpleague/commonmark/compare/0.7.0...0.7.1
[0.7.0]: https://github.com/thephpleague/commonmark/compare/0.6.1...0.7.0
[0.6.1]: https://github.com/thephpleague/commonmark/compare/0.6.0...0.6.1
[0.6.0]: https://github.com/thephpleague/commonmark/compare/0.5.1...0.6.0
[0.5.1]: https://github.com/thephpleague/commonmark/compare/0.5.0...0.5.1
[0.5.0]: https://github.com/thephpleague/commonmark/compare/0.4.0...0.5.0
[0.4.0]: https://github.com/thephpleague/commonmark/compare/0.3.0...0.4.0
[0.3.0]: https://github.com/thephpleague/commonmark/compare/0.2.1...0.3.0
[0.2.1]: https://github.com/thephpleague/commonmark/compare/0.2.0...0.2.1
[0.2.0]: https://github.com/thephpleague/commonmark/compare/0.1.2...0.2.0
[0.1.2]: https://github.com/thephpleague/commonmark/compare/0.1.1...0.1.2
[0.1.1]: https://github.com/thephpleague/commonmark/compare/0.1.0...0.1.1
