<?php

// use Github\ResultsPager;

class Github {

	protected $client;
	protected $org;
	protected $repo;
	private $gh_username;
	private $gh_password;
	public $labels;
	public $has_settings = FALSE;
	public $missing_settings_msg = '<h2>Missing GitHub settings!</h2>';

	public function __construct() {

		$this->repo = get_option('wpghdash_gh_repo');
		$this->org = get_option('wpghdash_gh_org');
		//TODO: make it user meta from currentuser
		//http://www.paulund.co.uk/add-custom-user-profile-fields
		//http://davidwalsh.name/add-profile-fields
		$this->gh_username = get_option('wpghdash_client_id');
		$this->gh_password = get_option('wpghdash_client_secret');

		$this->check_for_settings();

		$this->client = new \Github\Client();
		
		$auth = $this->client->authenticate( $this->gh_username, $this->gh_password, Github\Client::AUTH_HTTP_PASSWORD);
		// echo "<pre>";var_dump($this->client->getHttpClient());echo "</pre>";exit;

	}

	public function get_issues($options=array()) {

		$default_options = array(
							'labels'=>'', 
							'state'=>'all',
							'page' => 1
							);

		$params = array_merge($default_options, $options);

		$paginator = new Github\ResultPager($this->client);

		// $issues = $this->client->api('issue')->all($this->org, $this->repo, $params);

		//TODO: Add page links instead of full list
		$issues = $paginator->fetchAll($this->client->api('issue'), 'all', array($this->org, $this->repo, $params));
		// error_log( json_encode($issues));
		return $issues;
	}

	public function search_issues($options=array()) {

		$default_options = array(
							// 'labels'=>'',
							'term' => '', 
							'state'=>'all',
							);
		
		$params = array_merge($default_options, $options);
		$state = $params['state'];
		$term = $params['term'];
		
		// $this->client->api->setPerPage(50);
		$paginator = new Github\ResultPager($this->client);
		

		// $issues = $this->client->api('issue')->all($this->org, $this->repo, $params);

		//TODO: Add page links instead of full list
		$issues = $paginator->fetchAll($this->client->api('issue'), 'find', array($this->org, $this->repo, $state, $term));
		$issues = $this->add_label_details( $issues['issues'] );
		// $issues = $client->api('issue')->find('KnpLabs', 'php-github-api', 'closed', 'bug');
		return $issues;
	}

	private function add_label_details($issues) {
		$labels_list = $this->client->api('issue')->labels()->all($this->org, $this->repo);
		// error_log(json_encode($labels));
		foreach ($issues as &$issue){
			$new_labels = array();
			foreach ($issue['labels'] as &$label){
				
				foreach ($labels_list as $label_item) {
					if ( $label_item['name'] === $label ) {
						$new_labels[] = $label_item;
						break;
					}
				}
			}
			$issue['labels'] = $new_labels;
		}
		return $issues;
	}


	public function get_milestones($options=array()) {

		$default_options = array(
							'state'=>'all',
							'page' => 1
							);

		$params = array_merge($default_options, $options );

		$paginator = new Github\ResultPager($this->client);

		//TODO: Add page links instead of full list
		$milestones = $paginator->fetchAll($this->client->api('issue')->milestones(), 'all', array($this->org, $this->repo, $params));

		foreach ($milestones as &$milestone) {
			$issues = $this->_get_milestone_info($milestone['number']);
			$milestone['issues'] = $issues;
		}

		return $milestones;
	}

	private function _get_milestone_info($number) {

		$paginator = new Github\ResultPager($this->client);
		$issues = $paginator->fetchAll($this->client->api('issue'), 'all', array($this->org, $this->repo, array('milestone'=>$number, 'state'=>'all')));

		return $issues;
	}

	public function check_for_settings($error=TRUE){
		if (	$this->repo && 
				$this->org && 
				$this->gh_username && 
				$this->gh_password
			)
			$this->has_settings = TRUE;

		return $this->has_settings;
	}

}