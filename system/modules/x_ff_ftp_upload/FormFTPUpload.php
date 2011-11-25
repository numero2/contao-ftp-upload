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
 * @package    Frontend
 * @license    LGPL
 * @filesource
 */


/**
 * Class FormFTPUpload
 *
 * FTP upload field.
 * @copyright  numero2 - Agentur für Internetdienstleistungen <www.numero2.de>
 * @author     Benny Born <benny.born@numero2.de>
 * @package    Controller
 */
class FormFTPUpload extends Widget implements uploadable {

	/**
	 * Submit user input
	 * @var boolean
	 */
	protected $blnSubmitInput = true;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'form_widget';


	/**
	 * Add specific attributes
	 * @param string
	 * @param mixed
	 */
	public function __set($strKey, $varValue) {

		switch ($strKey) {

			case 'mandatory':
				$this->arrConfiguration['mandatory'] = $varValue ? true : false;
				break;

			default:
				parent::__set($strKey, $varValue);
				break;
		}
	}


	/**
	 * Validate input and set value
	 */
	public function validate() {

		// No file specified
		if( !isset($_FILES[$this->strName]) || empty($_FILES[$this->strName]['name']) ) {

			if( $this->mandatory ) {

				if( $this->strLabel == '' ) {
					$this->addError($GLOBALS['TL_LANG']['ERR']['mdtryNoLabel']);
				} else {
					$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['mandatory'], $this->strLabel));
				}
			}

