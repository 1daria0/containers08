<?php

require_once __DIR__ . '/testframework.php';

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../modules/database.php';
require_once __DIR__ . '/../modules/page.php';

$testFramework = new TestFramework();

// test 1: check database connection
function testDbConnection() {
    global $config;
    try {
        $db = new Database($config["db"]["path"]);
        return assertExpression(true, "Database connection OK", "Connection failed");
    } catch (Exception $e) {
        return assertExpression(false, "", "Exception: " . $e->getMessage());
    }
}

// test 2: test count method
function testDbCount() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $count = $db->Count("page");
    return assertExpression($count == 3, "Count = 3", "Count = $count, expected 3");
}

// test 3: test create method
function testDbCreate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = ['title' => 'Test', 'content' => 'Test content'];
    $id = $db->Create("page", $data);
    $record = $db->Read("page", $id);
    $success = ($id > 0 && $record && $record['title'] == 'Test');
    $db->Delete("page", $id);
    return assertExpression($success, "Create works", "Create failed");
}

// test 4: test read method
function testDbRead() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $record = $db->Read("page", 1);
    return assertExpression($record && $record['id'] == 1, "Read works", "Read failed");
}

// test 5: test update method
function testDbUpdate() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = ['title' => 'Old', 'content' => 'Old'];
    $id = $db->Create("page", $data);
    $db->Update("page", $id, ['title' => 'New', 'content' => 'New']);
    $updated = $db->Read("page", $id);
    $success = ($updated['title'] == 'New' && $updated['content'] == 'New');
    $db->Delete("page", $id);
    return assertExpression($success, "Update works", "Update failed");
}

// test 6: test delete method
function testDbDelete() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = ['title' => 'Del', 'content' => 'Del'];
    $id = $db->Create("page", $data);
    $db->Delete("page", $id);
    $record = $db->Read("page", $id);
    return assertExpression(!$record, "Delete works", "Delete failed");
}

// test 7: test execute method
function testDbExecute() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $data = ['title' => 'Exec', 'content' => 'Before'];
    $id = $db->Create("page", $data);
    $db->Execute("UPDATE page SET content = 'After' WHERE id = $id");
    $record = $db->Read("page", $id);
    $success = ($record['content'] == 'After');
    $db->Delete("page", $id);
    return assertExpression($success, "Execute works", "Execute failed");
}

// test 8: test fetch method
function testDbFetch() {
    global $config;
    $db = new Database($config["db"]["path"]);
    $rows = $db->Fetch("SELECT * FROM page LIMIT 2");
    return assertExpression(count($rows) == 2, "Fetch returns 2 rows", "Fetch returned " . count($rows));
}

// test 9: test page render
function testPageRender() {
    $page = new Page(__DIR__ . '/../site/templates/index.tpl');
    $data = ['title' => 'My Title', 'content' => 'My Content'];
    $output = $page->Render($data);
    $success = (strpos($output, 'My Title') !== false && strpos($output, 'My Content') !== false);
    return assertExpression($success, "Page render works", "Page render failed");
}

// add tests
$testFramework->add('Database connection', 'testDbConnection');
$testFramework->add('table count', 'testDbCount');
$testFramework->add('data create', 'testDbCreate');
$testFramework->add('data read', 'testDbRead');
$testFramework->add('data update', 'testDbUpdate');
$testFramework->add('data delete', 'testDbDelete');
$testFramework->add('execute', 'testDbExecute');
$testFramework->add('fetch', 'testDbFetch');
$testFramework->add('page render', 'testPageRender');

// run tests
$testFramework->run();

echo $testFramework->getResult();