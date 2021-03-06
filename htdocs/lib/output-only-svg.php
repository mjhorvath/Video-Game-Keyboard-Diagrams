<?php
	// Video Game Keyboard Database
	// Copyright (C) 2018  Michael Horvath
        // 
	// This file is part of Video Game Keyboard Database.
        // 
	// This program is free software: you can redistribute it and/or modify
	// it under the terms of the GNU Lesser General Public License as 
	// published by the Free Software Foundation, either version 3 of the 
	// License, or (at your option) any later version.
        // 
	// This program is distributed in the hope that it will be useful, but 
	// WITHOUT ANY WARRANTY; without even the implied warranty of 
	// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU 
	// Lesser General Public License for more details.
        // 
	// You should have received a copy of the GNU Lesser General Public 
	// License along with this program.  If not, see 
	// <https://www.gnu.org/licenses/>.

	header("Content-type: image/svg+xml");
	include($path_lib2 . "queries-chart.php");

	$path_vgkd		= "https://isometricland.net/keyboard/";
	$path_file		= "output-only-svg.php";		// this file
	$stylegroup_id		= 0;		// set by selThisStyleChart(), also contained inside $stylegroup_table
//	$stylegroup_table	= [];		// set in selStyleGroupsChart() and selStylesChart(), utilized by "footer-chart.php"
//	$style_table		= [];		// set in selStyleGroupsChart() and selStylesChart(), utilized by "footer-chart.php"
	$position_table		= [];		// populated by selPositionsChart()
	$keystyle_table		= [];		// populated by selKeyStylesChart()
	$binding_table		= [];		// populated by selBindingsChart()
	$legend_table		= [];		// populated by selLegendsChart()
	$author_table		= [];		// populated by selAuthorsChart()
	$gamesrecord_id		= 0;		// set by selThisGamesRecordChart()
	$gamesrecord_authors	= [];		// populated by selContribsGamesChart(), utilized by "footer-chart.php"
	$stylesrecord_id	= 0;		// set by selThisStylesRecordChart()
	$stylesrecord_authors	= [];		// populated by selContribsStylesChart(), utilized by "footer-chart.php"
	$layout_authors		= [];		// populated by selContribsLayoutsChart(), utilized by "footer-chart.php"
	$layout_keysnum		= 0;		// reset by selThisLayoutChart()
	$layout_keygap		= 4;		// reset by selThisLayoutChart()
	$layout_padding		= 18;		// reset by selThisLayoutChart()
	$layout_fullsize_width		= 1200;		// reset by selThisLayoutChart()
	$layout_fullsize_height		= 400;		// reset by selThisLayoutChart()
	$layout_tenkeyless_width	= 1200;		// reset by selThisLayoutChart()
	$layout_tenkeyless_height	= 400;		// reset by selThisLayoutChart()
	$layout_legend_padding		= 36;		// reset by selThisLayoutChart()
	$layout_legend_height		= 72;		// reset by selThisLayoutChart()
	$layout_legend_top	= 0;		// reset by selThisLayoutChart()
	$layout_min_horizontal	= 0;		// reset by selThisLayoutChart()
	$layout_max_horizontal	= 0;		// reset by selThisLayoutChart()
	$layout_min_vertical	= 0;		// reset by selThisLayoutChart()
	$layout_max_vertical	= 0;		// reset by selThisLayoutChart()
	$game_seo		= "";		// set by checkURLParameters(), utilized by checkForErrors()
	$game_name		= "";		// set by checkURLParameters(), utilized by checkForErrors()
	$game_id		= 0;		// set by checkURLParameters(), utilized by checkForErrors()
	$platform_name		= "";		// set by checkURLParameters(), utilized by checkForErrors()
	$platform_id		= 0;		// set by checkURLParameters(), utilized by checkForErrors()
	$layout_name		= "";		// set by checkURLParameters(), utilized by checkForErrors()
	$layout_id		= 0;		// set by checkURLParameters(), utilized by checkForErrors()
	$style_filename		= "";		// set by selThisStyleChart()
	$style_name		= "";		// set by selThisStyleChart() and checkURLParameters(), utilized by checkForErrors()
	$style_id		= 0;		// set by checkURLParameters(), utilized by checkForErrors()
	$format_name		= "";		// set by checkURLParameters(), utilized by checkForErrors()
