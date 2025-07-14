<?php

//Note: The terms stat panel and mini stat are the same.

        require "Html.php";
        require "Stier.php";
        require "lib/Localizer.php";
        require "lib/SiteContext.php";
        require "lib/UsersArea/Utils.php";
        require "lib/UsersArea/Panellib.php";

        //Path and options.
        $stier = new Stier();

        //Fetches parameters for the script. 
        $in = Html::setPostOrGetVars($HTTP_POST_VARS,$HTTP_GET_VARS);

        if (isset($in['username']))
                $username = $in['username'];
        if (isset($in['password']))
                $password = $in['password'];

        if (isset($in) and isset($username)) {
                //Validates the username.
                $datafile = DataSource::createInstance($username, $stier);

                //Fetches the users data.
                $res = $datafile->hentFil();

        //Creates error message if it fails.
        $errMsg = "";
        if ($res === -2)
                $errMsg .= "Din datafil er desvrre blevet beskadiet, og der kan derfor ikke registreres statistikker. Kontakt ".$stier->getOption('name_of_service')."'s administrator via e-mail-adressen nederst p siden.";
        elseif (! $res or $res === 0)
                $errMsg .= "Datafilen kunne hentes. Enten er det et problem p ".$stier->getOption('name_of_service')." eller ogs har du skrevet det forkerte brugernavn - det kan indeholder tegn der ikke er tilladt - prv at generere den obligatoriske kode igen.";//.$datafile->getFilnavn("zip");.

        }

        //Creates the standard libary.
        $lib = new Html($in,$datafile);
        
		//Instantiates the SiteContext object.
        $siteContext = new SiteContext($lib, $stier, $in, 'da'); 
        $lib->setSiteContext($siteContext);

        $lib->setStier($stier);

        $utils = new UsersAreaUtils($siteContext);

//todo: Handle the problems better than this!.
        if (strlen($errMsg) > 0)
        {
                echo $errMsg;
                exit;
        }

        if ((! isset($in)) or (! isset($username)))
        {
                $utils->doLoginForm(1, $siteContext->getOption('urlUserAreaMain'));
                exit;
        }

        //Set the latest use with username and password.
        $datafile->setLine(110, time());

        //End bootstrap.

        //Direct the execution to what to do.

        if (!isset($in['type'])) {
                $utils->echoSiteHead("Fejl", 0);
                echo "<P>Der opstod en fejl i kodegeneratoren. En ndvendigt parameter var ikke til stede. Det betyder, at programmet ikke ved hvor du skal sendes hen... Brug browserens tilbageknap og prv igen. Hvis det ogs mislykkes, s skriv til <a href=\"mailto:$options{'errorEMail'}\">$options{'errorEMail'}</A>, og vedlg flgende linie:<BR><tt>$ENV{'QUERY_STRING'}</tt><BR>";
                $utils->echoSiteEnd();
                exit;
        }

if (strpos(strtolower($in['type']), 'obligatorisk') !== FALSE) { // or ($in{'type'} eq "lav_obl_kode")).
        vis_obl_kode($utils, $siteContext); //ok.
} else if($in['type'] === 'vis_obl_kode2') {
        if ($in['taeltype'] === 'usynlig') {
                gen_obl_kode($utils, $siteContext); //ok.
        } else {
                vis_obl_kode2($utils, $siteContext); //ok.
        }
} else if ($in['type'] === 'vis_obl_kode3') {
        if ($utils->getUAType() === $utils->UA_TYPE_SIMPLE) {
                $in['alle'] = 'on';
                if ($in['fra'] === 'gtaeller') {
                        $in['taeltype'] = $in['gtaeltype'];
                } else if ($in['fra'] === 'ministatistik') {
                        $in['hits'] = 'on';
                        $in['hitsialt'] = 'on';
                        $in['hits_bruger'] = 'on';
                        $in['hits_dag'] = 'on';
                        $in['hits_maaned'] = 'on';
                        $in['bgr_online'] = 'on';
                        $in['taeltype'] = 'usynlig';
                }
                gen_obl_kode($utils, $siteContext);
        } else {
                vis_obl_kode3($utils, $siteContext); //ok.
        }
} else if (strpos(strtolower($in['type']), 'statistik') !== FALSE) {
        //Opens site for generating mini stat.
        vis_statpanel($utils, $siteContext); //ok.
} else if ($in['type'] === 'vis_statpanel2') {
        vis_statpanel2($utils, $siteContext); //ok.
} else if($in['type'] === 'gen_statpanel') {
        gen_statpanel($utils, $siteContext); //ok.
} else if (strpos(strtolower($in['type']), 'rgsm') !== FALSE) {
        //Opens site for generating questions.
        vis_sp_gen($utils, $siteContext); //ok.
} else if ($in['type'] === 'zipklik_vis') {
        //Opens site for generating Zip click counter links.
        vis_zipklik_gen($utils, $siteContext); //ok.
}
//else if (($in['type'] === 'lav_obl_kode') or ( ($in['type'] =~ /Obligatorisk/i) and (&simpel()) )  ).
else if ($in['type'] === 'lav_obl_kode') {
        gen_obl_kode($utils, $siteContext);
} else if ($in['type'] === 'gen_sp') {
        //Generate question code.
        gen_sp_kode($utils, $siteContext); //ok
} else if ($in['type'] === 'vis_js_kode') {
        vis_js_kode($utils, $siteContext); //ok
} else {
        $utils->echoSiteHead("Fejl", 0);
        echo "<P>Der opstod en fejl i kodegeneratoren. En ndvendigt parameter var ikke til stede. Det betyder, at programmet ikke ved hvor du skal sendes hen... Brug browserens tilbageknap og prv igen. Hvis det ogs mislykkes, s skriv til <a href=\"mailto:$options{'errorEMail'}\">$options{'errorEMail'}</A>, og vedlg flgende linie:<BR><tt>$ENV{'QUERY_STRING'}</tt><BR>";
        $utils->echoSiteEnd();
        exit;
}
exit;


