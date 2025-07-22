<?php

header ("Content-type: text/css\n\n");
require "Stier.php";
$options = new Stier();

$userAgent = $_SERVER['HTTP_USER_AGENT'];
if (! ( !(preg_match("#compatible#i",$userAgent)) and (preg_match("#Mozilla/4#i",$userAgent)) )  )
	{
	//Non Netscape 4 CSS.
?>
BODY {
	background: #EEEEEE;
	font-family: sans-serif;
	color: Black;
}

body.meget {
	font-family: serif;
}

H1, H2, H3, H4, H5, H6 {
	background-color: #CCCCCC;
	background-image: url(<?php echo $options->getOption('ZSHomePage'); ?>gitter_moenster.gif);
	border: 3px #666666 double;
	border-right: none;
	border-left: none;
/*Ignore the two lines above on the stat sige.*/
	color: Black;
	font-family: "Courier New", Courier, monospace;
	margin-top: 2em;
	margin-bottom: 3px;
	}

H1 {
	margin-bottom: 1em;
}

A:link {
	color: Blue;
	text-decoration: underline;
}

A:visited {
	color: Purple;
	text-decoration: underline;
}

A:hover {
	color: Navy;
	text-decoration: underline;
}

A:active {
	text-decoration: none;
	color: Red;
}

TD {	/*Hide in Netscape 4!*/
	vertical-align: top;
	padding-right: 4px;
	padding-left: 4px;
	padding-bottom: 4px;
}

.forside {
	background-color: #DDDDEE;
	background-image: url(<?php echo $options->getOption('ZSHomePage'); ?>bg.gif);
	margin-bottom: 1em;
}

.menu {
	background-color: #CCCCFF;
	background-image: url(<?php echo $options->getOption('ZSHomePage'); ?>gitter_moenster_menu.gif);
	border: thin black dotted;
	border-right: none;
	border-left: none;
	border-bottom: 2px red solid;
	color: Black;
	padding: 3px;
	font-family: "Courier New", Courier, monospace;
	font-variant: small-caps;
	text-align: left;
	font-size: medium;
}

.menulogo {
	color: Black;
	font-family: sans-serif;
	text-align: right;
	margin: 4px;
	white-space: nowrap;
}

.menulogo:first-letter {
	color: Red;
}

span.sp:first-letter {
	color: Blue;
	font-size: larger;
	margin-right: 0.4em;
}

span.sp {
	color: Black;
}

table {
	empty-cells: show;
}

.copy { /*The copyright-field in the bottom of the pages*/
	text-align: center;
}

.brugernavn {

}

.kodeord {

}

.knap {

}

.problemer {
	background-color: yellow;
}

.dato {
	font-variant: small-caps;
}

dt {
	font-weight: bold;
}

.broedkrumme {
	background-color: #FFFFFF;
	border-bottom: 1px silver dotted;
	color: #333333;
	font-family: "Courier New", Courier, monospace;
	margin-top: 2px;
	margin-bottom: 3px;
	font-size: 90%;
}

/*********************************************************/

	/*Basic stats*/
table {
	border-collapse: collapse;
}

.enkelttabel {
	border: thin black solid;
}

.enkeltA {
	background-color: #6666FF;
	border-bottom: thin red solid;
	color: white;
	font-size: 125%;
	font-weight: normal;
	text-align: left;
}

.enkeltB {
	border-top: thin #666666 solid;
	font-weight: normal;
}

/*Projections*/
.boldstatbeskrivelse {
	font-weight: bold;
}

/*Graphs*/
.stattabel {
	border: thin black solid;
}

.markeret {
	font-weight: bold;
}

th.thA {
	background-color: #6666FF;
	color: white;
	font-size: 125%;
	border-bottom: thin red solid;
	padding-right: 3px;
	padding-left: 3px;
	padding-bottom: 0px;
	font-weight: normal;
	text-align: left;
	white-space: nowrap;
}

th.thB {
	background-color: #6666FF;
	color:white;
	font-size: 125%;
	border-bottom: thin red solid;
	padding-right: 3px;
	padding-left: 7px;
	padding-bottom: 0px;
	font-weight: normal;
	text-align: left;
}

td.tdA {
	white-space : nowrap;
	font-size: smaller;
	border-top: thin #999999 solid;
	padding-bottom: 0px;
}

td.tdB {
	font-size: smaller;
	border-top: thin #999999 solid;
	padding-bottom: 0px;
}

.GrafVenstre { /*The visible (colourd) part of the graph*/
	background-color: #99CCFF;
	padding: 0px;
}

td.GrafHoejre { /*The invisible part of the graph*/
	background-color: White;
	font-size: smaller;
	padding: 0px;
}


th.sinfo { /*Headline for info about the latest visitors*/
	background-color: #6666FF;
	color: white;
	padding-right: 3px;
	padding-left: 3px;
	padding-bottom: 0px;
	font-weight: normal;
	font-size: 125%;
	text-align: left;
}

td.sinfo { /*Info about the latest visitors*/
	font-size: smaller;
	padding-bottom: 0px;
	border-top: thin #999999 solid;
	border-left: thin #CCCCCC solid;
	border-right: thin gray solid;
	white-space: nowrap;
}

th.sbinfo { /*2nd headline for info about the latest visitors*/
	background-color: #33FF99;
	padding-right: 3px;
	padding-left: 3px;
	padding-bottom: 0px;
}

td.sbinfo { /*2nd line info in info about the latest visitors*/
	font-size: smaller;
	padding-bottom: 0px;
}

/*For the calendar on total stats.*/
table.calendar {
	margin-bottom: 2em;
	border: thin solid black;
}

.calendar th {
	border: thin #666666 solid;
	background-color: #6666FF;
	color: white;
}
.dayNames {
	border-bottom: thin #ccc solid;
}

.other {
	color: silver;
}


<?php
	}