//	$format_id		= 0;		// should not be set again here since it has already been set in "keyboard-init.php"
//	$svgb_flag		= 0;		// should not be set again here since it has already been set in "keyboard-init.php"
	$tenk_flag		= 1;		// set by checkURLParameters()
	$vert_flag		= 0;		// set by checkURLParameters()

	// open MySQL connection
	$con = mysqli_connect($con_website, $con_username, $con_password, $con_database);
	if (mysqli_connect_errno())
	{
		trigger_error("Database connection failed: "  . mysqli_connect_error(), E_USER_ERROR);
	}
	mysqli_query($con, "SET NAMES 'utf8'");

	// MySQL queries
	selURLQueriesAll();		// gather and validate URL parameters
	selDefaultsAll();			// get default values for entities if missing
	getURLParameters();		// gather and validate URL parameters, not a query
	checkURLParameters();		// gather and validate URL parameters, not a query
	selThisLanguageStringsChart();
	selAuthorsChart();
//	selStyleGroupsChart();		// utilized by footer
//	selStylesChart();		// utilized by footer
	selThisStyleChart();
	selThisFormatChart();
	selPositionsChart();
	selThisGamesRecordChart();
	selThisStylesRecordChart();
	selThisLayoutChart();
	selThisPlatformChart();
	selBindingsChart();
	selLegendsChart();
	selContribsGamesChart();
	selContribsStylesChart();
	selContribsLayoutsChart();
	selLegendColorsChart();
	selKeyStylesChart();
	selKeyStyleClassesChart();

	// close MySQL connection
	mysqli_close($con);

	checkForErrors();
	pageTitle();

	// layout outer bounds
	if ($tenk_flag == 0)
	{
		$layout_min_horizontal	= -$layout_padding;
		$layout_max_horizontal	=  $layout_padding * 2 + $layout_tenkeyless_width;
		$layout_min_vertical	= -$layout_padding;
		$layout_max_vertical	=  $layout_padding * 2 + $layout_tenkeyless_height + $layout_legend_padding + $layout_legend_height;
		$layout_legend_top	=  $layout_tenkeyless_height + $layout_legend_padding;
	}
	else if ($tenk_flag == 1)
	{
		$layout_min_horizontal	= -$layout_padding;
		$layout_max_horizontal	=  $layout_padding * 2 + $layout_fullsize_width;
		$layout_min_vertical	= -$layout_padding;
		$layout_max_vertical	=  $layout_padding * 2 + $layout_fullsize_height + $layout_legend_padding + $layout_legend_height;
		$layout_legend_top	=  $layout_fullsize_height + $layout_legend_padding;
	}
	echo
'<!--
This file was generated using Video Game Keyboard Database by Michael Horvath.
https://isometricland.net/keyboard/keyboard.php
This work is licensed under the Creative Commons Attribution-ShareAlike 3.0
United States License. To view a copy of this license, visit
http://creativecommons.org/licenses/by-sa/3.0/us/ or send a letter to Creative
Commons, PO Box 1866, Mountain View, CA 94042, USA.
';
	echo "Binding scheme created by: ";
	$count_authors = count($gamesrecord_authors);
	for ($i = 0; $i < $count_authors; $i++)
	{
		echo $gamesrecord_authors[$i];
		if ($i < $count_authors - 1)
			echo ", ";
		else
			echo ".\n";
	}
	echo "Keyboard layout created by: ";
	$count_authors = count($layout_authors);
	for ($i = 0; $i < $count_authors; $i++)
	{
		echo $layout_authors[$i];
		if ($i < $count_authors - 1)
			echo ", ";
		else
			echo ".\n";
	}
	echo "Theme created by: ";
	$count_authors = count($stylesrecord_authors);
	for ($i = 0; $i < $count_authors; $i++)
	{
		echo $stylesrecord_authors[$i];
		if ($i < $count_authors - 1)
			echo ", ";
		else
			echo ".\n";
	}
	echo
'-->
<svg
	version="1.1"
	baseProfile="full"
	xmlns="http://www.w3.org/2000/svg"
	xmlns:xlink="http://www.w3.org/1999/xlink"
	xmlns:ev="http://www.w3.org/2001/xml-events"