/**
 * Shows generation of the mandatory code.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function vis_obl_kode($utils, $siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();
        $utils->echoSiteHead("", 0);

        ?>
        <form action="<?php echo htmlentities($siteContext->getOption('urlUserAreaCodegen')); ?>" method=POST>
        <div class=forside>
        <h1>Obligatorisk kode</h1>
        <p>Vlg hvordan din obligatoriske kode skal vises p din hjemmeside.</p>

        <table>
                <tr>
                        <td valign=bottom style="padding-right: 2em;"></td>
                        <td valign=bottom style="padding-right: 2em;"><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/sh_taeller.gif" width=40 height=16 alt="Eks. p tller" valign=bottom></td>
                        <td valign=bottom style="padding-right: 2em;"><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/stats5.gif" width=25 height=25 alt="Eks. p tllerbillede" align=center valign=bottom></td>
                        <td valign=bottom>
        <?php

        $panellib = new Panellib($siteContext);

        echo $panellib->hentpanel("standard",'','',"alle");

        //Creates selectionbox for possible counters.
        //&#37-counter hits.
        //&#38-counter name.
        $counters = explode('::', $datafile2->getLine(38));
        $counters_select = "<select size=1 name=taelnavn>\n";
        $counters_select .= "   <option value=\"\" SELECTED>Alle tllere\n";

        for ($i = 0; $i < count($counters); $i++)       {
                $counters_select .= "   <option value=\"$counters[$i]\">$i $counters[$i]\n";
        }
        $counters_select .= "</select>\n";

        ?>
                        </td>
                </tr>
                <tr>
                        <td style="padding-right: 2em;"><input type=radio name=taeltype value=usynlig>Usynlig t&aelig;ller</td>
                        <td style="padding-right: 2em;"><input type=radio name=taeltype value=gtaeller>Gammeldags t&aelig;ller</td>
                        <td style="padding-right: 2em;"><input type=radio name=taeltype value=ikon CHECKED>Ikon</td>
                        <td><input type=radio name=taeltype value=panel>Ministatistik</td>
                </tr>
        </table>
        Der er mange forskellige ikoner og ministatistikker<br>
        <input type=hidden value="<?php echo htmlentities($in['username']); ?>" name="username">
        <input type=hidden value="vis_obl_kode2" name="type">
        </div>
        <input type=submit value="Videre..."> <input type=reset value="Nulstil formular">
        <?php

        if ($utils->getUAType() !== $utils->UA_TYPE_SIMPLE) {
                ?>
                <div class=forside>
                <h4>Avanceret: Fast tller</h4>
                <p>Hvis denne kode altid skal t&aelig;lle en speciel t&aelig;ller op, skal du vlge denne t&aelig;ller her. Normalt skal du ikke &aelig;ndre dette.</p>
                <?php echo $counters_select; ?>
                </p>
                <div>

                <div class=forside>
                <h4>Framesikker kode</h4>
                <input type=checkbox name=framesikker> Lav framesikker kode<br>
                <b>Tip</b> (for den tekniske): Du skal kun benytte <em>framesikker kode</em>, hvis den side, den obligatoriske kode skal ligge p&aring;, er en del af et st rammer (frameset), og denne side ligger p et andet domne, end den side hvis adresse str i browserens adresselinie.<br>
                <b>Tip</b> (for almindelige mennesker)
                <ol>
                        <li>Bruger du ikke rammer/frames, skal du er det <em>ligemeget</em> om du laver en framesikker eller en almindelig kode.
                        <li>Bruger du rammer/frames, s&aring; lav en almindelig obligatorisk kode (alts en der <em>ikke</em> er framesikker) og s&aelig;t den p&aring; din side.
                        <li>L&aelig;g siden ud p&aring; internettet, se siden fra de forskellige adresser hvorfra man kan g&aring; ind p&aring; den. Husk at mange sider i dag kan ses b&aring;de med og uden &quot;<code>www</code>&quot;.
                        <li>F&aring;r du p&aring; et tidspunkt en fejlmeddelelse, der bl.a. indeholder teksten &quot;<code>top.document.referer</code>&quot; skal du lave en <em>framesikker</em> kode. Fr du <em>ikke</em> fejlmeddelelsen, skal du lave en almindelig kode.
                </ol>
                <b>OBS</b> (for alle): N&aring;r den framesikre kode benyttes, bliver der ikke registreret referencesider, sgeord og -maskiner, med mindre g&aelig;sten g&aring;r direkte ind p&aring; en underside med koden p&aring;, og ikke ser siden i rammerne (framesettet).
                </div>

                <?php
        }
        echo "</form>";

        $utils->echoSiteEnd();

}

/**
 * Generates the mandatory code.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gen_obl_kode(&$utils, &$siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();
        $panellib = new Panellib($siteContext);

        $elements = array(
        'enkeltstat','prognoser','maaned_i_aar','sidste_31_dage','timer_hits','ugedag_hits','top_domain','domaene_hits',
        'info20','hits_os','hits_sprog','hits_opl','hits_farver','java_support','js','taellere','spoergs','ref','sord',
        'smask','zipklik','bev','alle','udgang','indgang'
        );

        //Saves which elements is selected, so they can be selected when the user enters the page later.

        for ($i = 0; $i < count($elements); $i++) {
                //name on the saveICookie parameter,array with the elements that might be saved.
                gemICookie($in, $siteContext, 'kodegenGemICookie', $elements);
        }

        $utils->echoSiteHead("", 0);

        //For the simple users area: Always show the full stat page.
        if ($utils->getUAType() === $utils->UA_TYPE_SIMPLE) {
                //$in['billed'] = 1;.
                $in['alle'] = 'vis';
        }

        //Use the invisible counter.
        if (isset($in['taeltype']) and $in['taeltype'] === 'usynlig')
                $in['billed'] = 'trans';

        //Make code for the counter.
        if (isset($in['billed'])) {
                if ($in['billed'] === "2"){$count = "&amp;amp;billed=2"; $hw = "height=\\\"20\\\" width=\\\"60\\\"";}
                else if ($in['billed'] === "3"){$count = "&amp;amp;billed=3"; $hw = "height=\\\"20\\\" width=\\\"60\\\"";}
                else if ($in['billed'] === "4"){$count = "&amp;amp;billed=4"; $hw = "height=\\\"20\\\" width=\\\"60\\\"";}
                else if ($in['billed'] === "5"){$count = "&amp;amp;billed=5"; $hw = "height=\\\"25\\\" width=\\\"25\\\"";}
                else if ($in['billed'] === "6"){$count = "&amp;amp;billed=6"; $hw = "height=\\\"25\\\" width=\\\"25\\\"";}
                else if ($in['billed'] === "7"){$count = "&amp;amp;billed=7"; $hw = "height=\\\"25\\\" width=\\\"150\\\"";}
                else if ($in['billed'] === "8"){$count = "&amp;amp;billed=8"; $hw = "height=\\\"25\\\" width=\\\"150\\\"";}
                else if ($in['billed'] === "trans"){$count = "&amp;amp;billed=trans"; $hw = "height=\\\"1\\\" width=\\\"1\\\"";}
                else if ($in['billed'] === "taelsh"){$count = "&amp;amp;billed=taelsh";}
                else if ($in['billed'] === "taelhs"){$count = "&amp;amp;billed=taelhs";}
                else  {$count = "&amp;amp;billed=1"; $hw = "height=\\\"20\\\" width=\\\"60\\\"";}
        } else {
                $count = "&amp;amp;billed=1"; $hw = "height=\\\"20\\\" width=\\\"60\\\"";
        }

        if (isset($in['taeltype'])) {
                if ($in['taeltype'] === "ntael")
                        $count .= "&amp;amp;ntael=ja";
                else if ($in['taeltype'] === "etael")
                        $count .= "&amp;amp;etael=ja";
        }

        if (isset($in['taelnr']) and ($in['taelnr'] <= $lib->pro(5)))
                 $count .= "&amp;amp;taelnr=".$in['taelnr'];
        else if (isset($in['taelnavn']))
                 $count .= "&amp;amp;taelnavn=".$in['taelnavn'];
        else
                 $count .= "";

        //OK$count - what shall be written by counter + image type, and a possible counter color.
        //OK$show - what shall be shown with a click.

        $show = vis($in);

        //Do this twice.
        $show = htmlentities(htmlentities($show));

        //Output the site with the finished mandatory code.
        $utils->echoSiteHead("", 0);
        ?>
        <div class=forside>
        <h1>Her er din kode</h1>
        <p>Marker koden, og s&aelig;t den ind p&aring; din hjemmeside. Se mere om det lige under kassen med koden.</p>
        <?php
        echo "<form action=\"\" method=post><textarea cols=60 rows=15>\n";

//If a mini stat shall be shown.
        if (isset($in['fra']) and $in['fra'] === "ministatistik") {

                $include2 = '';
                if (isset($in['hits']))
                         $include2 .= '268:';
                if (isset($in['hitsialt']))
                         $include2 .= '2468:';
                if (isset($in['hits_bruger']))
                         $include2 .= '269:';
                if (isset($in['hits_dag']))
                         $include2 .= '159:';
                if (isset($in['hits_maaned']))
                         $include2 .= '1284:';
                if (isset($in['bgr_online']))
                         $include2 .= '3:';

                if (strlen($include2) > 0)
                        $include2 = substr($include2, 0, -1);

                //require "panellib.cgi";.

                echo "&lt;!-- Start p JS stats kode til ministatistik --&gt;\n";
                echo "&lt;script language=&quot;JavaScript&quot;  type=&quot;text/javascript&quot; src=&quot;"
                                .htmlentities($siteContext->getOption('cgiURL')).'/'
                                .htmlentities($siteContext->getPath('jsvarsCgi'))."?brugernavn="
                                .htmlentities($in['username'])."&amp;amp;type=ministatistik&quot;&gt;\n";
                echo "&lt;/script&gt;\n";
                echo "&lt;!-- Slut p JS stats kode til ministatistik --&gt;\n\n";

                echo $panellib->panel($in['paneler'], oblkode($siteContext, $in, $hw, "url", $count, $show), $in['minioversk'], $include2);
                echo "\n";
        }

        if (isset($in['billed']) or $in['billed'] !== "trans")
                echo oblkode($siteContext, $in, $hw, "fuld", $count, $show);
        else
                echo oblkode($siteContext, $in, $hw, "uden links", $count, $show);
        echo "</textarea></form>\n";

        ?>
        </div>
        <div class=forside>
        <h3>Lidt hj&aelig;lp</h3>
        <h4>HTML &quot;i h&aring;nden&quot;</h4>
        <p>Koden skal indsttes p&aring; alle dine sider, og v&aelig;re mellem <code>
        &lt;body&gt;</code> og <code> &lt;/body&gt;</code> tagsne. Hvis du bruger frames skal koden indsttes i en af de filer hvis indhold bliver
        vist - <em>ikke</em> i filen med <code>&lt;frameset&gt;</code> koderne!</p>

        <h4>FrontPage ol.</h4>
        <p>Hvis du bruger FrontPage, eller et lignende program hvor du ikke ser HTML-koderne, skal du huske at inds&aelig;tte
        koden som HTML. Ellers vil koden blive vist p&aring; din side, og i&oslash;vrigt v&aelig;re virkningsl&oslash;s. Har du det problem at din
        kode bliver vist p&aring; din side, s&aring; se i de <a href="<?php echo htmlentities($siteContext->getOption('ZSHomePage')); ?>/oss.shtml#oblkode_fp" target="_top">Ofte Stillede Sprgsm&oslash;l</a>,
        hvor du finder en l&oslash;sning, da dette problem er meget almindeligt.</p>
        </div>

        <?php

        if ($utils->getUAType() === $utils->UA_TYPE_SIMPLE) {
                echo "<div class=forside><h3>Mere avanceret</h3><p>nsker du selv at bestemme mere over koden, fx. vlge at en bestemt tller skal tlles op, kan du gre dette i den avancerede tilstand. Du kan skifte ved at trykke p linket &quot;Skift til avanceret brug&quot; i menuen til venstre.</div>\n";
                echo "<div class=forside><h3>F&aring;r du javascript-fejl</h3>\nGiver denne obligatoriske kode en fejl, n&aring;r du bruger den, skal du nok lave en &quot;framesikker&quot; kode. Dette kan du g&oslash;re ved at skifte til avanceret visning og lave den obligatoriske kode igen. P&aring; den f&oslash;rste side hvor du skal foretage nogle valg, skal du s&aring; stte kryds ud for &quot;Framesikker kode&quot;.</div>";
        } else {
                echo "<div class=forside><h3>F&aring;r du javascript-fejl</h3>\nGiver denne obligatoriske kode en fejl, n&aring;r du bruger den, skal du nok lave en &quot;framesikker&quot; kode. Dette kan du g&oslash;re ved at lave den obligatoriske kode igen, og p&aring; den f&oslash;rste side hvor du skal foretage nogle valg, skal du s&aelig;tte kryds ud for &quot;Framesikker kode&quot;.</div>";
        }

        $utils->echoSiteEnd();
}

/**
 * Shows step 2 in generating the mandatory code.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function vis_obl_kode2($utils, $siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();

        $utils->echoSiteHead("", 0);
        echo "<form action=\"".htmlentities($siteContext->getOption('urlUserAreaCodegen'))."\" method=POST>\n";
        echo "<div class=forside>";

        if (isset($in['taeltype']) and $in['taeltype'] === 'panel') {
                ?>
                <h1>Vlg ministatistik</h1>
                <p>Vlg det udseende din ministatistik skal have.</p>
                <?php

                $panellib = new Panellib($siteContext);
                echo $panellib->vis_alle_paneler(3, "med radiobuttons");

                echo "</div>\n";
                echo "<input type=hidden name=fra value=ministatistik>\n";
                echo "<input type=hidden name=taelnavn value=\"".$in['taelnavn']."\">\n";

                if ($utils->getUAType() !== $utils->UA_TYPE_SIMPLE) {

                        ?>
                        <p><input type=submit value="Videre..."> <input type=reset value="Nulstil formular"></p>

                        <div class=forside>
                        <h3>Avanceret: Vlg statistikker</h3>
                        <p>Vlg de statistikker der skal vises.</p>
                        <input type=checkbox name=hits CHECKED>Hits<br>
                        <input type=checkbox name=hitsialt CHECKED>Hits i alt<br>
                        <input type=checkbox name=hits_bruger CHECKED>Hits. pr. gst (besgende)<br>
                        <input type=checkbox name=hits_dag CHECKED>Hits pr. dag<br>
                        <input type=checkbox name=hits_maaned CHECKED>Hits pr. mned<br>
                        <input type=checkbox name=bgr_online CHECKED>Antal besgende p siden lige nu (online).<br>

                        <p>Nederst p ministatistikken er der et link til din statistikside. Vlg den tekst linket skal have:</p>
                        <select size=1 name=minioversk>
                                <option>Flere...
                                <option>Flere stats...
                                <option>Statistikker
                                <option>Stats
                        </select>
                        <p>
                        <?php
                } //End of - if not simpel.
        } else if (isset($in['taeltype']) and $in['taeltype'] === 'gtaeller') {
                $counters = explode('::', $datafile2->getLine(38));
                $counters_select = "<select size=1 name=taelnavn>\n";
                if (! isset($in['taelnavn']))
                        $sel = " SELECTED";
                else
                        $sel = "";
                $counters_select .= "   <option value=\"\"$sel>Alle tllere\n";

                for ($i = 0; $i < count($counters); $i++) {
                        if (isset($in['taelnavn']) and ($in['taelnavn'] === $counters[$i]))
                                        $sel = " SELECTED";
                        else
                                        $sel = "";
                        $counters_select .= "   <option value=\"$counters[$i]\"$sel>$i $counters[$i]\n";
                }
                $counters_select .= "</select>\n";

                ?>
                <div class=forside>
                <h2>Hvad skal vises</h2>
                <p>Vlg hvad tlleren skal vise. Du kan vlge:
                <?php

                if ($utils->getUAType() !== $utils->UA_TYPE_SIMPLE) {
                        ?>
                        <select name="gtaeltype" size="1">
                                <option value="ntael" selected>1. Hits for hele siden (der kan nulstilles [1])
                                <option value="etael">2. Hits for hele siden (der ikke kan nulstilles [1])
                                <option value="taeller">3. Tallet for din faste tller [2])
                        </select>

                        <a href="JAVAscript: alert('[1] Hvor 1. er antal hits p hele siden, siden seneste nulstilling, er 2. antal hits siden stastikken kom p. Indtil antal hits for frste gang er nulstillet, er disse to tal alts ens.');"><img src="<?php echo htmlentities($siteContext->getPath('zipstat_icons')); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til note [1]...">[1]</a>&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="JAVAscript: alert('[2] Angiv tllernavn eller -nummer neden for. Hvis intet angives, vil sidens filnavn blive brugt som tllernavn. Er alle tllere brugt op, vil der ikke blive talt op. Den tller du angiver, vil samtidig vre den der bliver talt op nr siden vises.');"><img src="<?php echo htmlentities($siteContext->getPath('zipstat_icons')); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til note [2]...">[2]</a>

                        <ol>
                        <li>Hvor mange besgende (hits) alle dine sider (med ZIP Stat p).
                        <li>Det tal du valgte som fast tller er net til. Hvis du ikke valgte en fast tller, eller vil ndre dit valg, kan du ndre det lngere nede p denne side.
                        <li>Antal hits for alle dine sider, siden du fik ZIP Stat p. Forskellen p denne og 1 er, at dette tal ikke kan nulstilles.
                        </ol>
                        <?php
                } else {
                        ?>
                        <select name="gtaeltype" size="1">
                                <option value="ntael" selected>Hits for hele siden
                                <option value="taeller">Hits for den enkelte html-fil
                        </select>
                        <?php
                } //End of - if simpel.

                ?>
                </div>

                <div class=forside>
                <h2>Vlg udseende</h2>
                <p>Du kan vlge mellem en tller med sort baggrund og hvide tal, eller hvid baggrund og sorte tal.</p>
                <input type=radio name=billed value=taelsh CHECKED><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/sh_taeller.gif" width=40 height=16 alt="Eks. p tller"><br>
                <input type=radio name=billed value=taelhs><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/hs_taeller.gif" width=40 height=16 alt="Eks. p tller">
                </p>
                </div>

                <input type=hidden name=fra value=gtaeller>
                <?php

                if ($utils->getUAType() !== $utils->UA_TYPE_SIMPLE) {
                        ?>
                        <p><input type=submit value="Videre..."> <input type=reset value="Nulstil formular"></p>

                        <div class=forside>
                        <h4>Avanceret: Fast tller</h4>
                        <p>Hvis denne kode altid skal tlle n speciel tller op, skal du vlge denne tller her.
                        Du havde muligheden for dette p forrige side, men set i lyset af mulighederne du har med den gammeldags tller,
                        kunne det vre du ville ndre dit valg.</p>

                        <?php echo $counters_select; ?>
                        </p>
                        </div>
                        <?php
                } //End of - if not simpel.
        } else { //Show icon.
                ?>
                <h1>Vlg ikon</h1>
                <p>Vlg det ikon du nsker at f vist</p>
                <table>
                        <tr>
                                <td>
                                <input type=radio name=billed value="1" checked><td><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/stats1.gif" height=20 width=60 border=0 align=bottom>
                                </td>
                                <td>
                                        <input type=radio name=billed value="2"><td><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/stats2.gif" height=20 width=60 border=0 align=bottom>
                                </td>
                                <td>
                                        <input type=radio name=billed value="3"><td><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/stats3.gif" height=20 width=60 border=0 align=bottom>
                                </td>
                                <td>
                                        <input type=radio name=billed value="4"><td><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/stats4.gif" height=20 width=60 border=0 align=bottom>
                                </td>
                        </tr>
                        <tr>
                                <td>
                                        <input type=radio name=billed value="5"><td><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/stats5.gif" height=25 width=25 border=0 align=bottom>
                                </td>
                                <td>
                                        <input type=radio name=billed value="6"><td><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/stats6.gif" height=25 width=25 border=0 align=bottom>
                                </td>
                                <td>
                                        <input type=radio name=billed value="7"><td><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/stats7.gif" height=25 width=150 border=0 align=bottom>
                                </td>
                                <td>
                                        <input type=radio name=billed value="8"><td><img src="<?php echo htmlentities($siteContext->getOption('imageURL')); ?>/stats8.gif" height=25 width=150 border=0 align=bottom>
                        </tr>
                </table>
                <input type=hidden name=taelnavn value="<?php echo htmlentities($in['taelnavn']); ?>">
                <input type=hidden name=fra value=ikon>
                <?php
        }

        echo "</div>\n";
        echo "<input type=submit value=\"Videre...\"> <input type=reset value=\"Nulstil formular\">\n";
        echo "<input type=hidden value=\"".$in['username']."\" name=\"username\">\n";
        echo "<input type=hidden value=\"vis_obl_kode3\" name=\"type\">\n";
        echo "<input type=hidden value=\"".htmlentities(isset($in['framesikker']) ? $in['framesikker'] : '')."\" name=\"framesikker\">\n";
        echo "</form>\n";

        $utils->echoSiteEnd();

}

/**
 * Shows step 3 in generating the mandatory code.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function vis_obl_kode3(&$utils, &$siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();

        $hidden = '';
        $addtext = '';

        if (isset($in['fra']) and $in['fra'] === 'ministatistik') {
                //If shown in the context of a mini stat.
                if (!isset($in['minioversk'])) {
                        gen_obl_kode($utils, $siteContext);
                        exit;
                }

                $addtext = "ministatistikkens overskrift";

                $hidden .= "<input type=hidden name=hits value=\"".(isset($in['hits']) ? $in['hits'] : '')."\">\n";
                $hidden .= "<input type=hidden name=hitsialt value=\"".(isset($in['hitsialt']) ? $in['hitsialt'] : '')."\">\n";
                $hidden .= "<input type=hidden name=hits_bruger value=\"".(isset($in['hits_bruger']) ? $in['hits_bruger'] : '')."\">\n";
                $hidden .= "<input type=hidden name=hits_dag value=\"".(isset($in['hits_dag']) ? $in['hits_dag'] : '')."\">\n";
                $hidden .= "<input type=hidden name=hits_maaned value=\"".(isset($in['hits_maaned']) ? $in['hits_maaned'] : '')."\">\n";
                $hidden .= "<input type=hidden name=bgr_online value=\"".(isset($in['bgr_online']) ? $in['bgr_online'] : '')."\">\n";
                $hidden .= "<input type=hidden name=minioversk value=\"".(isset($in['minioversk']) ? $in['minioversk'] : '')."\">\n";
                $hidden .= "<input type=hidden name=paneler value=\"".(isset($in['paneler']) ? $in['paneler'] : '')."\">\n";

                $hidden .= "<input type=hidden name=billed value=trans>\n"; //Fixed setting.
        } else if (isset($in['fra']) and $in['fra'] === 'gtaeller') {
                //If shown i the context of the legasy/graphic counter.
                $addtext = "tlleren";

                $hidden .= "<input type=hidden name=taeltype value=".(isset($in['gtaeltype']) ? $in['gtaeltype'] : '').">\n";
                $hidden .= "<input type=hidden name=billed value=".(isset($in['billed']) ? $in['billed'] : '').">\n";
        } else {
                //If shown in the context of an icon as a counter image.
                $addtext = "ikonet";
                $hidden .= "<input type=hidden name=billed value=".(isset($in['billed']) ? $in['billed'] : '').">\n";
        }
        $hidden .= "<input type=hidden name=taelnavn value=\"".(isset($in['taelnavn']) ? $in['taelnavn'] : '')."\">\n"; //Valid in either situation.
        $hidden .= "<input type=hidden name=fra value=".(isset($in['fra']) ? $in['fra'] : '').">\n";
        $hidden .= "<input type=hidden name=framesikker value=\"".(isset($in['framesikker']) ? $in['framesikker'] : '')."\">\n";

        $utils->echoSiteHead("", 0);

        //Code for saving in cookies.
        $elements = array(
        'enkeltstat','prognoser','maaned_i_aar','sidste_31_dage','timer_hits','ugedag_hits','top_domain','domaene_hits',
        'info20','hits_os','hits_sprog','hits_opl','hits_farver','java_support','js','taellere','spoergs','ref','sord',
        'smask','zipklik','bev','alle','udgang','indgang'
        );

        $showAll = 1;
        for ($i = 0; $i < count($elements); $i++) {
                $select[$elements[$i]] = checkedCookie($in, "kodegenGemICookie", $elements[$i]);
                if (($select[$elements[$i]]) and ($elements[$i] !== 'alle'))
                        $showAll = 0;
        }

        if ($showAll)
                $select['alle'] = ' SELECTED';

        ?>
        <div class=forside>
        <form action="<?php echo htmlentities($siteContext->getOption('urlUserAreaCodegen')); ?>" method=POST>

        <h3>Visning p statistikside</h3>
        <p>Nr man klikker p <?php echo (isset($addtext) ? $addtext : ''); ?> kommer man til din statistikside. Vlg hvilke statistikker skal der vises p denne:</p>

        <p><input type=checkbox name="alle"<?php echo (isset($select['alle']) ? $select['alle'] : ''); ?>> Alle statistikker (anbefales)<BR>
        <a href="JAVAscript: alert('Hvis du har valgt ikke at stte kryds i ovenstende boks,\\nskal du stte kryds i en eller flere af de nedenstende.\\nNr en besgende klikker p statistikbilledet p din hjemeside, vil\\ndisse ting blive vist.');"><img src="<?php echo htmlentities($siteContext->getPath('zipstat_icons')); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til resten..."></a>
        Hvis du ikke har valgt at stte kryds i ovenstende boks, skal du stte kryds i en eller flere af nedenstende.</p>
        </div>

        <input type=submit value="Generer kode"> <input type=reset value="Nulstil formular">
        <?php echo (isset($hidden) ? $hidden : ''); ?>
        <input type=hidden value="<?php echo (isset($in['username']) ? $in['username'] : ''); ?>" name="username">
        <input type=hidden value="lav_obl_kode" name="type">

        <p><i>(Lngere nede er der mulighed for at lave firewall-sikker kode...)</i></p>

        <h4>Avanceret</h4>
        <div class=forside>
        <table border=1>
        <tr><td><input type=checkbox name="enkeltstat"<?php echo (isset($select['enkeltstat']) ? $select['enkeltstat'] : ''); ?>> Div. enkeltstende stastikker.
                <td><input type=checkbox name="prognoser"<?php echo (isset($select['prognoser']) ? $select['prognoser'] : ''); ?>>Prognoser.
        <tr><td><input type=checkbox name="maaned_i_aar"<?php echo (isset($select['maaned_i_aar']) ? $select['maaned_i_aar'] : ''); ?>>Hits for de seneste 12 mneder.
                <td><input type=checkbox name="sidste_31_dage"<?php echo (isset($select['sidste_31_dage']) ? $select['sidste_31_dage'] : ''); ?>>Hits for de seneste 31 dage.
        <tr><td><input type=checkbox name="timer_hits"<?php echo (isset($select['timer_hits']) ? $select['timer_hits'] : ''); ?>>Hits pr. time.
                <td><input type=checkbox name="ugedag_hits"<?php echo (isset($select['ugedag_hits']) ? $select['ugedag_hits'] : ''); ?>>Hits pr. ugedag.
        <tr><td><input type=checkbox name="top_domain"<?php echo (isset($select['top_domain']) ? $select['top_domain'] : ''); ?>>Hits pr. topdomne (.dk, .com osv.)
                <td><input type=checkbox name="domaene_hits"<?php echo (isset($select['domaene_hits']) ? $select['domaene_hits'] : ''); ?>>Hits pr. domne.
        <tr><td><input type=checkbox name="info20"<?php echo (isset($select['info20']) ? $select['info20'] : ''); ?>>Div. informationer om de seneste besgende.
                <td><input type=checkbox name="hits_browser"<?php echo (isset($select['hits_browser']) ? $select['hits_browser'] : ''); ?>>Hits pr. browser.
        <tr><td><input type=checkbox name="hits_os"<?php echo (isset($select['hits_os']) ? $select['hits_os'] : ''); ?>>Hits pr. styresystem.
                <td><input type=checkbox name="hits_sprog"<?php echo (isset($select['hits_sprog']) ? $select['hits_sprog'] : ''); ?>>Hits pr. sprog.
        <tr><td><input type=checkbox name="hits_opl"<?php echo (isset($select['hits_opl']) ? $select['hits_opl'] : ''); ?>>Hits pr. skrmoplsning.
                <td><input type=checkbox name="hits_farver"<?php echo (isset($select['hits_farver']) ? $select['hits_farver'] : ''); ?>>Hits pr. antal understttede farver (i bits).
        <tr><td><input type=checkbox name="java_support"<?php echo (isset($select['java_support']) ? $select['java_support'] : ''); ?>>JAVA support.
                <td><input type=checkbox name="js"<?php echo (isset($select['js']) ? $select['js'] : ''); ?>>JAVA-script support.
        <tr><td><input type=checkbox name="taellere"<?php echo (isset($select['taellere']) ? $select['taellere'] : ''); ?>>Hits for dine 30 tllere.
                <td><input type=checkbox name="spoergs"<?php echo (isset($select['spoergs']) ? $select['spoergs'] : ''); ?>>Sprgsml og svar.
        <tr><td><input type=checkbox name="ref"<?php echo (isset($select['ref']) ? $select['ref'] : ''); ?>>Referencesider.
                <td><input type=checkbox name="indgang"<?php echo (isset($select['indgang']) ? $select['indgang'] : ''); ?>>Indgangssider.
        <tr><td><input type=checkbox name="udgang"<?php echo (isset($select['udgang']) ? $select['udgang'] : ''); ?>>Udgangssider.
                <td><input type=checkbox name="sord"<?php echo (isset($select['sord']) ? $select['sord'] : ''); ?>>Sgeord.
        <tr><td><input type=checkbox name="smask"<?php echo (isset($select['smask']) ? $select['smask'] : ''); ?>>Sgemaskiner.
                <td><input type=checkbox name="zipklik"<?php echo (isset($select['zipklik']) ? $select['zipklik'] : ''); ?>>Viser hvilke links (angivet i &quot;Adresser&quot;) der er klikket p.
        <tr><td><input type=checkbox name="bev"<?php echo (isset($select['bev']) ? $select['bev'] : ''); ?>>Bevgelser.
                <td>
        </table>
        <input type=hidden name=kodegenGemICookie value=1>

        </div>
        <div class=forside>
        <h4>Firewall-sikker kode</h4>
        <input type=checkbox name=firewallsikker> Lav firewall-sikker kode<br>
        Den firewall-sikre kode indholder ikke det JAVA-script, som den almindelige obligatoriske kode. Du br kun benytte
        den firewall-sikre kode, hvis den skal bruges p et intranet (lokalnetvrk der benytter internetteknologi), hvor
        din firewall giver fejlmeddelelser mht. den almindelige obligatoriske kode.
        </div>

        <?php

        ?></div>

        <p><input type=submit value="Generer kode"> <input type=reset value="Nulstil formular"></p>

        </form>

        <?php

        $utils->echoSiteEnd();
}


/**
 * Shows the stat panel.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function vis_statpanel(&$utils, &$siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();

        $utils->echoSiteHead("", 0);

        ?>
        <form action="<?php echo htmlentities($siteContext->getOption('urlUserAreaCodegen')); ?>" method=POST>
        <div class=forside>
        <h1>Ministatistik</h1>
        <p>Vlg den ministatistik du nsker:</p>
        <?php

        $panellib = new Panellib($siteContext);
        echo $panellib->vis_alle_paneler(3, 'med radiobuttons');

        ?>

        </div>

        <p><input type=submit value="Videre..."> <input type=reset value="Nulstil formular"></p>
        <input type=hidden value="<?php echo (isset($in['username']) ? $in['username'] : ''); ?>" name="username">
        <input type=hidden name=type value="vis_statpanel2">

        <div class=forside>
        <h3>Avanceret: Vlg statistikker</h3>
        <p>Vlg de statistikker der skal vises.</p>
        <input type=checkbox name=hits CHECKED>Hits<br>
        <input type=checkbox name=hitsialt CHECKED>Hits i alt<br>
        <input type=checkbox name=hits_bruger CHECKED>Hits. pr. gst (besgende)<br>
        <input type=checkbox name=hits_dag CHECKED>Hits pr. dag<br>
        <input type=checkbox name=hits_maaned CHECKED>Hits pr. mned<br>
        <input type=checkbox name=bgr_online CHECKED>Antal besgende p siden lige nu (online).<br>

        <h3>Overskrift</h3>
        <p>Nederst p ministatistikken er der et link til din statistikside. Vlg den tekst linket skal have:</p>
        <select size=1 name=minioversk>
                <option>Flere...
                <option>Flere stats...
                <option>Statistikker
                <option>Stats
        </select>

        </form>
        <?php

        $utils->echoSiteEnd();
}

/**
 * Shows 2nd step for the stat panel.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function vis_statpanel2(&$utils, &$siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();

        $utils->echoSiteHead("", 0);

        $hidden = "<input type=hidden name=hits value=\"".htmlentities(isset($in['hits']) ? $in['hits'] : '')."\">\n";
        $hidden .= "<input type=hidden name=hitsialt value=\"".htmlentities(isset($in['hitsialt']) ? $in['hitsialt'] : '')."\">\n";
        $hidden .= "<input type=hidden name=hits_bruger value=\"".htmlentities(isset($in['hits_bruger']) ? $in['hits_bruger'] : '')."\">\n";
        $hidden .= "<input type=hidden name=hits_dag value=\"".htmlentities(isset($in['hits_dag']) ? $in['hits_dag'] : '')."\">\n";
        $hidden .= "<input type=hidden name=hits_maaned value=\"".htmlentities(isset($in['hits_maaned']) ? $in['hits_maaned'] : '')."\">\n";
        $hidden .= "<input type=hidden name=bgr_online value=\"".htmlentities(isset($in['bgr_online']) ? $in['bgr_online'] : '')."\">\n";
        $hidden .= "<input type=hidden name=minioversk value=\"".htmlentities(isset($in['minioversk']) ? $in['minioversk'] : '')."\">\n";
        $hidden .= "<input type=hidden name=paneler value=\"".htmlentities(isset($in['paneler']) ? $in['paneler'] : '')."\">\n";


        //Code for saving in cookies.
        $elements = array(
        'enkeltstat','prognoser','maaned_i_aar','sidste_31_dage','timer_hits','ugedag_hits','top_domain','domaene_hits',
        'info20','hits_os','hits_sprog','hits_opl','hits_farver','java_support','js','taellere','spoergs','ref','sord',
        'smask','zipklik','bev','alle','indgang','udgang'
        );

        for ($i = 0; $i < count($elements); $i++) {
                $select[$elements[$i]] = checkedCookie($in, "kodegenGemICookie", $elements[$i]);
                if (isset($select[$elements[$i]]) and ($elements[$i] !== 'alle'))
                        $showAll = 0;
        }
        if ($showAll)
                $select['alle'] = ' SELECTED';

        ?>
        <form action="<?php echo htmlentities($siteContext->getOption('urlUserAreaCodegen')); ?>" method=POST>

        <div class=forside>
        <h3>Visning p statistikside</h3>
        <p>Nr man klikker p ministatistikkens overskrift kommer man til din statistikside. Vlg hvilke statistikker skal der vises p denne:</p>

        <p><input type=checkbox name="alle"<?php echo (isset($select['alle']) ? $select['alle'] : ''); ?>> Alle statistikker (anbefales)<BR>
        <a href="JAVAscript: alert('Hvis du har valgt ikke at stte kryds i ovenstende boks,\\nskal du stte kryds i en eller flere af de nedenstende.\\nNr en besgende klikker p ministatistikkens overskrift, p din hjemeside, vil\\ndisse ting blive vist.');"><img src="<?php echo htmlentities($siteContext->getPath('zipstat_icons')); ?>/stegn2.gif" width=9 height=14 border=0 alt="Hjlp til resten..."></a>
        Hvis du ikke har valgt at stte kryds i ovenstende boks, skal du stte kryds i en eller flere af nedenstende.</p>
        </div>

        <input type=submit value="Generer kode"> <input type=reset value="Nulstil formular">
        <?php echo (isset($hidden) ? $hidden : ''); ?>
        <input type=hidden value="<?php echo (isset($in['username']) ? $in['username'] : ''); ?>" name="username">
        <input type=hidden name=type value=gen_statpanel>
        <h4>Avanceret</h4>
        <div class=forside>
        <table border=1>
        <tr><td><input type=checkbox name="enkeltstat"<?php echo (isset($select['enkeltstat']) ? $select['enkeltstat'] : ''); ?>> Div. enkeltstende stastikker.
                <td><input type=checkbox name="prognoser"<?php echo (isset($select['prognoser']) ? $select['prognoser'] : ''); ?>>Prognoser.
        <tr><td><input type=checkbox name="maaned_i_aar"<?php echo (isset($select['maaned_i_aar']) ? $select['maaned_i_aar'] : ''); ?>>Hits for de seneste 12 mneder.
                <td><input type=checkbox name="sidste_31_dage"<?php echo (isset($select['sidste_31_dage']) ? $select['sidste_31_dage'] : ''); ?>>Hits for de seneste 31 dage.
        <tr><td><input type=checkbox name="timer_hits"<?php echo (isset($select['timer_hits']) ? $select['timer_hits'] : ''); ?>>Hits pr. time.
                <td><input type=checkbox name="ugedag_hits"<?php echo (isset($select['ugedag_hits']) ? $select['ugedag_hits'] : ''); ?>>Hits pr. ugedag.
        <tr><td><input type=checkbox name="top_domain"<?php echo (isset($select['top_domain']) ? $select['top_domain'] : ''); ?>>Hits pr. topdomne (.dk, .com osv.)
                <td><input type=checkbox name="domaene_hits"<?php echo (isset($select['domaene_hits']) ? $select['domaene_hits'] : ''); ?>>Hits pr. domne.
        <tr><td><input type=checkbox name="info20"<?php echo (isset($select['info20']) ? $select['info20'] : ''); ?>>Div. informationer om de seneste besgende.
                <td><input type=checkbox name="hits_browser"<?php echo (isset($select['hits_browser']) ? $select['hits_browser'] : ''); ?>>Hits pr. browser.
        <tr><td><input type=checkbox name="hits_os"<?php echo (isset($select['hits_os']) ? $select['hits_os'] : ''); ?>>Hits pr. styresystem.
                <td><input type=checkbox name="hits_sprog"<?php echo (isset($select['hits_sprog']) ? $select['hits_sprog'] : ''); ?>>Hits pr. sprog.
        <tr><td><input type=checkbox name="hits_opl"<?php echo (isset($select['hits_opl']) ? $select['hits_opl'] : ''); ?>>Hits pr. skrmoplsning.
                <td><input type=checkbox name="hits_farver"<?php echo (isset($select['hits_farver']) ? $select['hits_farver'] : ''); ?>>Hits pr. antal understttede farver (i bits).
        <tr><td><input type=checkbox name="java_support"<?php echo (isset($select['java_support']) ? $select['java_support'] : ''); ?>>JAVA support.
                <td><input type=checkbox name="js"<?php echo (isset($select['js']) ? $select['js'] : ''); ?>>JAVA-script support.
        <tr><td><input type=checkbox name="taellere"<?php echo (isset($select['taellere']) ? $select['taellere'] : ''); ?>>Hits for dine 30 tllere.
                <td><input type=checkbox name="spoergs"<?php echo (isset($select['spoergs']) ? $select['spoergs'] : ''); ?>>Sprgsml og svar
        <tr><td><input type=checkbox name="ref"<?php echo (isset($select['ref']) ? $select['ref'] : ''); ?>>Referencesider.
                <td><input type=checkbox name="indgang"<?php echo (isset($select['indgang']) ? $select['indgang'] : ''); ?>>Indgangssider.
        <tr><td><input type=checkbox name="udgang"<?php echo (isset($select['udgang']) ? $select['udgang'] : ''); ?>>Udgangssider.
                <td><input type=checkbox name="sord"<?php echo (isset($select['sord']) ? $select['sord'] : ''); ?>>Sgeord.
        <tr><td><input type=checkbox name="smask"<?php echo (isset($select['smask']) ? $select['smask'] : ''); ?>>Sgemaskiner.
                <td><input type=checkbox name="zipklik"<?php echo (isset($select['zipklik']) ? $select['zipklik'] : ''); ?>>Viser hvilke links (angivet i &quot;Adresser&quot;) der er klikket p.
        <tr><td><input type=checkbox name="bev"<?php echo (isset($select['bev']) ? $select['bev'] : ''); ?>>Bevgelser.
                <td>
        </table>

        </div>

        <input type=hidden name=kodegenGemICookie value=1>
        <input type=submit value="Generer kode"> <input type=reset value="Nulstil formular">

        </form>

        <?php

        $utils->echoSiteEnd();
}

/**
 * Shows generation of the stat panel.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gen_statpanel(&$utils, &$siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();

        $hw = ''; //todo: Find out what this is used for - maybe it can be deleted.
        $count = ''; //todo: Find out what this is used for - maybe it can be deleted.

        $elements = array(
        'enkeltstat','prognoser','maaned_i_aar','sidste_31_dage','timer_hits','ugedag_hits','top_domain','domaene_hits',
        'info20','hits_os','hits_sprog','hits_opl','hits_farver','java_support','js','taellere','spoergs','ref','sord',
        'smask','zipklik','bev','alle','udgang','indgang'
        );

        for ($i = 0; $i < count($elements); $i++) {
                //name on the gemICookie parameter,array with the items that can be saved.
                gemICookie($in, $siteContext, 'kodegenGemICookie', $elements);
        }

        $utils->echoSiteHead("", 0);
        echo "<div class=forside><h1>Ministatistik kode</h1>\nHer er koden til din ministatistik. Kopier den, og st den ind p din hjemmeside hvor ministatistikken skal vre.";

        echo "<form><textarea cols=60 rows=15>\n";

        //If a mini stat shall be shown.
        $include2 = '';
        if ($in['hits'])
                 $include2 .= "268:";
        if ($in['hitsialt'])
                 $include2 .= "2468:";
        if ($in['hits_bruger'])
                 $include2 .= "269:";
        if ($in['hits_dag'])
                 $include2 .= "159:";
        if ($in['hits_maaned'])
                 $include2 .= ":1284";
        if ($in['bgr_online'])
                 $include2 .= "3:";
        $include2 = substr($include2, 0, -1);

        //require "panellib.cgi";.

        echo "&lt;!-- Start p JS stats kode til ministatistik --&gt;\n";
        echo "&lt;script language=&quot;JavaScript&quot;  type=&quot;text/javascript&quot; src=&quot;"
                                .htmlentities($siteContext->getOption('cgiURL')).'/'
                                .htmlentities($siteContext->getPath('jsvarsCgi'))."?brugernavn="
                                .htmlentities($in['username'])."&amp;amp;type=ministatistik&quot;&gt;\n";
        echo "&lt;/script&gt;\n";
        echo "&lt;!-- Slut p JS stats kode til ministatistik --&gt;\n\n";

        $show = vis($in);

        $panellib = new Panellib($siteContext);
        echo $panellib->panel($in['paneler'], oblkode($siteContext, $in, $hw, "url", $count, $show), $in['minioversk'], $include2);

        echo "</textarea></form></div>\n";

        $utils->echoSiteEnd();
}

/**
 * Shows generation of poll code.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function vis_sp_gen(&$utils, &$siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();

        $utils->echoSiteHead("", 0);
        ?>
        <form action="<?php echo htmlentities($siteContext->getOption('urlUserAreaCodegen')); ?>" method="POST">

        <div class=forside>
        <h1>Generer sprgsmlskode</h1>
        <input type=hidden name=type value="gen_sp">
        <input type=hidden name=username value="<?php echo (isset($in['username']) ? $in['username'] : ''); ?>">
        <h3>St kryds ud for de sprgsml koden skal vise</h3>

        <?php

        $pro_max_questions = $lib->pro(3);

        $question = explode('::', $datafile2->getLine(41));
        for ($i = 0; $i < $pro_max_questions; $i++) {
                $k = $i+1;
                print "<a href=\"JAVAscript: alert('Hvis dette sprgsml skal vises hvor du stter\\nkoden ind p din hjemmeside, skal du\\\nstte kryds i boksen.\\\n\\\nVises der ikke noget sprgsml, skal du indtaste det\\np \\\"Rediger sprgsml\\\" siden');\"><img src=".htmlentities($siteContext->getOption('ZSHomePage'))." width=9 height=14 border=0 alt=\"Hjlp til sprgsml...\"></a>";
                print "<input type=checkbox name=sp$k>$question[$i]<BR>\n";
        }

        ?>
        <input type=submit value="Generer kode"> <input type=reset value="Nulstil formular">
        </form>
        </div>
        <?php

        $utils->echoSiteEnd();
}

/**
 * Shows generation of the click counter.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function vis_zipklik_gen(&$utils, &$siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();

        //69-ZIPClick - hits$indata.
        //70-ZIPClick - adress names.
        //71-ZIPClick - URLs.

        $names = explode('::', $datafile2->getLine(70));
        $urls = explode('::', $datafile2->getLine(71));

        $utils->echoSiteHead("", 0);
        echo "<div class=forside><h1>Klikt&aelig;llere</h1>Istedet for at benytte de links du har angivet p&aring; siden &quot;Adresser&quot;, p&aring; din hjemmeside, skal du benytte de nedenstende links, der svarer til dem. S&aring; vil der blive registreret hvor mange der klikker p&aring; de enkelte links.</div>";

        $pro_max_moments = $lib->pro(10);

        for ($i = 0; $i <= $pro_max_moments; $i++) {
        if (isset($urls[$i])) {
                $url = htmlentities($siteContext->getOption('cgiURL'))."/clickCounter.php?brugernavn=".htmlentities(isset($in['username']) ? $in['username'] : '')."&urlnavn=".htmlentities(urlencode($names[$i]))."&urlnr=$i";
                print "<hr size=1 width=\"66%\">Nr. $i: <a href=\"".htmlentities($url)."\"><tt>".htmlentities($url)."</tt></a><br>\n";
                print "Sender folk til: <a href=\"".htmlentities($urls[$i])."\">".htmlentities($urls[$i])."</a>\n";
                }
        }

        $utils->echoSiteEnd();
}

/**
 * Shows generation of the poll.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function gen_sp_kode(&$utils, &$siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();

        $pro_max_questions = $lib->pro(3);

        for ($i = 1; $i <= $pro_max_questions; $i++) {
                if (isset($in["sp$i"]))
                        $qstring .= "sp$i=vis&";
        }
        $qstring .= "brugernavn=".htmlentities(isset($in['username']) ? $in['username'] : '');

        $utils->echoSiteHead("", 0);
        ?>
        <div class=forside>Kopier koden ind p din hjemmeside hvor folk skal kunne svare p dine sprgsml. Hvis du ndre et sprgsml via ZIP stat, blive det automatisk ndret p din hjemmeside.<br>
        <pre>
        &lt;!-- Start p sprgsmlskode --&gt;
        &lt;SCRIPT language=&quot;JAVAscript&quot; SRC=&quot;<?php echo htmlentities($siteContext->getOption('cgiURL')); ?>/pollSiteScript.php?<?php echo htmlentities(htmlentities($qstring)); ?>&quot;&gt;&lt/SCRIPT&gt;
        &lt;!-- Slut  p sprgsmlskode --&gt;
        </pre>
        </div>
        <?php

        $utils->echoSiteEnd();
}

/**
 * Shows generation of the java script code.
 *
 * @param $utils       the UsersAreaUtils object
 * @param $siteContext the instance of the site context.
 * @public
 */
