<?php

include __DIR__ . "/Platform.php";

/**
 * Platform Janch LIPI
 *
 * @author Tushar Kant <tushar.kant@kayako.com>
 */
class PlatformTester extends Platform
{
	// const MASTER_DOMAIN = 'kayakostage.net';
	// const MASTER_DOMAIN = 'kayakodev.net';
	// const MASTER_DOMAIN = 'kayako.com';

	/**
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 */
	public function GetInput($for)
	{
		echo $this->getColoredString("Please $for:", "yellow");
		$handle = fopen("php://stdin", "r");
		$line   = fgets($handle);
		$line   = trim($line);
		echo $this->getColoredString("You Entered: $line", "yellow");

		return $line;
	}

	/**
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 */
	public function GenerateRandomString($length = 10)
	{
		$characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString     = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}

		return $randomString;
	}

	/**
	 * @author Tushar Kant <tushar.kant@kayako.com>
	 */
	public function Menu($options)
	{
		echo $this->getColoredString("\n======== Please choose an option ========", "cyan");
		foreach ($options as $key => $value) {
			echo $this->getColoredString("$key. $value", "cyan");
		}

		echo $this->getColoredString("=========================================", "cyan");

		return strtolower($this->GetInput('choose your option'));
	}
}

$Platform = new PlatformTester();

echo "\n";