';

	if ($vert_flag == false)
	{
		echo
'	viewBox="' . $layout_min_horizontal . ' ' . $layout_min_vertical . ' ' . $layout_max_horizontal . ' ' . $layout_max_vertical . '"
	width="' . $layout_max_horizontal . '" height="' . $layout_max_vertical . '">
';
	}
	else
	{
		echo
'	viewBox="' . $layout_min_vertical . ' ' . $layout_min_horizontal . ' ' . $layout_max_vertical . ' ' . $layout_max_horizontal . '"
	width="' . $layout_max_vertical . '" height="' . $layout_max_horizontal . '">
';
	}

	echo
'	<title>' . cleantextSVG($page_title_a) . cleantextSVG($page_title_b) . '</title>
	<desc>' . cleantextSVG($language_title) . ': ' . cleantextSVG($language_description) . '</desc>
	<metadata id="license"
		xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
		xmlns:dc="http://purl.org/dc/elements/1.1/"
		xmlns:cc="http://creativecommons.org/ns#">
		<rdf:RDF>
			<cc:Work rdf:about="">
				<dc:format>image/svg+xml</dc:format>
				<dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage" />
				<cc:license rdf:resource="http://creativecommons.org/licenses/by-sa/3.0/" />
			</cc:Work>
			<cc:License rdf:about="http://creativecommons.org/licenses/by-sa/3.0/">
				<cc:permits rdf:resource="http://creativecommons.org/ns#Reproduction" />
				<cc:permits rdf:resource="http://creativecommons.org/ns#Distribution" />
				<cc:requires rdf:resource="http://creativecommons.org/ns#Notice" />
				<cc:requires rdf:resource="http://creativecommons.org/ns#Attribution" />
				<cc:permits rdf:resource="http://creativecommons.org/ns#DerivativeWorks" />
				<cc:requires rdf:resource="http://creativecommons.org/ns#ShareAlike" />
			</cc:License>
			<rdf:Description about=""
				dc:title="' . cleantextSVG($page_title_a . $page_title_b) . '"
				dc:description="' . cleantextSVG($language_title) . ': ' . cleantextSVG($language_description) . '"
				dc:publisher="Video Game Keyboard Database"
				dc:date="' . date("Y-m-d H:i:s") . '"
				dc:format="image/svg+xml"
				dc:language="' . $language_code . '">
				<dc:creator>
					<rdf:Bag>
';

	// need to handle duplicate names better somehow
	// or prefix each line with "Binding scheme created by..." or whatever
	for ($i = 0; $i < count($gamesrecord_authors); $i++)
	{
		echo
"						<rdf:li>" . cleantextSVG($gamesrecord_authors[$i]) . "</rdf:li>\n";
	}
	for ($i = 0; $i < count($layout_authors); $i++)
	{
		echo
"						<rdf:li>" . cleantextSVG($layout_authors[$i]) . "</rdf:li>\n";
	}
	for ($i = 0; $i < count($stylesrecord_authors); $i++)
	{
		echo
"						<rdf:li>" . cleantextSVG($stylesrecord_authors[$i]) . "</rdf:li>\n";
	}

	echo
'					</rdf:Bag>
				</dc:creator>
			</rdf:Description>
		</rdf:RDF>
	</metadata>
	<style type="text/css">
/* <![CDATA[ */
';

	include($path_lib2 . "svg-" . $style_filename . ".css");

	echo
'/* ]]> */
	</style>
	<defs>
		<filter id="f1" x="-100%" y="-100%" width="300%" height="300%"><feGaussianBlur in="SourceGraphic" stdDeviation="4" /></filter>
		<filter id="f2" width="130%" height="130%">
			<feGaussianBlur in="SourceAlpha" stdDeviation="1"/> 
			<feOffset dx="1" dy="1" result="offsetblur"/>
			<feComponentTransfer>
				<feFuncA type="linear" slope="0.5"/>
			</feComponentTransfer>
			<feMerge> 
				<feMergeNode/>
				<feMergeNode in="SourceGraphic"/> 
			</feMerge>
		</filter>
