<?php
require 'btn.php';

//TWITTER
//gets html code of twitter post(and inserts it in db if it's not already there)
function get_tweet_html($args){
    global $wpdb;
    $q=$wpdb->prepare("SELECT * from ".$wpdb->prefix."social_media_posts WHERE post_id='%s';",$args['tweet_id']);
    $res=$wpdb->get_row($q,ARRAY_A);
    if($res!=null){
       echo($res['post_html']);
        return;
    }
    else{
        $t=call_tweet($args);
        $html=show_tweet($t);
        echo $html;
        sm_instert_into_db($args['tweet_id'],$html);
        return;
    }

}


//prints out tweet's html on page
function show_tweet($par){
    $html='
    <div class="tw_sms_wrapper">
        <blockquote>
            <div class="tw_sms_header">
                <div class="tw_sms_brand">
                    <a href="'.$par['profile_url'].'">Follow</a>
                </div>
                <div class="tw_sms_author">
                    <span class="tw_sms_author-image">
                        <a href="'.$par['profile_url'].'"><img src="'.$par['avatar_url'].'" alt=""></a>
                    </span>
                    <div class="tw_sms_author-link">
                        <span class="tw_sms_author-name">
                            <a href="'.$par['profile_url'].'" class="tw_sms_author"><p>'.$par['full_name'].'</p></a></br>
                            <span class="tw_sms_screen-name">
                            <a href="'.$par['profile_url'].'" class="tw_sms_name"><p>@'.$par['screen_name'].'</p></a>
                            </span>
                        </span>
                    </div>
                </div>

            </div>

            <div class="tw_sms_body">
                <p class="tw_sms_tweet-text">
                    '.$par['content'].'
                </p>
                <div class="tw_sms_tweet-time">
                    <a href="'.$par['tweet_url'].'"><p>'.$par['time_posted'].'</p></a>
                </div>
                <div class="tw_sms_tweet-actions">
                <ul>
                    <li><a href="'.$par['reply'].'" class="tw_sms_reply">Reply</a></li>
                    <li><a href="'.$par['retweet'].'" class="tw_sms_retweet">Retweet</a></li>
                    <li><a href="'.$par['fav'].'" class="tw_sms_favorite">Favorite</a></li>
                </ul>
                </div>
            </div>
        </blockquote>
    </div><br>
    ';
    return $html;

}

//if tweet isn't saved in db, this function gets it's json file and returns the html code that we are going to show
function call_tweet($args)
{
    $conn = curl_init();
    if($conn!=false) {
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, 5);
        $url = 'https://syndication.twitter.com/tweets.json?callback=__twttr.callbacks.cb0&ids='.$args['tweet_id'].'&lang=en&new_html=true&suppress_response_codes=true';
        curl_setopt($conn, CURLOPT_URL, $url);
        $res = curl_exec($conn);
        curl_close($conn);
        if($res!=false) {
            $res=str_replace("\\",'',$res);
            $tw_avatar_url=substr($res,(strpos($res,"data-src-2x=")+13),((strpos($res,("alt"))-19)-strpos($res,"data-src-2x=")+4));
            $screen_name=substr($res,(strpos($res,"screen name:")+13),((strpos($res,(")")))-strpos($res,"screen name:")-13));
            $time_posted=substr($res,(strpos($res,"Time posted:")+13),((strpos($res,("(UTC)")))-strpos($res,"Time posted:")-14));
            $profile_url='https://twitter.com/'.$screen_name;
            $tweet_url='https://twitter.com/'.$screen_name.'/status/'.$args['tweet_id'];
            $html=array(
                'avatar_url'=>$tw_avatar_url,
                'screen_name'=>$screen_name,
                'profile_url'=>$profile_url,
                'time_posted'=>$time_posted,
                'tweet_url'=>$tweet_url
            );
        }else return '<h2>Sadrzaj nije dostupan!</h2>';
    }else return '<h2>Sadrzaj nije dostupan!</h2>';
    $conn2=curl_init();
    if($conn2!=false){
        curl_setopt($conn2, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conn2, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($conn2, CURLOPT_CONNECTTIMEOUT, 5);
        $url2 = 'https://api.twitter.com/1/statuses/oembed.json?url=https://twitter.com/Interior/status/'.$args['tweet_id'];
        curl_setopt($conn2, CURLOPT_URL, $url2);
        $res2 = curl_exec($conn2);
        $res2=json_decode($res2);
        curl_close($conn2);
        $content=substr($res2->html,(strpos($res2->html,'dir="ltr"'))+10,((strpos($res2->html,'</p>'))-4)-(strpos($res2->html,'dir="ltr"'))-6);
        $reply_but='https://twitter.com/intent/tweet?in_reply_to='.$args['tweet_id'];
        $retweet_but='https://twitter.com/intent/retweet?tweet_id='.$args['tweet_id'];
        $fav_but='https://twitter.com/intent/favorite?tweet_id='.$args['tweet_id'];
        $full_name=$res2->author_name;
        $html['content']=$content;
        $html['reply']=$reply_but;
        $html['retweet']=$retweet_but;
        $html['fav']=$fav_but;
        $html['full_name']=$full_name;
    }
    return $html;

}


//INSTAGRAM

//gets html code of instagram post(and inserts it in db if it's not already there)
function get_inst_html($args){
    global $wpdb;
   $q=$wpdb->prepare("SELECT * from ".$wpdb->prefix."social_media_posts WHERE post_id='%s';",$args['inst_id']);
    $res=$wpdb->get_row($q,ARRAY_A);
    if($res!=null){
        echo($res['post_html']);
        return;
    }
    else{
        $t=call_inst($args);
        $html=show_inst($t);
        echo $html;
        sm_instert_into_db($args['inst_id'],$html);
        return;
    }


}