function vis_js_kode($utils, $siteContext) {
        $lib = &$siteContext->getCodeLib();
        $in = $lib->getHTTPVars();
        $datafile2 = &$lib->getDatafil();

        $utils->echoSiteHead("JavaScript stats kode", 0);
        echo"<span class=forside><p>Placer denne kode p&aring; de sider, hvorfra du vil have adgang til en r&aelig;kke statistikker i form af javascript-variable. Du skal placerer koden <em>f&oslash;r</em> det javascript der skal bruge dem, hvis det skal virke.<br>Er dette sort snak for dig, vil jeg anbefale du ser p&aring; ministatistikken i stedet.</p></span>";
        echo "<span class=forside>\n<pre>";

        echo "&lt;-- Start på JS stats kode --&gt;\n";
        echo "&lt;script language=&quot;JavaScript&quot; src=&quot;"
             .$siteContext->getOption('cgiURL').'/'
             .$siteContext->getPath('jsvarsCgi')."?brugernavn="
             .$in['username']."&quot;&gt;\n";
        echo "&lt;/script&gt;\n";
        echo "&lt;-- Slut på JS stats kode --&gt;\n";

        echo "</pre>\n";
        echo "</span>\n";

        $utils->echoSiteEnd();
}

/**
 * Returns the mandatory code for of the type given oin $p0.
 *
 * @public
 * @param $in the HTTP parameters
 * @param $hw html code for the height and width of the counter image
 *            (if an image is used). The code could be:
 *            <code>height=\"10\" width=\"15\"</code>
 *            and yes: The quote must have a backslash in front of it, so
 *            it is written in a stirng as
 *            <code>height=\\\"10\\\" width=\\\"15\\\"</code>.
 * @param $codeType the type of code to return. Look in the
 *                  implementation of this method for valid
 *                  values (not good).
 * @param $siteContext the instance of the site context.
 * @param $count        html url code for the name and number of the
 *                     counter to increment.
 * @param $show         html url code which states which stats shall be
 *                     shown on the stat site.
 */