';

	if (($style_id == 5) || ($style_id == 6))	// Dark Gradient & Light Gradient
	{
		echo
'		<linearGradient id="grad_1" x1="0" x2="0" y1="0" y2="1">
			<stop offset="0.0" stop-color="white" stop-opacity="0.0" />
			<stop offset="1.0" stop-color="white" stop-opacity="1.0" />
		</linearGradient>
';
	}
	if (($style_id == 16) || ($style_id == 18))	// CIELCh Shiny
	{
		echo
'		<linearGradient id="grad_2" x1="0" x2="0" y1="0" y2="1">
			<stop offset="0.0" stop-color="white" stop-opacity="0.2" />
			<stop offset="0.5" stop-color="white" stop-opacity="0.2" />
			<stop offset="0.5" stop-color="white" stop-opacity="0.0" />
			<stop offset="1.0" stop-color="white" stop-opacity="0.0" />
		</linearGradient>
';
	}
	// Patterned Grayscale
	if ($style_id == 9)
	{
		echo
'		<pattern id="pat01" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAE0lEQVQImWP4vx8CGaD0fwayRADXsTfBHa7CGAAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat02" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAIElEQVQImWP4/////f///99ngNL/GaD0fwYo/Z+BCDUAr8A9wZ1do0gAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat03" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAKElEQVQImUXLwREAIBCDwGjjxsrX1418gRRoFCqUsiQ3J9kZRv149gc33Svfk/J4xAAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat04" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAJUlEQVQImTXGsQEAMAjDMPf/o9LPzEDQJOLCXDrssMNuY3wCwB+U5TfJB20x8gAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat05" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAHUlEQVQImWNumC84X3C+4HzG+wwQwLSQAQJJEgEARWcORcItfCQAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat06" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAHUlEQVQImWNssD9of9D+oD3jfgYIYDrIAIEkiQAAiQcP0w/Ue9AAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat07" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAGElEQVQImWNsYIAAJijNwPgfXYSlkbAaAH1lAw9WJMEiAAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat08" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFklEQVQImWP8z9DIUM/QyMDEAAXkMQCuBAKQ/EVNLQAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat09" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAALUlEQVQImWNo+P+/oeH//wYmhkaGeoZGhnqmeiiL8T+Uwczo4HDwoIPDQTxqAJCDFk4qwQYgAAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat10" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAKElEQVQImWP+z+iwv4HRYT8zI0M9QyNDPQMTlG5k/A+hGZigdD0eNQCYExGRPdzmZgAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat11" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAHUlEQVQImWP8z9DIUM/QyMDEAAUsEH49QoSRCDUAgAMJE1nslWwAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat12" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAI0lEQVQImWNsYKhnaGSoZ2BsYIAAxv8MjQz1DI0IERYGwmoAoR0KihxALMEAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat13" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAJklEQVQImWNucNjP6OCwn5HxfyMDQ30jAwMzI0P9wYMM9QdJEgEAMlcWl9fR1DgAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat14" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAHElEQVQImWP+z7ifcT/jfkaGhv8QyAClG0gSAQAFxi0o8hVkgQAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat15" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAGUlEQVQImWP43/D///+G//8ZINT/BgayRADd6TfR4133+AAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat16" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAI0lEQVQImX2KMRIAAASA4uW8PIPBpq0uSi01ZElooLmyDs8zwm4OB6wkBqMAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat17" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAI0lEQVQImWNo+A8BjP8ZIICJgYGBoZGBgQEuwszoAGHgUQMAq/IMxKod1SMAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat18" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAGElEQVQImWNsYIAAxv9QBhMDOoOlkbAaAI9BAw/yqYY5AAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat19" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAF0lEQVQImWP4//9/w////xuYGKCAPAYA2rcHCfGqMmMAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat20" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAIElEQVQImWNo+P//f8P///8Z/kNZDP+hLAaoTAMDEWoA2fE30f0SY/YAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat21" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAH0lEQVQImY3KIQEAMAwEsZNe5xl5AUMhCYdramrq4zzZ8TfREKxKRAAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat22" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAHUlEQVQImWP4//9/w////xuYGKAAzmBsgLGIUAMA0YgO/7MiaWoAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat23" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAAAAADhZOFXAAAACXBIWXMAAAsSAAALEgHS3X78AAAAKElEQVQImXWKsQ0AMAyDbOVxf+bTyJClS8UEYlLXdQUEiMKhc3jK91mRSzFflGM2CwAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="pat24" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAIAAABLbSncAAAACXBIWXMAAA7DAAAOwwHHb6hkAAAAOElEQVQImXWOuxEAIAxCn+6S/fchw2CRxtODhk8BIMkfJDH0pLa5zS2WbQDobqCqxm4SUlUeT3cPx7qQPS4vVDYAAAAASUVORK5CYII=" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry00" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFUlEQVQYlWNkYGBoYMADmPBJDh8FAItIAJCfGlr0AAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry01" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFUlEQVQYlWMUEBBoYMADmPBJDh8FALxoAMDTemTaAAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry02" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFUlEQVQYlWNUUFBoYMADmPBJDh8FAO2IAPDmRQ9/AAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry03" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFUlEQVQYlWM0MDBoYMADmPBJDh8FAB63ASBvBuXXAAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry04" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFUlEQVQYlWN0cHBoYMADmPBJDh8FAE/XAVDN0NRiAAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry05" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFUlEQVQYlWMMCAhoYMADmPBJDh8FAID3AYCtiCn5AAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry06" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFUlEQVQYlWNMSEhoYMADmPBJDh8FALIXAbCxJBqxAAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry07" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFUlEQVQYlWMsKChoYMADmPBJDh8FAOM3AeA83He7AAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry08" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFUlEQVQYlWOsr6//z4AHMOGTHD4KAJJQAozLinLVAAAAAElFTkSuQmCC" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry10" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFklEQVQYlWNcsGBBAwMewIRPcvgoAAB2pgJw6td3owAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry12" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFklEQVQYlWM8cOBAAwMewIRPcvgoAADY5gLQiqFhGQAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
		<pattern id="gry16" patternUnits="userSpaceOnUse" width="8" height="8"><image xlink:href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAgAAAAICAYAAADED76LAAAACXBIWXMAAAsSAAALEgHS3X78AAAAFklEQVQYlWP8//9/AwMewIRPcvgoAACaYwONca/KLAAAAABJRU5ErkJggg==" x="0" y="0" width="8" height="8" /></pattern>
';
	}

	echo