//prints out the html code of the selected instagram post on our page
function show_inst($par){
    $path=plugin_dir_url(__FILE__ ).'../includes/';
    $html='
    <div class="inst_sms_wrapper">
    <div class="inst_sms_header">
        <a href="'.$par['author_url'].'">
            <img src="'.$par['profile_pic_url'].'" class="inst_sms_avatar">
        </a>
        <a href="'.$par['author_url'].'" class="inst_sms_username" target="blank">'.$par['author_name'].'</a>
        <a href="'.$par['author_url'].'" class="inst_sms_follow" target="blank">+Follow</a>
    </div>
    <div class="inst_sms_body">
        <a href="'.$par['post_url'].'" target="blank">
            <img src="'.$par['img_url'].'" alt="" class="inst_sms_image">
        </a>
        <p class="inst_sms_content">'.$par['content'].'</p>
    </div>
        <div class="inst_sms_footer">
                <a href="'.$par['post_url'].'" target="blank"><img src="'.$path.'inst_sms_like_icon.png" class="inst_sms_like_icon" alt="">Like</a>
                <a href="'.$par['post_url'].'" target="blank"><img src="'.plugin_dir_url(__FILE__ ).'../includes/inst_sms_comment_icon.png" class="inst_sms_comment_icon" alt="">Comment</a>
                <a href="https://instagram.com/" target="blank">
                    <img src="'.plugin_dir_url(__FILE__ ).'../includes/inst_sms_instagram_logo.png" alt="" class="inst_sms_logo">
                </a>
        </div>
    </div><br>
    ';
    return $html;
}

//if instagram post isn't saved in db, this function gets it's json file and returns the html code that we are going to show
function call_inst($args)
{
    $conn = curl_init();
    if($conn!=false) {
        curl_setopt($conn, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($conn, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($conn, CURLOPT_CONNECTTIMEOUT, 5);
        $url = 'http://api.instagram.com/publicapi/oembed/?url=http://instagr.am/p/'.$args['inst_id'];
        curl_setopt($conn, CURLOPT_URL, $url);
        $res = curl_exec($conn);
        if($res!=false) {
            curl_close($conn);
            $x = json_decode($res);
            $content=$x->title;
            $author_name=$x->author_name;
            $author_url=$x->author_url;
            $author_id=$x->author_id;
            $img_url='http://instagram.com/p/'.$args['inst_id'].'/media/?size=l';
            $post_url='https://instagram.com/p/'.$args['inst_id'].'/';
            $html=array(
                'content'=>$content,
                'author_name'=>$author_name,
                'author_url'=>$author_url,
                'author_id'=>$author_id,
                'img_url'=>$img_url,
                'post_url'=>$post_url
            );
            $conn2 = curl_init();
            if($conn2!=false) {
                curl_setopt($conn2, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($conn2, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($conn2, CURLOPT_CONNECTTIMEOUT, 5);
                $url2 = 'https://instagram.com/p/'.$args['inst_id'].'/';
                curl_setopt($conn2, CURLOPT_URL, $url2);
                $res2 = curl_exec($conn2);
                $res2=esc_attr($res2);
                curl_close($conn2);
                $res2=substr($res2,2000,(strlen($res2)-2000));
                $profile_pic_url=substr($res2,(strpos($res2,'profile_pic_url')+28),((strpos($res2,'.')+100)));
                $profile_pic_url=substr($profile_pic_url,0,strpos($profile_pic_url,'jpg')+3);
                $html['profile_pic_url']=$profile_pic_url;
            }
            return $html;
        }else return '<h2>Sadrzaj nije dostupan!</h2>';
    }else return '<h2>Sadrzaj nije dostupan!</h2>';
}



//FACEBOOK(iframe)
function call_fb($args){
    echo '<div class="fb-post" data-href="'.$args['url'].'"></div>';
}


//Enqueues our js script
function my_scripts_method() {
    wp_register_script( 'myscript1',  plugin_dir_url(__FILE__ ).'../btn.js' );
    wp_localize_script( 'myscript1', 'putanjajs', array(putanja => plugin_dir_url( __FILE__ )) );
    wp_register_script( 'myscript1',  plugin_dir_url(__FILE__ ).'../btn.js' );
    wp_register_script('sm-instagram-embed', '//platform.instagram.com/en_US/embeds.js');
    wp_enqueue_script('sm-instagram-embed');
    wp_register_script('sm-twitter-embed', plugin_dir_url(__FILE__ ).'../reverse-twitter-widget.js');
    wp_enqueue_script('sm-twitter-embed');
    wp_register_style('tw_sms_style', plugin_dir_url(__FILE__ ).'../includes/tw_sms_style.css');
    wp_enqueue_style('tw_sms_style');
    wp_register_style('inst_sms_style', plugin_dir_url(__FILE__ ).'../includes/inst_sms_style.css');
    wp_enqueue_style('inst_sms_style');

}

//Creates plugin's custom table in db
function sm_create_post_table(){
    global $wpdb;
    $q=$wpdb->prepare("CREATE TABLE ".$wpdb->prefix."social_media_posts
        (
          smp_id int AUTO_INCREMENT,
          post_id varchar(255),
          post_html text,
          PRIMARY KEY(smp_id)
          )");
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($q);
}

//Drops plugin's custom table from db
function sm_drop_post_table(){
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $wpdb->query($wpdb->prepare("DROP TABLE IF EXISTS ".$wpdb->prefix."social_media_posts;"));
}

//Inserts posts into our custom table in db
function sm_instert_into_db($i,$h){
    global $wpdb;
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    $sql=$wpdb->prepare("INSERT INTO ".$wpdb->prefix."social_media_posts(post_id,post_html) VALUES(%s,%s)",$i,$h);
    $wpdb->query($sql);
}

?>