			return;
		}

		$file = $_FILES[$this->strName];
		$maxlength_kb = $this->getReadableSize($this->maxlength);

		// Romanize the filename
		$file['name'] = utf8_romanize($file['name']);

		// File was not uploaded
		if( !is_uploaded_file($file['tmp_name']) ) {

			if( in_array($file['error'], array(1, 2)) ) {
				$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filesize'], $maxlength_kb));
				$this->log('File "'.$file['name'].'" exceeds the maximum file size of '.$maxlength_kb, 'FormFTPUploadleUpload validate()', TL_ERROR);
                $this->value = 'File "'.$file['name'].'" exceeds the maximum file size of '.$maxlength_kb;
			}

			if( $file['error'] == 3 ) {
				$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filepartial'], $file['name']));
				$this->log('File "'.$file['name'].'" was only partially uploaded', 'FormFTPUpload validate()', TL_ERROR);
                $this->value = 'File "'.$file['name'].'" was only partially uploaded';
			}

			unset($_FILES[$this->strName]);
			return;
		}

		// File is too big
		if( $this->maxlength > 0 && $file['size'] > $this->maxlength ) {

			$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filesize'], $maxlength_kb));
			$this->log('File "'.$file['name'].'" exceeds the maximum file size of '.$maxlength_kb, 'FormFTPUpload validate()', TL_ERROR);
            $this->value = 'File "'.$file['name'].'" exceeds the maximum file size of '.$maxlength_kb;

			unset($_FILES[$this->strName]);
			return;
		}

		$pathinfo = pathinfo($file['name']);
		$uploadTypes = trimsplit(',', $this->extensions);

		// File type is not allowed
		if( !in_array(strtolower($pathinfo['extension']), $uploadTypes) ) {

			$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filetype'], $pathinfo['extension']));
			$this->log('File type "'.$pathinfo['extension'].'" is not allowed to be uploaded ('.$file['name'].')', 'FormFTPUpload validate()', TL_ERROR);
            $this->value = 'File type "'.$pathinfo['extension'].'" is not allowed to be uploaded ('.$file['name'].')';

			unset($_FILES[$this->strName]);
			return;
		}

		if( ($arrImageSize = @getimagesize($file['tmp_name'])) != false ) {

			// Image exceeds maximum image width
			if ($arrImageSize[0] > $GLOBALS['TL_CONFIG']['imageWidth'])
			{
				$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['filewidth'], $file['name'], $GLOBALS['TL_CONFIG']['imageWidth']));
				$this->log('File "'.$file['name'].'" exceeds the maximum image width of '.$GLOBALS['TL_CONFIG']['imageWidth'].' pixels', 'FormFTPUpload validate()', TL_ERROR);
                $this->value = 'File "'.$file['name'].'" exceeds the maximum image width of '.$GLOBALS['TL_CONFIG']['imageWidth'].' pixels';

				unset($_FILES[$this->strName]);
				return;
			}

			// Image exceeds maximum image height
			if( $arrImageSize[1] > $GLOBALS['TL_CONFIG']['imageHeight'] ) {

				$this->addError(sprintf($GLOBALS['TL_LANG']['ERR']['fileheight'], $file['name'], $GLOBALS['TL_CONFIG']['imageHeight']));
				$this->log('File "'.$file['name'].'" exceeds the maximum image height of '.$GLOBALS['TL_CONFIG']['imageHeight'].' pixels', 'FormFTPUpload validate()', TL_ERROR);
                $this->value = 'File "'.$file['name'].'" exceeds the maximum image height of '.$GLOBALS['TL_CONFIG']['imageHeight'].' pixels';

				unset($_FILES[$this->strName]);
				return;
			}
		}

		// Store file on the given ftp server
		if( !$this->hasErrors() ) {
        
            if( $this->ftpHost && $this->ftpUser ) {
            
                // open connection
                $hFTP = NULL;
                $hFTP = ftp_connect($this->ftpHost);
                
                if( $hFTP ) {
                
                    // send login credentials
                    $bLogin = false;
                    $bLogin = ftp_login( $hFTP, $this->ftpUser, ($this->ftpPass ? $this->ftpPass : '') );
                    
                    if( $bLogin ) {
                    
                        $sDir = $this->ftpDir ? $this->ftpDir : '/';
                    
                        // change directory if needed
                        if( $sDir != '/' ) {
                            ftp_chdir( $hFTP, $this->ftpDir );
                        }
                        
                        $remoteFileName = $file['name'];
                        
                        // rename file
                        if( $this->ftpRenameFiles ) {
                            $remoteFileName .= '.'.substr(md5(microtime(true)),0,4);
                        }
                        
                        // begin upload
                        $bUpload = false;
                        $bUpload = ftp_put( $hFTP, $remoteFileName, $file['tmp_name'], FTP_BINARY );
                        
                        if( $bUpload ) {
                            $this->value = $this->ftpHost.':'.$sDir.$remoteFileName;
                            $this->log('File "'.$file['name'].'" successfully uploaded to FTP server '.$this->ftpHost.'', 'FormFTPUpload validate()', TL_FILES);
                        } else {
                            $this->value = 'File could not be uploaded';
                            $this->log('File "'.$file['name'].'" could not be uploaded to FTP server '.$this->ftpHost.'', 'FormFTPUpload validate()', TL_ERROR);
                        }

                    } else {
                        $this->log('File "'.$file['name'].'" could not be uploaded to FTP server '.$this->ftpHost.' because of invalid login credentials', 'FormFTPUpload validate()', TL_ERROR);            
                    }
                }

            } else {
                $this->log('File "'.$file['name'].'" could not be uploaded to FTP server '.$this->ftpHost.' because of missing host or username', 'FormFTPUpload validate()', TL_ERROR);            
            }

		} else {
            $this->value = 'File could not be uploaded';
        }

		unset($_FILES[$this->strName]);
	}


	/**
	 * Generate the widget and return it as string
	 * @return string
	 */
	public function generate() {

		return sprintf(
            '<input type="file" name="%s" id="ctrl_%s" class="ftp upload%s"%s />'
        ,   $this->strName
        ,   $this->strId
        ,   (strlen($this->strClass) ? ' ' . $this->strClass : '')
        ,   $this->getAttributes()
        ).$this->addSubmit();
	}
}

?>