"	</defs>\n";
	if ($vert_flag == true)
	{
		echo
"	<g transform=\"translate(" . ($layout_min_vertical*2+$layout_max_vertical) . ",0) rotate(90)\">\n";
	}
	else
	{
		echo
"	<g>\n";
	}
	echo
"		<rect id=\"bkgrec\" x=\"" . $layout_min_horizontal . "\" y=\"" . $layout_min_vertical . "\" width=\"" . $layout_max_horizontal . "\" height=\"" . $layout_max_vertical . "\" fill=\"none\" stroke=\"none\"/>\n";

	// print error messages
	for ($i = 0; $i < count($errors_table); $i++)
	{
		echo
"		<text y=\"" . ($i * 20) . "\">" . cleantextSVG($errors_table[$i]) . "</text>\n";
	}
	if (($gamesrecord_id > 0) && ($stylesrecord_id > 0))
	{
		// keys
		$txt_hgh_sml = 11;		// text height
		$txt_hgh_lrg = 13;		// text height
		$txt_mar_sml = 2;		// text margin
		$txt_mar_lrg = 3;		// text margin
		if ($kcap_flag == 0) {
			$lbl_sty = " lblvis";
		} elseif ($kcap_flag == 1) {
			$lbl_sty = " lbldim";
		} elseif ($kcap_flag == 2) {
			$lbl_sty = " lblblr";
		} elseif ($kcap_flag == 3) {
			$lbl_sty = " lblhid";
		}
		foreach ($position_table as $i => $position_row)
		{
			// position_left, position_top, position_width, position_height, symbol_norm_low, symbol_norm_cap, symbol_altgr_low, symbol_altgr_cap, key_number, lowkey_optional, numpad
			$key_sty	= array_key_exists($i, $keystyle_table) ? getkeyclass($keystyle_table[$i][0]) : "";
			$pos_lft	= $position_row[ 0] + $layout_keygap/2;
			$pos_top	= $position_row[ 1] + $layout_keygap/2;
			$pos_wid	= $position_row[ 2] - $layout_keygap;		//4
			$pos_hgh	= $position_row[ 3] - $layout_keygap;
			$low_nor	= splitkeytext(cleantextSVG($position_row[ 4]));
			$upp_nor	= splitkeytext(cleantextSVG($position_row[ 5]));
			$low_agr	= splitkeytext(cleantextSVG($position_row[ 6]));
			$upp_agr	= splitkeytext(cleantextSVG($position_row[ 7]));
			$key_num	= $position_row[ 8];
			$key_opt	= $position_row[ 9];
			$key_ten	= $position_row[ 10];
			$img_wid	= 48;
			$img_hgh	= 48;
			$img_pos_x	= $layout_keygap/2 + $pos_wid/2 - $img_wid/2 - 1/2;
			$img_pos_y	= $layout_keygap/2 + $pos_hgh/2 - $img_hgh/2 - 1/2;

			if (array_key_exists($i, $binding_table))
			{
				// normal_group, normal_action, shift_group, shift_action, ctrl_group, ctrl_action, alt_group, alt_action, altgr_group, altgr_action, extra_group, extra_action, image_file, image_uri
				$binding_row	= $binding_table[$i];
				$bkg_nor = getkeycolor($binding_row[ 0]);
				$cap_nor = splitkeytext(cleantextSVG($binding_row[ 1]));
				$bkg_shf = getkeycolor($binding_row[ 2]);
				$cap_shf = splitkeytext(cleantextSVG($binding_row[ 3]));
				$bkg_ctl = getkeycolor($binding_row[ 4]);
				$cap_ctl = splitkeytext(cleantextSVG($binding_row[ 5]));
				$bkg_alt = getkeycolor($binding_row[ 6]);
				$cap_alt = splitkeytext(cleantextSVG($binding_row[ 7]));
				$bkg_agr = getkeycolor($binding_row[ 8]);
				$cap_agr = splitkeytext(cleantextSVG($binding_row[ 9]));
				$bkg_xtr = getkeycolor($binding_row[10]);
				$cap_xtr = splitkeytext(cleantextSVG($binding_row[11]));
				$img_fil = $binding_row[12];
				$img_uri = $binding_row[13];
			}
			else
			{
				$bkg_nor = "non";
				$cap_nor = [];
				$bkg_shf = "non";
				$cap_shf = [];
				$bkg_ctl = "non";
				$cap_ctl = [];
				$bkg_alt = "non";
				$cap_alt = [];
				$bkg_agr = "non";
				$cap_agr = [];
				$bkg_xtr = "non";
				$cap_xtr = [];
				$img_fil = null;
				$img_uri = null;
			}

			$top_nor = $pos_hgh - $txt_mar_lrg;
			if (($key_opt == false) && ($kcap_flag != 3)) {
				$top_nor -= $txt_hgh_lrg * count($low_nor);
			}

			// mask
			if (($style_id == 5) || ($style_id == 6))	// Dark Gradient & Light Gradient
			{
				echo
"		<mask id=\"mask_" . $i . "\">\n" .
"			<rect x=\"0\" y=\"0\" width=\"" . ($pos_wid+1) . "\" height=\"" . ($pos_hgh+1) . "\" fill=\"url(#grad_1)\"/>\n" .
"		</mask>\n";
			}

			echo
"		<g transform=\"translate(" . ($pos_lft-0.5) . " " . ($pos_top-0.5) . ")\">\n";

			// rects & image
			if (($style_id == 5) || ($style_id == 6))	// Dark Gradient & Light Gradient
			{
				echo
"			<rect class=\"keyrec rec" . $bkg_nor . "\" x=\"0.5\" y=\"0.5\" rx=\"4\" ry=\"4\" width=\"" . ($pos_wid) . "\" height=\"" . ($pos_hgh) . "\" mask=\"url(#mask_" . $i . ")\"/>\n";
			}
			else
			{
				echo
"			<rect class=\"keyrec rec" . $bkg_nor . " rec" . $key_sty . "\" x=\"0.5\" y=\"0.5\" rx=\"4\" ry=\"4\" width=\"" . ($pos_wid) . "\" height=\"" . ($pos_hgh) . "\"/>\n";
			}
			if ($img_fil)
			{
				echo
"			<image x=\"" . $img_pos_x . "\" y=\"" . $img_pos_y . "\" width=\"" . $img_wid . "\" height=\"" . $img_hgh . "\" xlink:href=\"" . $img_uri . "\"/>\n";
			}
			if (($style_id == 16) || ($style_id == 18))	// CIELCh Shiny
			{
				echo
"			<rect x=\"0.5\" y=\"0.5\" rx=\"4\" ry=\"4\" width=\"" . ($pos_wid) . "\" height=\"" . ($pos_hgh) . "\" fill=\"url(#grad_2)\"/>\n";
			}

			// backgrounds
			if ($style_id == 9)
			{
				$jcount		= 0;
				for ($j = 0; $j < count($cap_shf); $j++)
				{
					echo
"			<rect class=\"bakshf\" x=\"1.0\" y=\"" . ($jcount++ * 12 + 3) . "\" width=\"" . ($pos_wid-1) . "\" height=\"13\" rx=\"1\" ry=\"1\"></rect>\n";
				}
				for ($j = 0; $j < count($cap_ctl); $j++)
				{
					echo
"			<rect class=\"bakctl\" x=\"1.0\" y=\"" . ($jcount++ * 12 + 3) . "\" width=\"" . ($pos_wid-1) . "\" height=\"13\" rx=\"1\" ry=\"1\"></rect>\n";
				}
				for ($j = 0; $j < count($cap_alt); $j++)
				{
					echo
"			<rect class=\"bakalt\" x=\"1.0\" y=\"" . ($jcount++ * 12 + 3) . "\" width=\"" . ($pos_wid-1) . "\" height=\"13\" rx=\"1\" ry=\"1\"></rect>\n";
				}
				for ($j = 0; $j < count($cap_agr); $j++)
				{
					echo
"			<rect class=\"bakagr\" x=\"1.0\" y=\"" . ($jcount++ * 12 + 3) . "\" width=\"" . ($pos_wid-1) . "\" height=\"13\" rx=\"1\" ry=\"1\"></rect>\n";
				}
				for ($j = 0; $j < count($cap_xtr); $j++)
				{
					echo
"			<rect class=\"bakxtr\" x=\"1.0\" y=\"" . ($jcount++ * 12 + 3) . "\" width=\"" . ($pos_wid-1) . "\" height=\"13\" rx=\"1\" ry=\"1\"></rect>\n";
				}
			}

			// labels
			if ($kcap_flag != 3)
			{
				if ($key_opt == false)
				{
					for ($j = 0; $j < count($low_nor); $j++)
					{
						// bottom, left
						echo
"			<text class=\"lownor txt" . $bkg_nor . " txt" . $key_sty . $lbl_sty . "\" x=\"" . ($txt_mar_lrg) . "\" y=\"" . ($pos_hgh-$txt_mar_lrg) . "\" dy=\"" . ($j * -$txt_hgh_lrg) . "\">" . $low_nor[count($low_nor)-$j-1] . "</text>\n";
					}
				}
				for ($j = 0; $j < count($upp_nor); $j++)
				{
					// top, left
					echo
"			<text class=\"uppnor txt" . $bkg_nor . " txt" . $key_sty . $lbl_sty . "\" x=\"" . ($txt_mar_lrg) . "\" y=\"" . ($txt_hgh_lrg) . "\" dy=\"" . ($j * $txt_hgh_lrg) . "\">" . $upp_nor[$j] . "</text>\n";
				}
				for ($j = 0; $j < count($low_agr); $j++)
				{
					// bottom, right
					echo
"			<text class=\"lowagr txt" . $bkg_nor . " txt" . $key_sty . $lbl_sty . "\" x=\"" . ($pos_wid-$txt_mar_lrg) . "\" y=\"" . ($pos_hgh-$txt_mar_lrg) . "\" dy=\"" . ($j * -$txt_hgh_lrg) . "\">" . $low_agr[count($low_agr)-$j-1] . "</text>\n";
				}
				for ($j = 0; $j < count($upp_agr); $j++)
				{
					// top, right
					echo
"			<text class=\"uppagr txt" . $bkg_nor . " txt" . $key_sty . $lbl_sty . "\" x=\"" . ($pos_wid-$txt_mar_lrg) . "\" y=\"" . ($txt_hgh_lrg) . "\" dy=\"" . ($j * $txt_hgh_lrg) . "\">" . $upp_agr[$j] . "</text>\n";
				}
			}

			// captions text
			$jcount		= 0;
			for ($j = 0; $j < count($cap_nor); $j++)
			{
				// bottom, left
				echo
"			<text class=\"capnor txt" . $bkg_nor . " txt" . $key_sty . " ideo\" x=\"" . ($txt_mar_lrg) . "\" y=\"" . ($top_nor) . "\" dy=\"" . ($j * -$txt_hgh_lrg) . "\">" . $cap_nor[count($cap_nor)-$j-1] . "</text>\n";
			}
			for ($j = 0; $j < count($cap_shf); $j++)
			{
				// top, right
				echo
"			<text class=\"capshf hang\" x=\"" . ($pos_wid-$txt_mar_sml) . "\" y=\"" . ($txt_hgh_sml) . "\" dy=\"" . ($jcount++ * $txt_hgh_sml) . "\">" . $cap_shf[$j] . "</text>\n";

			}
			for ($j = 0; $j < count($cap_ctl); $j++)
			{
				// top, right
				echo
"			<text class=\"capctl hang\" x=\"" . ($pos_wid-$txt_mar_sml) . "\" y=\"" . ($txt_hgh_sml) . "\" dy=\"" . ($jcount++ * $txt_hgh_sml) . "\">" . $cap_ctl[$j] . "</text>\n";
			}
			for ($j = 0; $j < count($cap_alt); $j++)
			{
				// top, right
				echo
"			<text class=\"capalt hang\" x=\"" . ($pos_wid-$txt_mar_sml) . "\" y=\"" . ($txt_hgh_sml) . "\" dy=\"" . ($jcount++ * $txt_hgh_sml) . "\">" . $cap_alt[$j] . "</text>\n";
			}
			for ($j = 0; $j < count($cap_agr); $j++)
			{
				// top, right
				echo
"			<text class=\"capagr hang\" x=\"" . ($pos_wid-$txt_mar_sml) . "\" y=\"" . ($txt_hgh_sml) . "\" dy=\"" . ($jcount++ * $txt_hgh_sml) . "\">" . $cap_agr[$j] . "</text>\n";
			}
			for ($j = 0; $j < count($cap_xtr); $j++)
			{
				// top, right
				echo
"			<text class=\"capxtr hang\" x=\"" . ($pos_wid-$txt_mar_sml) . "\" y=\"" . ($txt_hgh_sml) . "\" dy=\"" . ($jcount++ * $txt_hgh_sml) . "\">" . $cap_xtr[$j] . "</text>\n";
			}

			echo
"		</g>\n";
		}

		// legend keys
		echo
"		<g class=\"legkey\" transform=\"translate(1.5 " . ($layout_legend_top + 1.5) . ")\">
			<rect class=\"keyrec recnon\" x=\"0.5\" y=\"0.5\" rx=\"4\" ry=\"4\" width=\"68\" height=\"68\"/>
			<rect class=\"bakshf\" x=\"1.0\" y=\"3\" width=\"67\" height=\"12\" rx=\"1\" ry=\"1\"></rect>
			<text class=\"capshf hang\" x=\"65.5\" y=\"13\">Shift</text>
			<rect class=\"bakctl\" x=\"1.0\" y=\"15\" width=\"67\" height=\"12\" rx=\"1\" ry=\"1\"></rect>
			<text class=\"capctl hang\" x=\"65.5\" y=\"25\">Ctrl</text>
			<rect class=\"bakalt\" x=\"1.0\" y=\"27\" width=\"67\" height=\"12\" rx=\"1\" ry=\"1\"></rect>
			<text class=\"capalt hang\" x=\"65.5\" y=\"37\">Alt</text>
			<text class=\"capnor txtnon ideo\" x=\"2.5\" y=\"50.5\">Caption</text>
			<text class=\"lownor txtnon\" x=\"2.5\" y=\"64.5\">Lowcase</text>
			<text class=\"uppnor txtnon\" x=\"2.5\" y=\"13.5\">Upcase</text>
		</g>\n";

		// non!
		// legend descriptions
		if ($stylegroup_id == 1)
		{
			echo
"		<g transform=\"translate(109.5 " . ($layout_legend_top + 1.5) . ")\">\n";
			$row_count = 0;
			foreach ($legend_table as $i => $legend_row)
			{
				$leg_grp = getkeycolor($legend_row[0]);
				$leg_dsc = $legend_row[1];
				$row_div = floor($row_count/3);
				$row_mod = $row_count % 3;
				echo
"			<rect class=\"keyrec rec" . $leg_grp . "\" x=\"" . ($row_div*200+0.5) . "\" y=\"" . ($row_mod*20+0.5) . "\" width=\"16\" height=\"16\"/>
			<text class=\"legtxt\" x=\"" . ($row_div*200+20.5) . "\" y=\"" . ($row_mod*20+14.5) . "\">" . cleantextSVG($leg_dsc) . "</text>\n";
				$row_count += 1;
			}
			echo
"		</g>\n";
		}
	}
	echo
'		<text class="" x="' . (0) . '" y="' . ($layout_fullsize_height+16) . '" style="fill:#c0c0c0;">&#169; CC BY-SA VGKD</text>
	</g>
</svg>
';
?>