else /*Prints Netscape 4 css*/
	{
?>
BODY {
	background: #EEEEEE;
	font-family: sans-serif;
	color: Black;
}

body.meget {
	font-family: serif;
}

H1, H2, H3, H4, H5, H6 {
	background-color: #CCCCCC;
	border: 3px #666666 double;
	/*Ignore the two lines above on the stat page*/
	color: Black;
	font-family: "Courier New", Courier, monospace;
	margin-top: 2em;
	margin-bottom: 3px;
}

H1 {
	margin-bottom: 1em;
}

A:link {
	color: Blue;
	text-decoration: underline;
}

A:visited {
	color: Purple;
	text-decoration: underline;
}

TD {

}

.forside {
	background-color: #DDDDEE;
	margin-bottom: 1em;
}

.menu {
	background-color: #CCCCFF;
	border: thin black dotted;
	color: Black;
	padding: 3px;
	font-family: "Courier New", Courier, monospace;
	text-align: left;
	font-size: medium;
}

.menulogo {
	color: Black;
	font-family: sans-serif;
	text-align: right;
	margin: 4px;
	white-space: nowrap;
}

.menulogo:first-letter {
	color: Red;
}


span.sp:first-letter {
	color: Blue;
	font-size: larger;
	margin-right: 0.4em;
}

span.sp {
	color: Black;
}

table {

}

.copy {
	/*The copyright-field in the bottom of the pages*/
	text-align: center;
}

.brugernavn {
}

.kodeord {

}

.knap {

}

.problemer {
	background-color: yellow;
}

.dato {
	font: normal normal small/normal sans-serif;
}

dt {
	font-weight: bold;
}

.broedkrumme {
	background-color: #FFFFFF;
	border-bottom-width: thin;
	color: #333333;
	font-family: "Courier New", Courier, monospace;
	margin-top: 2px;
	margin-bottom: 3px;
	font-size: 90%;
}

/*********************************************************/
	/*Basic stats*/
.enkelttabel {
	border: thin black solid;
}

.enkeltA {
	background-color: #6666FF;
	border-bottom-width: thin;
	color: white;
	font-size: 125%;
	font-weight: normal;
	text-align: left;
}

.enkeltB {
	border-bottom-width: thin;
	font-weight: normal;
}

/*Projections*/
.boldstatbeskrivelse {
	font-weight: bold;
}

/*Graphs*/
.stattabel {
	border: thin black solid;
}

.markeret {
	font-weight: bold;
}

th.thA {
	background-color: #6666FF;
	color: white;
	font-size: 125%;
	border-bottom-width: thin;
	padding-right: 3px;
	padding-left: 3px;
	padding-bottom: 0px;
	font-weight: normal;
	text-align: left;
}

th.thB {
	background-color: #6666FF;
	color:white;
	font-size: 125%;
	border-bottom-width: thin;
	padding-right: 3px;
	padding-left: 7px;
	padding-bottom: 0px;
	font-weight: normal;
	text-align: left;
}

td.tdA {
	white-space : nowrap;
	font-size: smaller;
	border-top-width: thin;
	padding-bottom: 0px;
}

td.tdB {
	font-size: smaller;
	border-top-width: thin;
	padding-bottom: 0px;
}

.GrafVenstre {
	background-color: #99CCFF;
	padding: 0px;
}

td.GrafHoejre {
	/*The invisible part of the graph*/
	background-color: White;
	font-size: smaller;
	padding: 0px;
}

th.sinfo {
	/*Headline for info about the latest visitors*/
	background-color: #6666FF;
	color: white;
	padding-right: 3px;
	padding-left: 3px;
	padding-bottom: 0px;
	font-weight: normal;
	font-size: 125%;
	text-align: left;
}

td.sinfo {
	/*Info about the latest visitors*/
	font-size: smaller;
	padding-bottom: 0px;
	border-top-width: thin;
}

th.sbinfo {
	/*2nd headline for info about the latest visitors*/
	background-color: #33FF99;
	padding-right: 3px;
	padding-left: 3px;
	padding-bottom: 0px;
}

td.sbinfo {
	/*2nd line info in info about the latest visitors*/
	font-size: smaller;
	padding-bottom: 0px;
}

<?php
	}

?>