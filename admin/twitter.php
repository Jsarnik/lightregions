<?php
require_once('/lib/codebird/codebird.php');

class Twitter
{
    protected $consumer_key = "0PZAvCERjOAExHWYFWTPn8VIU";
    protected $consumer_secret = "shIl6syo3va8LEmbFaPx7rdsGClqLrVDTxqVSNhc76v1YePTGF";
    protected $access_token = "3171108409-WkbEpNbjDWlvYtonMJeF5k1RcP4tOVkyXSV2FCX";
    protected $access_secret = "81Kw0xulUbynp4DjTbRGECX1fPQLuLc8BQ9rd8iOdW7KS";
    protected $cb;

    public function __construct()
    {
        // Fetch new Twitter Instance

	$cb = \Codebird\Codebird::getInstance();
    $cb->setConsumerKey($this->consumer_key, $this->consumer_secret);

        // Set access token

    $cb->setToken($this->access_token, $this->access_secret);

    }

    public function tweet($message)
    {    

    $reply = $cb->media_upload([
    'media' => 'http://lightregions.com/images/music/Lady_Gaga.jpg'
    ]);

    return $reply;

	//$params = array(
	//  'status' => $message
	//);
	//	$reply = $this->cb->statuses_update($params);
    	//$reply = $cb->statuses_update($message);
    	//return $reply;
        //return $this->cb->statuses_update(['status' => $message]);
    //    return $reply;
    //}

}
?>