<?php
/**
 * NOTA SOBRE LA LICENCIA DE USO DEL SOFTWARE
 *
 * El uso de este software está sujeto a las Condiciones de uso de software que
 * se incluyen en el paquete en el documento "Aviso Legal.pdf". También puede
 * obtener una copia en la siguiente url:
 * http://www.redsys.es/wps/portal/redsys/publica/areadeserviciosweb/descargaDeDocumentacionYEjecutables
 *
 * Redsys es titular de todos los derechos de propiedad intelectual e industrial
 * del software.
 *
 * Quedan expresamente prohibidas la reproducción, la distribución y la
 * comunicación pública, incluida su modalidad de puesta a disposición con fines
 * distintos a los descritos en las Condiciones de uso.
 *
 * Redsys se reserva la posibilidad de ejercer las acciones legales que le
 * correspondan para hacer valer sus derechos frente a cualquier infracción de
 * los derechos de propiedad intelectual y/o industrial.
 *
 * Redsys Servicios de Procesamiento, S.L., CIF B85955367
 */
if ( ! class_exists( 'ISLogger' ) ) {
	class ISLogger {
		public static $DISABLED   = 0;
		public static $ERROR      = 1;
		public static $INFO       = 2;
		public static $DEBUG      = 3;
		private static $log_path  = null;
		private static $log_level = 0;
		public static function initialize( $path, $level ) {
			self::$log_path  = $path;
			self::$log_level = $level;
		}
		public static function error( $message ) {
			if ( self::$log_level >= self::$ERROR ) {
				self::writeLogLine( $message, 'ERROR' );
			}
		}
		public static function info( $message ) {
			if ( self::$log_level >= self::$INFO ) {
				self::writeLogLine( $message, 'INFO ' );
			}
		}
		public static function debug( $message ) {
			if ( self::$log_level >= self::$DEBUG ) {
				$finalMsg = preg_replace( '/<Ds_Card_Number>.*<\/Ds_Card_Number>/', '<Ds_Card_Number>****************</Ds_Card_Number>', $message );
				$finalMsg = preg_replace( '/<Ds_ExpiryDate>.*<\/Ds_ExpiryDate>/', '<Ds_ExpiryDate>****</Ds_ExpiryDate>', $finalMsg );
				$finalMsg = preg_replace( '/<Ds_Merchant_Identifier>.*<\/Ds_Merchant_Identifier>/', '<Ds_Merchant_Identifier>****************</Ds_Merchant_Identifier>', $finalMsg );
				$finalMsg = preg_replace( '/<DS_MERCHANT_IDENTIFIER>.*<\/DS_MERCHANT_IDENTIFIER>/', '<DS_MERCHANT_IDENTIFIER>****************</DS_MERCHANT_IDENTIFIER>', $finalMsg );

				self::writeLogLine( $finalMsg, 'DEBUG' );
			}
		}
		public static function getLog_path() {
			return self::log_path;
		}
		public static function setLog_path( $log_path ) {
			self::$log_path = $log_path;
		}
		public static function getLog_level() {
			return self::log_level;
		}
		public static function setLog_level( $log_level ) {
			self::$log_level = $log_level;
		}
		private static function writeLogLine( $message, $level ) {
			if ( self::$log_path !== null && is_writable( self::$log_path . '/redsysLog.log' ) ) {
				if ( ! isset( $_SESSION ) ) {
					session_start();
				}

				if ( $GLOBALS ['REDSYS_LOG_ENABLED'] ) {
					self::generateIdLog();

					$objTrace = null;
					for ( $i = 0; $i < sizeof( debug_backtrace() ) && $objTrace === null; $i ++ ) {
						if ( isset( debug_backtrace() [ $i ] ['class'] ) && debug_backtrace() [ $i ] ['class'] != 'ISLogger' ) {
							$objTrace = debug_backtrace() [ $i ];
						}
					}

					$strTrace = '<NoBacktraceInfo>';
					if ( $objTrace !== null ) {
						$strTrace = get_class( $objTrace ['object'] ) . $objTrace ['type'] . $objTrace ['function'];
					}

					$lineText = date( 'Y/m/d H:i:s' ) . ' [' . $level . '] ' . $_SESSION ['NCRedsysIdLog'] . ' - ' . $strTrace . ': ' . $message . "\n";
					file_put_contents( self::$log_path . '/redsysLog.log', $lineText, FILE_APPEND );
				}
			}
		}
		public static function generateIdLog( $force = false ) {
			if ( ! isset( $_SESSION ['NCRedsysIdLog'] ) || $force ) {
				$vars         = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
				$stringLength = strlen( $vars );
				$result       = '';
				for ( $i = 0; $i < 30; $i ++ ) {
					$result .= $vars [ rand( 0, $stringLength - 1 ) ];
				}

				$_SESSION ['NCRedsysIdLog'] = $result;
			}
		}
		public static function beautifyXML( $xml ) {
			$xml     = preg_replace( '/(>)(<)(\/*)/', "$1\n$2$3", $xml );
			$token   = strtok( $xml, "\n" );
			$result  = "\n";
			$pad     = 0;
			$matches = array();
			while ( $token !== false ) {
				if ( preg_match( '/.+<\/\w[^>]*>$/', $token, $matches ) ) {
					$indent = 0;
				} else {
					if ( preg_match( '/^<\/\w/', $token, $matches ) ) {
						$pad --;
						$indent = 0;
					} else {
						if ( preg_match( '/^<\w[^>]*[^\/]>.*$/', $token, $matches ) ) {
							$indent = 1;
						} else {
							$indent = 0;
						}
					}
				}
				$line    = str_pad( $token, strlen( $token ) + $pad, "\t", STR_PAD_LEFT );
				$result .= $line . "\n";
				$token   = strtok( "\n" );
				$pad    += $indent;
			}

			return substr( $result, 0, - 1 );
		}
		private static function escapeStringCSV( $str, $sep = ';' ) {
			if ( preg_match( '/[\r\n"' . preg_quote( $sep, '/' ) . ']/', $str ) ) {
				return '"' . str_replace( '"', '""', $str ) . '"';
			} else {
				return $str;
			}
		}
	}
}
