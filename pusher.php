<?php
# License: Creative Commons, Attribution 3.0 Unported (CC BY 3.0)
# Details: http://creativecommons.org/licenses/by/3.0/
# Source:  https://github.com/fvdm/pusher-php/
# Version: 1.0.0

# pusher( Array $settings )
#  appID		your Pusher app ID (API access tab)
#  channel		the channel name to push to
#  event		the event to trigger
#  fields		array with variables to push
#  authKey		your Pusher auth key (API access tab)
#  authSecret		your Pusher auth secret (API access tab)

# optional:
#  excludeSocketID	a client socket ID that must not be pushed

function pusher( $settings )
{
	# basics
	$url = 'https://api.pusherapp.com';
	
	$path = '/apps/'. $settings['appID'] .'/channels/'. $settings['channel'] .'/events';
	$body = json_encode( $settings['fields'] );
	$vars = array(
		'name'			=>	$settings['event'],
		'auth_key'		=>	$settings['authKey'],
		'auth_timestamp'	=>	time(),
		'auth_version'		=>	'1.0',
		'body_md5'		=>	md5($body)
	);
	
	# exclude a socket
	if( !empty($settings['excludeSocketID']) )
	{
		$vars['socket_id']	=	$settings['excludeSocketID'];
	}
	
	# signature
	ksort($vars);
	$query = http_build_query($vars);
	
	$sign = "POST\n";
	$sign .= "$path\n";
	$sign .= $query;
	
	$signature = hash_hmac( 'sha256', $sign, $settings['authSecret'], false );
	
	# set last vars
	$signed_query = $query .'&auth_signature='. $signature;
	$url .= $path .'?'. $signed_query;
	
	# push!
	$c = curl_init();
	curl_setopt_array( $c, array(
		CURLOPT_URL		=>	$url,
		CURLOPT_RETURNTRANSFER	=>	true,
		CURLOPT_TIMEOUT		=>	5,
		CURLOPT_CONNECTTIMEOUT	=>	5,
		CURLOPT_POST		=>	true,
		CURLOPT_POSTFIELDS	=>	$body,
		CURLOPT_USERAGENT	=>	'Pusher-PHP/1.0.0 (https://github.com/fvdm/pusher-php/)',
		CURLOPT_SSL_VERIFYHOST	=>	true,
		CURLOPT_HTTPHEADER	=>	array( 'Content-Type: application/json' )
	));
	$data = curl_exec($c);
	$info = curl_getinfo($c);
	$errno = curl_errno($c);
	$errstr = curl_error($c);
	curl_close($c);
	
	# check
	if( $info['http_code'] == 202 )
	{
		return $data;
	}
	
	return "$errno: $errstr\n$data";
}
?>