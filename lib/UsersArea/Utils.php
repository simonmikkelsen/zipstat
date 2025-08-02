<?php


require_once "lib/UsersArea/file_get_contents.php";


/**
 * Utils for the users area.
 */
class UsersAreaUtils {

	/**
	 * Text for the template to create sites.
	 */
	var $template;
	
	/**
	 * An instance of the siteContext.
	 */
	var $siteContext;
	
	/**
	 * States that the users area is of the simple type.
	 *
	 * @static
	 * @public
	 */
	var $UA_TYPE_SIMPLE = 1;
	
	/**
	 * States that the users area is of the advanced type.
	 *
	 * @static
	 * @public
	 */
	var $UA_TYPE_ADVANCED = 2;
	/**
	 * Creatres a new instance.
	 *
	 * @param $siteContext an optional instance of the site context. Some
	 *                     methods will fail if it is not set.
	 */
	function __construct(&$siteContext) {
		//if (is_object($siteContext))
		$this->setSiteContext($siteContext);
	}
	
	/**
	 * Returns the type of the users area as one of the values of the constatns
	 * UA_TYPE_*
	 *
	 * @static
	 */
	function getUAType() {
		if (isset($_COOKIE['shg']) and $_COOKIE['shg'] === 'simpel')
			return $this->UA_TYPE_SIMPLE;
		else
			return $this->UA_TYPE_ADVANCED;
	}
	
	/**
	 * Sets the user area type. If no parameter is given, the date of
	 * the cookie is just reset. If a parameter is given it must be the
	 * value of one of the constants UA_TYPE_*
	 */
	function setUAType($type = NULL) {
		$cval = 'advanced';
		if (($type === NULL and $this->getUAType() === $this->UA_TYPE_SIMPLE)
					or $type === $this->UA_TYPE_SIMPLE) {
			$cval = 'simpel';
		}
		setcookie('shg', $cval, time()+60*60*24*365, '/', '.'.$this->siteContext->getOption('domain'));
	}
	
	/**
	 * Returns an instance of the siteContext.
	 *
	 * @returns siteContext
	 * @return an instance of the siteContext.
	 * @public
	 */
	function getSiteContext() {
		return $this->siteContext;
	}

	/**
	 * Sets an instance of the siteContext.
	 *
	 * @public
	 * @param $siteContext an instance of the siteContext.
	 */
	function setSiteContext(&$siteContext) {
		$this->siteContext = $siteContext;
	}

	/**
	 * Shows the given $message using the given $title.
	 *
	 * @public
	 * @param $title the title to show.
	 * @param $message the message to show.
	 * @returns void
	 */
	function showMessage($title, $message) {
			require_once "lib/SiteGenerator/SiteGenerator.php";
			require_once "lib/StatGenerator.php";
	
			$sg = SiteGenerator::getGenerator('html', $this->siteContext);
			$headline = $sg->newElement("headline");
			$headline->setHeadline($title);
			$headline->setSize(1);
			$sg->addElement($headline);
			$text = $sg->newElement("text");
			$text->setText($message);
			$sg->addElement($text);
	
			echo $sg->getSite();

	}
	
	
	/**
	 * Shows the given $errors using the given $title.
	 *
	 * @public
	 * @param $title the title to show.
	 * @param $errors the errors to show.
	 * @returns void
	 */
	function showErrors($errors, $title = NULL) {
		switch ($errors->getCount()) {
			case 0:
				return;
				//break;
			case 1:
				$errorList = $errors->getErrors();
				$message = $errorList[0]->getMessage();
				break;
			default:
				$message = '<ul>';
				foreach ($errors as $error) {
					$message .= '<li>'.$error->getMessage().'</li>';
				}
				$message .= '</ul>';
		}
		if ($title === NULL)
			$title = $this->siteContext->getLocale('errAnErrorOccured');
		
		$this->showMessage($title, $message);
	}
	
	/**
	 * Prints the login form.
	 *
	 * @param $type  login type. 1: first time, 2: wrong username/pwd.
	 * @param $submitUrl the url of the page to submit the form to.
	 * @public
	 * @returns void
	 */
	function doLoginForm($type, $submitUrl) {
		require_once "./lib/SiteGenerator/SiteGenerator.php";
		require_once "./lib/StatGenerator.php";

		$sg = SiteGenerator::getGenerator('html', $this->siteContext);
		$headline = $sg->newElement('headline');
		$headline->setHeadline($this->siteContext->getLocale('siteEnterPwdHead'));
		$headline->setSize(1);
		$sg->addElement($headline);
		$text = $sg->newElement('text');
		if ($type === 2)
			$text->setText($this->siteContext->getLocale('uaEnterPwdWrong'));
		else
			$text->setText($this->siteContext->getLocale('uaEnterPwd'));
		$sg->addElement($text);

		$login = $sg->newElement("loginForm");
		$login->setUrl($submitUrl);
		$login->setKeyUsername('username');
		$login->setKeyPassword('password');
		$login->setSubmitMethod("POST");
		$login->setUsername($this->siteContext->getHttpVar('username'));
		$sg->addElement($login);

		echo $sg->getSite();
	}

