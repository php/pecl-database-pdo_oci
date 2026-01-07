--TEST--
OCI
--EXTENSIONS--
pdo_oci
--REDIRECTTEST--
# magic auto-configuration

$config = array(
    'TESTS' => getenv('PDO_TEST_DIR')
);

putenv('ORA_SUPPRESS_ERROR_URL=TRUE');    // suppress Oracle Database 23ai error message URLs

if (false !== getenv('PDO_OCI_TEST_DSN')) {
    $config['ENV']['PDOTEST_DSN'] = getenv('PDO_OCI_TEST_DSN');
    $config['ENV']['PDOTEST_USER'] = getenv('PDO_OCI_TEST_USER');
    $config['ENV']['PDOTEST_PASS'] = getenv('PDO_OCI_TEST_PASS');
    $config['ENV']['PDOTEST_ATTR'] = getenv('PDO_OCI_TEST_ATTR');
} else {
    $config['ENV']['PDOTEST_DSN'] = 'oci:dbname=localhost/freepdb1;charset=AL32UTF8';
    $config['ENV']['PDOTEST_USER'] = 'system';
    $config['ENV']['PDOTEST_PASS'] = 'oracle';
}

return $config;
