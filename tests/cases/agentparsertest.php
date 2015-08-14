<?php
require_once(dirname(__FILE__)."/../../Html.php");
require_once(dirname(__FILE__)."/../testcase.php");
require_once(dirname(__FILE__)."/../../lib/ZipStatEngine.php");

/**
 * Tests the agent / browser / os parser.
 */
class AgentParserTest extends TestCase {

	/**
	 * Tests the agent / browser / os parser.
	 *
	 * @public
	 * @return @c true on success @c false on failure.
	 */
	function test() {
		$tests = array(
			"Mozilla/4.0 (compatible; MSIE 6.0; Symbian OS; Nokia 6630/5.03.08; 6936) Opera 8.50 [it]" => "Symbian OS, Nokia 6630",
			"Mozilla/4.0 (compatible; MSIE 6.0; Mac_PowerPC Mac OS X; en) Opera 8.5" => "Macintosh - OS X",
			"Mozilla/4.0 (PSP (PlayStation Portable); 2.00)" => "PlayStation Portable 2.X",
			"Mozilla/4.01 (Compatible; Acorn Phoenix 2.08 [intermediate]; RISC OS 4.39) Acorn-HTTP/0.84" => "RISC OS 4.X",
			"Mozilla/4.7C-SGI [en] (X11; I; IRIX 6.5 IP32)" => "UNIX - IRIX 6.X",
			"Mozilla/5.0 (Macintosh; U; Intel Mac OS X; sv-se) AppleWebKit/418.9.1 (KHTML, like Gecko) Safari/419.3" => "Macintosh - Intel Mac OS X",
			"Mozilla/5.0 (Macintosh; U; PPC Mac OS X Mach-O; da; rv:1.8.0.6) Gecko/20060728 Firefox/1.5.0.6" => "Macintosh - PowerPC Mac OS X",
			"Mozilla/5.0 (OS/2; U; Warp 4.5; en-US; rv:1.7.12) Gecko/20050922 Firefox/1.0.7" => "OS/2 Warp 4.X",
			"Mozilla/5.0 (SymbianOS/9.1; U; en-us) AppleWebKit/413 (KHTML, like Gecko) Safari/413" => "Symbian OS 9.X",
			"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.9) Gecko/20070102 Ubuntu/dapper-security Firefox/1.5.0.9" => "UNIX - Linux, Ubuntu",
			"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7.8) Gecko/20060926 Debian/1.7.8-1sarge7.3.1" => "UNIX - Linux, Debian",
			"SonyEricssonK750i/R1CA Browser/SEMC-Browser/4.2 Profile/MIDP-2.0 Configuration/CLDC-1.1" => "SonyEricssonK750i"
		);
		
		foreach ($tests as $in => $out) {
			$res = ZipStatEngine::platform($in);
			if ($res !== $out) {
				echo "Platform: The string $in\nshould give \"$out\" but gave \"$res\".";
				return false;
			}
		}
		
		$tests = array(
			"Mozilla/4.0 (compatible; Voyager; AmigaOS)" => "Voyager",
			"Mozilla/4.0 (PSP (PlayStation Portable); 2.00)" => "PlayStation Portable 2.X",
			"Mozilla/4.01 (Compatible; Acorn Phoenix 2.08 [intermediate]; RISC OS 4.39) Acorn-HTTP/0.84" => "Acorn Phoenix v2.X",
			"Mozilla/4.5 (compatible; HTTrack 3.0x; Windows 98)" => "HTTrack v3.X",
			"Mozilla/4.5 (compatible; iCab 2.7.1; Macintosh; I; PPC)" => "iCab v2.X",
			"Mozilla/5.0 (000000000; 0; 000 000 00 0 000000; 00000; 000000) Gecko/20030624 Netscape/7.1" => "Netscape v7.X",
			"Mozilla/5.0 (BeOS; U; BeOS BePC; en-US; rv:1.9a1) Gecko/20051002 Firefox/1.6a1" => "Firefox v1.X",
			"Mozilla/5.0 (Macintosh; U; Intel Mac OS X; da; rv:1.8.1.1) Gecko/20061204 Firefox/2.0.0.1" => "Firefox v2.X",
			"Mozilla/5.0 (Macintosh; U; Intel Mac OS X; da-dk) AppleWebKit/418 (KHTML, like Gecko) Safari/417.9.2" => "Safari",
			"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.7.3) Gecko/20041007 Galeon/1.3.17 (Debian package 1.3.17-2)" => "Galeon v1.X",
			"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.7) Gecko/20061022 Iceweasel/1.5.0.7-g2" => "Iceweasel v1.X",
			"Mozilla/5.0 (X11; U; Linux i686; en-US; rv:1.8.0.8) Gecko/20061105 Iceape/1.0.6 (Debian-1.0.6-1)" => "Iceape v1.X",
			"Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.8.0.1) Gecko/Debian-1.8.0.1-5 Epiphany/1.8.5" => "Epiphany v1.X",
			"Mozilla/5.0 (X11; U; SunOS sun4m; en-US; rv:1.4b) Gecko/20030517 Mozilla Firebird/0.6" => "Mozilla Firebird",
			"Wget/1.8.2" => "Wget v1.X"
		);
		
		foreach ($tests as $in => $out) {
			$res = ZipStatEngine::short_browser($in);
			if ($res !== $out) {
				echo "Browser: The string $in\nshould give \"$out\" but gave \"$res\".";
				return false;
			}
		}
		return true;
	}
	
	/**
	 * Returns the name of the test.
	 * 
	 * @public
	 * @return Returns the name of the test.
	 */
	function getName() {
		return "AgentParser";
	}
}

?>