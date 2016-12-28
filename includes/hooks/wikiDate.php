<?php /* ------------------------------------------------------------
-																	-
- WHMCS Time Ago ( Client Date ) By Milad Maldar ( www.ltiny.ir )	-
-																	-
------------------------------------------------------------------ */

function wikiDate($format, $time) {
	$now 		= time();
	$timestamp 	= ($now<$time) ? ($time-$now) : ($now-$time);
	$tense 		= ($now<$time) ? "دیگر" : "پیش";
	if($timestamp < 1)
	return false;

	$values = array( 
		12*30*24*60*60*10 =>  'دهه',
		12*30*24*60*60    =>  'سال',
		30*24*60*60       =>  'ماه',
		24*60*60*7        =>  'هفته',
		24*60*60          =>  'روز',
		60*60             =>  'ساعت',
		60                =>  'دقیقه',
		1                 =>  'ثانیه'
	);

	foreach($values as $secs=>$point) {
		$res = $timestamp / $secs;
	if($res >= 1)
		return "<span title='". date($format, $time) ."'>". round($res) . " {$point} {$tense}" ."</span>";
	}
}

add_hook('ClientAreaPage', 1, function($wikiDateReplace) {

    $wikiDateReplace['wikiDate'] = TRUE;
    foreach ($wikiDateReplace['announcements'] as &$announcements) {
        $announcements['date'] = $announcements['timestamp'] ? wikiDate("j F", $announcements['timestamp']) : wikiDate("j F", strtotime($announcements['rawDate']));
    }

    foreach ($wikiDateReplace['domains'] as &$domains) {
        $domains['registrationdate'] = wikiDate("Y/m/d", strtotime($domains['normalisedRegistrationDate']));		
		if ($domains['nextduedate'] != '-') {
			$domains['nextduedate'] = wikiDate("Y/m/d", strtotime($domains['normalisedNextDueDate']));
		}
        $domains['expirydate'] = wikiDate("Y/m/d", strtotime($domains['normalisedExpiryDate']));
    }

    foreach ($wikiDateReplace['services'] as &$services) {
        $services['regdate'] = wikiDate("Y/m/d", strtotime($services['normalisedRegDate']));
		if ($services['nextduedate'] != '-') {
			$services['nextduedate'] = wikiDate("Y/m/d", strtotime($services['normalisedNextDueDate']));
		}
    }

    foreach ($wikiDateReplace['tickets'] as &$tickets) {
        $tickets['date'] 		= wikiDate("Y/m/d H:i", strtotime($tickets['normalisedDate']));
        $tickets['lastreply'] 	= wikiDate("Y/m/d H:i", strtotime($tickets['normalisedLastReply']));
    }

    foreach ($wikiDateReplace['replies'] as &$replies) {
        $replies['date'] 		= wikiDate("Y/m/d", strtotime($replies['date']));
    }

    foreach ($wikiDateReplace['ascreplies'] as &$ascreplies) {
        $ascreplies['date'] 	= wikiDate("Y/m/d", strtotime($ascreplies['date']));
    }

    foreach ($wikiDateReplace['descreplies'] as &$descreplies) {
        $descreplies['date'] 	= wikiDate("Y/m/d", strtotime($descreplies['date']));
    }

    foreach ($wikiDateReplace['invoices'] as &$invoices) {
        $invoices['datecreated'] = wikiDate("Y/m/d", strtotime($invoices['normalisedDateCreated']));
        $invoices['datedue'] 	= wikiDate("Y/m/d", strtotime($invoices['normalisedDateDue']));
    }
    
    if ($wikiDateReplace['date']) {
        $wikiDateReplace['jfulldate'] = wikiDate("l ، j F Y", strtotime($wikiDateReplace['date']));
    }
	
    if (strpos($_SERVER['REQUEST_URI'],'viewinvoice') === false) {
		$datefields = array('registrationdate','nextduedate','expirydate','regdate','lastupdate','date','duedate','datepaid','datecreated','datedue');
		foreach ($datefields as $datefield) {
			if ($wikiDateReplace[$datefield])
				$wikiDateReplace[$datefield] = wikiDate("Y/m/d", strtotime($wikiDateReplace[$datefield]));
		}
    }
    
    return $wikiDateReplace;
});