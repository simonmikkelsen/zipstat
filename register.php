<?php
//Import modules
require "Html.php";
require "Stier.php";
require "view.php";
require "lib/SiteContext.php";
require "lib/Localizer.php";
require "lib/SiteGenerator/SiteGenerator.php";

//Initialize
$options = new Stier();
$in = Html::setPostOrGetVars($HTTP_POST_VARS,$HTTP_GET_VARS);

$datafil = NULL;
$lib = new Html($in,$datafil);
$lib->setStier($options);
$siteContext = new SiteContext($lib, $options, $in, 'da');

//Create the registration apge
$register = new Registrer($siteContext);

if (isset($in['step']) and $in['step'] === "reg1") {
	$errors = $register->validateStartPage();
	if ($errors->isOccured())
		$register->displayErrors($errors);
	else
		$register->doRegister();
} else {
	$register->showStartPage();
	exit;
}

/**
 * Handles the register pages
 *
 * @public
 * @version 0.0.1
 * @author Simon Mikkelsen
 */
class Registrer
{
	/**
	 * The site context of this site.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $siteContext;

	/**
	 * The total number of pages in the registration process.
	 *
	 * @private
	 * @since 0.0.1
	 */
	var $pages = 2;

	/**
	 * Creates a new instance.
	 *
	 * @param $siteContext the site context.
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 */
	function Registrer($siteContext)
	{
		$this->setSiteContext($siteContext);
	}

	/**
	 * Displays the given $errors.
	 */
	function displayErrors(&$errors) {
		if ($errors->isOccured()) {
			require_once "lib/SiteGenerator/SiteGenerator.php";
			require_once "lib/StatGenerator.php";

			$sg = SiteGenerator::getGenerator('html', $this->siteContext);
			$headline = $sg->newElement("headline");
			$headline->setHeadline($this->siteContext->getLocale('errAnErrorOccured'));
			$headline->setSize(1);
			$sg->addElement($headline);

			$errorList = $errors->getErrors();
			foreach ($errorList as $error) {
				$text = $sg->newElement("text");
				$text->setText($error->getMessage());
				$sg->addElement($text);
			}

			echo $sg->getSite();
			exit;
		}
	}
	
