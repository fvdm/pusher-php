### Pusher-PHP

At the moment there is only one file _pusher.php_ to send messages to your [Pusher](http://pusher.com/) app from a PHP script.

Just require it and do like this:

```php
require 'pusher.php';
pusher( array(
	'appID'		=>	'1234',
	'authKey'	=>	'f843j838v49',
	'authSecret'	=>	'2kh4s9dv1e',
	'channel'		=>	'chat',
	'event'		=>	'user_text',
	'fields'		=>	array(
		'username'	=>	'johndoe',
		'time'		=>	date( 'H:i:s' ),
		'text'		=>	'hello world'
	)
));
?>```