do {
	$option = $Platform->Menu(['a' => 'Create Instance', 'b' => 'Instance Actions', 'c' => 'Backup Actions',
							   'd' => 'Move Actions', 'e' => 'Domain Actions', 'f' => 'Alias Actions', 'g' => 'Availability of an Instance', 'h' => 'Region Actions',
							   'i' => 'Location Actions', 'j' => 'Provider Actions', 'k' => 'Synchronise Legacy Builds', 'l' => 'ElasticSearch Services Actions',
							   'm' => 'Redis Services Actions', 'n' => 'ElasticSearch Membership Actions', 'o' => 'Redis Membership Actions',
							   'p' => 'Pod Actions', 'r' => 'Shards Actions', 's' => 'Server Actions',
							   'q' => 'Quit']);

	switch ($option) {
		case 'a':
			$version_c = $Platform->Menu(['a' => 'K5 Instance', 'b' => 'K4 Instance']);

			switch ($version_c) {
				case 'a':
					$type      = $Platform->GetInput('enter instance Type, either TRIAL, ONDEMAND or SANDBOX');
					$parent_id = false;
					if ($type == 'SANDBOX') {
						$parent_id = $Platform->GetInput('enter instance parent_id');
					}

					$Platform->CreateInstance('IT', $Platform->GenerateRandomString(8), '5', 'Kayako Pvt Ltd', 'tushar.kant@kayako.com', 'some_password',
											  'Tushar Kant', 'fusion', 'fusionsaastrial', strtotime('+30 days'), 10, $type, $Platform::MASTER_DOMAIN, 'US', $parent_id);
					break;

				case 'b':
					$type = $Platform->GetInput('enter instance Type, either TRIAL or ONDEMAND');

					if ($type == 'ONDEMAND') {
						$Platform->CreateInstance('IT', $Platform->GenerateRandomString(8), '4', 'Kayako Pvt Ltd', 'tushar.kant@kayako.com', 'some_password',
												  'Tushar Kant', 'case', 'casesaas3', strtotime('+10 days'), 1, $type, $Platform::MASTER_DOMAIN, 'US');
					} else {
						$Platform->CreateInstance('IT', $Platform->GenerateRandomString(8), '4', 'Kayako Pvt Ltd', 'tushar.kant@kayako.com', 'some_password',
												  'Tushar Kant', 'fusion', 'fusionsaastrial', strtotime('+30 days'), 10, $type, $Platform::MASTER_DOMAIN, 'US');
					}

					break;

				default:
					continue;
			}
			break;

		case 'b':
			$instance_action = $Platform->Menu(['a' => 'Get all instances', 'b' => 'Get an instance', 'c' => 'Clone an instance',
												'd' => 'Update instance', 'e' => 'Update license of instance', 'f' => 'Enable an instance',
												'g' => 'Disable an instance', 'h' => 'Upgrade an instance', 'i' => 'Delete an instance',
												'j' => 'Import Database', 'k' => 'Update Admin Credential', 'l' => 'Reset instance config',
												'm' => 'Reindex Elastic-Search data for instance',
												'0' => 'Main Menu']);

			if ($instance_action == '0' || $instance_action == '') {
				continue;
			}

			$instance_id = $Platform->GetInput('enter instance id');

			switch ($instance_action) {
				case 'a':
					$Platform->InstanceActions($instance_id, 'getall');
					break;

				case 'b':
					$Platform->InstanceActions($instance_id, 'get');
					break;

				case 'c':
					$is_migration = $Platform->GetInput('mark this as migration: TRUE or FALSE');
					$Platform->InstanceActions($instance_id, 'clone', ['app_domain'   => $Platform->GenerateRandomString(5), 'is_migration' => $is_migration,
																	   'country_code' => 'US']);
					break;

				case 'd':
					$elastic_search_id = $Platform->GetInput('enter elastic search cluster id');
					$redis_shard_id    = $Platform->GetInput('enter redis shard id');
					$Platform->InstanceActions($instance_id, 'update', ['elastic_search_cluster_id' => $elastic_search_id, 'redis_shard_id' => $redis_shard_id]);
					break;

				case 'e':
					$type    = $Platform->GetInput('enter Type for instance: ONDEMAND or TRIAL');
					$upgrade = $Platform->GetInput('enter TRUE if you want to upgrade else FALSE');
					$Platform->UpdateLicense($instance_id, ['type'       => $type, 'upgrade' => $upgrade, 'company' => 'Updated Kayako', 'full_name' => 'Updated Name',
															'seat_count' => rand(1, 10), 'expiry' => strtotime('+' . rand(5, 15) . ' days'),
															'policy'     => json_encode(['lead_id' => '00Qp0000000uG2fEAE', 'v' => 'a', 'parmider_ji_ke_liye' => 'it works'])]);
					break;

				case 'f':
					$Platform->InstanceActions($instance_id, 'enable');
					break;

				case 'g':
					$Platform->InstanceActions($instance_id, 'disable');
					break;

				case 'h':
					$Platform->InstanceActions($instance_id, 'upgrade');
					break;

				case 'i':
					$Platform->InstanceActions($instance_id, 'delete');
					break;

				case 'j':
					$dump_url = $Platform->GetInput('enter dump_url');
					$Platform->ImportDatabase($instance_id, $dump_url);
					break;

				case 'k':
					$Platform->InstanceActions($instance_id, 'credential', ['username' => 'admin', 'password' => 'new_password']);
					break;

				case 'l':
					$option          = $Platform->GetInput('enter option to sync: ALL (Sync everything), CONFIG_VHOST (Sync only Novo configs and VHosts), DCS (Sync only DCS cron entires), DNS (Sync DNS entires)');
					$protected_cname = '';

					if (strtolower($option) == 'dns') {
						$protected_cname = $Platform->GetInput('enter protected_cname which is provided by DOSARREST, if left empty then it will sync the dns records with the default entries present in Platform\'s DB');
					}

					$Platform->InstanceActions($instance_id, 'config', ['option' => $option, 'protected_cname' => $protected_cname]);
					break;

				case 'm':
					$Platform->InstanceActions($instance_id, 'reindex');
					break;

				default:
					continue;
			}
			break;

		case 'c':
			$backup_action = $Platform->Menu(['a' => 'Retrieve all backups', 'b' => 'Retrieve a backup', 'c' => 'Backup an instance',
											  'd' => 'Delete a backup', 'e' => 'Delete all backups', 'm' => 'Main Menu']);

			if ($backup_action == 'm' || $backup_action == '') {
				continue;
			}

			$instance_id = $Platform->GetInput('enter instance id');

			switch ($backup_action) {
				case 'a':
					$Platform->InstanceBackup($instance_id, 'RetriveAllBackup');
					break;

				case 'b':
					$backup_id = $Platform->GetInput('enter backup id');
					$Platform->InstanceBackup($instance_id, 'RetriveAnBackup', ['backupid' => $backup_id]);
					break;

				case 'c':
					$Platform->InstanceBackup($instance_id, 'BackupAnInstance');
					break;

				case 'd':
					$backup_id = $Platform->GetInput('enter backup id');
					$Platform->InstanceBackup($instance_id, 'DeleteABackup', ['backupid' => $backup_id]);
					break;

				case 'e':
					$Platform->InstanceBackup($instance_id, 'DeleteAllBackup');
					break;

				default:
					continue;
			}
			break;

		case 'd':
			$move_action = $Platform->Menu(['a' => 'Move an instance', 'b' => 'Cancel move for and instance', 'c' => 'Retrieve move status', 'd' => 'Move an instance Tushars way', 'm' => 'Main Menu']);

			if ($move_action == 'm' || $move_action == '') {
				continue;
			}

			$instance_id = $Platform->GetInput('enter instance id');

			switch ($move_action) {
				case 'a':
					$target_pod = $Platform->GetInput('enter target Pod ID');
					$Platform->InstanceMove($instance_id, 'MoveAnInstance', $target_pod);
					break;

				case 'b':
					$Platform->InstanceMove($instance_id, 'CancelAnInstance');
					break;

				case 'c':
					$Platform->InstanceMove($instance_id, 'RetriveMoveStatus');
					break;

				case 'd':
					$target_pod = $Platform->GetInput('enter target Pod ID');
					$Platform->InstanceMove($instance_id, 'MoveAnInstance', $target_pod, true);
					break;

				default:
					continue;
			}
			break;

		case 'e':
			$domain_action = $Platform->Menu(['a' => 'Retrieve domains', 'b' => 'Rename an instance', 'c' => 'Delete a historical domain', 'd' => 'Delete all historical domains',
											  'm' => 'Main Menu']);

			if ($domain_action == 'm' || $domain_action == '') {
				continue;
			}

			$instance_id = $Platform->GetInput('enter instance id');

			switch ($domain_action) {
				case 'a':
					$Platform->DomainActions($instance_id);
					break;

				case 'b':
					$Platform->InstanceActions($instance_id, 'rename', ['newAppDomain' => 'updated' . $Platform->GenerateRandomString(4)]);
					break;

				case 'c':
					$domain_id = $Platform->GetInput('enter domain_id');
					$Platform->DomainActions($instance_id, 'DeleteAHistoricalDomain', ['domain_id' => $domain_id]);
					break;

				case 'd':
					$Platform->DomainActions($instance_id, 'DeleteAllHistoricalDomains');
					break;

				default:
					continue;
			}
			break;

		case 'f':
			$alias_action = $Platform->Menu(['a' => 'Retrieve all aliases', 'b' => 'Retrieve an alias', 'c' => 'Create an alias', 'd' => 'Update an alias',
											 'e' => 'Delete an alias', 'f' => 'Delete all aliases', 'm' => 'Main Menu']);

			if ($alias_action == 'm' || $alias_action == '') {
				continue;
			}

			$instance_id = $Platform->GetInput('enter instance id');

			switch ($alias_action) {
				case 'a':
					$Platform->InstanceAliasActions($instance_id, 'getallaliases');
					break;

				case 'b':
					$alias_id = $Platform->GetInput('enter alias id');
					$Platform->InstanceAliasActions($instance_id, 'get', $alias_id);
					break;

				case 'c':
					$Platform->InstanceAliasActions($instance_id, 'create', 1, ['app_domain' => 'alias' . strtolower($Platform->GenerateRandomString(5))]);
					break;

				case 'd':
					$alias_id    = $Platform->GetInput('enter alias id');
					$cname       = $Platform->GetInput('enter CNAME for instance, else press enter');
					$certificate = '';
					$privateKey  = '';

					if (isset($cname)) {
						$certificate = $Platform->GetInput('enter certificate for instance cname');
						$privateKey  = $Platform->GetInput('enter private_key for instance cname');
					}

					if (isset($cname)) {
						$Platform->InstanceAliasActions($instance_id, 'update', $alias_id, ['app_domain'  => 'new_alias' . strtolower($Platform->GenerateRandomString(4)), 'cname' => $cname,
																							'certificate' => $certificate,
																							'private_key' => $privateKey]);
					} else {
						$Platform->InstanceAliasActions($instance_id, 'update', $alias_id, ['app_domain' => 'new_alias' . strtolower($Platform->GenerateRandomString(4))]);
					}

					break;

				case 'e':
					$alias_id = $Platform->GetInput('enter alias id');
					$Platform->InstanceAliasActions($instance_id, 'delete', $alias_id);
					break;

				case 'f':
					$Platform->InstanceAliasActions($instance_id, 'deleteall');
					break;

				default:
					continue;
			}
			break;

		case 'g':
			$app_domain    = $Platform->GetInput('enter app-domain');
			$master_domain = $Platform->GetInput('enter master-domain or just `Enter` for default: ' . $Platform::MASTER_DOMAIN);

			if ($master_domain == '') {
				$Platform->CheckAvailability($app_domain, $Platform::MASTER_DOMAIN);
			} else {
				$Platform->CheckAvailability($app_domain, $master_domain);
			}
			break;

		case 'h':
			$region_action = $Platform->Menu(['a' => 'Retrieve all regions', 'b' => 'Retrieve a region', 'c' => 'Add a new region', 'd' => 'Delete a region',
											  'm' => 'Main Menu']);

			if ($region_action == 'm' || $region_action == '') {
				continue;
			}

			switch ($region_action) {
				case 'a':
					$Platform->RegionActions('RetrieveAllRegion');
					break;

				case 'b':
					$region_id = $Platform->GetInput('enter region_id');
					$Platform->RegionActions('RetrieveARegion', $region_id);
					break;

				case 'c':
					$region_name  = $Platform->GetInput('enter region name');
					$country_code = $Platform->GetInput('enter country code');
					$Platform->RegionActions('AddRegion', 1, $region_name, $country_code);
					break;

				case 'd':
					$region_id = $Platform->GetInput('enter region_id');
					$Platform->RegionActions('DeleteARegion', $region_id);
					break;

				default:
					continue;
			}
			break;

		case 'i':
			$location_action = $Platform->Menu(['a' => 'Retrieve all locations', 'b' => 'Retrieve a location', 'c' => 'Add a new location', 'd' => 'Delete a location',
												'm' => 'Main Menu']);

			if ($location_action == 'm' || $location_action == '') {
				continue;
			}

			switch ($location_action) {
				case 'a':
					$Platform->LocationActions();
					break;

				case 'b':
					$location_id = $Platform->GetInput('enter location id');
					$Platform->LocationActions('RetrieveALocation', $location_id);
					break;

				case 'c':
					$location_name = $Platform->GetInput('enter location name');
					$region_id     = $Platform->GetInput('enter region_id');
					$Platform->LocationActions('AddLocation', 1, $location_name, $region_id);
					break;

				case 'd':
					$location_id = $Platform->GetInput('enter location id');
					$Platform->LocationActions('DeleteALocation', $location_id);
					break;

				default:
					continue;
			}
			break;

		case 'j':
			$provider_action = $Platform->Menu(['a' => 'Retrieve all providers', 'b' => 'Retrieve a provider', 'c' => 'Add a new provider', 'd' => 'Delete a provider',
												'm' => 'Main Menu']);

			if ($provider_action == 'm' || $provider_action == '') {
				continue;
			}

			switch ($provider_action) {
				case 'a':
					$Platform->ProviderActions('RetrieveAllProvider');
					break;

				case 'b':
					$provider_id = $Platform->GetInput('enter provider_id');
					$Platform->ProviderActions('RetrieveAProvider', $provider_id);
					break;

				case 'c':
					$provider_name = $Platform->GetInput('enter provider_name');
					$Platform->ProviderActions('AddProvider', 1, $provider_name);
					break;

				case 'd':
					$provider_id = $Platform->GetInput('enter provider_id');
					$Platform->ProviderActions('DeleteAProvider', $provider_id);
					break;

				default:
					continue;
			}
			break;

		case 'k':
			$plan_name    = $Platform->GetInput('enter plan name');
			$package_link = $Platform->GetInput('enter package_link');
			$Platform->SyncBuild($plan_name, $package_link);
			break;

		case 'l':
			$es_services_action = $Platform->Menu(['a' => 'Retrieve all ES services', 'b' => 'Retrieve an ES service', 'c' => 'Add a new ES service', 'd' => 'Update an ES service',
												   'e' => 'Enable an ES service', 'f' => 'Disable an ES service', 'g' => 'Delete an ES service',
												   'm' => 'Main Menu']);

			if ($es_services_action == 'm' || $es_services_action == '') {
				continue;
			}

			switch ($es_services_action) {
				case 'a':
					$Platform->ServiceActions('RetrieveAll', 'elasticsearch');
					break;

				case 'b':
					$service_id = $Platform->GetInput('enter service_id');
					$Platform->ServiceActions('RetrieveAService', 'elasticsearch', ['service_id' => $service_id]);
					break;

				case 'c':
					$service_name = $Platform->GetInput('enter service_name');
					$pod_id       = $Platform->GetInput('enter pod_id');
					$port         = $Platform->GetInput('enter port');
					$Platform->ServiceActions('AddANewService', 'elasticsearch', ['service_name' => $service_name, 'pod_id' => $pod_id, 'port' => $port]);
					break;

				case 'd':
					$service_name = $Platform->GetInput('enter service_name');
					$port         = $Platform->GetInput('enter port');
					$Platform->ServiceActions('UpdateAService', 'elasticsearch', ['service_name' => $service_name, 'port' => $port]);
					break;

				case 'e':
					$service_id = $Platform->GetInput('enter service_id');
					$Platform->ServiceActions('EnableAService', 'elasticsearch', ['service_id' => $service_id]);
					break;

				case 'f':
					$service_id = $Platform->GetInput('enter service_id');
					$Platform->ServiceActions('DisableAService', 'elasticsearch', ['service_id' => $service_id]);
					break;

				case 'g':
					$service_id = $Platform->GetInput('enter service_id');
					$Platform->ServiceActions('DeleteAService', 'elasticsearch', ['service_id' => $service_id]);
					break;

				default:
					continue;
			}

			break;

		case 'm':
			$redis_services_action = $Platform->Menu(['a' => 'Retrieve all Redis services', 'b' => 'Retrieve an Redis service', 'c' => 'Add a new Redis service', 'd' => 'Update an Redis service',
													  'e' => 'Enable an Redis service', 'f' => 'Disable an Redis service', 'g' => 'Delete an Redis service',
													  'm' => 'Main Menu']);

			if ($redis_services_action == 'm' || $redis_services_action == '') {
				continue;
			}

			switch ($redis_services_action) {
				case 'a':
					$Platform->ServiceActions('RetrieveAll', 'redis');
					break;

				case 'b':
					$service_id = $Platform->GetInput('enter service_id');
					$Platform->ServiceActions('RetrieveAService', 'redis', ['service_id' => $service_id]);
					break;

				case 'c':
					$service_name = $Platform->GetInput('enter service_name');
					$pod_id       = $Platform->GetInput('enter pod_id');
					$port         = $Platform->GetInput('enter port');
					$Platform->ServiceActions('AddANewService', 'redis', ['service_name' => $service_name, 'pod_id' => $pod_id, 'port' => $port]);
					break;

				case 'd':
					$service_name = $Platform->GetInput('enter service_name');
					$port         = $Platform->GetInput('enter port');
					$Platform->ServiceActions('UpdateAService', 'redis', ['service_name' => $service_name, 'port' => $port]);
					break;

				case 'e':
					$service_id = $Platform->GetInput('enter service_id');
					$Platform->ServiceActions('EnableAService', 'redis', ['service_id' => $service_id]);
					break;

				case 'f':
					$service_id = $Platform->GetInput('enter service_id');
					$Platform->ServiceActions('DisableAService', 'redis', ['service_id' => $service_id]);
					break;

				case 'g':
					$service_id = $Platform->GetInput('enter service_id');
					$Platform->ServiceActions('DeleteAService', 'redis', ['service_id' => $service_id]);
					break;

				default:
					continue;
			}

			break;

		case 'n':
			$es_membership_action = $Platform->Menu(['a' => 'Retrieve all ES members', 'b' => 'Add a new ES member', 'c' => 'Delete an ES  member', 'd' => 'Delete all ES members',
													 'm' => 'Main Menu']);

			if ($es_membership_action == 'm' || $es_membership_action == '') {
				continue;
			}

			$service_id = $Platform->GetInput('enter service_id');

			switch ($es_membership_action) {
				case 'a':
					$Platform->ServiceMembershipActions('RetrieveAll', 'elasticsearch', ['service_id' => $service_id]);
					break;

				case 'b':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServiceMembershipActions('AddANewMember', 'elasticsearch', ['service_id' => $service_id, 'server_id' => $server_id]);
					break;

				case 'c':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServiceMembershipActions('DeleteAMember', 'elasticsearch', ['service_id' => $service_id, 'server_id' => $server_id]);
					break;

				case 'd':
					$Platform->ServiceMembershipActions('DeleteAllMember', 'elasticsearch', ['service_id' => $service_id]);
					break;

				default:
					continue;
			}
			break;

		case 'o':
			$redis_membership_action = $Platform->Menu(['a' => 'Retrieve all Redis members', 'b' => 'Add a new Redis member', 'c' => 'Delete an Redis  member',
														'd' => 'Delete all Redis members', 'm' => 'Main Menu']);

			if ($redis_membership_action == 'm' || $redis_membership_action == '') {
				continue;
			}

			$service_id = $Platform->GetInput('enter service_id');

			switch ($redis_membership_action) {
				case 'a':
					$Platform->ServiceMembershipActions('RetrieveAll', 'redis', ['service_id' => $service_id]);
					break;

				case 'b':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServiceMembershipActions('AddANewMember', 'redis', ['service_id' => $service_id, 'server_id' => $server_id]);
					break;

				case 'c':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServiceMembershipActions('DeleteAMember', 'redis', ['service_id' => $service_id, 'server_id' => $server_id]);
					break;

				case 'd':
					$Platform->ServiceMembershipActions('DeleteAllMember', 'redis', ['service_id' => $service_id]);
					break;

				default:
					continue;
			}
			break;

		case 'p':
			$pod_actions = $Platform->Menu(['a' => 'Retrieve all pods', 'b' => 'Retrieve a pod', 'c' => 'Retrieve all servers in a pod',
											'd' => 'Retrieve all instances in a pod', 'e' => 'Retrieve statistics of all instances in a pod', 'f' => 'Retrieve least used pod',
											'g' => 'Add a new pod', 'h' => 'Update a pod', 'i' => 'Delete a pod', 'j' => 'Delete cache of a pod',
											'k' => 'Reset config of all instances on this pod', 'l' => 'Update pod state',
											'm' => 'Main Menu']);

			if ($pod_actions == 'm' || $pod_actions == '') {
				continue;
			}

			switch ($pod_actions) {
				case 'a':
					$Platform->PodActions();
					break;

				case 'b':
					$pod_id = $Platform->GetInput('enter pod_id');
					$Platform->PodActions('RetrieveAPod', $pod_id);
					break;

				case 'c':
					$pod_id = $Platform->GetInput('enter pod_id');
					$Platform->PodActions('RetrieveAllServersInAPod', $pod_id);
					break;

				case 'd':
					$pod_id = $Platform->GetInput('enter pod_id');
					$Platform->PodActions('RetrieveAllInstancesInAPod', $pod_id);
					break;

				case 'e':
					$pod_id = $Platform->GetInput('enter pod_id');
					$Platform->PodActions('RetrieveStatisticsOfAllInstancesInAPod', $pod_id);
					break;

				case 'f':
					$pod_type     = $Platform->GetInput('enter Pod Type');
					$version      = $Platform->GetInput('enter version of product');
					$country_code = $Platform->GetInput('enter country_code');
					$Platform->PodActions('LeastUsedPod', 1, 'name', $pod_type, '', 1, 1, 1, 1, 1, $version, $country_code);
					break;

				case 'g':
					$values = ['type', 'environment', 'region_id', 'enable_ondemand_routing', 'enable_trial_routing', 'cdn_url', 'seat_cap', 'customer_cap'];
					$args   = [];
					foreach ($values as $key => $value) {
						$args[$value] = $Platform->GetInput("enter $value");
					}

					$Platform->PodActions('AddPod', '', '', $args['type'], $args['environment'], $args['region_id'], $args['enable_ondemand_routing'],
										  $args['enable_trial_routing'], $args['seat_cap'], $args['customer_cap'], '', '', $args['cdn_url']);
					break;

				case 'h':
					$values = ['pod_id', 'enable_ondemand_routing', 'enable_trial_routing', 'cdn_url'];
					$args   = [];
					foreach ($values as $key => $value) {
						$args[$value] = $Platform->GetInput("enter $value");
					}

					$Platform->PodActions('UpdatePod', $args['pod_id'], '', '', '', '', $args['enable_ondemand_routing'], $args['enable_trial_routing'], '', '', '', '',
										  $args['cdn_url']);
					break;

				case 'i':
					$pod_id = $Platform->GetInput('enter pod_id');
					$Platform->PodActions('DeletePod', $pod_id);
					break;

				case 'j':
					$pod_id = $Platform->GetInput('enter pod_id');
					$Platform->PodActions('DeleteCacheOfAPod', $pod_id);
					break;

				case 'k':
					$pod_id     = $Platform->GetInput('enter pod_id');
					$option     = $Platform->GetInput('enter option to sync: ALL (Sync everything), CONFIG_VHOST (Sync only Novo configs and VHosts), DCS (Sync only DCS cron entires), SEARCH (re-index Elastic Search), LB_RECORDS (Sync lb A record), PURGE_BUFFERS (Purge all buffered instances in this POD)');
					$lb_records = '';
					if (strtolower($option) == 'lb_records' || strtolower($option) == 'all') {
						$lb_records = $Platform->GetInput('enter lb\'s A records in single list JSON for example: ["127.0.0.1", "127.0.0.2"], if left blank then the A records will be synced with the entries present in database for this POD');
					}

					$Platform->PodActions('ResetConfigOfAllInstancesOnThisPod', $pod_id, 'name', 'type', 'env', 'region', 'on', 'trial', 'seat', 'cus', 'ver', 'cc', 'cdn', 'state', $option, $lb_records);
					break;

				case 'l':
					$pod_id = $Platform->GetInput('enter pod_id');
					$state  = $Platform->GetInput('enter state');
					$Platform->PodActions('UpdatePodState', $pod_id, 'name', 'type', 'env', 'region', 'on', 'trial', 'seat', 'cus', 'ver', 'cc', 'cdn', $state);
					break;

				default:
					continue;
			}
			break;

		case 'r':
			$shard_actions = $Platform->Menu(['a' => 'Retrieve all shards', 'b' => 'Retrieve an existing shard', 'c' => 'Retrieve all servers in a shard',
											  'd' => 'Add a new shard', 'e' => 'Sync a shard', 'f' => 'Delete a shard',
											  'm' => 'Main Menu']);

			if ($shard_actions == 'm' || $shard_actions == '') {
				continue;
			}

			switch ($shard_actions) {
				case 'a':
					$Platform->ShardActions();
					break;

				case 'b':
					$shard_id = $Platform->GetInput('enter shard_id');
					$Platform->ShardActions('RetrieveAShard', $shard_id);
					break;

				case 'c':
					$shard_id = $Platform->GetInput('enter shard_id');
					$Platform->ShardActions('RetrieveAllServersInShard', $shard_id);
					break;

				case 'd':
					$values = ['name', 'pod_id', 'root_username'];
					$args   = [];
					foreach ($values as $key => $value) {
						$args[$value] = $Platform->GetInput("enter shards's $value");
					}

					$Platform->ShardActions('AddShard', '', $args['name'], $args['pod_id'], $args['root_username']);
					break;

				case 'e':
					$shard_id = $Platform->GetInput('enter shard_id');
					$Platform->ShardActions('SyncAShard', $shard_id);
					break;

				case 'f':
					$shard_id = $Platform->GetInput('enter shard_id');
					$Platform->ShardActions('DeleteAShard', $shard_id);
					break;

				default:
					continue;
			}
			break;

		case 's':
			$server_actions = $Platform->Menu(['a' => 'Retrieve servers', 'b' => 'Retrieve a server', 'c' => 'Add a new server',
											   'd' => 'Get config of a server', 'e' => 'Get token of a server', 'f' => 'Provision a server',
											   'g' => 'Enable a server', 'h' => 'Disable a server', 'i' => 'Sync a server', 'j' => 'Delete/Terminate a server',
											   'm' => 'Main Menu']);

			if ($server_actions == 'm' || $server_actions == '') {
				continue;
			}

			switch ($server_actions) {
				case 'a':
					$Platform->ServerActions();
					break;

				case 'b':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServerActions('RetrieveAServer', $server_id);
					break;

				case 'c':
					$values = ['name', 'type', 'pod_id', 'location_id', 'provider_id', 'hostname', 'public_ip', 'private_ip', 'shard_id', 'parent_id', 'role', 'service', 'is_enabled'];
					$args   = [];
					foreach ($values as $key => $value) {
						$args[$value] = $Platform->GetInput("enter server's $value");
					}

					$Platform->ServerActions('AddServer', '', '', $args['name'], $args['type'], $args['pod_id'], $args['location_id'], $args['provider_id'], $args['public_ip'],
											 $args['private_ip'], $args['shard_id'], $args['parent_id'], $args['role'], $args['service'], $args['is_enabled'], $args['hostname']);
					break;

				case 'd':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServerActions('GetConfigOfAServer', $server_id);
					break;

				case 'e':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServerActions('GetTokenOfAServer', $server_id);
					break;

				case 'f':
					$server_id    = $Platform->GetInput('enter server_id');
					$server_token = $Platform->GetInput('enter server\'s token');
					$Platform->ServerActions('ProvisionAServer', $server_id, $server_token);
					break;

				case 'g':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServerActions('EnableAServer', $server_id);
					break;

				case 'h':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServerActions('DisableAServer', $server_id);
					break;

				case 'i':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServerActions('SyncAServer', $server_id);
					break;

				case 'j':
					$server_id = $Platform->GetInput('enter server_id');
					$Platform->ServerActions('DeleteAServer', $server_id);
					break;

				default:
					continue;
			}
			break;

		case 'q':
			echo $Platform->getColoredString("Platform jaanch lipi ka prayog karne ke liye dhynawaad..", "light_purple");
			exit();

		default:
			continue;
	}
//	die();
} while (1);
