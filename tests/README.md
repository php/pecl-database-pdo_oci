# PDO_OCI TESTS

The PDO_OCI tests augment the generic PDO tests.

To run the PDO tests for PDO_OCI, execute commands similar to:

    export PDO_TEST_DIR=/wherever/php-src/ext/pdo/tests/
    export PDO_OCI_TEST_DIR=/wherever/pecl-database-pdo_oci/tests/

    export PDO_OCI_TEST_DSN='oci:dbname=localhost/freepdb1;charset=AL32UTF8'
    export PDO_OCI_TEST_USER=system
    export PDO_OCI_TEST_PASS=oracle

    cd /wherever/pecl-database-pdo_oci
    php /wherever/php-src/run-tests.php --set-timeout 600 ./tests