function oblkode(&$siteContext, $in, $hw, $codeType, $count, $show) {

        $hwnosc = str_replace("\\\"", "&quot;", $hw);

        if (!isset($in['framesikker']))
                $topDocReferer = "top.document.referrer";
        else
                $topDocReferer = "document.referrer";

        $mandatory[0] = "&lt;!-- Start på ZIP Stat kode --&gt;\n";
        $mandatory[0] .= "&lt;script language=&quot;JavaScript&quot; type=&quot;text/javascript&quot;&gt;\n";
        $mandatory[0] .= "&lt;!--\n";
        $mandatory[0] .= "far=&quot;Andre&quot;; jav=&quot;Ved ikke&quot;; skh=&quot;Andre&quot;; jav=navigator.javaEnabled(); \n";
        $mandatory[0] .= "ref=&quot;&quot;+escape(".$topDocReferer."); nav=navigator.appName;\n";
        $mandatory[0] .= "if (navigator.appName == &quot;Netscape&quot; &amp;&amp; (navigator.appVersion.charAt(0) == &quot;2&quot; || navigator.appVersion.charAt(0) == &quot;3&quot;)) {skriv=false;} else {skriv=true;}\n";
        $mandatory[0] .= "if (skriv==true) { skh=screen.width + &quot;x&quot; + screen.height;\n";
        $mandatory[0] .= "if (nav != &quot;Netscape&quot;){far=screen.colorDepth;} else {far=screen.pixelDepth;}\n";
        $mandatory[0] .= "puri=&quot;brugernavn=".$in['username']."&amp;amp;version=150&amp;amp;ssto=&quot;+skh+&quot;&amp;amp;referer=&quot;+ref+&quot;&amp;amp;colors=&quot;+far+&quot;&amp;amp;java=&quot;+jav+&quot;&amp;amp;js=true".$count."&quot;;\n";

        $mandatory[1] = "document.write(&quot;&lt;a href=\\&quot;".$siteContext->getOption('urlStatsite')."?brugernavn=".$in['username'].$show."\\&quot; target=\\&quot;_top\\&quot;&gt;&quot;);\n";

        $mandatory[2] = "document.write(&quot;&lt;img $hw border=\\&quot;0\\&quot; src=\\&quot;".$siteContext->getOption('cgiURL').'/'.$siteContext->getPath('zipstatCgi')."?&quot;+puri+&quot;\\&quot; alt=\\&quot;\\&quot; align=left&gt;";
        $mandatory[3] = "&lt;\\/a&gt;";
        $mandatory[4] = "&quot;); }\n";

        $mandatory[5] = "//--&gt;\n&lt;/script&gt; &lt;noscript&gt;\n";

        $mandatory[6] = "&lt;a href=&quot;".$siteContext->getOption('urlStatsite')."?brugernavn=".$in['username'].$show."&quot; target=&quot;_top&quot;&gt;\n";

        $mandatory[7] = "&lt;img $hwnosc border=&quot;0&quot; src=&quot;".$siteContext->getOption('cgiURL').'/'.$siteContext->getPath('zipstatCgi')."?brugernavn=".$in['username']."&amp;amp;js=false".$count."&quot; alt=&quot;&quot; align=left&gt;";
        $mandatory[8] = "&lt;/a&gt;\n";
        $mandatory[9] = "&lt;/noscript&gt;\n&lt;!-- Slut p ZIP Stat kode --&gt;";

        if (isset($in['firewallsikker']) and strlen($in['firewallsikker']) > 0 and $codeType === 'fuld') {
                $return = "<div class=forside>\n";
                $return .= "<pre>\n";
                $return .= "&lt;!-- Start på ZIP Stat firewall-sikker kode --&gt;\n";
                $return .= "&lt;a href=&quot;".$siteContext->getOption('urlStatsite')."?brugernavn=".$in['username'].$show."&quot; target=&quot;_top&quot;&gt;\n";
                $return .= "&lt;img $hw border=0 src=&quot;".$siteContext->getOption('cgiURL').'/'.$siteContext->getPath('zipstatCgi')."?brugernavn=".$in['username'].$count."&quot;&gt;&lt;/a&gt;\n";
                $return .= "&lt;!-- Slut på ZIP Stat firewall-sikker kode --&gt;\n";
                $return .= "</pre>\n";
                $return .= "</div>\n";
                return $return;
        } else if (isset($in['firewallsikker']) and strlen($in['firewallsikker']) > 0 and $codeType === 'uden links') {
                $return .= "&lt;!-- Start på ZIP Stat firewall-sikker kode --&gt;\n";
                $return .= "&lt;img $hw border=0 src=&quot;".$siteContext->getOption('cgiURL').'/'.$siteContext->getPath('zipstatCgi')."?brugernavn=".$in['username'].$count."&quot;&gt;\n";
                $return .= "&lt;!-- Slut på ZIP Stat firewall-sikker kode --&gt;\n";
                return $return;
        } else if ($codeType === 'fuld') {
                $return = '';
                for ($i = 0; $i < count($mandatory); $i++)
                         $return .= $mandatory[$i];
                return $return;
        } else if ($codeType === 'uden links') {
                $return = '';
                $return .= $mandatory[0];
                $return .= $mandatory[2];
                $return .= $mandatory[4];
                $return .= $mandatory[5];
                $return .= $mandatory[7];
                $return .= $mandatory[9];
                return $return;
        } else if ($codeType === 'url') {
                //Do it twice.
                $show = str_replace("&amp;", "&", $show);
                $show = str_replace("&amp;", "&", $show);
                return $siteContext->getOption('urlStatsite')."?brugernavn=".$in['username'].$show;
        }

} //End of - sub oblkode().

