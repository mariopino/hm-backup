<?php

/**
 * Tests for the Archive process with various permissions,
 * broken files, symlinks and other edge cases
 *
 * @extends WP_UnitTestCase
 */
class testUnreadableFileTestCase extends WP_UnitTestCase {

	/**
	 * Contains the current backup instance
	 *
	 * @var object
	 * @access protected
	 */
	protected $backup;

	/**
	 * Setup the backup object and create the tmp directory
	 *
	 * @access public
	 * @return null
	 */
	function setUp() {

		$this->backup = new HM_Backup();
		$this->backup->root = dirname( __FILE__ ) . '/test-data/';
		$this->backup->path = dirname( __FILE__ ) . '/tmp';
		$this->backup->files_only = true;

		mkdir( $this->backup->path );

		chmod( $this->backup->root . '/test-data.txt', 0220 );

	}

	/**
	 * Cleanup the backup file and tmp directory
	 * after every test
	 *
	 * @access public
	 * @return null
	 */
	function tearDown() {

		if ( file_exists( $this->backup->archive_filepath() ) )
			unlink( $this->backup->archive_filepath() );

		if ( file_exists( $this->backup->path ) )
			rmdir( $this->backup->path );

		chmod( $this->backup->root . '/test-data.txt', 0664 );

	}

	/**
	 * Test an unreadable file with the shell commands
	 *
	 * @access public
	 * @return null
	 */
	function testArchiveUnreadableFileWithZip() {

		$this->assertNotEmpty( $this->backup->zip_command_path );

		$this->assertFalse( is_readable( $this->backup->root . '/test-data.txt' ) );

		$this->backup->zip();

		$this->assertFileExists( $this->backup->archive_filepath() );

		$this->assertArchiveNotContains( $this->backup->archive_filepath(), array( 'test-data.txt' ) );
		$this->assertArchiveFileCount( $this->backup->archive_filepath(), 2 );

	}

	/**
	 * Test an unreadable file with the zipArchive commands
	 *
	 * @access public
	 * @return null
	 */
	function testArchiveUnreadableFileWithZipArchive() {

		$this->backup->zip_command_path = false;

		$this->assertFalse( is_readable( $this->backup->root . '/test-data.txt' ) );

		$this->backup->zip_archive();

		$this->assertFileExists( $this->backup->archive_filepath() );

		$this->assertArchiveNotContains( $this->backup->archive_filepath(), array( 'test-data.txt' ) );
		$this->assertArchiveFileCount( $this->backup->archive_filepath(), 2 );

	}

	/**
	 * Test an unreadable file with the PclZip commands
	 *
	 * @access public
	 * @return null
	 */
	function testArchiveUnreadableFileWithPclZip() {

		$this->backup->zip_command_path = false;

		$this->assertFalse( is_readable( $this->backup->root . '/test-data.txt' ) );

		$this->backup->pcl_zip();

		$this->assertFileExists( $this->backup->archive_filepath() );

		$this->assertArchiveNotContains( $this->backup->archive_filepath(), array( 'test-data.txt' ) );
		$this->assertArchiveFileCount( $this->backup->archive_filepath(), 2 );

	}

}