<?php

function milestones_func( $atts ) {
	$atts = shortcode_atts( array(
		'state' => 'all'
	), $atts, 'gh_milestones' );
	$gh = new Github();
	// error_log( print_r($atts, TRUE));
	$milestones = $gh->get_milestones($atts);

	return format_milestones( $milestones ); 
}
add_shortcode( 'gh_milestones', 'milestones_func' );

function format_milestones( $milestones ) {

	$return = '<div class="milestones-list">';

	foreach ($milestones as $milestone) {

		$return .= '<h3 class="milestone__title">'.$milestone['title'].'</h3>';

		$return .= '<ul class="milestone__details">';
		// $return .= '<li>State: '.$milestone['state'].'</li>';
		$return .= ($milestone['due_on']) ? '<li>Due: '.wpghdash_formatdate($milestone['due_on']).'</li>' : NULL;
		$return .= '<li>Open issues: '.$milestone['open_issues'].' Closed issues: '.$milestone['closed_issues'].'</li>';

		$return .= '<li>Issues:<br/>'. print_milestone_issues($milestone['issues']) .'</li>';

		$return .= '</ul>';
	}

	$return .= '</div>';

	return $return;
}


function print_milestone_issues($issues) {

	if (!$issues)
		return;

	$return = '<ul class="milestone__issues-list">';
	foreach ($issues as $issue) {
		$class = ( $issue['state']=='open' ) ? 'open' : 'closed';
		$return .='<li class="milestone__issue-item '.$class.'">'. $issue['title'] . ' ';
		$return .= build_labels($issue['labels']);
		$return .='</li>';
	}
	$return .= '</ul>';
	return $return;

}


function issues_func( $atts ) {
	$atts = shortcode_atts( array(
		'labels' => NULL,
		'state' => NULL,
		'per_page' => 50, #max is 100
	), $atts, 'gh_issues' );
	$gh = new Github();
	// error_log( print_r($atts, TRUE));
	$issues = $gh->get_issues($atts);

	return format_issues($issues);
}
add_shortcode( 'gh_issues', 'issues_func' );


// function issues_search_func( $atts ) {
// 	$atts = shortcode_atts( array(
// 		'term' => NULL,
// 		'state' => 'all'
// 		// 'labels' => NULL,
// 		// 'state' => NULL,
// 		// 'per_page' => 50, #max is 100
// 	), $atts, 'gh_issues' );
// 	$gh = new Github();
// 	$issues = $gh->search_issues($atts);

// 	// error_log( json_encode($issues));


// 	return format_issues($issues);
// }
// add_shortcode( 'gh_search', 'issues_search_func' );


function format_issues( $issues ) {

	$return = '<div class="issues-list">';

	foreach ($issues as $issue) {

		$return .= '<h3 class="issue__title">'.$issue['title'].' #'. $issue['number'].'</h3>';

		$return .= build_labels( $issue['labels'] );

		$return .= '<ul class="issue__details">';
		$return .= '<li>State: '.$issue['state'].'</li>';
		$return .= (!empty($issue['closed_at'])) ? '<li>Closed: '.wpghdash_formatdate($issue['closed_at']).'</li>' : NULL;
		$return .= (!empty($issue['assignee'])) ? '<li>Assigned to: '.$issue['assignee']['login'].'</li>' : NULL;
		$return .= '</ul>';
	}

	$return .= '</div>';

	return $return;
}

function build_labels( $labels ) {
	$return = '';
	foreach ($labels as $label) {
		$return .= '<span class="issue__label" ';
		$return .= 'style="background-color:#'. $label['color'].'" ';
		$return .= '>'; 
		$return .= $label['name'];
		$return .= '</span>';
	}
	return $return;
}

function print_search_form() {
	?>
	<form class="issue-searchform" method="POST" action="<?php the_permalink() ?>">
		<input type="text" name="gh_searchterm" value="<?php echo (isset($_POST['gh_searchterm'])) ? $_POST['gh_searchterm'] : NULL; ?>" />
		<input type="submit" value="Search" />
	</form>
	<?php	
}

function searchform_func( $atts ) {
	$atts = shortcode_atts( array(
		'placeholder' => NULL,
	), $atts, 'gh_searchform' );

	$results = NULL;
	$msg = 'Enter a search term to begin.';

	print_search_form();

	if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
		if (strlen( $_POST['gh_searchterm'] ) < 3) {
			$msg = 'Search term is too short!';
			$results = 0;

		} else {

			$gh = new Github();
			$issues = $gh->search_issues(['term'=>$_POST['gh_searchterm']]);
			$results = count($issues);
			$msg = "Results: " . $results;

		}
	

	}

	echo '<div class="gh_searchform__msg">' . $msg . '</div>';

	return (!empty($issues)) ? format_issues($issues) : NULL;

}
add_shortcode( 'gh_searchform', 'searchform_func' );
