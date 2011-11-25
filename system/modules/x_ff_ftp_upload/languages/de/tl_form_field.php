<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2011 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  numero2 - Agentur für Internetdienstleistungen <www.numero2.de>
 * @author     Benny Born <benny.born@numero2.de>
 * @package    Language
 * @license    LGPL
 * @filesource
 */


/**
 * Form fields
 */
$GLOBALS['TL_LANG']['FFL']['ftpupload']   = array('FTP-Upload', 'Ein einzeiliges Eingabefeld zur Übertragung lokaler Dateien auf einen FTP Server.');


/**
 * Fields
 */
$GLOBALS['TL_LANG']['tl_form_field']['ftpHost']        = array('Host', 'Hostname oder IP-Adresse');
$GLOBALS['TL_LANG']['tl_form_field']['ftpUser']        = array('Benutzername', '');
$GLOBALS['TL_LANG']['tl_form_field']['ftpPass']        = array('Passwort', '');
$GLOBALS['TL_LANG']['tl_form_field']['ftpDir']         = array('Verzeichnis', 'Pfad zum Verzeichnis in das die Dateien hochgeladen werden sollen');
$GLOBALS['TL_LANG']['tl_form_field']['ftpRenameFiles'] = array('Datei umbenennen?', 'Hängt eine Zeichenkette an den Dateinamen an um Überschreiben zu vermeiden');


/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_form_field']['ftp_legend']     = 'FTP Verbindungsdaten';

?>