	/**
	 * Performs the registration.
	 */
	function doRegister() {
		$errors = new Errors();
		$in = $this->siteContext->getHTTP_VARS();
		$lib = $this->siteContext->getCodeLib();
		
		//Use the simple user area?
		if (strpos(strtolower($in['brugerom']), 'simpelt') === 0) {
			//Set the cookie for users area type
			$uaUtils = new UsersAreaUtils($this->siteContext);
			$uaUtils->setUAType();
			$simpelt_avan = 1;
		} else {
			$simpelt_avan = 0;
		}

		$datasource = DataSource::createInstance($in['brugernavn'], $this->siteContext->getOptions());
		$datasource->setLine(0, "filen er ok");
		$datasource->setLine(1, $in['navn']);
		$datasource->setLine(2, $in['e-mail']);
		$datasource->setLine(3, $in['url']);
		$datasource->setLine(4, $in['titel']);
		$datasource->setLine(5, $lib->kortdato());
		$datasource->setLine(6, ''); //Old way of storing password, no longer used.
		$in['kodeord'] = $in['pwd1'];
		$datasource->setLine(7, "0");
		$datasource->setLine(8, $lib->kortdato() . "");
		$datasource->setLine(9, "0::0::0::0::0::0::0::0::0::0::0::0");
		$datasource->setLine(10, "0");
		$datasource->setLine(11, "0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0");
		$datasource->setLine(12, "0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0");
		$datasource->setLine(13, "0");
		$datasource->setLine(14, "0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::0::");
		$datasource->setLine(15, "0::0::0::0::0::0::0");
		$datasource->setLine(16, "0");
		$datasource->setLine(17, $lib->kortdato() . "");
		$datasource->setLine(18, "0");
		$datasource->setLine(19, $lib->kortdato() . "");
		$datasource->setLine(20, "");
		$datasource->setLine(21, "::");
		$datasource->setLine(22, "");
		$datasource->setLine(23, "");
		$datasource->setLine(24, "");
		$datasource->setLine(25, "::");
		$datasource->setLine(26, "");
		$datasource->setLine(27, "::");
		$datasource->setLine(28, "");
		$datasource->setLine(29, "");
		$datasource->setLine(30, "::");
		$datasource->setLine(31, "");
		$datasource->setLine(32, "::");
		$datasource->setLine(33, "");
		$datasource->setLine(34, "::");
		$datasource->setLine(35, "");
		$datasource->setLine(36, "::");
		$datasource->setLine(37, "");
		$datasource->setLine(38, "");
		$datasource->setLine(39, "");
		$datasource->setLine(40, "::");
		$datasource->setLine(41, "");
		$datasource->setLine(42, "");
		$datasource->setLine(43, "");
		$datasource->setLine(44, "");
		$datasource->setLine(45, "");
		$datasource->setLine(46, "");
		$datasource->setLine(47, "");
		$datasource->setLine(48, "");
		$datasource->setLine(49, "");
		$datasource->setLine(50, "");
		$datasource->setLine(51, "");
		$datasource->setLine(52, "");
		$datasource->setLine(53, $in['url']);
		$datasource->setLine(54, "");
		$datasource->setLine(55, "");
		$datasource->setLine(56, "");
		$datasource->setLine(57, "");
		$datasource->setLine(58, "");
		$datasource->setLine(59, "");
		$datasource->setLine(60, "");
		$datasource->setLine(61, "");
		$datasource->setLine(62, "");
		$datasource->setLine(63, "");
		$datasource->setLine(64, "");
		$datasource->setLine(65, "");
		$datasource->setLine(66, "");
		$datasource->setLine(67, "");
		$datasource->setLine(68, "");
		$datasource->setLine(69, "");
		$datasource->setLine(70, "");
		$datasource->setLine(71, "");
		$datasource->setLine(72, "");
		$datasource->setLine(73, "0:0");
		$datasource->setLine(74, "");
		$datasource->setLine(75, "");
		$datasource->setLine(76, "0");
		$datasource->setLine(77, "0");
		$datasource->setLine(78, $lib->kortdato()."");
		$datasource->setLine(79, "0");
		$datasource->setLine(80, "0");
		$datasource->setLine(81, $lib->kortdato()."");
		$datasource->setLine(82, "0");

		#Registrere om siden er erotisk eller ej
		if ($in['under18ok'] === "Ja") {
			$datasource->setLine(83, "erotik");
		} else if ($in['under18ok'] === "Nej") {
			$datasource->setLine(83, "okunder18");
		} else {
			$datasource->setLine(83, "");
		}

		$datasource->setLine(84, (isset($in['beskrivelse']) ? $in['beskrivelse'] : ''));
		$datasource->setLine(85, (isset($in['sord']) ? $in['sord'] : ''));

		//The two are not used anymore
		$datasource->setLine(86, "");
		$datasource->setLine(87, "");

		$datasource->setLine(88, "");
		$datasource->setLine(89, "0");
		$datasource->setLine(90, "");
		$datasource->setLine(91, "");
		$datasource->setLine(92, "");
		$datasource->setLine(93, "");
		$datasource->setLine(94, "");
		$datasource->setLine(95, "");
		$datasource->setLine(96, "");
		$datasource->setLine(97, "");
		$datasource->setLine(98, "");
		$datasource->setLine(99, "");
		$datasource->setLine(100, "");
		$datasource->setLine(101, "");
		$datasource->setLine(102, "");
		$datasource->setLine(103, "");
		$datasource->setLine(104, "");
		$datasource->setLine(105, "");
		$datasource->setLine(106, "1::1::1");
		$datasource->setLine(107, "1::0::0::1::0::0::0");
		$datasource->setLine(108, "");
		$datasource->setLine(109, "");
		$datasource->setLine(110, time());

		$shortDate = $lib->kortdato();
		$nul[7]  = $shortDate;
		$nul[8]  = $shortDate;
		$nul[9]  = $shortDate;
		$nul[11] = $shortDate;
		$nul[14] = $shortDate;
		$nul[15] = $shortDate;
		$nul[16] = $shortDate;
		$nul[18] = $shortDate;
		$nul[20] = $shortDate;
		$nul[22] = $shortDate;
		$nul[24] = $shortDate;
		$nul[26] = $shortDate;
		$nul[28] = $shortDate;
		$nul[29] = $shortDate;
		$nul[31] = $shortDate;
		$nul[33] = $shortDate;
		$nul[35] = $shortDate;
		$nul[37] = $shortDate;
		$nul[39] = $shortDate;
		$nul[43] = $shortDate;
		$nul[44] = $shortDate;
		$nul[46] = $shortDate;
		$nul[47] = $shortDate;
		$nul[49] = $shortDate;
		$nul[54] = $shortDate;
		$nul[64] = $shortDate;
		$nul[69] = $shortDate;
		$nul[73] = $shortDate;
		$nul[74] = $shortDate;
		$nul[77] = $shortDate;
		$nul[80] = $shortDate;
		$nul[112] = $shortDate;
		$nul[114] = $shortDate;

		$datasource->setLine(51, implode("::", $nul));

		if (! $errors->isOccured()) {
			$datasource->createUser();
			$datasource->gemFil();
                        $authFactory = new AuthenticationFactory($this->siteContext->getOptions());
                        $auth = $authFactory->create();
                        $auth->updatePasswordHash($in['brugernavn'], $in['pwd1']);
		} else {
			$this->displayErrors($errors);
		}

	if (Html::okmail($in['e-mail'])) {
			$this->doSendEmail($simpelt_avan);
		}
//TODO: Get the following html into using e.g. view.php to get proper formatting and headers.
?>
<div class=forside>
<h1><?php echo $this->siteContext->getLocale('regYouAreRegistered');?></h1>
	<P><?php echo $this->siteContext->getLocale('regCongRegistered');?></p>

<h2><?php echo $this->siteContext->getLocale('regNowOnlyMissing');?></h2>
	<p><?php echo $this->siteContext->getLocale('regGenCode1');?>
	<a href="userarea.php?username=<?php echo htmlentities(urlencode($in['brugernavn']));
		?>&amp;start=Obligatorisk+kode&amp;start_type=kodegen">
	<?php echo $this->siteContext->getLocale('regGenCode2');?></a>.
	<?php echo $this->siteContext->getLocale('regGenCode3');?></p>

	<p><?php echo sprintf($this->siteContext->getLocale('regGenCodeAgain'),
	  "<a href=\"".$this->siteContext->getOption('ZSHomePage')."\">".$this->siteContext->getOption('ZSHomePage')."</a>"); ?></P>

<h2><?php echo $this->siteContext->getLocale('regAboutHelp');?></h2>
	<p><?php echo sprintf($this->siteContext->getLocale('regAboutHelpText'),
		"<a href=\"".$this->siteContext->getLocale('regUrlHelp')."\">".$this->siteContext->getLocale('regAboutHelp')."</a>"); ?></p></div>
<?php

	}
	
