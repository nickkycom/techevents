<?
// Based on https://github.com/J7mbo/twitter-api-php
// RETURNS TWEET (id) FROM ACCORDING HASHTAG (eventid)

require_once('twitter-api-php/TwitterAPIExchange.php');
$settings = array(
    'oauth_access_token' => "633599938-ctBDmI4UCxlvOEq9prVGurKZBeVTtr3NeEl3RRjQ",
    'oauth_access_token_secret' => "edPioiiiqunhBtioQsqJBMouvpjuwfMsMoacd7pn689aX",
    'consumer_key' => "2cvCpphITyIZGsxEfOcA",
    'consumer_secret' => "OkpPPTl9NaIWLF5HTieTkYMXkilddSMcMrYJjCu4io"
);

// processing parameters
$id = ($_GET['id']);
$id = HTMLSpecialChars($id);
$eventid = ($_GET['eventid']);
$eventid = HTMLSpecialChars($eventid);

$url = 'https://api.twitter.com/1.1/search/tweets.json';

//Choosing getfield according to eventID

switch ($eventid) {
    case 0:
		//mwc
        $getfield = '?q=%23mwc13&count=21';
		$file = 'mwc_tweets.db';
        break;
    case 1:
		//cebit
        $getfield = '?q=%23cebit&count=21';
		$file = 'cebit_tweets.db';
        break;
    case 2:
		//ces
		$getfield = '?q=%23ces&count=21';
		$file = 'ces_tweets.db';
        break;
    case 3:
		//computex
		$getfield = '?q=%23computex&count=21';
		$file = 'computex_tweets.db';
        break;
    case 4:
		//e3
		$getfield = '?q=%23e3&count=21';
		$file = 'e3_tweets.db';
        break;
    case 99:
		//recache all stuff - just delete the files and it'll work
		@unlink('mwc_tweets.db');
		@unlink('cebit_tweets.db');
		@unlink('ces_tweets.db');
		@unlink('e3_tweets.db');
		@unlink('computex_tweets.db');
        break;
}

$requestMethod = 'GET';
//only if file's not there, bug the API
if (!file_exists($file)) { 
	$twitter = new TwitterAPIExchange($settings);
$string = json_decode($twitter->setGetfield($getfield)
             ->buildOauth($url, $requestMethod)
             ->performRequest(),$assoc = TRUE);
$results = serialize($string);
file_put_contents($file, $results, LOCK_EX);
//caching
}
$raw=file_get_contents($file);
$string=unserialize($raw);

$k=0;

foreach($string['statuses'] as $items)
{
	$k++;
	//need to save them before major replace
	$image_url=$items['user']['profile_image_url'];
	$tweet_url='"https://twitter.com/'.$items['user']['screen_name'].'/status/'.$items['id_str'].'"';
	$formattedtweet = '<STRONG><a href=URLLLL2>'.$items['user']['name']. '</a></STRONG><BR />'.'<IMG CLASS="tweetimg" SRC=URLLLLL ALT="Profile pic" >'.$items['text'];
	if ($k==$id) break;
}

# Make links active
$formattedtweet = preg_replace("/(http:\/\/|(www\.))(([^\s<]{4,68})[^\s<]*)/", '<a href="http://$2$3" target="_blank">$1$2$4</a>', $formattedtweet);

# Linkify user mentions
$formattedtweet = preg_replace("/@(\w+)/", '<a href="http://www.twitter.com/$1" target="_blank">@$1</a>', $formattedtweet);

# Linkify tags
$formattedtweet = preg_replace("/#(\w+)/", '<a href="https://twitter.com/search?q=$1" target="_blank">#$1</a>', $formattedtweet);

# Pic URL - GET BACK IT SO THE PREGS WON'T SCREW IT UP
$formattedtweet = str_replace("URLLLLL", $image_url, $formattedtweet);
$formattedtweet = str_replace("URLLLL2", $tweet_url, $formattedtweet);

echo($formattedtweet);
//echo "<pre>" . var_dump($items) . "</pre>";

?>