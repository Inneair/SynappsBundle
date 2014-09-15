# Innéair Synapps bundle for Symfony
------
This bundle integrates the Synapps library into Symfony, and provide additional features such as:

- Efficient transaction management in services with annotations and AOP.
- A default ready for use REST controller.
- A generic entity repository providing extended persistence services in Doctrine ORM.
- Classes for error management and serialization with JSON format.
- A base class to define business services in Symfony.
- Additional validators, for instance to check HTTP request parameters.

------
## 1. Change log
This change log references the relevant changes (bug fixes, security fixes, new features, improvements, documentation
fixes) done in the bundle.

Syntax for changes: _`<type of modification> [domain] <description>`_

`<type of modification>` can be one of the following:

- _NEW_: new feature.
- _IMP_: improvement of an existing functionality.
- _REF_: code refactoring (no functional changes).
- _BUG_: bug fix.
- _UPG_: dependency upgrade.

`[domain]` is the name of the updated domain/component, and is optional (brackets are mandatory).

`<description>` is a descriptive text of the modification. 

#### 1.0.0 (2014-09-14)

 * REF Migration into a dedicated VCS.

------
## 2. Requirements
### Software requirements
- [PHP](http://www.php.net/) 5.5
- [Symfony](http://www.symfony.com/) 2.5+

### PHP configuration
#### Settings
    ; Even not mandatory, using UTC for PHP is highly recommended.
    date.timezone = UTC

    ; Restricted directories shall be disabled.
    ; If enabled, be sure the directories you are accessing are under one of the directories in the directive:
    ; - Each path in the 'include_path' directive
    ; - Database file of Unix command 'file': either /etc/magic or /usr/share/file/magic
    ; open_basedir =

#### Extensions
According to the list of extensions provided [here](http://php.net/manual/en/extensions.alphabetical.php):

- Calendar
- Ctype
- cURL
- Date/Time
- Directories
- DOM
- Error handling
- Fileinfo
- Filesystem
- Filter
- Function handling
- iconv
- PHP Options/Info
- intl
- JSON
- libxml 2.7.3+
- Mail
- Math
- Multibyte string
- Misc.
- Mysqli
- Mysqlnd
- Network
- Output control
- PCRE
- PDO
- Program execution
- MySQL (PDO)
- Sessions
- Simple XML
- SPL
- Strings
- URLs
- Variable handling
- XSL
- ZIP

-------
## 3. License
This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE

