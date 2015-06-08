<?php
defined('is_running') or die('Not an entry point...');
$fileVersion = '4.4';
$fileModTime = '1432590209';
$file_stats = array (
  'created' => 1432588086,
  'gpversion' => '4.4',
  'modified' => 1432590209,
  'username' => 'Magic',
);

$config = array (
  'language' => 'pl',
  'toemail' => 'whitewolf13@wp.pl',
  'gpLayout' => 'default',
  'title' => 'Test CMS gpEASY',
  'keywords' => 'gpEasy CMS, Easy CMS, Content Management, PHP, Free CMS, Website builder, Open Source',
  'desc' => 'A new gpEasy CMS installation. You can change your site\'s description in the configuration.',
  'timeoffset' => '0',
  'langeditor' => 'inherit',
  'dateformat' => '%m/%d/%y - %I:%M %p',
  'gpversion' => '4.4',
  'passhash' => 'sha512',
  'gpuniq' => 'ua7BIuoc1NQiNIkdSUnw',
  'combinecss' => true,
  'combinejs' => true,
  'etag_headers' => true,
  'file_count' => 6,
  'maximgarea' => '691200',
  'maxthumbsize' => '100',
  'check_uploads' => false,
  'colorbox_style' => 'example1',
  'customlang' => 
  array (
  ),
  'showgplink' => true,
  'showsitemap' => true,
  'showlogin' => true,
  'auto_redir' => 90,
  'resize_images' => true,
  'jquery' => 'local',
  'addons' => 
  array (
    '9dmgl1k' => 
    array (
      'code_folder_part' => '/data/_addoncode/9dmgl1k',
      'data_folder' => '9dmgl1k',
      'name' => 'Simple Blog',
      'version' => '2.0.7',
      'id' => '17',
      'remote_install' => true,
      'editable_text' => 'Text.php',
    ),
    'erqb0rx' => 
    array (
      'code_folder_part' => '/data/_addoncode/erqb0rx',
      'data_folder' => 'erqb0rx',
      'name' => 'Simple Event Calendar',
      'version' => '1.10',
      'id' => '184',
      'remote_install' => true,
    ),
    'vrzxd3o' => 
    array (
      'code_folder_part' => '/data/_addoncode/vrzxd3o',
      'data_folder' => 'vrzxd3o',
      'name' => 'EasyNewsLetter',
      'version' => '1.1.1',
      'id' => '258',
      'remote_install' => true,
      'editable_text' => 'Text.php',
    ),
  ),
  'themes' => 
  array (
  ),
  'gadgets' => 
  array (
    'Contact' => 
    array (
      'script' => '/include/special/special_contact.php',
      'class' => 'special_contact_gadget',
    ),
    'Search' => 
    array (
      'script' => '/include/special/special_search.php',
      'method' => 
      array (
        0 => 'special_gpsearch',
        1 => 'gadget',
      ),
    ),
    'Simple_Blog' => 
    array (
      'addon' => '9dmgl1k',
      'data' => '/data/_addondata/9dmgl1k/gadget.php',
    ),
    'Simple_Blog_Categories' => 
    array (
      'addon' => '9dmgl1k',
      'script' => '/data/_addoncode/9dmgl1k/CategoriesGadget.php',
      'class' => 'SimpleBlogCategories',
    ),
    'Simple_Blog_Archives' => 
    array (
      'addon' => '9dmgl1k',
      'script' => '/data/_addoncode/9dmgl1k/ArchivesGadget.php',
      'class' => 'SimpleBlogArchives',
    ),
    'Event Calendar Gadget' => 
    array (
      'addon' => 'erqb0rx',
      'script' => '/data/_addoncode/erqb0rx/Event_Calendar_Gadget.php',
      'class' => 'Event_Calendar_Gadget',
    ),
    'EasyNewsLetter' => 
    array (
      'addon' => 'vrzxd3o',
      'script' => '/data/_addoncode/vrzxd3o/Gadget/Subscribe.php',
      'class' => 'EasyNewsLetter_Subscribe_Gadget',
    ),
  ),
  'hooks' => 
  array (
    'RenameFileDone' => 
    array (
      '9dmgl1k' => 
      array (
        'addon' => '9dmgl1k',
        'script' => '/data/_addoncode/9dmgl1k/SimpleBlogCommon.php',
        'class' => 'SimpleBlogCommon',
      ),
    ),
    'Search' => 
    array (
      '9dmgl1k' => 
      array (
        'addon' => '9dmgl1k',
        'script' => '/data/_addoncode/9dmgl1k/Search.php',
        'class' => 'BlogSearch',
      ),
    ),
    'GetHead' => 
    array (
      '9dmgl1k' => 
      array (
        'addon' => '9dmgl1k',
        'script' => '/data/_addoncode/9dmgl1k/HookHead.php',
      ),
      'vrzxd3o' => 
      array (
        'addon' => 'vrzxd3o',
        'script' => '/data/_addoncode/vrzxd3o/Hook/GetHead.php',
        'class' => 'EasyNewsLetter_GetHead',
      ),
    ),
  ),
  'homepath_key' => 'a',
  'homepath' => 'Home',
  'admin_links' => 
  array (
    'Admin_Blog' => 
    array (
      'label' => 'Admin Simple Blog',
      'addon' => '9dmgl1k',
      'script' => '/data/_addoncode/9dmgl1k/AdminSimpleBlog.php',
      'class' => 'AdminSimpleBlog',
    ),
    'Admin_BlogCategories' => 
    array (
      'label' => 'Admin Simple Blog Categories',
      'addon' => '9dmgl1k',
      'script' => '/data/_addoncode/9dmgl1k/CategoriesAdmin.php',
      'class' => 'AdminSimpleBlogCategories',
    ),
    'Admin_BlogComments' => 
    array (
      'label' => 'Admin Blog Comments',
      'addon' => '9dmgl1k',
      'script' => '/data/_addoncode/9dmgl1k/AdminComments.php',
      'class' => 'SimpleBlogComments',
    ),
    'Admin_Event_Calendar' => 
    array (
      'label' => 'Event Calendar Admin',
      'addon' => 'erqb0rx',
      'script' => '/data/_addoncode/erqb0rx/Event_Calendar_Admin.php',
      'class' => 'Event_Calendar_Admin',
    ),
    'Admin_EasyNewsLetter_EditConfig' => 
    array (
      'label' => 'Edit Config',
      'addon' => 'vrzxd3o',
      'script' => '/data/_addoncode/vrzxd3o/Admin/EditConfig/EditConfig.php',
      'class' => 'EasyNewsLetter_EditConfig',
    ),
    'Admin_EasyNewsLetter_EmailList' => 
    array (
      'label' => 'Email List',
      'addon' => 'vrzxd3o',
      'script' => '/data/_addoncode/vrzxd3o/Admin/EmailList/EmailList.php',
      'class' => 'EasyNewsLetter_EmailList',
    ),
    'Admin_EasyNewsLetter_Mailing' => 
    array (
      'label' => 'Mailing Form',
      'addon' => 'vrzxd3o',
      'script' => '/data/_addoncode/vrzxd3o/Admin/Mailing/Mailing.php',
      'class' => 'EasyNewsLetter_Mailing',
    ),
  ),
);

$meta_data = array (
);