/**
 * Parses the HTTP parameters ($in) and produces html which can be used
 * for the stat site.
 *
 * @param $in the HTTP parameters.
 * @returns String
 * @return html for the url for the stat site.
 * @public
 */
function vis($in) {
        $show = '';
        if (isset($in['alle'])) {
                $show = "&alle=ja";
        } else {
                if (isset($in['prognoser']))           {$show .= "&prognoser=ja";}
                if (isset($in['enkeltstat']))  {$show .= "&enkeltstat=ja";}
                if (isset($in['smask']))                       {$show .= "&smask=ja";}
                if (isset($in['sord']))                        {$show .= "&sord=ja";}
                if (isset($in['ref']))                                 {$show .= "&ref=ja";}
                if (isset($in['spoergs']))             {$show .= "&spoergs=ja";}
                if (isset($in['js']))                          {$show .= "&js=ja";}
                if (isset($in['taellere']))            {$show .= "&taellere=ja";}
                if (isset($in['info20']))                      {$show .= "&info20=ja";}
                if (isset($in['java_support']))        {$show .= "&java_support=ja";}
                if (isset($in['hits_farver']))         {$show .= "&hits_farver=ja";}
                if (isset($in['hits_opl']))            {$show .= "&hits_opl=ja";}
                if (isset($in['hits_sprog']))  {$show .= "&hits_sprog=ja";}
                if (isset($in['hits_os']))             {$show .= "&hits_os=ja";}
                if (isset($in['hits_browser']))        {$show .= "&hits_browser=ja";}
                if (isset($in['hits_domain']))         {$show .= "&hits_domain=ja";}
                if (isset($in['top_domain']))  {$show .= "&top_domain=ja";}
                if (isset($in['domaene_hits']))        {$show .= "&domaene_hits=ja";}
                if (isset($in['ugedag_hits']))         {$show .= "&ugedag_hits=ja";}
                if (isset($in['timer_hits']))  {$show .= "&timer_hits=ja";}
                if (isset($in['sidste_31_dage'])){$show .= "&sidste_31_dage=ja";}
                if (isset($in['maaned_i_aar']))        {$show .= "&maaned_i_aar=ja";}
                if (isset($in['zipklik']))                     {$show .= "&zipklik=ja";}
                if (isset($in['bev']))                 {$show .= "&bev=ja";}
                if (isset($in['indgang']))                     {$show .= "&indgang=ja";}
                if (isset($in['udgang']))                      {$show .= "&udgang=ja";}
        }
        return $show;
} //End of - sub vis.