	/**
	 * Outputs the header for a site.
	 *
	 * @public
	 * @returns void
	 * @param $title the title of the page to show.
	 * @param $showMenu 1 if the menu shall be shown (default), 0 if it
	 *                  shall be hidden.
	 */
	function echoSiteHead($title, $showMenu = 1) {
		$this->loadTemplate();
		if ($showMenu === 1)
			$localTpl = substr($this->template, 0, strpos($this->template, '%mainSite%'));
		else
			$localTpl = substr($this->template, 0, strpos($this->template, '%start_menu%'));

		$keys = array(
			'%title%',
			'%css_url%',
			'%start_menu%',
			'%end_menu%',
			'%mainSite%',
			'%start_footer%',
			'%end_footer%',
			'%frontpage_url%',
			'%signup_url%',
			'%userarea_url%',
			'%contact_url%',
			'%help_url%',
                        '%logout_url%',
			'%service_email%',
			'%admin_name%',
			'%service_name%');
		$vals = array(
			$title,	//title
			$this->siteContext->getOption('urlCss'),							//css_url
			'',										//start_menu
			'',										//end_menu
			'',										//mainSite
			'',										//start_footer
			'',										//end_footer
			$this->siteContext->getOption('ZSHomePage'),					//frontpage_url
			$this->siteContext->getOption('urlSignup'),						//signup_url
			$this->siteContext->getOption('urlUserArea'),					//userarea_url
			$this->siteContext->getOption('urlContact'),					//contact_url
			$this->siteContext->getOption('urlHelp'),							//help_url
                        $this->siteContext->getOption('urlLogout'),
			$this->siteContext->getOption('adminEMail'), 					//E-mail
			$this->siteContext->getOption('adminName'),            //Admin name
			$this->siteContext->getOption('name_of_service')      //Name of the service (e.g. ZIP Stat)
		);
                header('Content-Type: text/html; charset=utf-8'); //TODO: Put in a nicer location, but this works for now.
		echo str_replace($keys, $vals, $localTpl);
	}

	/**
	 * Outputs the end for a site.
	 *
	 * @param $showFooter states if the footer shall be shown. 1 (default)
	 *                    if yes, 0 if no.
	 * @public
	 * @returns void
	 */
	function echoSiteEnd($showFooter = 1) {
		$this->loadTemplate();
		
		$localTpl = substr($this->template, strpos($this->template, '%mainSite%')+strlen('%mainSite%'));
		if ($showFooter === 0) {
			$localTpl = substr($localTpl, 0, strpos($localTpl, '%start_footer%')).substr($localTpl, strpos($localTpl, '%end_footer%')+strlen('%end_footer%'));
		}

		$keys = array(
			'%title%',
			'%css_url%',
			'%start_menu%',
			'%end_menu%',
			'%mainSite%',
			'%start_footer%',
			'%end_footer%',
			'%frontpage_url%',
			'%signup_url%',
			'%userarea_url%',
			'%contact_url%',
			'%help_url%',
                        '%logout_url%',
			'%service_email%',
			'%admin_name%',
			'%service_name%');
		$vals = array(
			'',	//title
			$this->siteContext->getOption('urlCss'),							//css_url
			'',										//start_menu
			'',										//end_menu
			'',										//mainSite
			'',										//start_footer
			'',										//end_footer
			$this->siteContext->getOption('ZSHomePage'),					//frontpage_url
			$this->siteContext->getOption('urlSignup'),						//signup_url
			$this->siteContext->getOption('urlUserArea'),					//userarea_url
			$this->siteContext->getOption('urlContact'),					//contact_url
			$this->siteContext->getOption('urlHelp'),							//help_url
                        $this->siteContext->getOption('urlLogout'),
			$this->siteContext->getOption('adminEMail'), 					//E-mail
			$this->siteContext->getOption('adminName'),            //Admin name
			$this->siteContext->getOption('name_of_service')      //Name of the service (e.g. ZIP Stat)
		);
		echo str_replace($keys, $vals, $localTpl);
	}

