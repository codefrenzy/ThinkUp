<?php
require_once dirname(__FILE__).'/config.tests.inc.php';
require_once $SOURCE_ROOT_PATH.'extlib/simpletest/autorun.php';
ini_set("include_path", ini_get("include_path").PATH_SEPARATOR.$INCLUDE_PATH);

require_once $SOURCE_ROOT_PATH.'tests/classes/class.ThinkTankBasicUnitTestCase.php';
require_once $SOURCE_ROOT_PATH.'webapp/controller/interface.Controller.php';
require_once $SOURCE_ROOT_PATH.'webapp/controller/class.ThinkTankController.php';
require_once $SOURCE_ROOT_PATH.'webapp/controller/class.ThinkTankAuthController.php';
require_once $SOURCE_ROOT_PATH.'webapp/controller/class.ThinkTankAdminController.php';
require_once $SOURCE_ROOT_PATH.'tests/classes/class.TestAdminController.php';
require_once $SOURCE_ROOT_PATH.'extlib/Smarty-2.6.26/libs/Smarty.class.php';
require_once $SOURCE_ROOT_PATH.'webapp/model/class.SmartyThinkTank.php';
require_once $SOURCE_ROOT_PATH.'webapp/model/class.Config.php';
require_once $SOURCE_ROOT_PATH.'webapp/model/class.Profiler.php';
require_once $SOURCE_ROOT_PATH.'webapp/model/class.Session.php';
require_once $SOURCE_ROOT_PATH.'webapp/config.inc.php';

/**
 * Test TestAdminController class
 *
 * TestController isn't a real ThinkTank controller, this is just a template for all Controller tests.
 *
 * @author Gina Trapani <ginatrapani[at]gmail[dot]com>
 */
class TestOfTestAdminController extends ThinkTankBasicUnitTestCase {
    /**
     * Constructor
     */
    public function __construct() {
        $this->UnitTestCase('TestAdminController class test');
    }

    public function setUp(){
        parent::setUp();
        $config = Config::getInstance();
        $config->setValue('debug', true);
    }

    public function tearDown(){
        parent::tearDown();
    }

    /**
     * Test constructor
     */
    public function testConstructor() {
        $controller = new TestAdminController(true);
        $this->assertTrue(isset($controller), 'constructor test');
    }

    /**
     * Test controller for non-logged in user
     * @TODO Possibly load the resulting markup as a DOM object and test various children in it;
     * this would enforce valid markup
     */
    public function testControlNotLoggedIn() {
        $config = Config::getInstance();
        $controller = new TestAdminController(true);
        $results = $controller->go();

        $this->assertEqual($results, 'You must be a ThinkTank admin in to do this',
        "not logged in, not admin, auth controller output");
    }

    public function testLoggedInAsAdmin() {
        $_SESSION['user'] = 'me@example.com';
        $_SESSION['user_is_admin'] = true;
        $config = Config::getInstance();
        $config->setValue('site_root_path', '/my/path/to/thinktank/');

        $controller = new TestAdminController(true);
        $results = $controller->go();

        //test if view variables were set correctly
        $v_mgr = $controller->getViewManager();
        $this->assertEqual($v_mgr->getTemplateDataItem('test'), 'Testing, testing, 123');
        $this->assertEqual($v_mgr->getTemplateDataItem('app_title'), 'ThinkTank');

        $this->assertEqual($results,
        '<a href="/my/path/to/thinktank/index.php">ThinkTank</a>: Testing, testing, 123 | Logged in as me@example.com', 
        "auth controller output when logged in");
    }

    public function testLoggedInNotAsAdmin() {
        $_SESSION['user'] = 'me@example.com';
        $_SESSION['user_is_admin'] = false;
        $config = Config::getInstance();
        $config->setValue('site_root_path', '/my/path/to/thinktank/');

        $controller = new TestAdminController(true);
        $results = $controller->go();

        $this->assertEqual($results, 'You must be a ThinkTank admin in to do this',
        "not logged in, not admin, auth controller output");
    }
}
