# The PDO_OCI Extension

Use the PDO_OCI extension to access Oracle Database.

Documentation is at https://www.php.net/pdo_oci

Use `pecl install pdo_oci` to install for PHP 8.3.

For older PHP versions, use php_pdo_oci.dll from the Windows PHP release
bundle, or build from the PHP source code by running:

  phpize
  ./configure -with-pdo_oci=shared,instantclient,/path/to/instant/client/lib
  make install

To complete installation, add "extension=pdo_oci.so" to your php.ini file.

PHP is available from https://www.php.net/releases/

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
