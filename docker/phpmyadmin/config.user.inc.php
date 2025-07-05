<?php
/**
 * phpMyAdmin configuration file for tastack2
 */

// Set default language to Japanese
$cfg['DefaultLang'] = 'ja';

// Set timezone to Japan
$cfg['DefaultConnectionCollation'] = 'utf8mb4_unicode_ci';

// Enable upload of large files
$cfg['UploadDir'] = '/tmp/';
$cfg['SaveDir'] = '/tmp/';

// Set maximum execution time
$cfg['ExecTimeLimit'] = 300;

// Set memory limit
$cfg['MemoryLimit'] = '512M';

// Set upload limit
$cfg['MaxSizeForInputField'] = '50MiB';

// Increase number of rows displayed
$cfg['MaxRows'] = 100;

// Enable zip/gzip compression for exports
$cfg['ZipDump'] = true;
$cfg['GZipDump'] = true;
$cfg['BZipDump'] = true;

// Set theme to pmahomme (modern theme)
$cfg['ThemeDefault'] = 'pmahomme';

// Enable login cookie validity for 8 hours
$cfg['LoginCookieValidity'] = 28800;

// Set charset conversion
$cfg['DefaultCharset'] = 'utf-8';

// Enable foreign key checks
$cfg['DefaultForeignKeyChecks'] = 'enable';

// Set default functions for edit
$cfg['DefaultFunctions'] = array(
    'FUNC_CHAR' => '',
    'FUNC_DATE' => '',
    'FUNC_NUMBER' => '',
    'FUNC_SPATIAL' => 'ST_GeomFromText',
    'FUNC_UUID' => 'UUID'
);

// Show extended information about MySQL processes
$cfg['ShowStats'] = true;
$cfg['ShowServerInfo'] = true;
$cfg['ShowPhpInfo'] = true;

// Set navigation settings
$cfg['NavigationTreeDefaultTabTable'] = 'structure';
$cfg['NavigationTreeDefaultTabTable2'] = 'sql';
$cfg['NavigationTreeEnableGrouping'] = true;
$cfg['NavigationTreeDbSeparator'] = '_';
$cfg['NavigationTreeTableSeparator'] = array('__', '_');

// Enable autocomplete for SQL editor
$cfg['EnableAutocompleteForTablesAndColumns'] = true;

// Set SQL editor settings
$cfg['CodemirrorEnable'] = true;
$cfg['LintEnable'] = true;

// Set export settings
$cfg['Export']['method'] = 'quick';
$cfg['Export']['format'] = 'sql';
$cfg['Export']['compression'] = 'none';
$cfg['Export']['charset'] = 'utf-8';

// Set import settings
$cfg['Import']['charset'] = 'utf-8';
