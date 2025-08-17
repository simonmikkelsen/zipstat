<?php

$sec =  date('s'); /*Sekund*/
$min =  date('i'); /*Minut*/
$hour = date('H'); /*time*/
$mday = date('j'); /*dag i mned*/
$mon =  date('n'); /*mned*/
$year = date('Y'); /*r*/
$wday = date('w'); /*ugedage*/
$yday = date('z'); /*dag i r*/

//Der er forelbig ikke opstet problemer
$problemer = "";

//New-line tegn
$nl = "\n";

//Program
require "Stier.php";
$stier = new Stier();

require "Html.php";
$ind = Html::setPostOrGetVars($_POST, $_GET);

//Tjekker brugernavnet
$datafil = DataSource::createInstance($ind['brugernavn'],$stier);

if (! $datafil->hentFil())
	$problemer .= "Datafilen kunne hentes. Enten er det et problem p ".$stier->getOption('name_of_service')." eller ogs har du skrevet det forkerte brugernavn - det kan indeholder tegn der ikke er tilladt - prv at generere denne kode igen.";

$lib = new Html($ind,$datafil);
$lib->setStier($stier);

//Der er noget galt
if (strlen($problemer) > 0)
{
	echo $lib->problemer($problemer,1);
	exit;
}

//////////////////

//Er det ministatistik eller alle jsvars?
if ($ind['type'] == "ministatistik")
	$minimal = 1;
else
	$minimal = 0;

//Sender headers
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-type: application/javascript");

//M javascriptvars vises
$tillad = explode("::",$datafil->getLine(106));
if (! $tillad[1])
{
	echo "document.write(\"Denne statistiks ejer (sidens ejer) har valgt ikke at tillade at disse statistikker m vises. Dette kan tillades af vedkommende p siden indstillinger p brugeromrdet p ".$stier->getOption('ZSHomePage').", i avanceret visning.\");$nl";
	exit;
}

//Udskriver statistikkerne

$line7 = trim($datafil->getLine(7));
$line82 = trim($datafil->getLine(82));
if (is_numeric($line7) and is_numeric($line82)) {
  $hitsSiden1 = $datafil->getLine(7)+$datafil->getLine(82);
} else {
  $hitsSiden1 = 0;
}

echo "var hits_siden_1 = '$hitsSiden1';$nl";
echo "var hits_siden_1_dato = '".$datafil->getLine(8)."';$nl";

echo "var hits_siden_2 = '".$datafil->getLine(13)."';$nl";
echo "var hits_siden_2_dato = '".$datafil->getLine(5)."';$nl";


echo "var unikke_hits = '".$datafil->getLine(44)."';$nl";
echo "var unikke_hits_idag = '".$datafil->getLine(76)."';$nl";


$hpb = $lib->hpb(); /*Antal hits pr. bruger puttes i $hpb*/
$hpb = $lib->afrund($hpb);
echo "var hits_pr_bruger = '$hpb';$nl";

echo "var max_hits_dag = '".$datafil->getLine(16)."';$nl";
echo "var max_hits_dag_dato = '".$datafil->getLine(17)."';$nl";

echo "var max_hits_maaned = '".$datafil->getLine(18)."';$nl";
echo "var max_hits_maaned_dato = '".$datafil->getLine(19)."';$nl";

		//Hits pr. mned (3, 6, 12)
		$tmp = explode("::",$datafil->getLine(9));
		$i = $mon -1;
		$stk = 0;
		$mhits = 0;
    $htm = 0; // I don't know what this is, but it is used in the original code.
    $hm12 = 0; // Hits per month for 12 months.
    $hm6 = 0; // Hits per month for 6 months.
    $hm3 = 0; // Hits per month for 3 months.
    $hm3b = 0; // Hits per month for 3 months, but adjusted for the current month.
    // Note to my 25 years younger self: Don't code like this!
		for ($n = 1;$n <= 12;$n++)
		{
      if (isset($tmp[$i])) {
        $mhits += $tmp[$i];
        if ($tmp[$i] > 0) $stk++;
      }

			if ($i == 0)
				$i = 12;
			else
				$i--;

			if ($n == 2) 
				{
				if ($stk >= 3) $hm3b = round(($mhits + $htm) / 3);
				else $hm3b = round(($mhits + ($htm * (3 - $stk))) / 3);
				}
			if ($stk == 3) $hm3 = $lib->afrund($mhits / 3);
			if ($stk == 6) $hm6 = $lib->afrund($mhits / 6);
			if ($stk ==12) $hm12 = $lib->afrund($mhits / 12);
		}

