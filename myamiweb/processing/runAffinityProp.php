<?php
/**
 *      The Leginon software is Copyright 2003 
 *      The Scripps Research Institute, La Jolla, CA
 *      For terms of the license agreement
 *      see  http://ami.scripps.edu/software/leginon-license
 *
 *      Simple viewer to view a image using mrcmodule
 */

require_once "inc/particledata.inc";
require_once "inc/leginon.inc";
require_once "inc/project.inc";
require_once "inc/viewer.inc";
require_once "inc/processing.inc";
require_once "inc/summarytables.inc";

// IF VALUES SUBMITTED, EVALUATE DATA
if ($_POST['process']) {
	runAffinityProp();
} else {
	createAffinityPropForm();
}

function createAffinityPropForm($extra=false, $title='affPropCluster.py Launcher', 
 $heading='Clustering by Affinity Propagation') {
	// check if coming directly from a session
	$expId=$_GET['expId'];
	$selectAlignId=$_GET['alignId'];
	if ($expId){
		$sessionId=$expId;
		$projectId=getProjectId();
		$formAction=$_SERVER['PHP_SELF']."?expId=$expId";
	} else {
		$sessionId=$_POST['sessionId'];
		$formAction=$_SERVER['PHP_SELF'];
		$projectId=getProjectId();
	}
	if ($selectAlignId)
		$formAction.="&alignId=$selectAlignId";

	// connect to particle database
	$particle = new particledata();
	$alignIds = $particle->getAlignStackIds($sessionId, true);
	$alignruns = ($alignIds) ? count($alignIds) : 0;
	$analysisIds = $particle->getAnalysisRuns($sessionId, $projectId, true);
	//foreach ($analysisIds as $analysisId)
	//	echo print_r($analysisId)."<br/><br/>\n";
	$analysisruns = ($analysisIds) ? count($analysisIds) : 0;

	$javascript = "<script src='../js/viewer.js'></script>\n";
	// javascript to switch the defaults based on the stack
	$javascript .= "<script>\n";
	$javascript .= "function switchDefaults(stackval) {\n";
	$javascript .= "	var stackArray = stackval.split('|--|');\n";
	// remove commas from number
	$javascript .= "	stackArray[3] = stackArray[3].replace(/\,/g,'');\n";
	$javascript .= "	document.viewerform.numpart.value = stackArray[3];\n";
	// set mask radius
	$javascript .= "	if (stackArray[1]) {\n";
	$javascript .= "		var maxmask = Math.floor(stackArray[2]*stackArray[1]/3);\n";
	$javascript .= "		document.viewerform.maskrad.value = maxmask;\n";
	$javascript .= "		var bestbin = Math.floor(stackArray[2]/64);\n";
	$javascript .= "		if (bestbin < 1) {\n";
	$javascript .= "			var bestbin = 1 ;}\n";
	$javascript .= "		document.viewerform.bin.value = bestbin;\n";
	$javascript .= "	}\n";
	$javascript .= "}\n";
	$javascript .= "</script>\n";

	$javascript .= writeJavaPopupFunctions('appion');	

	processing_header($title,$heading,$javascript);

	// write out errors, if any came up:
	if ($extra) {
		echo "<font color='#cc3333' size='+2'>$extra</font>\n<hr/>\n";
	}
  
	echo"
       <FORM NAME='viewerform' method='POST' ACTION='$formAction'>\n";
	$sessiondata=getSessionList($projectId,$expId);
	$sessioninfo=$sessiondata['info'];
	$sessionpath=getBaseAppionPath($sessioninfo).'/align/';

	// set commit on by default when first loading page, else set
	$commitcheck = ($_POST['commit']=='on' || !$_POST['process']) ? 'checked' : '';
	// Set any existing parameters in form
	$sessionpathval = ($_POST['outdir']) ? $_POST['outdir'] : $sessionpath;
	while (file_exists($sessionpathval.'affprop'.($analysisruns+1)))
		$analysisruns += 1;
	$runnameval = ($_POST['runname']) ? $_POST['runname'] : 'affprop'.($analysisruns+1);
	$bin = ($_POST['bin']) ? $_POST['bin'] : '1';
	$rundescrval = $_POST['description'];
	if ($selectAlignId)
		$numpart = ($_POST['numpart']) ? $_POST['numpart'] : $particle->getNumAlignStackParticles($selectAlignId);
	else
		$numpart = ($_POST['numpart']) ? $_POST['numpart'] : 0;

	$defaultmaskrad = 100;

	echo"
	<table border='0' class='tableborder'>
	<tr>
		<td valign='top'>\n";
	echo "<table border='0' cellpadding='5'>\n";
	echo "<tr><td>\n";
	echo openRoundBorder();
	echo docpop('runid','<b>Affinity Prop Run Name:</b>');
	echo "<input type='text' name='runname' value='$runnameval'>\n";
	echo "<br />\n";
	echo "<br />\n";
	echo docpop('outdir','<b>Output Directory:</b>');
	echo "<br />\n";
	echo "<input type='text' name='outdir' value='$sessionpathval' size='38'>\n";
	echo "<br />\n";
	echo "<br />\n";
	echo docpop('descr','<b>Description of Affinity Prop Run:</b>');
	echo "<br />\n";
	echo "<textarea name='description' rows='3' cols='60'>$rundescrval</textarea>\n";
	echo closeRoundBorder();
	echo "</td>
		</tr>\n";
	echo "<tr>
			<td>\n";

	if ($selectAlignId) {
		$alignstack = $particle->getAlignStackParams($selectAlignId);
		// get pixel size and box size
		$apix = $alignstack['pixelsize'];
		if ($apix) {
			$mpix = $apix/1E10;
			$apixtxt=format_angstrom_number($mpix)."/pixel";
		}
		$boxsz = $alignstack['boxsize'];
		$totprtls=commafy($particle->getNumAlignStackParticles($selectAlignId));
		$stackval = "$selectAlignId|--|$apix|--|$boxsz|--|$totprtls";
		//echo $stackval;
		echo "<input type='hidden' name='stackid' value='$stackval'>\n";
		echo alignstacksummarytable($selectAlignId, true);
		$alignstack = $particle->getAlignStackParams($selectAlignId);
		$defaultmaskrad = (int) ($alignstack['boxsize']/3)*$alignstack['pixelsize'];
	} elseif ($alignIds) {
		echo "
		Aligned Stack:<br>
		<select name='stackid' onchange='switchDefaults(this.value)'>\n";
		foreach ($alignIds as $alignarray) {
			$alignid = $alignarray['alignstackid'];
			$alignstack = $particle->getAlignStackParams($alignid);

			// get pixel size and box size
			$apix = $alignstack['pixelsize'];
			if ($apix) {
				$mpix = $apix/1E10;
				$apixtxt=format_angstrom_number($mpix)."/pixel";
			}
			$boxsz = $alignstack['boxsize'];
			//handle multiple runs in stack
			$runname=$alignstack['runname'];
			$totprtls=commafy($particle->getNumAlignStackParticles($alignid));
			echo "<OPTION VALUE='$alignid|--|$apix|--|$boxsz|--|$totprtls'";
			// select previously set prtl on resubmit
			if ($stackidval==$alignid) echo " SELECTED";
			echo ">$alignid: $runname ($totprtls prtls,";
			if ($mpix) echo " $apixtxt,";
			echo " $boxsz pixels)</OPTION>\n";
		}
		echo "</SELECT>\n";
	} else {
		echo"
		<FONT COLOR='RED'><B>No Aligned Stacks for this Session</B></FONT>\n";
	}

	echo "</TD></tr>\n";
	echo "</table>\n";
	echo "</TD>\n";
	echo "<TD CLASS='tablebg'>\n";
	echo "  <TABLE CELLPADDING='5' BORDER='0'>\n";
	echo "  <TR><TD VALIGN='TOP'>\n";

	$maskrad = ($_POST['maskrad']) ? $_POST['maskrad'] : (int) $defaultmaskrad;
	echo "<INPUT TYPE='text' NAME='maskrad' SIZE='4' VALUE='$maskrad'>\n";
	echo docpop('maskrad','Mask Radius');
	echo "<font size='-2'>(&Aring;ngstroms)</font>\n";
	echo "<br/>\n";
	echo "<br/>\n";

	echo "<INPUT TYPE='text' NAME='bin' VALUE='$bin' SIZE='4' onChange='estimatetime()'>\n";
	echo docpop('partbin','Particle binning');
	echo "<br/>\n";
	echo "<br/>\n";

	echo "Preference value setting";
	echo "<br/>\n";
	echo "&nbsp;&nbsp;<select name='preftype' ";
	echo ">\n";
	echo " <option value='median'>Median value, normal number of classes</option>\n";
	echo " <option value='minimum'>Minimum value, fewer number of classes</option>\n";
	echo " <option value='minlessrange' selected>Minimum minus the range, fewest number of classes</option>\n";
	echo "</select>\n";
	echo "<br/>\n";
	echo "<br/>\n";

	echo "<INPUT TYPE='text' NAME='numpart' VALUE='$numpart' SIZE='5'>\n";
	echo docpop('numpart','Number of particles to use');
	echo "<br/>\n";
	echo "<br/>\n";

	echo "<INPUT TYPE='checkbox' NAME='commit' $commitcheck>\n";
	echo docpop('commit','<B>Commit to Database</B>');
	echo "<br/>\n";
	echo "<br/>\n";

	echo "  </td>\n";
	echo "  </tr>\n";
	echo "</table>\n";
	echo "</TD>\n";
	echo "</tr>\n";
	echo "<TR>\n";
	echo "	<TD COLSPAN='2' ALIGN='CENTER'>\n";
	echo "	<hr />\n";
	echo getSubmitForm("Run Affinity Propagation");
	echo "  </td>\n";
	echo "</tr>\n";
	echo "</table>\n";
	echo "</form>\n";
	// first time loading page, set defaults:
	if (!$_POST['process']) {
		echo "<script>switchDefaults(document.viewerform.stackid.options[0].value);</script>\n";
	}

	echo referenceBox("Clustering by Passing Messages Between Data Points.", 2007, "Brendan J. Frey and Delbert Dueck", "Science", 315, 5814, 17218491, false, false, "img/affinityprop.png");

	processing_footer();
	exit;
}

