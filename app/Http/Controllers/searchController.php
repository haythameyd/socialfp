<?php

namespace App\Http\Controllers;

use Abraham\TwitterOAuth\TwitterOAuth;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Browser\Casper;

class searchController extends Controller
{

    public function tweetsinf($tweets)
    {
      $count=0;
      $retweets=0;
      $likes=0;
      foreach($tweets->statuses as $obj){
         $retweets=$retweets+$obj->retweet_count;
         $likes=$likes+$obj->favorite_count;
         $count=$count+1;
      }
      $data=array($likes,$retweets,$count);
      return $data;
    }

    public function twittersearch($query)
    {
      $consumer="3Hsr7qk9HEYseF9bpJA07Go6H";
      $consumersecret="q1TjkWCHcPKrJn0YCmtKMwIHaXURI7ip3z5eDxDd4G0GxLsUOi";
      $accesstoken = "3803581341-FAlmx3CbE2amgcMyD2AkGYuLdRa7kZ3QcDSxj5a";
      $accesstokensecret = "1JtROKIjMaIhP6IJGMPGbTyRTF9WMyq1RGclpdvcKzZWD";
      $twitter= new TwitterOAuth($consumer,$consumersecret,$accesstoken,$accesstokensecret);
      $url="search/tweets";
      $tweets = $twitter->get($url, array('q'=>$query,'result_type'=>'recent','count'=>'200'));
      return $tweets;
    }

    public function gtrendsdisplay($query)
    {
      $output=$query;
      $casper = new Casper();

      $casper->setOptions(array(
          'ignore-ssl-errors' => 'yes'
      ));
      $casper->setUserAgent('Mozilla/4.0 (comptible; MSIE 6.0; Windows NT 5.1)');
      $url='https://www.google.com/trends/explore#q='.$query;

      $casper->start($url);


      $casper->wait(5000);
      $casper->captureSelector('#reportMain', $output);
      $casper->capturePage('pagesample.png');

      $casper->run();
      $regex = '#\<div id="reportMain"\>(.+?)\<\/div\>#s';
      preg_match($regex, $output, $matches);

      return $output;
    }

    public function display(Request $request)
    {
      $tweets="";
      $trends="Nothing until now";
      $keyword = $request->input('keyword');
      $tweets=$this->twittersearch($keyword);
      $tweetsinfo=$this->tweetsinf($tweets);
      //$trends=$this->gtrendsdisplay($keyword);
      $data=array('keyword'=>$keyword,'tweets'=>$tweets,'tweets_info'=>$tweetsinfo,'trends'=>$trends);
      return view('results',$data);
    }


}
