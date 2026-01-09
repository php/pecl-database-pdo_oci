# The PDO_OCI Extension

Use the PDO_OCI extension to access Oracle Database via PHP Data Objects (PDO) APIs.

This repository is for PHP 8.3+ as it
[was decided to unbundle](https://wiki.php.net/rfc/unbundle_imap_pspell_oci8)
PDO OCI / OCI8 extension from the PHP source code.

Documentation is at https://www.php.net/pdo_oci

Installation
------------

**PECL**

The PDO_OCI extension is available in the PECL repository [here](https://pecl.php.net/package/PDO_OCI).

Use `pecl install pdo_oci` to install for PHP 8.3 and newer versions.

For older PHP versions, use php_pdo_oci.dll from the Windows PHP release
bundle, or build from the PHP source code by running:
```
phpize
./configure --with-pdo_oci=shared,instantclient,/path/to/instant/client/sdk
make install

```

To complete installation, add "extension=pdo_oci.so" or
"extension=php_pdo_oci.dll" (Windows) to your php.ini file.

**PIE**

The PHP OCI8 extension is also available in the PIE Packagist repository [here](https://packagist.org/packages/pecl/pdo_oci).

To install from PIE, use the [pie](https://github.com/php/pie/releases) utility and run:
```
pie install pecl/pdo_oci
```

Tests
-----

To run the tests, see [tests/README.md](tests/README.md).

Additional Requirements
------------------------

The PDO_OCI extension can be linked with Oracle Client libraries from Oracle
Database 11.2 or later. These libraries are found in your database
installation, or in the free Oracle Instant Client packages from
https://www.oracle.com/database/technologies/instant-client.html.

Install the 'Basic' or 'Basic Light' Instant Client package for running
applications with this extension. If building from source, then also install
the Instant Client SDK package.

Oracle's standard cross-version connectivity applies. For example, PHP PDO_OCI
linked with Instant Client 19c can connect to Oracle Database 11.2 onward. See
Oracle's note "Oracle Client / Server Interoperability Support" (ID 207303.1)
for details.

PHP is available from https://www.php.net/releases/.
