# The PDO_OCI Extension

Use the PDO_OCI extension to access Oracle Database.

This repository is for PHP 8.3+ as it [was decided to unbundle](https://wiki.php.net/rfc/unbundle_imap_pspell_oci8) PDO OCI / OCI8 extension from the PHP source code.

Documentation is at https://www.php.net/pdo_oci

Use `pecl install pdo_oci` to install for PHP 8.2.

The PDO_OCI extension can be linked with Oracle client libraries from Oracle
Database 11.2 or later.  These libraries are found in your database
installation, or in the free Oracle Instant Client from
https://www.oracle.com/database/technologies/instant-client.html
Install the 'Basic' or 'Basic Light' Instant Client package. If building from
source, then also install the SDK package.

Oracle's standard cross-version connectivity applies.  For example, PHP PDO_OCI
linked with Instant Client 19c can connect to Oracle Database 11.2 onward.  See
Oracle's note "Oracle Client / Server Interoperability Support" (ID 207303.1)
for details.