echo $nl;

if ($hm3b > 0)
	echo "var hits_pr_maaned_03mdr = '$hm3b';$nl";
else
	echo "var hits_pr_maaned_03mdr = 0;$nl";

if ($hm3 > 0)
	echo "var hits_pr_maaned_3mdr = '$hm3';$nl";
else
	echo "var hits_pr_maaned_3mdr = 0;$nl";

if ($hm6 > 0)
	echo "var hits_pr_maaned_6mdr = '$hm6';$nl";
else
	echo "var hits_pr_maaned_6mdr = 0;$nl";

if ($hm12 > 0)
	echo "hits_pr_maaned_12mdr = '$hm12';$nl";
else
	echo "hits_pr_maaned_12mdr = 0;$nl";
echo $nl;

	//Beregner hits pr. dag
	$n = 0;

	$hd = 0;
	$tmp = explode("::",$datafil->getLine(11));
	for ($i = 0; $i < 31;$i++)
	{
		if ($tmp[$i] > 0 and $i != $mday-1)
		{
			$hd += $tmp[$i];
			$n++;
		}
	}
	if ($n > 0)
		$hd = $hd / $n;
	else
		$hd = 0;
  $hdOrig = $hd;
	$hd = $lib->afrund($hd);

echo "var hits_pr_dag = '$hd';$nl";
$ht = $lib->afrund($hdOrig / 24);
echo "var hits_pr_time = '$ht';$nl";

	$tmp = explode(":",$datafil->getLine(73));
	if ($tmp[1] != 0)
	{
		$tpb = $lib->afrund($tmp[0]/$tmp[1]);
	}
	//tid pr. bruger på siden
echo "var tid_paa_hver_side = '$tpb';$nl";

function numberOrZero($str) {
  if (is_numeric(trim($str))) {
    return trim($str);
  } else {
    return 0;
  }
}

//Personer på siden lige nu: $hps
	$hps = 0;
	$iper = ":";
	$ut = explode(":",$datafil->getLine(72));
	$ip = explode(":",$datafil->getLine(45));

	for ($i = 0;$i < sizeof($ut);$i++)
	{
		if ((numberOrZero($ut[$i]) + numberOrZero($tpb) > time()) and (! preg_match("/:$ip[$i]:/",$iper)))
		{
			$hps++;
			$iper .= $ip[$i].":";
		}
	}
if ($hps == 0)
	$hps = 1;

echo "var personer_paa_siden_nu = '$hps';$nl";

