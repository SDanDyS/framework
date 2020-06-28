<?php
	namespace DataHandler;
	use DatabaseConnection\Connection;
	use \Files;
	/*
	*Recordset is the database class, which will do the CRUD for you
	* DELETE
	* UPDATE
	* INSERT
	* SELECT
	*/
	class Recordset
	{
		/*
		* $row will keep the database table its column names
		* $rowArray will keep the fetched/inserted data
		* $index decides what the count will be for $rowArray
		* $table sets the table name
		* $conn sets the connection for the table to retrieve/insert from
		* $errorSuppression "decides" whether errors should be thrown, such as undefined etc.
		*/
		private $row = [];

		private $rowArray = [];

		private $index = -1;

		private $errorSuppression;

		private $conn;

		private static $allowedExtensions;

		private static $nameDistortion;

		private static $image;

		/*
		* $table will be assigned at creation time, this way it'll be accessable by
		* the script at any given time.
		* This way you can also target the correct database table
		*/
		private $table;

		public function __construct($query, $table, $errorSuppression = true)
		{
			$this->conn = Connection::setConnection();

			$this->errorSuppression = $errorSuppression;

			$this->table = $table;

			$this->setTableColumns();

			self::setNameDistortion();

			self::setExtension();

			if (!empty($query)) 
			{
				$this->executeQuery($query);
			}
		}

		/*
		* call save() to start the CRUD process
		*/
		public function save($mixture = NULL)
		{
			/*
			* Check in what way the request was send
			* Based on the REQUEST method, request the method to save
			* If for some reason the method does not exist, call suppressionCaller
			* NOTICE: Send an argument along to save, to fetch both GET and POST.
			* Send a hierarchy argument along, to decide which one should overwrite
			*/
			$action = "saveBy{$_SERVER['REQUEST_METHOD']}";

			if (is_null($mixture))
			{
				if (method_exists($this, $action))
				{
					$this->$action();
				} else
				{
					self::suppressionCaller(__METHOD__, $action);
				}			
			} else
			{
				$this->getMixedMETHOD($mixture);
			}
		}

		private function getMixedMETHOD($mixture)
		{
			$fetchMethods = ["POST/GET" => "saveByPOSTMixture", "GET/POST" => "saveByGETMixture", "POST" => "saveByPOST", "GET" => "saveByGET"];

			$request = $mixture;

			if(array_key_exists($request, $fetchMethods))
			{
				$action = $fetchMethods[$request];

				$this->$action();
			} else
			{
				self::getSuppressionCaller(__METHOD__, $mixture);
			}
		}

		private function saveByPOSTMixture()
		{
			/*
			* Combination between GET and POST
			* In which the $_POST method is the leading method
			*/
			if (count($_POST) > 0) 
			{
				foreach ($_POST as $key => $value) 
				{
					if ($this->hasField($key, $this->row))
					{
						if (isset($_GET[$key]))
						{
							$value = "{$_POST[$key]}{$_GET[$key]}";
						}
						$this->setField($key, $value);
					}
				}

				$this->setImages();

				$this->executeQuery();
			}
		}

		private function saveByGETMixture()
		{
			/*
			* Combination between GET and POST
			* In which the $_GET method is the leading method
			*/
			if (count($_GET) > 0)
			{
				foreach ($_GET as $key => $value) 
				{
					if ($this->hasField($key, $this->row))
					{
						if (isset($_POST[$key]))
						{
							$value = "{$_GET[$key]}{$_POST[$key]}";
						}
						$this->setField($key, $value);
					}
				}

				$this->setImages();

				$this->executeQuery();
			}
		}

		/*
		* if $_POST is not empty, start looping through it to assign keys and values to write to database
		*/
		private function saveByPOST()
		{
			if (count($_POST) > 0) 
			{
				foreach ($_POST as $key => $value) 
				{
					if ($this->hasField($key, $this->row))
					{
						$this->setField($key, $value);
					}
				}

				$this->setImages();

				$this->executeQuery();
			}	
		}

		/*
		* if $_GET is not empty, start looping through it to assign keys and values to write to database
		*/
		private function saveByGET()
		{
			if (count($_GET) > 0)
			{
				foreach ($_GET as $key => $value)
				{
					if ($this->hasField($key))
					{
						$this->setField($key, $value);
					}
				}

				$this->setImages();

				$this->executeQuery();
			}
		}

		public static function setExtension($allowedExtensions = NULL)
		{
			
			//OUT OF USE, BUT REMAINED AS A LIBRARY
			/*$mime_map = [
				'video/3gpp2'                                                               => '3g2',
				'video/3gp'                                                                 => '3gp',
				'video/3gpp'                                                                => '3gp',
				'application/x-compressed'                                                  => '7zip',
				'audio/x-acc'                                                               => 'aac',
				'audio/ac3'                                                                 => 'ac3',
				'application/postscript'                                                    => 'ai',
				'audio/x-aiff'                                                              => 'aif',
				'audio/aiff'                                                                => 'aif',
				'audio/x-au'                                                                => 'au',
				'video/x-msvideo'                                                           => 'avi',
				'video/msvideo'                                                             => 'avi',
				'video/avi'                                                                 => 'avi',
				'application/x-troff-msvideo'                                               => 'avi',
				'application/macbinary'                                                     => 'bin',
				'application/mac-binary'                                                    => 'bin',
				'application/x-binary'                                                      => 'bin',
				'application/x-macbinary'                                                   => 'bin',
				'image/bmp'                                                                 => 'bmp',
				'image/x-bmp'                                                               => 'bmp',
				'image/x-bitmap'                                                            => 'bmp',
				'image/x-xbitmap'                                                           => 'bmp',
				'image/x-win-bitmap'                                                        => 'bmp',
				'image/x-windows-bmp'                                                       => 'bmp',
				'image/ms-bmp'                                                              => 'bmp',
				'image/x-ms-bmp'                                                            => 'bmp',
				'application/bmp'                                                           => 'bmp',
				'application/x-bmp'                                                         => 'bmp',
				'application/x-win-bitmap'                                                  => 'bmp',
				'application/cdr'                                                           => 'cdr',
				'application/coreldraw'                                                     => 'cdr',
				'application/x-cdr'                                                         => 'cdr',
				'application/x-coreldraw'                                                   => 'cdr',
				'image/cdr'                                                                 => 'cdr',
				'image/x-cdr'                                                               => 'cdr',
				'zz-application/zz-winassoc-cdr'                                            => 'cdr',
				'application/mac-compactpro'                                                => 'cpt',
				'application/pkix-crl'                                                      => 'crl',
				'application/pkcs-crl'                                                      => 'crl',
				'application/x-x509-ca-cert'                                                => 'crt',
				'application/pkix-cert'                                                     => 'crt',
				'text/css'                                                                  => 'css',
				'text/x-comma-separated-values'                                             => 'csv',
				'text/comma-separated-values'                                               => 'csv',
				'application/vnd.msexcel'                                                   => 'csv',
				'application/x-director'                                                    => 'dcr',
				'application/vnd.openxmlformats-officedocument.wordprocessingml.document'   => 'docx',
				'application/x-dvi'                                                         => 'dvi',
				'message/rfc822'                                                            => 'eml',
				'application/x-msdownload'                                                  => 'exe',
				'video/x-f4v'                                                               => 'f4v',
				'audio/x-flac'                                                              => 'flac',
				'video/x-flv'                                                               => 'flv',
				'image/gif'                                                                 => 'gif',
				'application/gpg-keys'                                                      => 'gpg',
				'application/x-gtar'                                                        => 'gtar',
				'application/x-gzip'                                                        => 'gzip',
				'application/mac-binhex40'                                                  => 'hqx',
				'application/mac-binhex'                                                    => 'hqx',
				'application/x-binhex40'                                                    => 'hqx',
				'application/x-mac-binhex40'                                                => 'hqx',
				'text/html'                                                                 => 'html',
				'image/x-icon'                                                              => 'ico',
				'image/x-ico'                                                               => 'ico',
				'image/vnd.microsoft.icon'                                                  => 'ico',
				'text/calendar'                                                             => 'ics',
				'application/java-archive'                                                  => 'jar',
				'application/x-java-application'                                            => 'jar',
				'application/x-jar'                                                         => 'jar',
				'image/jp2'                                                                 => 'jp2',
				'video/mj2'                                                                 => 'jp2',
				'image/jpx'                                                                 => 'jp2',
				'image/jpm'                                                                 => 'jp2',
				'image/jpeg'                                                                => 'jpeg',
				'image/pjpeg'                                                               => 'jpeg',
				'application/x-javascript'                                                  => 'js',
				'application/json'                                                          => 'json',
				'text/json'                                                                 => 'json',
				'application/vnd.google-earth.kml+xml'                                      => 'kml',
				'application/vnd.google-earth.kmz'                                          => 'kmz',
				'text/x-log'                                                                => 'log',
				'audio/x-m4a'                                                               => 'm4a',
				'audio/mp4'                                                                 => 'm4a',
				'application/vnd.mpegurl'                                                   => 'm4u',
				'audio/midi'                                                                => 'mid',
				'application/vnd.mif'                                                       => 'mif',
				'video/quicktime'                                                           => 'mov',
				'video/x-sgi-movie'                                                         => 'movie',
				'audio/mpeg'                                                                => 'mp3',
				'audio/mpg'                                                                 => 'mp3',
				'audio/mpeg3'                                                               => 'mp3',
				'audio/mp3'                                                                 => 'mp3',
				'video/mp4'                                                                 => 'mp4',
				'video/mpeg'                                                                => 'mpeg',
				'application/oda'                                                           => 'oda',
				'audio/ogg'                                                                 => 'ogg',
				'video/ogg'                                                                 => 'ogg',
				'application/ogg'                                                           => 'ogg',
				'font/otf'                                                                  => 'otf',
				'application/x-pkcs10'                                                      => 'p10',
				'application/pkcs10'                                                        => 'p10',
				'application/x-pkcs12'                                                      => 'p12',
				'application/x-pkcs7-signature'                                             => 'p7a',
				'application/pkcs7-mime'                                                    => 'p7c',
				'application/x-pkcs7-mime'                                                  => 'p7c',
				'application/x-pkcs7-certreqresp'                                           => 'p7r',
				'application/pkcs7-signature'                                               => 'p7s',
				'application/pdf'                                                           => 'pdf',
				'application/octet-stream'                                                  => 'pdf',
				'application/x-x509-user-cert'                                              => 'pem',
				'application/x-pem-file'                                                    => 'pem',
				'application/pgp'                                                           => 'pgp',
				'application/x-httpd-php'                                                   => 'php',
				'application/php'                                                           => 'php',
				'application/x-php'                                                         => 'php',
				'text/php'                                                                  => 'php',
				'text/x-php'                                                                => 'php',
				'application/x-httpd-php-source'                                            => 'php',
				'image/png'                                                                 => 'png',
				'image/x-png'                                                               => 'png',
				'application/powerpoint'                                                    => 'ppt',
				'application/vnd.ms-powerpoint'                                             => 'ppt',
				'application/vnd.ms-office'                                                 => 'ppt',
				'application/msword'                                                        => 'ppt',
				'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'pptx',
				'application/x-photoshop'                                                   => 'psd',
				'image/vnd.adobe.photoshop'                                                 => 'psd',
				'audio/x-realaudio'                                                         => 'ra',
				'audio/x-pn-realaudio'                                                      => 'ram',
				'application/x-rar'                                                         => 'rar',
				'application/rar'                                                           => 'rar',
				'application/x-rar-compressed'                                              => 'rar',
				'audio/x-pn-realaudio-plugin'                                               => 'rpm',
				'application/x-pkcs7'                                                       => 'rsa',
				'text/rtf'                                                                  => 'rtf',
				'text/richtext'                                                             => 'rtx',
				'video/vnd.rn-realvideo'                                                    => 'rv',
				'application/x-stuffit'                                                     => 'sit',
				'application/smil'                                                          => 'smil',
				'text/srt'                                                                  => 'srt',
				'image/svg+xml'                                                             => 'svg',
				'application/x-shockwave-flash'                                             => 'swf',
				'application/x-tar'                                                         => 'tar',
				'application/x-gzip-compressed'                                             => 'tgz',
				'image/tiff'                                                                => 'tiff',
				'font/ttf'                                                                  => 'ttf',
				'text/plain'                                                                => 'txt',
				'text/x-vcard'                                                              => 'vcf',
				'application/videolan'                                                      => 'vlc',
				'text/vtt'                                                                  => 'vtt',
				'audio/x-wav'                                                               => 'wav',
				'audio/wave'                                                                => 'wav',
				'audio/wav'                                                                 => 'wav',
				'application/wbxml'                                                         => 'wbxml',
				'video/webm'                                                                => 'webm',
				'image/webp'                                                                => 'webp',
				'audio/x-ms-wma'                                                            => 'wma',
				'application/wmlc'                                                          => 'wmlc',
				'video/x-ms-wmv'                                                            => 'wmv',
				'video/x-ms-asf'                                                            => 'wmv',
				'font/woff'                                                                 => 'woff',
				'font/woff2'                                                                => 'woff2',
				'application/xhtml+xml'                                                     => 'xhtml',
				'application/excel'                                                         => 'xl',
				'application/msexcel'                                                       => 'xls',
				'application/x-msexcel'                                                     => 'xls',
				'application/x-ms-excel'                                                    => 'xls',
				'application/x-excel'                                                       => 'xls',
				'application/x-dos_ms_excel'                                                => 'xls',
				'application/xls'                                                           => 'xls',
				'application/x-xls'                                                         => 'xls',
				'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'         => 'xlsx',
				'application/vnd.ms-excel'                                                  => 'xlsx',
				'application/xml'                                                           => 'xml',
				'text/xml'                                                                  => 'xml',
				'text/xsl'                                                                  => 'xsl',
				'application/xspf+xml'                                                      => 'xspf',
				'application/x-compress'                                                    => 'z',
				'application/x-zip'                                                         => 'zip',
				'application/zip'                                                           => 'zip',
				'application/x-zip-compressed'                                              => 'zip',
				'application/s-compressed'                                                  => 'zip',
				'multipart/x-zip'                                                           => 'zip',
				'text/x-scriptzsh'                                                          => 'zsh',
			];*/

			$extensionsLibrary = 
			[
				// Image formats
				"IMAGE" => 
				[
					'jpg' => 'image/jpeg',
					'jpeg' => 'image/jpeg',
					'jpe' => 'image/jpeg',
					'gif' => 'image/gif',
					'png' => 'image/png',
					'bmp' => 'image/bmp',
					'tif' => 'image/tiff',
					'tiff' => 'image/tiff',
					'ico' => 'image/x-icon'
				],
				// Video formats
				"VIDEO" =>
				[
					'asf' => 'video/asf',
					'asx' => 'video/asf',
					'wax' => 'video/asf',
					'wmv' => 'video/asf',
					'wmx' => 'video/asf',
					'avi' => 'video/avi',
					'divx' => 'video/divx',
					'flv' => 'video/x-flv',
					'mov' => 'video/quicktime',
					'qt' => 'video/quicktime',
					'mpeg' => 'video/mpeg',
					'mpg' => 'video/mpeg',
					'mpe' => 'video/mpeg',
					'mp4' => 'video/mp4',
					'm4v' => 'video/mp4',
					'ogv' => 'video/ogg',
					'mkv' => 'video/x-matroska'				
				],
				// Text formats
				"TEXT" =>
				[
					'txt' => 'text/plain',
					'asc' => 'text/plain',
					'c' => 'text/plain',
					'cc' => 'text/plain',
					'h' => 'text/plain',
					'csv' => 'text/csv',
					'tsv' => 'text/tab-separated-values',
					'ics' => 'text/calendar',
					'rtx' => 'text/richtext',
					'css' => 'text/css',
					'htm' => 'text/html',
					'html' => 'text/html'		
				],
				// Audio formats
				"AUDIO" =>
				[
					'mp3' => 'audio/mpeg',
					'm4a' => 'audio/mpeg',
					'm4b' => 'audio/mpeg',
					'ra' => 'audio/x-realaudio',
					'ram' => 'audio/x-realaudio',
					'wav' => 'audio/wav',
					'ogg' => 'audio/ogg',
					'oga' => 'audio/ogg',
					'mid' => 'audio/midi',
					'midi' => 'audio/midi',
					'wma' => 'audio/wma',
					'mka' => 'audio/x-matroska'			
				],
				// Misc application formats
				"MISC" =>
				[
					'rtf' => 'application/rtf',
					'js' => 'application/javascript',
					'pdf' => 'application/pdf',
					'swf' => 'application/x-shockwave-flash',
					'class' => 'application/java',
					'tar' => 'application/x-tar',
					'zip' => 'application/zip',
					'gz' => 'application/x-gzip',
					'gzip' => 'application/x-gzip',
					'rar' => 'application/rar',
					'7z' => 'application/x-7z-compressed'
				],
				// MS Office formats
				"MSOFFICE" =>
				[
					'doc' => 'application/msword',
					'pot' => 'application/vnd.ms-powerpoint',
					'pps' => 'application/vnd.ms-powerpoint',
					'ppt' => 'application/vnd.ms-powerpoint',
					'wri' => 'application/vnd.ms-write',
					'xla' => 'application/vnd.ms-excel',
					'xls' => 'application/vnd.ms-excel',
					'xlt' => 'application/vnd.ms-excel',
					'xlw' => 'application/vnd.ms-excel',
					'mdb' => 'application/vnd.ms-access',
					'mpp' => 'application/vnd.ms-project',
					'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
					'docm' => 'application/vnd.ms-word.document.macroEnabled.12',
					'dotx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
					'dotm' => 'application/vnd.ms-word.template.macroEnabled.12',
					'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
					'xlsb' => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
					'xltx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
					'xltm' => 'application/vnd.ms-excel.template.macroEnabled.12',
					'xlam' => 'application/vnd.ms-excel.addin.macroEnabled.12',
					'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
					'pptm' => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
					'ppsx' => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
					'ppsm' => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12',
					'potx' => 'application/vnd.openxmlformats-officedocument.presentationml.template',
					'potm' => 'application/vnd.ms-powerpoint.template.macroEnabled.12',
					'ppam' => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
					'sldx' => 'application/vnd.openxmlformats-officedocument.presentationml.slide',
					'sldm' => 'application/vnd.ms-powerpoint.slide.macroEnabled.12',
					'onetoc' => 'application/onenote',
					'onetoc2' => 'application/onenote',
					'onetmp' => 'application/onenote',
					'onepkg' => 'application/onenote'				
				],
				// OpenOffice formats
				"OPENOFFICE" =>
				[
					'odt' => 'application/vnd.oasis.opendocument.text',
					'odp' => 'application/vnd.oasis.opendocument.presentation',
					'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
					'odg' => 'application/vnd.oasis.opendocument.graphics',
					'odc' => 'application/vnd.oasis.opendocument.chart',
					'odb' => 'application/vnd.oasis.opendocument.database',
					'odf' => 'application/vnd.oasis.opendocument.formula'				
				],
				// WordPerfect formats
				"WORDPERFECT" =>
				[
					'wp' => 'application/wordperfect',
					'wpd' => 'application/wordperfect',
					'wpd' => 'application/wordperfect'				
				]
			];

			if (is_null($allowedExtensions))
			{
				foreach ($extensionsLibrary as $innerArray)
				{
					foreach($innerArray as $k => $v)
					{
						self::$allowedExtensions[$k] = $v;
					}
				}
			} else
			{
				$formattedExtensions = str_replace(" ", ",", $allowedExtensions);
				$formattedExtensions = str_replace(",,", ",", $formattedExtensions);
				$settedExtensions = explode(",", $formattedExtensions);

				foreach ($settedExtensions as $key => $extension)
				{

					$extension = strtoupper($extension);

					if (array_key_exists($extension, $extensionsLibrary))
					{
						foreach ($extensionsLibrary[$extension] as $k => $v)
						{
							self::$allowedExtensions[$k] = $v;
						}
					} else
					{
						self::suppressionCaller(__METHOD__, $key);
					}
				}			
			}
			
		}

		public static function setNameDistortion($distortion = TRUE)
		{
			if (!is_bool($distortion))
			{
				self::getSuppressionCaller(__METHOD__, $distortion);
			}
			self::$nameDistortion = $distortion;
		}

		public static function getNameDistortion()
		{
			return self::$nameDistortion;
		}

		public static function setImageObject($object)
		{
			self::$image = $object;
		}

		private function setImages()
		{
			if (count($_FILES) > 0) 
			{
				foreach($_FILES as $fileName => $singleFile)
				{
					$name = $singleFile["name"];
					$tmpName = $singleFile["tmp_name"];
					$type = $singleFile["type"];
					$size = $singleFile["size"];
					$error = $singleFile["error"];

					/*
					* Check whether name is empty
					*/
					if (!empty($name))
					{
						/*
						* Check whether the default upload is UPLOAD_ERR_OK
						*/
						if ($error === UPLOAD_ERR_OK)
						{
							$dissolvedFileName = explode(".", $name);
							$fileExtension = strtolower(end($dissolvedFileName));

							if (array_key_exists($fileExtension, self::$allowedExtensions))
							{
								$finfo = new \finfo(FILEINFO_MIME_TYPE);
								$mimeType = in_array($finfo->file($tmpName), self::$allowedExtensions, true);

								if ($mimeType && self::$allowedExtensions[$fileExtension] === $finfo->file($tmpName))
								{

									$image = self::$image;
									
									if (empty($image->getParams("path")))
									{
										self::getSuppressionCaller(__METHOD__, "File path");
									} else
									{
										if(!is_dir($image->getParams("path")))
										{
											mkdir($image->getParams("path"), $image->getParams("mode"), $image->getParams("recursive"));
										}

										if (self::$nameDistortion)
										{
											$name = uniqid("", true);
										} else if (!self::$nameDistortion)
										{
											$name = $name;
										} else 
										{
											self::errorSuppression(__METHOD__, self::$nameDistortion);
										}

										$filePath = $image->getParams("path");

										$completePath = "{$filePath}/{$name}.{$fileExtension}";

										$pathTrimmer = Files\FilesController::getUrlBase(Files\FilesController::getBaseDir());

										$databasePath = str_replace($pathTrimmer, "", $completePath);

										$this->setField($fileName, $databasePath);

										move_uploaded_file($tmpName, $completePath);

										Files\FilesController::chmod($completePath, Files\FilesController::getFilePermission());
									}
								} else
								{
									self::errorSuppression(__METHOD__, $mimeType);
								}								
							}
						} else
						{
							exit(new UploadException($error));
						}
					}
				}		
			}
		}

		private function setTableColumns()
		{
			/*
			* Retrieve the column names and types when method executeQuery is fired
			*/
			$columnRetriever = $this->conn->prepare("SHOW COLUMNS FROM `{$this->table}`");

			$columnRetriever->execute();

			/*
			* Store the fetched result
			*/
			$result = $columnRetriever->get_result();
			
			/*
			* Store the retrieved information of the columns
			*/
			while ($row = $result->fetch_assoc()) 
			{
				$this->row[$row["Field"]]["Field"] = $row["Field"];
				$this->row[$row["Field"]]["Type"] = $row["Type"];
				$this->row[$row["Field"]]["Null"] = $row["Null"];
				$this->row[$row["Field"]]["Key"] = $row["Key"];
				$this->row[$row["Field"]]["Default"] = $row["Default"];
				$this->row[$row["Field"]]["Extra"] = $row["Extra"];

				$this->setField($row["Field"], "");
			}
		}

		public function getTableColumns()
		{
			return $this->row;
		}

		private function getPrimaryKey()
		{
			$uniqueID = NULL;
			foreach ($this->row as $entryArray) 
			{
				if ($entryArray["Key"] === "PRI")
				{
					$uniqueID = $entryArray["Field"];
				}
			}
			return $uniqueID;
		}

		private function executeQuery($sql = NULL)
		{

			if (!is_null($sql)) 
			{
				$haystack = 
				[
					"select" => "SELECT",
					"update" => "UPDATE",
					"insert" => "INSERT",
					"delete" => "DELETE"
				];

				$sqlExplosion = explode(" ", $sql);
				$sqlAction = $sqlExplosion[0];

				
			// 	* SQL commands

			 	$select = $haystack["select"];
			 	$insert = $haystack["insert"];
			 	$update = $haystack["update"];
			 	$delete = $haystack["delete"];


				$completedQuery = $this->conn->prepare($sql);

				/*
				* There has been a SELECT statement
				* Retrieve data
				* else do whatever the query has set
				*/
				if($sqlAction === $select)
				{

					$completedQuery->execute();

					$result = $completedQuery->get_result();

					$num_of_rows = $result->num_rows;

					/*
					* loop through the rows and add it
					* if the resultset is 0, there is no need to fetch data, it's simply set to fetch the keys 
					*/
					if ($num_of_rows > 0) 
					{

						$this->setIndex();

						while ($row = $result->fetch_assoc())
						{
							foreach ($row as $key => $columnName) 
							{
								$this->rowArray[$this->index][$key] = $row[$key];
							}

							/*
							* increment $this->index with 1.
							*/
							$this->next();
						}

						/*
						* Reset $this->index, so during fetch time $this->index starts at 0.
						*/
						$this->resetIndex();
					}
					
				} else
				{
					/*
					* execute the query if it does not contain a SELECT statement
					*/
					$completedQuery->execute();

					/*
					* if the query contains an update or insert, retrieve the unique key. 
					*/
					if ($sqlAction === $insert || $sqlAction === $update)
					{
						/*
						* call the primary key
						* if it is the primary key, set the field to the fetched ID
						*/
						$uniqueID = $this->getPrimaryKey();
						$this->setField($uniqueID, $completedQuery->insert_id);
					}
				}
			} else
			{
				$this->selectQuery();
			}
		}

		private function selectQuery()
		{
			$requiredRow = NULL;
			
			$uniqueID = $this->getPrimaryKey();

			/*
			* if the primary key is set, but there is no value given to it, set to 0
			* this will ensure the query won't fail.
			*/
			if ($this->getField($uniqueID) === "" || is_null($this->getField($uniqueID)) || $this->getField($uniqueID) === "undefined")
			{
				$this->setField($uniqueID, 0);
			}
			
			$sql = "SELECT * FROM `{$this->table}` WHERE `{$uniqueID}` = ?";

			$selectQuery = $this->conn->prepare($sql);

			/*
			* Set all the type arguments to string
			* This is done, because type jugling at the time of writing could not be done by the developer
			*/
			$selectQuery->bind_param("s", $uniqueSelector);

			$uniqueSelector = $this->getField($uniqueID);

			$selectQuery->execute();

			$result = $selectQuery->get_result();

			$num_of_rows = $result->num_rows;

			if ($num_of_rows > 0) 
			{
				$this->updateQuery();
			} else
			{
				$this->insertQuery();
			}
		}

		private function insertQuery()
		{
			/*
			* Instantiate variables
			*/
			$createQuery = NULL;
			$placeholders = NULL;
			$bindPARAM = NULL;
			$completeSet = NULL;
			$counter = 0;

			/*
			* Array to push values which will be bound later on
			* This way SQL injection is prevented
			*/
			$args = [];

			$createQuery = "INSERT INTO `{$this->table}` (";

			/*
			* Set index count to 0
			*/
			$this->setIndex();

			/*
			* Start looping through the required elements and create a query string
			*/
			foreach($this->rowArray[$this->index] as $key => $value)
			{
				/*
				* If the primary key equals the key in the loop
				* If yes, return the loop and go on with the next key
				*/
				if ($this->getPrimaryKey() == $key)
				{
					$counter++;
					continue;
				}
				/*
				* If the required field is empty, set to NULL
				*/
				if ($value === "")
				{
					$this->setField($key, NULL);
				}

				/*
				* Check whether the end of the loop has been reached
				* Yes, start closing the query string
				* No, keep the query string open
				*/
				if ($counter === count($this->rowArray[$this->index]) - 1) 
				{
					$placeholders .= "?)";
					$createQuery .= "{$key}";
				} else
				{
					$placeholders .= "?,";
					$createQuery .= "{$key},";
				}

				/*
				* Types for bind_param();
				*/
				$bindPARAM .= "s";

				/*
				* Values / References pushed
				*/
				$args[] = $value;

				$counter++;
			}
			
			$createQuery .= ") VALUES (";

			/*
			* Create the complete query string
			*/
			$completeSet = "{$createQuery}{$placeholders}";

			/*
			* Create the query object
			*/
			$stmt = $this->conn->prepare($completeSet);

			/*
			* Bind the argument array and unpack it
			*/
			$stmt->bind_param($bindPARAM, ...$args);

			/*
			* Execute the created SQL object
			*/
			$stmt->execute();

			/*
			* Return inserted key and assign it to its respective field
			*/
			$this->setField($this->getPrimaryKey(), $stmt->insert_id);

			/*
			* Reset $this->index, so during fetch time $this->index starts at 0.
			*/
			$this->resetIndex();
		}

		private function updateQuery()
		{
			/*
			* Instantiate variables
			*/
			$createQuery = NULL;
			$bindPARAM = NULL;
			$counter = 0;

			/*
			* Array to push values which will be bound later on
			* This way SQL injection is prevented
			*/
			$args = [];

			$createQuery = "UPDATE `{$this->table}` SET";

			/*
			* Set index count to 0
			*/
			$this->setIndex();

			/*
			* Start looping through the required elements and create a query string
			*/
			foreach ($this->rowArray[$this->index] as $key => $value)
			{

				/*
				* Types for bind_param();
				*/
				$bindPARAM .= "s";
				
				/*
				* If the primary key equals the key in the loop
				* If yes, return the loop and go on with the next key
				*/
				if ($this->getPrimaryKey() == $key)
				{
					$counter++;
					continue;
				}

				/*
				* Values / References pushed
				*/
				$args[] = $value;

				/*
				* Check whether the end of the loop has been reached
				* Yes, start closing the query string
				* No, keep the query string open
				*/
				if ($counter === count($this->rowArray[$this->index]) - 1)
				{
					$createQuery .= " {$key} = ? ";
					$createQuery .= "WHERE {$this->getPrimaryKey()} = ?";

					/*
					* Get the primary key and its value
					* Push it so the WHERE clause can be completed
					*/
					$args[] = $this->getField("{$this->getPrimaryKey()}");
				} else
				{
					$createQuery .= " {$key} = ?, ";
				}

				$counter++;
			}

			/*
			* Create the query object
			*/
			$stmt = $this->conn->prepare($createQuery);

			/*
			* Bind the argument array and unpack it
			*/
			$stmt->bind_param($bindPARAM, ...$args);

			/*
			* Execute the created SQL object
			*/
			$stmt->execute();

			/*
			* Reset $this->index, so during fetch time $this->index starts at 0.
			*/
			$this->resetIndex();
		}

		/*
		* setField initiates the column you wish to set
		*/
		public function setField($key, $value)
		{
			/*
			* Set index to 0
			*/
			$this->setIndex();

			/*
			* If the column exists
			*/
			if ($this->hasField($key, $this->row))
			{
				$this->rowArray[$this->index][$key] = $value;
			}
		}

		/*
		* getField retrieves the requested column
		*/
		public function getField($key)
		{
			$this->setIndex();
			// return $this->row;
			/*
			* if key exists, return the requested key
			* else return nothing (silence).
			* returning empty will prevent PHP from throwing an error.
			*/
			if ($this->hasField($key, $this->row)) 
			{
				return $this->rowArray[$this->index][$key];
			}
			else
			{
				self::getSuppressionCaller(__METHOD__, $key);
			}
		}

		public function getRow($key = NULL)
		{
			if (is_null($key)) 
			{
				return $this->rowArray;
			}
			else if (array_key_exists($key, $this->rowArray))
			{
				return $this->rowArray[$key];
			}
			else
			{
				self::getSuppressionCaller(__METHOD__, $key);
			}
		}

		//MOVE METHOD TO DEDICATED ERROR HANDLING CLASS.
		private static function getSuppressionCaller($error, $clause)
		{
			if ($this->errorSuppression) 
			{
				return;
			}
			else
			{
				exit("{$error} <br/> Developer input: {$clause} <br/> Developer input failed. Check manual for further instructions.");
			}
		}

		/*
		* function hasField checks whether the array_key exists which was requested.
		* it is an expansion of in_array(). 
		* the function will check whether the multidimensional array contains the value.
		*/
		private function hasField($key, $haystack, $strict = false)
		{
			foreach ($haystack as $item) {

				if (($strict ? $item === $key : $item == $key) || (is_array($item) && $this->hasField($key, $item, $strict))) 
				{
					return true;
				}
    		}

    		return false;
		}

		public function next()
		{
			$this->index = $this->index + 1;

			return $this->index;
		}

		public function previous()
		{
			$this->index = $this->index - 1;

			return $this->index;
		}

		private function setIndex()
		{
			if ($this->index === -1) 
			{
				$this->index = $this->index + 1;

				return $this->index;
			}
		}

		private function resetIndex()
		{
			if ($this->index > -1) 
			{
				$this->index = $this->index = -1;

				return $this->index;
			}
		}

	}
?>