	/**
	 * Sends the user a confirmation about the registration.
	 * 
	 * @param $simpelt_avan states if the user has chosen to use a simple or
	 *                      advanced users area. The value should be 1 for
	 *                      advanced and 0 for the simple.
	 */
	function doSendEmail($simpelt_avan) {
		$in = $this->siteContext->getHTTP_VARS();
		if ($simpelt_avan === 1) {
			$sav = $this->siteContext->getLocale('regUASimple');
		} else {
			$sav = $this->siteContext->getLocale('regUAAdvanced');
		}

		$email = sprintf($this->siteContext->getLocale('regConfirmationEmail'),
		$this->siteContext->getOption('name_of_service'),
		$in['brugernavn'],
		'', // Never send password in clear text again.
		$this->siteContext->getOption('ZSHomePage'),
		$this->siteContext->getOption('adminName'),
		$this->siteContext->getOption('name_of_service'),
		$sav
		);

		mail($in['e-mail'],
		     $this->siteContext->getLocale('regConfirmationEmailSubj'),
				 $email,
				 "From: ".$this->siteContext->getOption('adminEMail')
				  ."\nReply-To: ".$this->siteContext->getOption('adminEMail'));
	}
	
	/**
	 * Validates the start page and returns the result as an Errors object.
	 */
	function validateStartPage() {
		$errors = new Errors();
		$in = $this->siteContext->getHTTP_VARS();
		$lib = $this->siteContext->getCodeLib();

		if (!isset($in['navn']) or strlen($in['navn']) === 0) {
			$errors->addError(new ZsError(2, $this->siteContext->getLocale('regErrorNoName')));
		}

		if (!isset($in['e-mail']) or strlen($in['e-mail']) === 0 or !$lib->okmail($in['e-mail'])) {
			$errors->addError(new ZsError(2, $this->siteContext->getLocale('regErrorBadEmail')));
		}

		if (!isset($in['url']) or strlen($in['url']) === 0 or !$lib->okurl($in['url'])) {
			$errors->addError(new ZsError(2, $this->siteContext->getLocale('regErrorBadUrl')));
		}

		if (!isset($in['titel']) or strlen($in['titel']) === 0) {
			$errors->addError(new ZsError(2, $this->siteContext->getLocale('regErrorNoTitle')));
		}

		if (!isset($in['brugernavn']) or strlen($in['brugernavn']) === 0 or !Datafil::isUsernameValid($in['brugernavn'])) {
			$errors->addError(new ZsError(2, $this->siteContext->getLocale('regBadUsername')));
		} else {
			$datasource = DataSource::createInstance($in['brugernavn'], $this->siteContext->getOptions());
			if ($datasource->userExists()) {
				$errors->addError(new ZsError(2, $this->siteContext->getLocale('regTakenUsername')));
			}
		}

		if (!isset($in['pwd1']) or strlen($in['pwd1']) === 0 or !isset($in['pwd2']) or strlen($in['pwd2']) === 0 or $in['pwd1'] !== $in['pwd2']) {
			$errors->addError(new ZsError(2, $this->siteContext->getLocale('regBadPassword')));
		}
		return $errors;
	}