/**
 * Looks in the HTTP parameters ($in) and
 * sets a cookie which turns on (1) the elements in $elements.
 * If an element is turned on in a cookie, but not given in $elements
 * the cookie value will be set to off (0). $gemICookieNavn is treated
 * as an element.
 *
 * @public
 * @param $in            the http parameters
 * @param $siteContext    the instance of the site context
 * @param $gemICookieNavn the name of the cookie to save info in.
 * @param $elements      array of the elements to save
 */
function gemICookie($in, $siteContext, $gemICookieNavn, $elements) {
        if (isset($in[$gemICookieNavn])) {
                for ($i = 0; $i < count($elements); $i++) {
                        if (isset($in[$elements[$i]])) {
                                setcookie($elements[$i], 1, time()+60*60*24*365, '/', '.'.$siteContext->getOption('domain'));
                        } else if (isset($_COOKIE[$elements[$i]])) {
                                setcookie($elements[$i], 0, time()+60*60*24*365, '/', '.'.$siteContext->getOption('domain'));
                        }
                } //End for.
        } //End of - if ($in{$gemICookieNavn}).

        if (isset($in[$gemICookieNavn])) {
                setcookie($gemICookieNavn, 1, time()+60*60*24*365, '/', '.'.$siteContext->getOption('domain'));
        } else if (isset($_COOKIE[$gemICookieNavn])) {
                setcookie($gemICookieNavn, 0, time()+60*60*24*365, '/', '.'.$siteContext->getOption('domain'));
        }

} //Slut of - sub gemICookie.

//name of the gemICookie parameter, name of the form-item that shall be evaluated.
//The variable $in must exsist, be global and contain the submitted form-fields.
/**
 * Returns the &quot; CHECKED&quot; code or an empty string, for a html
 * checkbox, depending on if the checkbox shall be checked acording to
 * the HTTP parameter and cookie info.
 *
 * @param $in            the HTTP parameters
 * @param $gemICookieNavn
 * @param $navn           the name/key of the checkbox to check.
 * @public
 */
function checkedCookie($in, $gemICookieNavn, $navn) {
        if (( isset($in[$navn]) and isset($in[$gemICookieNavn])) or isset($_COOKIE[$navn]))
                return ' CHECKED';
        else
                return '';
}


?>
