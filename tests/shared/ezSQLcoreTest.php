<?php
require_once('ez_sql_loader.php');

require 'vendor/autoload.php';
use PHPUnit\Framework\TestCase;

/**
 * Test class for ezSQLcore.
 * Generated by PHPUnit
 *
 * @author  Stefanie Janine Stoelting <mail@stefanie-stoelting.de>
 * @name    ezSQLcoreTest
 * @package ezSQL
 * @subpackage Tests
 * @license FREE / Donation (LGPL - You may do what you like with ezSQL - no exceptions.)
 */
class ezSQLcoreTest extends TestCase {
	
    /**
     * @var ezSQLcore
     */
    protected $object;
    private $errors;
 
    function errorHandler($errno, $errstr, $errfile, $errline, $errcontext) {
        $this->errors[] = compact("errno", "errstr", "errfile",
            "errline", "errcontext");
    }

    function assertError($errstr, $errno) {
        foreach ($this->errors as $error) {
            if ($error["errstr"] === $errstr
                && $error["errno"] === $errno) {
                return;
            }
        }
        $this->fail("Error with level " . $errno .
            " and message '" . $errstr . "' not found in ", 
            var_export($this->errors, TRUE));
    }   
	
    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {
        $this->object = new ezSQLcore;
    } // setUp

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->object = null;
    } // tearDown

    /**
     * @covers ezSQLcore::get_host_port
     */
    public function testGet_host_port()
    {
        $hostport = $this->object->get_host_port("localhost:8181");
        $this->assertEquals($hostport[0],"localhost");
        $this->assertEquals($hostport[1],"8181");
    }
	
    /**
     * @covers ezSQLcore::register_error
     */
    public function testRegister_error() {
        $err_str = 'Test error string';
        
        $this->object->register_error($err_str);
        
        $this->assertEquals($err_str, $this->object->last_error);
    } // testRegister_error

    /**
     * @covers ezSQLcore::show_errors
     */
    public function testShow_errors() {
        $this->object->hide_errors();
        
        $this->assertFalse($this->object->getShowErrors());
        
        $this->object->show_errors();
        
        $this->assertTrue($this->object->getShowErrors());
    } // testShow_errors

    /**
     * @covers ezSQLcore::hide_errors
     */
    public function testHide_errors() {
        $this->object->hide_errors();
        
        $this->assertFalse($this->object->getShowErrors());
    } // testHide_errors

    /**
     * @covers ezSQLcore::flush
     */
    public function testFlush() {
        $this->object->flush();
        
        $this->assertNull($this->object->last_result);
        $this->assertNull($this->object->col_info);
        $this->assertNull($this->object->last_query);
        $this->assertFalse($this->object->from_disk_cache);
    } // testFlush

    /**
     * @covers ezSQLcore::get_var
     */
    public function testGet_var() {
        $this->assertNull($this->object->get_var());
    } // testGet_var

    /**
     * @covers ezSQLcore::get_row
     */
    public function testGet_row() {
        $this->assertNull($this->object->get_row());
    } // testGet_row

    /**
     * @covers ezSQLcore::get_col
     */
    public function testGet_col() {
        $this->object->last_result = array();
        $this->assertEmpty($this->object->get_col());
    } // testGet_col

    /**
     * @covers ezSQLcore::get_results
     */
    public function testGet_results() {
        $this->assertNull($this->object->get_results());
    } // testGet_results

    /**
     * @covers ezSQLcore::get_col_info
     */
    public function testGet_col_info() {
        $this->assertEmpty($this->object->get_col_info());
    } // testGet_col_info

    /**
     * @covers ezSQLcore::store_cache
     */
    public function testStore_cache() {
        $sql = 'SELECT * FROM ez_test';
        
        $this->object->store_cache($sql, true);
        
        $this->assertNull($this->object->get_cache($sql));
    } // testStore_cache

    /**
     * @covers ezSQLcore::get_cache
     */
    public function testGet_cache() {
        $sql = 'SELECT * FROM ez_test';
        
        $this->object->store_cache($sql, true);
        
        $this->assertNull($this->object->get_cache($sql));
    } // testGet_cache

    /**
     * The test does not echos HTML, it is just a test, that is still running
     * @covers ezSQLcore::vardump
     */
    public function testVardump() {
        $this->object->debug_echo_is_on = false;
        $this->object->last_result = array('Test 1');
        $this->assertNotEmpty($this->object->vardump($this->object->last_result));
        $this->object->debug_echo_is_on = true;
        $this->expectOutputRegex('/[Last Function Call]/');
        $this->object->vardump('');
        
    } // testVardump

    /**
     * The test echos HTML, it is just a test, that is still running
     * @covers ezSQLcore::dumpvar
     */
    public function testDumpvar() {
        $this->object->last_result = array('Test 1', 'Test 2');
        $this->expectOutputRegex('/[Last Function Call]/');
        $this->object->dumpvar('');
    } // testDumpvar

    /**
     * @covers ezSQLcore::debug
     */
    public function testDebug() {
        $this->assertNotEmpty($this->object->debug(false));
        
        // In addition of getting a result, it fills the console
        $this->expectOutputRegex('/[make a donation]/');
        $this->object->debug(true);
        $this->object->last_error = "test last";
        $this->expectOutputRegex('/[test last]/');
        $this->object->debug(true);
        $this->object->from_disk_cache = true;
        $this->expectOutputRegex('/[Results retrieved from disk cache]/');
        $this->object->debug(true);
    } // testDebug

    /**
     * @covers ezSQLcore::donation
     */
    public function testDonation() {
        $this->assertNotEmpty($this->object->donation());
    } // testDonation

    /**
     * @covers ezSQLcore::timer_get_cur
     */
    public function testTimer_get_cur() {
        list($usec, $sec) = explode(' ',microtime());
        
        $expected = ((float)$usec + (float)$sec);
        
        $this->assertGreaterThanOrEqual($expected, $this->object->timer_get_cur());
    } // testTimer_get_cur

    /**
     * @covers ezSQLcore::timer_start
     */
    public function testTimer_start() {
        $this->object->timer_start('test_timer');
        $this->assertNotNull($this->object->timers['test_timer']);        
    } // testTimer_start

    /**
     * @covers ezSQLcore::timer_elapsed
     */
    public function testTimer_elapsed() {
        $expected = 0;        
        $this->object->timer_start('test_timer');      
		usleep( 5 );        
        $this->assertGreaterThanOrEqual($expected, $this->object->timer_elapsed('test_timer'));
    } // testTimer_elapsed

    /**
     * @covers ezSQLcore::timer_update_global
     */
    public function testTimer_update_global() {
        $this->object->timer_start('test_timer');           
		usleep( 5 );
        $this->object->do_profile = true;
        $this->object->timer_update_global('test_timer');
        $expected = $this->object->total_query_time;     
        $this->assertGreaterThanOrEqual($expected, $this->object->timer_elapsed('test_timer'));    
    }

    /**
     * @covers ezSQLcore::get_set
     */
    public function testGet_set()
    {
        $this->assertNull($this->object->get_set(''));    
 
        //$this->errors = array();
        //set_error_handler(array($this, 'errorHandler')); 
        $this->expectExceptionMessage('Call to undefined method ezSQLcore::escape()');
        $this->object->get_set(
            array('test_unit'=>'NULL',
            'test_unit2'=>'NOW()',
            'test_unit3'=>'true',
            'test_unit4'=>'false'));   
    }

    /**
     * @covers ezSQLcore::count
     */
    public function testCount()
    {
        $this->assertEquals(0,$this->object->count());
        $this->object->count(true,true);
        $this->assertEquals(1,$this->object->count());
        $this->assertEquals(2,$this->object->count(false,true));
    }
    
    /**
     * @covers ezSQLcore::delete
     */
    public function testDelete()
    {
        $this->assertFalse($this->object->delete(''));
        $this->assertFalse($this->object->delete('test_unit_delete',''));
        $this->assertFalse($this->object->delete('test_unit_delete',
            array('good'=>'null'),
                      'bad'));
    }
       
    /**
     * @covers ezSQLcore::selecting
     */
    public function testSelecting()
    {
        $this->assertFalse($this->object->selecting('',''));
    } 
    
    /**
     * @covers ezSQLcore::create_select
     */
    public function testCreate_select()
    {
        $this->assertFalse($this->object->create_select('','',''));
    }
    
    /**
     * @covers ezSQLcore::insert_select
     */
    public function testInsert_select()
    {
        $this->assertFalse($this->object->insert_select('','',''));
    }
    
    /**
     * @covers ezSQLcore::insert
     */
    public function testInsert()
    {
        $this->assertFalse($this->object->insert('',''));
    }
    
    /**
     * @covers ezSQLcore::update
     */
    public function testUpdate()
    {
        $this->assertFalse($this->object->update('',''));
        $this->assertFalse($this->object->update('test_unit_delete',array('test_unit_update'=>'date()'),''));
    }
	
    /**
     * @covers ezSQLcore::replace
     */
    public function testReplace()
    {
        $this->assertFalse($this->object->replace('',''));
    }
    
    /**
     * @covers ezSQLcore::_query_insert_replace
     */
    public function test_Query_insert_replace() 
    {        
        $this->assertFalse($this->object->_query_insert_replace('', array('id'=>'2' ),'replace')); 
        $this->assertFalse($this->object->_query_insert_replace('unit_table', array('id'=>'2' ),''));  
        $this->assertContains('replace INTO unit_table',$this->object->_query_insert_replace('unit_table', 'id' ,'replace',false));   
        $this->assertContains('(test, INSERT, INTO, SELECT)',$this->object->_query_insert_replace('unit_table', array('test','INSERT','INTO','SELECT') ,'insert',false)); 
    }   
    
    /**
     * @covers ezSQLcore::affectedRows
     */
    public function testAffectedRows() {
        $this->assertEquals(0, $this->object->affectedRows());
    } // testAffectedRows
} //