	/**
	 * Displays the first page of the registration site, and exists the
	 * application.
	 *
	 * @public
	 * @version 0.0.1
	 * @since 0.0.1
	 * @return void
	 */
	function showStartPage() {
		$sc = $this->getSiteContext();
		$site = new HtmlSite($sc);
		$site->setTitle($sc->getLocale('regHeadline')." - ".sprintf($sc->getLocale("regXOfY"),
				"1", $this->pages));
		$site->setGenererOverskrift(1);

		$site->addHtml("<form action=\"register.php\" method=post>\n");

		$site->addHtml("<div class=forside>\n");
		$site->addHtml($sc->getLocale("regWelcomePart1"));
		$site->addHelp($sc->getLocale("regHelpHText"), $sc->getLocale("regHelpHelp"));
		$site->addHtml($sc->getLocale("regWelcomePart2"));
		$site->addHtml("</div>\n");

		$site->addHtml("<div class=forside>\n");
		$site->addHtml("<h1>".$sc->getLocale("regWithLegal")."</h1>\n");
		$site->addHtml($sc->getLocale("regWithLegalTxt"));
		$site->addHtml("</div>\n");

		$site->addHtml("<div class=forside>\n");
		$site->addHtml("<h2>".$sc->getLocale('regHeadline')."</h2>\n");
		$site->addHtml("<table border=0>\n");
		//Name
		$site->addHtml("	<tr><td>".$sc->getLocale("regName")."</td><td>");
		$site->addHelp($sc->getLocale("regNameHelp"), $sc->getLocale("regName"));
		$site->addHtml("<input type=text name=navn size=40> ".$sc->getLocale("regNameEx")."</td></tr>\n");
		//E-mail
		$site->addHtml("	<tr><td>".$sc->getLocale("regEmail")."</td><td>");
		$site->addHelp($sc->getLocale("regEmail"), $sc->getLocale("regEmailHelp"));
		$site->addHtml("<input type=text name=\"e-mail\" size=40> ".$sc->getLocale("regEmailEx")."</td></tr>\n");
		//Url
		$site->addHtml("	<tr><td>".$sc->getLocale("regUrl")."</td><td>");
		$site->addHelp($sc->getLocale("regUrl"), $sc->getLocale("regUrlHelp"));
		$site->addHtml("<input type=text name=url value=\"http://\" size=40> ".$sc->getLocale("regUrlEx")."</td></tr>\n");
		//Site titel
		$site->addHtml("	<tr><td>".$sc->getLocale("regTitle")."</td><td>");
		$site->addHelp($sc->getLocale("regTitle"), $sc->getLocale("regTitleHelp"));
		$site->addHtml("<input type=text name=titel size=40> ".$sc->getLocale("regTitleEx")."</td></tr>\n");
		//User name
		$site->addHtml("	<tr><td>".$sc->getLocale("regUsername")."</td><td>");
		$site->addHelp($sc->getLocale("regUsername"), $sc->getLocale("regUsernameHelp"));
		$site->addHtml("<input type=text name=brugernavn size=40> ".$sc->getLocale("regUsernameEx")."</td></tr>\n");
		//Password
		$site->addHtml("	<tr><td>".$sc->getLocale("regPassword")."</td><td>");
		$site->addHelp($sc->getLocale("regPassword"), $sc->getLocale("regPasswordHelp"));
		$site->addHtml("<input type=password name=pwd1 size=40> ".$sc->getLocale("regPasswordEx")."</td></tr>\n");
		//Password, confirm
		$site->addHtml("	<tr><td>".$sc->getLocale("regPassword2")."</td><td>");
		$site->addHelp($sc->getLocale("regPassword2"), $sc->getLocale("regPassword2Help"));
		$site->addHtml("<input type=password name=pwd2 size=40> ".$sc->getLocale("regPassword2Ex")."</td></tr>\n");

		$site->addHtml("</table>\n<p>\n");

		$site->addHelp($sc->getLocale("regUserAreaHelp"), $sc->getLocale("regUserArea"));
		$site->addHtml($sc->getLocale("regUserAreaWich")."<br>\n");
		$site->addHtml("<input type=radio name=brugerom value=\"simple\" CHECKED> ".$sc->getLocale("regUserAreaSimple"));
		$site->addHtml("<input type=radio name=brugerom value=\"advanced\"> ".$sc->getLocale("regUserAreaAdvanced"));
		$site->addHtml("</p>\n");

		$site->addHtml("<p>\n");
		$site->addHelp($sc->getLocale("regForKidsHelpFor"), $sc->getLocale("regForKidsHelp"));
		$site->addHtml($sc->getLocale("regForKids")."<br>\n");
		$site->addHtml("<input type=radio name=under18ok value=\"yes\"> ".$sc->getLocale("regForKidsYes")."<br>\n");
		$site->addHtml("<input type=radio name=under18ok value=\"no\"> ".$sc->getLocale("regForKidsNo")."<br>\n");
		$site->addHtml("</p>\n");
		$site->addHtml("<input type=hidden name=\"step\" value=\"reg1\">\n");
		$site->addHtml("<input type=submit value=\"".$sc->getLocale("regNext")."\">");
		$site->addHtml("</form>\n");
		$site->addHtml("</div>\n");

		echo $site->getSite();
	}

	/**
	 * Returns the site context of this site.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return SiteContext the site context of this site.
	 */
	function getSiteContext()
	{
		return $this->siteContext;
	}

	/**
	 * Sets the site context of this site.
	 *
	 * @public
	 * @since 0.0.1
	 * @version 0.0.1
	 * @return void
	 * @param $siteContext the site context of this site.
	 */
	function setSiteContext($siteContext)
	{
		$this->siteContext = $siteContext;
	}

}

?>
