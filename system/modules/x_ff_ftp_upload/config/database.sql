-- ********************************************************
-- *                                                      *
-- * IMPORTANT NOTE                                       *
-- *                                                      *
-- * Do not import this file manually but use the Contao  *
-- * install tool to create and maintain database tables! *
-- *                                                      *
-- ********************************************************

-- 
-- Table `tl_form_field`
-- 

CREATE TABLE `tl_form_field` (

  `ftpHost` varchar(255) NOT NULL default '',
  `ftpUser` varchar(255) NOT NULL default '',
  `ftpPass` varchar(255) NOT NULL default '',
  `ftpDir` varchar(255) NOT NULL default '',
  `ftpRenameFiles` char(1) NOT NULL default '',

) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------