/*Beregner hvor mange der vil komme resten af dagen.
		Tller antal brugere sammen for de seneste 31 dage (ex. denne, den hjeste og laveste)
		Antal hits $h31, antal talte dage $ant31
*/
		$tmp = explode("::",$datafil->getLine(11));
		$ant31 = 0;
		$maxi = 0;
		$mini = 0;
    $h31 = 0; // Hits for the last 31 days.
		for ($i = 0;$i < 31;$i++)
		{
			if (($i != $mday -1) and ($tmp[$i] != 0))
			{
				$h31 += $tmp[$i];
				$ant31++;
				if ($tmp[$i] > $maxi) $maxi = $tmp[$i];
				if ($tmp[$i] < $mini) $mini = $tmp[$i];
			}
			elseif ($i != $mday -1)
				$hdag = $tmp[$i];
		} /*Slut p for ...*/
		
		if ($ant31 > 1)
		{
			if ($maxi)
			{
				$h31 -= $maxi;
				$ant31--;
			}

			if ($mini)
			{
				$h31 -= $maxi;
				$ant31--;
			}
		}

		//Beregner hvor mange % p en uge der kommer p denne dag $pu - $antuge antal talte
		
		$tmp = explode("::",$datafil->getLine(15));
		$antuge = 0;
    $thiu = 0;
		for ($i = 0;$i < 7;$i++)
		{
			if ($tmp[$i] != 0)
			{
				$thiu += $tmp[$i];
				$antuge++;
			}
		}
		
		if ($thiu != 0)
			$pu = 1 - ( ($thiu - $tmp[$wday]) /$thiu);
		else
			$pu = 0;
		
		 //Beregner en gennemsnits dag med hensyntagen til ugedag og time $rge
		if ($ant31 != 0)
			$rge = ( $h31 /$ant31) * $antuge * $pu;
		else 
			$rge = 0;

		
		//tller antal timer sammen $tialt ialt, $tinu indtil nu
		
		$tialt = 0;
		$tinu = 0;
		$tmp = explode("::",$datafil->getLine(14));
		for ($i = 0;$i < 24;$i++)
		{
			if ($i < $hour)	$tinu += $tmp[$i];

			$tialt += $tmp[$i];
		}
		
		$tinu += $tmp[$hour]*(1-((60-$min)/60));


		//Beregner % timer tilbage idag $ptt og timer indtil nu $ptn
		$ptt = 0;
		if ($tialt != 0)
		{
			$ptt = ( ($tialt - $tinu) /$tialt);
			$ptn = 1 - $ptt;
		}
		else 
		{
			$ptt = 0;
			$ptn = 0;
		}

		//Beregner dagens udsving $pud
		$tmp = explode("::",$datafil->getLine(11));
		if (($rge * $ptn) != 0)
			$pud = 1 - (  (($rge * $ptn) - $tmp[$mday-1]) / ($rge * $ptn)  );
		else
			$pud = 0;

		if ($ptn < 0)
			$ptn *= -1;

    $ti = 0; // Not initialized in the original code, but used later.
		if ($ti < 0)
			$ti *= -1;

    $hits_ialt_timer = 0; // Not initialized in the original code, but used later.
		if ($hits_ialt_timer < 0)
			$hits_ialt_timer *= -1;

    $hits_ialt_maaned = 0; // Not initialized in the original code, but used later.
		if ($hits_ialt_maaned < 0)
			$hits_ialt_maaned *= -1;

		if ($htm < 0)
			$htm *= -1;

		$ptn = $lib->afrund($ptn * 100);
		$hits_ialt_timer = $lib->afrund($tmp[$mday-1] + $ti);
		$ti  = $lib->afrund($ti);

echo $nl;
echo "var procent_foer_nu = '$ptn';$nl"; 
echo "var hits_mere_idag = '$ti';$nl";
echo "var hits_ialt_idag = '$hits_ialt_timer';$nl";
echo "var htis_mere_denne_maaned = '$hits_ialt_maaned';$nl";
echo "var hits_ialt_denne_maaned = '$htm';$nl";
echo $nl;

/*
Laver kode til placering
86-Sti til indexkategori::Sti til indexkategori
87-Placering i kategori/antal i alt::Placering i kategori/antal i alt
kategorifil - eks.:
	Private hjemmesider : ;;Private_hjemmesider/AA
*/

$kats = explode("::",$datafil->getLine(86));
$plac = explode("::",$datafil->getLine(87));

echo "var antal_kategorier = '".sizeof($kats)."';$nl";
echo "var kategori_navne=new Array;$nl";
echo "var kategori_placeringer=new Array;$nl";
echo "var antal_i_kategori=new Array;$nl";