	/**
	 * Loads the template if it hasn't been loaded.
	 *
	 * @public
	 * @returns void
	 */
	function loadTemplate() {
		//Don't load the template twice
		if (isset($this->template) or strlen($this->template) > 0)
			return;

		//todo: The real method, $this->siteContext->getPath("templates")
		//does not seem to work, so we use this temporarely:
		//$filename = $this->siteContext->getPath('templates')."/HtmlDefault.txt";
		$filename = $this->siteContext->options->stier['templates']."/HtmlDefault.txt";
		//$fp = fopen('templates/HtmlDefault.txt', 'r');
		if (! file_exists($filename)) {
			die("The file '$filename' does not exist.");
		}
		$fp = fopen($filename, 'r');
		
		//$this->template = file_get_contents("templates/HtmlDefault.txt");
		//$this->template = implode("\n", file("templates/HtmlDefault.txt"));
		$this->template = fread($fp, filesize($filename));
		fclose($fp);
	}

	/**
	 * Returns if the $stringToTest contains illegal charaters.
	 *
	 * @param $stringToTest the one to test.
	 * @returns boolean
	 * @return if the $stringToTest contains illegal charaters.
	 * @public
	 */
	function validateString($stringToTest) {
		//An empty string is ok
		if (strlen($stringToTest) === 0)
			return 1;
			
		if (preg_match("/[^0-9a-z\.\,\:'_\-\&\"= ]/i", $stringToTest))
			return 0;
		else
			return 1;
	}
	
	/**
	 * Returns if the given $url is valid.
	 *
	 * @param $url the one to check.
	 * @public
	 * @returns boolean
	 * @return if the $url is valid.
	 */
	function validateUrl($url) {
    // Starts with http, https or mailto.
    $url = trim(strtolower($url));
    return strpos($url, 'http://') === 0
        or strpos($url, 'https://') === 0
        or strpos($url, 'mailto:') === 0;
	}

	/**
	 * Returns if the given $email is valid.
	 *
	 * @param $email the one to check.
	 * @public
	 * @returns boolean
	 * @return if the $email is valid.
	 */
	function validateEmail($email) {
		if (preg_match("/[\w-_.]+\@[\w-_.]/", $email))
			return 1;
		else
			return 0;
	}
	
	/**
	 * Returns if the given integer is valid.
	 *
	 * @param $anInteger the one to check.
	 * @public
	 * @returns boolean
	 * @return if the integer is valid.
	 */
	function validateInteger($anInteger) {
		if (!preg_match("/[^0-9]/", $anInteger))
			return 1;
		else
			return 0;
	}

	/**
	 * Saves the $datafile and produces an error message if something went
	 * wrong.
	 *
	 * @public
	 * @returns void
	 * @param $datafile the one to save
	 * @param $mainMessage the message to display if everything went ok
	 * @param $problemMessage the message to display if somehting went wrong
	 * @param $optionKey option to state when to display messages.
	 */
	function saveData(&$datafile, $mainMessage, $problemMessage = '', $optionKey = '') {
		$saveRes = $datafile->gemFil();
	
		if ($saveRes === FALSE) {
			$problemMessage .= "<li>".$this->siteContext->getLocale('errDatasourceNotSaved');
		}

		if (isset($problemMessage) and strlen($problemMessage) > 0
																		 and $optionKey === 'kunHvisProblemer') {
			$problems = $this->problems($problemMessage);
			$this->echoSiteHead("Data gemt", 0);
			echo "<p>".$mainMessage.$problems."</p>";
			$this->echoSiteEnd();
			exit;
		}
	}

	/**
	 * If a non empty $message is given an error message is formated and
	 * returned. Else an empty string is returned.
	 *
	 * @public
	 * @returns String
	 * @return an error message or empty string.
	 * @param $message the one to display.
	 */
	function problems($message) {
		if (isset($message) and strlen($message) !== 0)
			return "<h3>Der opstod flgende problem(er):</h3><ul>" . $message . "</ul>";
		else
			return '';
		
	}
	
	/**
	 * Creates a formated time stamp.
	 *
	 * @public
	 * @param $timeStamp the time to show, if not given the current time is used.
	 * @returns String
	 * @return a formated time stamp.
	 */
	function getShortDate($timeStamp = -1) {
		if ($timeStamp === -1)
			$timeStamp = time();
		$days = array('søn', 'man', 'tir', 'ons', 'tor', 'fre', 'lør');
		
		$fmt = $this->siteContext->getOption('dateformat_shortdate');
		
		//If the format is not defined the system has done wired stuff, like not beeing able to open files.
		if ($fmt == NULL or strlen($fmt) === 0) {
			echo "<b>Warning</b>: Date format 'dateformat_shortdate' not defined in the settings file!<br>";
		}
		
		return $days[date('w')].date($fmt, $timeStamp);
	}

}

?>