function runAffinityProp() {
	/* *******************
	PART 1: Get variables
	******************** */
	$expId=$_GET['expId'];
	$runname=$_POST['runname'];
	$outdir=$_POST['outdir'];
	$stackval=$_POST['stackid'];
	list($stackid,$apix,$boxsz,$totpart) = preg_split('%\|--\|%',$stackval);
	$maskrad=$_POST['maskrad'];
	$bin=$_POST['bin'];
	$numpart=$_POST['numpart'];
	$preftype = $_POST['preftype'];
	$commit = ($_POST['commit']=="on") ? '--commit' : '';
	$description=$_POST['description'];
	
	/* *******************
	PART 2: Check for conflicts, if there is an error display the form again
	******************** */
	
	//make sure a session was selected
	if (!$description)
		createAffinityPropForm("<B>ERROR:</B> Enter a brief description of the particles to be aligned");

	//make sure a stack was selected
	if (!$stackid)
		createAffinityPropForm("<B>ERROR:</B> No stack selected");

	if ($numpart < 4)
		createAffinityPropForm("<B>ERROR:</B> Must have more than 4 particles");

	// check particle radii
	$particle = new particledata();
	$stackparams=$particle->getAlignStackParams($stackid);
	$boxrad = $stackparams['pixelsize'] * $stackparams['boxsize'];
	if ($maskrad > $boxrad)
		createAffinityPropForm("<b>ERROR:</b> Mask radius too large! $maskrad > $boxrad ".print_r($stackparams));

	// make sure outdir ends with '/' and append run name
	if (substr($outdir,-1,1)!='/') $outdir.='/';
	$rundir = $outdir.$runname;
	
	/* *******************
	PART 3: Create program command
	******************** */
	$command ="affinityPropCluster.py ";
	$command.="--projectid=".getProjectId()." ";
	$command.="--rundir=$rundir ";
	$command.="--description=\"$description\" ";
	$command.="--runname=$runname ";
	$command.="--preftype=$preftype ";
	$command.="--alignid=$stackid ";
	$command.="--maskrad=$maskrad ";
	$command.="--bin=$bin ";
	$command.="--numpart=$numpart ";
	if ($commit) $command.="--commit ";
	else $command.="--no-commit ";

	/* *******************
	PART 4: Create header info, i.e., references
	******************** */
	$headinfo .= referenceBox("Clustering by Passing Messages Between Data Points.", 2007, "Brendan J. Frey and Delbert Dueck", "Science", 315, 5814, 17218491, false, false, "img/affinityprop.png");
	
	/* *******************
	PART 5: Show or Run Command
	******************** */
	// submit command
	$errors = showOrSubmitCommand($command, $headinfo, 'alignanalysis', $nproc);

	// if error display them
	if ($errors)
		createAffinityPropForm("<b>ERROR:</b> $errors");
	
}
?>
