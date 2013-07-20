<?php

class fs_functions_test extends PHPUnit_Framework_TestCase
{
    public function getAbsoluteData()
    {
        return array(
            array(true, '/path/to/dir'),
            array(true, '/path/to/../dir'),
            array(true, 'C:\\Path\\To\\Dir'),
            array(true, 'C:\\Path\\To\\..\\Dir'),
            array(true, 'protocol://some/uri'),
            array(false, 'to/dir'),
            array(false, 'To\\Dir'),
            array(false, null),
            array(false, '://some/uri/without/a/protocol'),
        );
    }

    public function getCanonicalData()
    {
        return array(
            array(
                join(DIRECTORY_SEPARATOR, array('C:', 'Test', 'Dir')),
                'C:\\Users\\kherrera\\..\\..\\Test\\.\\Dir'
            ),
            array(
                join(DIRECTORY_SEPARATOR, array('', 'test', 'dir')),
                '/home/kherrera/../../test/./dir'
            ),
        );
    }

    public function testIsWindows()
    {
        $this->assertSame(
            "\r\n" === PHP_EOL,
            FILE_SYSTEM_IS_WINDOWS
        );
    }

    /**
     * @dataProvider getCanonicalData
     */
    public function testCanonicalPath($expected, $path)
    {
        $this->assertEquals($expected, canonical_path($path));
    }

    /**
     * @dataProvider getAbsoluteData
     */
    public function testIsAbsolutePath($expected, $path)
    {
        $this->assertSame($expected, is_absolute_path($path));
    }

    public function testIsHiddenNotExist()
    {
        $this->setExpectedException(
            'PHPUnit_Framework_Error_Warning',
            'The path "/should/not/exist" does not exist.'
        );

        is_hidden_path('/should/not/exist');
    }

    public function testIsHidden()
    {
        $this->assertFalse(is_hidden_path(__FILE__));
    }

    public function testIsHiddenDot()
    {
        unlink($file = tempnam(sys_get_temp_dir(), 'fsf'));
        mkdir($file);
        touch($file = $file . DIRECTORY_SEPARATOR . '.test');

        $this->assertTrue(is_hidden_path($file));
    }

    /**
     * @depends testIsWindows
     */
    public function testIsHiddenWindows()
    {
        if (false === FILE_SYSTEM_IS_WINDOWS) {
            $this->markTestSkipped('Not running on Windows.');
        }

        $file = tempnam(sys_get_temp_dir(), 'fsf');

        exec('attrib +H ' . escapeshellarg($file), $junk, $status);

        if (0 === $status) {
            $this->assertTrue(is_hidden_path($file));
        } else {
            $this->markTestIncomplete('Could not mark test file as hidden.');
        }
    }
}
