<?php
# Simple configuration;
$OLD_CERTIFICATE = 'old-certificate-name';
$NEW_CERTIFICATE = 'new-certificate-name';
require('/usr/share/php/AWSSDKforPHP/sdk.class.php');
$ec2 = new AmazonEC2();
$elb = new AmazonELB();
$iam = new AmazonIAM();
$response = $iam->get_server_certificate($NEW_CERTIFICATE);
$response_available_regions = $ec2->describe_regions();
$available_regions = Array(AmazonELB::REGION_US_E1, AmazonELB::REGION_US_W1, AmazonELB::REGION_EU_W1, AmazonELB::REGION_APAC_SE1, AmazonELB::REGION_APAC_SE2, AmazonELB::REGION_APAC_NE1, AmazonELB::REGION_SA_E1);
foreach($available_regions as $region) {
	$elb->set_region($region);
	$list_of_elbs = $elb->describe_load_balancers();
	$elbs = $list_of_elbs->body->to_stdClass();
	foreach($elbs->DescribeLoadBalancersResult->LoadBalancerDescriptions->member as $single_elb) {
		echo($single_elb->LoadBalancerName ."\n");
		foreach($single_elb->ListenerDescriptions->member as $single_listener) {
			if ($single_listener->SSLCertificateId != "" ) {
				$pos = strpos($single_listener->SSLCertificateId, $OLD_CERTIFICATE);
				if ($pos === false) {
					echo("\t- No need to replace: " . $single_listener->SSLCertificateId ."\n");
				} else {
					echo("\t- Replacing certificate for port " .$single_listener->LoadBalancerPort. "...");
					$elb->set_load_balancer_listener_ssl_certificate($single_elb->LoadBalancerName, $single_listener->LoadBalancerPort, $response->body->GetServerCertificateResult->ServerCertificate->ServerCertificateMetadata->Arn);
					echo(" Done!\n");
				}
			}
		}
	}
}
?>