//Temporarely disabeling, untill this never fails
#if (file_exists($stier->getSti('index_kategorier')))
#{
#	$kat = file($stier->getSti('index_kategorier'));
#
#	for ($i = 0;$i < sizeof($kats);$i++)
#	{
#		$temp = explode("/\//",$kats[$i]);
#		$plac = $temp[0];
#		$af = $temp[1];
#
#		for ($n = 0;$n < sizeof($kat);$n++)
#		{
#			$temp = explode(";;",$kat[$n]);
#			$titel = $temp[0];
#			$sti = $temp[1];
#
#			if ($kats[$i] == $sti)
#			{
#				$titel = preg_replace("/\t/g","",$titel);
#				echo "kategori_navne[$i] = '$titel';$nl";
#				echo "kategori_placeringer[$i] = '$plac';$nl";
#				echo "antal_i_kategori[$i] = '$af';$nl";
#			}
#		}
#	}
#	echo $nl;
#}

//Tllere
$tHits = explode("::",$datafil->getLine(37)); /*Tllerhist*/
$tNavne = explode("::",$datafil->getLine(38)); /*Tllernavne*/
$proMaxAntTaellere = $lib->pro(5);
$filnavne = array("");
if (isset($_SERVER['HTTP_REFERER'])) {
  $filnavne = explode("/",$_SERVER['HTTP_REFERER']);
}
$filnavne = array_reverse($filnavne);
$filnavn = $filnavne[0];

$taelNr = 0;
for ($i = 0; $i <= $proMaxAntTaellere;$i++) {
	if (isset($tNavne[$i]) and ($filnavn == $tNavne[$i]) and ($filnavn != "") and isset($tHits[$i]) and ($tHits[$i] > 0)) {
		$taelNr = $i;
  }
}

echo "var denne_taellers_nr = '$taelNr';$nl";

$taelUd = "var taellere=new Array(";

for ($i = 0;$i <= $proMaxAntTaellere;$i++) {
  if (! isset($tHits[$i])) {
    $tHits[$i] = 'n/a';
  }
	$taelUd .= "'$tHits[$i]',";
}

$taelUd = substr($taelUd,0,strlen($taelUd)-1);
$taelUd .= ");$nl";
echo $taelUd;
echo $nl;

$hits31d_array = explode("::",$datafil->getLine(11));

$hits31d = "var hits_31_dage=new Array(";
for ($i = 0;$i < 31;$i++)
{
	if (! $hits31d_array[$i]) $hits31d_array[$i] = 0;
	$hits31d .= "'$hits31d_array[$i]',";
}
$hits31d = substr($hits31d,0,strlen($hits31d)-1);
$hits31d .= ");$nl";
echo $hits31d;
echo $nl;


//Laver kode til sprgsml
if (! $minimal)
{

	$sp = explode("::",$datafil->getLine(41));
	$sv = explode(",,",$datafil->getLine(42));
	$hi = explode(",,",$datafil->getLine(43));

	$pro_max_sp = $lib->pro(3);
	$pro_max_sv = $lib->pro(4);

	echo "var antal_sp = $pro_max_sp;$nl";
	echo "var antal_sv_pr_sp = $pro_max_sp;$nl";
	echo "var sp=new Array;$nl";
	echo "var sv=new Array;$nl";
	echo "var spsv=new Array;$nl";
	echo "var sv_hits=new Array;$nl";

	$cookie = $_COOKIE;
	$svnr = 0;
	for ($isse = 0;$isse < $pro_max_sp; $isse++)
	{
		$hits = explode("::",$hi[$isse]);
		$svar = explode("::",$sv[$isse]);
		$sp[$isse] = addslashes($sp[$isse]);

		echo "sp[$isse] = '$sp[$isse]';$nl";
		if ($cookie[$ind['brugernavn']."sp".$isse] == strlen($sp[$isse]))
			echo "spsv[$isse] = 1;$nl";
		else
			echo "spsv[$isse] = 0;$nl";

		for ($n = 0;$n < $pro_max_sv; $n++)
		{
      if (! isset($svar[$n])) {
        $svar[$n] = "";
      }
			$svar[$n] = addslashes($svar[$n]);
			echo "sv[$svnr] = '$svar[$n]';$nl";
			echo "sv_hits[$svnr] = '$hits[$n]';$nl";
			$svnr++;
		}
	} /*end p for $i = 0;$i < 5...*/
} /*Slut p if not mini*/

?>
