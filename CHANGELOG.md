Changelog
=========

## 1.3.0

### General Improvements

 - Added a new command: `status`
 - Added composer parameter loading to CLI commands
 - Bundle all commands into single script smyver.php
 - Added port configuration option
 - Refactor: implemented the command pattern
 - Refactor: implemented the configuration adapter pattern
 - Added some examples

### Configuration adapters:

 - Ini (the default adapter)
 - PhpArray
 - PhpFile

### Up command:

 - Option `--versions-path` is relative to project root if not an absolute path

## 1.2.0

### Up command:

 - Added option `--install-provisional-version`
 - Added option `--provisional-version`

## 1.1.0

### Up command:

 - Added option `--no-schema`
 - Added option `--versions-path`
