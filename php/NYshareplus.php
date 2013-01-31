<?php
/**
 * @Project  NY Like and Share
 * @author   Victor Arias
 * @package  
 * @copyright CC
 * @license  http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU/GPL version 2
*/

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );
jimport( 'joomla.html.parameter' );
include('bitly.php');

class plgContentNYSharePlus extends JPlugin {

	private $loggerOptions  = array();
	private $locale         = "en_US";
	private $fbLocale       = "en_US";
	private $plusLocale     = "en";
	private $gshareLocale   = "en";
	private $twitterLocale  = "en";
	private $currentView    = "";
	private $currentTask    = "";
	private $currentOption  = "";

	private $goflag = '';
	private $gsflag = '';
	private $fbflag = '';
	private $twflag = '';
	private $tfflag = '';
	private $diflag = '';
	private $piflag = '';

    /**
     * Constructor
     *
     * @param object $subject The object to observe
     * @param array  $config  An optional associative array of configuration settings.
     * Recognized key values include 'name', 'group', 'params', 'language'
     * (this list is not meant to be comprehensive).
     */
    public function __construct(&$subject, $config = array()) {
    	parent::__construct($subject, $config);

    	$app =& JFactory::getApplication();
    	/* @var $app JApplication */

    	if($app->isAdmin()) {
    		return;
    	}

        // Get locale code automatically
    	if($this->params->get("dynamicLocale", 0)) {
    		$lang = JFactory::getLanguage();
    		$locale = $lang->getTag();
    		$this->locale = str_replace("-","_",$locale);
    	}

    	$this->currentView    = JRequest::getCmd("view");
    	$this->currentTask    = JRequest::getCmd("task");
    	$this->currentOption  = JRequest::getCmd("option");

    }
    
    /**
     * Add social buttons into the article
     *
	 * @param	string	The context of the content being passed to the plugin.
	 * @param	object	The article object.  Note $article->text is also available
	 * @param	object	The article params
	 * @param	int		The 'page' number
	 */

    public function onContentPrepare($context, &$article, &$params, $page = 0) {

    	if (!$article OR !isset($this->params)) { return; };            

    	$app =& JFactory::getApplication();
    	/** @var $app JApplication **/

    	if($app->isAdmin()) {
    		return;
    	}

    	$doc     = JFactory::getDocument();
    	/**  @var $doc JDocumentHtml **/
    	$docType = $doc->getType();

        // Check document type
    	if(strcmp("html", $docType) != 0){
    		return;
    	}

    	if($this->isRestricted($article, $context)) {
    		return;
    	}

    	if($this->params->get("loadCss")) {
    		$doc->addStyleSheet(JURI::root() . "plugins/content/NYshareplus/NYstyleplus.css");
    	}

        // Generate content
    	$content	= $this->getContent($article, $context, $doc);
    	$position	= $this->params->get('position');
    	$embed	= $this->params->get('embed');
    	$ogtags	= $this->params->get('ogtags');

    	if($ogtags == 'yes'){
    		$ogurl  = $this->getUrl($article, $context);
    		$ogtitle = $this->getTitle($article, $context);
    		$ogimage = $this->getImage($article, $context);

    		$this->params->get('ogtype') != '' and $ogtype = $this->params->get('ogtype');
    		$this->params->get('ogtitle') != '' and $ogtitle	= $this->params->get('ogtitle');
    		$this->params->get('ogdescription') != '' and $ogdescription = $this->params->get('ogdescription');
    		$this->params->get('ogimage') != '' and $ogimage	= $this->params->get('ogimage');
    		$this->params->get('ogurl') != '' and $ogurl = $this->params->get('ogurl');
    		$this->params->get('ogdeterminer') != '' and $ogdeterminer = $this->params->get('ogdeterminer');
    		$this->params->get('ogaudio') != '' and $ogaudio = $this->params->get('ogaudio');
    		$this->params->get('oglocale') != '' and $oglocale = $this->params->get('oglocale');
    		$this->params->get('ogsitename') != '' and $ogsitename = $this->params->get('ogsitename');
    		$this->params->get('ogvideo') != '' and $ogvideo = $this->params->get('ogvideo');
    		$this->params->get('ogimagesecure') != '' and $ogimagesecure = $this->params->get('ogimagesecure');
    		$this->params->get('ogimagetype') != '' and $ogimagetype = $this->params->get('ogimagetype');
    		$this->params->get('ogimagewidth') != '' and $ogimagewidth = $this->params->get('ogimagewidth');
    		$this->params->get('ogimageheight') != '' and $ogimageheight = $this->params->get('ogimageheight');
    		$this->params->get('ogvideosecure') != '' and $ogvideosecure = $this->params->get('ogvideosecure');
    		$this->params->get('ogvideotype') != '' and $ogvideotype = $this->params->get('ogvideotype');
    		$this->params->get('ogvideowidth') != '' and $ogvideowidth = $this->params->get('ogvideowidth');
    		$this->params->get('ogvideoheight') != '' and $ogvideoheight = $this->params->get('ogvideoheight');
    		$this->params->get('ogaudiosecure') != '' and $ogaudiosecure = $this->params->get('ogaudiosecure');
    		$this->params->get('ogaudiotype') != '' and $ogaudiotype = $this->params->get('ogaudiotype');


    		$this->setTags('ogtype', $article->text, $doc, $ogtype);
    		$this->setTags('ogtitle', $article->text, $doc, $ogtitle);
    		$this->setTags('ogdesc', $article->text, $doc, $ogdescription);
    		$this->setTags('ogurl', $article->text, $doc, $ogurl);
    		$this->setTags('ogimage', $article->text, $doc, $ogimage);
    		$this->setTags('ogdeterminer', $article->text, $doc, $ogdeterminer);
    		$this->setTags('ogaudio', $article->text, $doc, $ogaudio);
    		$this->setTags('oglocale', $article->text, $doc, $oglocale);
    		$this->setTags('ogsitename', $article->text, $doc, $ogsitename);
    		$this->setTags('ogvideo', $article->text, $doc, $ogvideo);
    		$this->setTags('ogimagesecure', $article->text, $doc, $ogimagesecure);
    		$this->setTags('ogimagetype', $article->text, $doc, $ogimagetype);
    		$this->setTags('ogimagewidth', $article->text, $doc, $ogimagewidth);
    		$this->setTags('ogimageheight', $article->text, $doc, $ogimageheight);
    		$this->setTags('ogvideosecure', $article->text, $doc, $ogvideosecure);
    		$this->setTags('ogvideotype', $article->text, $doc, $ogvideotype);
    		$this->setTags('ogvideowidth', $article->text, $doc, $ogvideowidth);
    		$this->setTags('ogvideoheight', $article->text, $doc, $ogvideoheight);
    		$this->setTags('ogaudiosecure', $article->text, $doc, $ogaudiosecure);
    		$this->setTags('ogaudiotype', $article->text, $doc, $ogaudiotype);

    	}


    	if (($embed == 'yes') && (strstr($article->text, '{NYlikeandshare}'))) {
    		$article2 = $this->rstrstr($article->text, '{NYlikeandshare}');
    		$article2 .= $content;
    		$article2 .= substr(strstr($article->text, '{NYlikeandshare}'), 18);
    		$article->text = $article2;
    	}else{
		/*switch($position){
			case 1:
				$article->text = $content . $article->text;
				break;
			case 2:
				$article->text = $article->text . $content;
				break;
			default:
				$article->text = $content . $article->text . $content;
				break;
			}*/
		//print_r($article->text);
			$article2 = $this->rstrstr($article->text, '{K2Splitter}');
			$article2 .= '{K2Splitter}';
			$article2 .= $content;
			$article2 .= substr(strstr($article->text, '{K2Splitter}'), 12);
			$article->text = $article2;
		}        
		return;
	}

	private function getContent(&$article, $context, &$doc){
		$url  = $this->getUrl($article, $context);
		$title = $this->getTitle($article, $context);
		$image = $this->getImage($article, $context);
		$piimage	= $this->params->get('piimage');
		$surl = bitly_v3_shorten($url);

		if($image != ''){
			$piimage = $image;
		}

		$htmlCode		= "";
		$css		= $this->params->get('css');
		$align	= $this->params->get('align');
		$centering	= $this->params->get('centering');
		
		$goheight	= $this->params->get('goheight','');
		$gowidth	= $this->params->get('gowidth','');
		$gocss	= $this->params->get('gocss','');

		$twheight	= $this->params->get('twheight','');
		$twwidth	= $this->params->get('twwidth','');
		$twcss	= $this->params->get('twcss','');
		$twtitle = $title;

		$fbheight	= $this->params->get('fbheight','');
		$fbwidth	= $this->params->get('fbwidth','');
		$fbcss	= $this->params->get('fbcss','');

		$diheight	= $this->params->get('diheight','');
		$diwidth	= $this->params->get('diwidth','');
		$dicss	= $this->params->get('dicss','');

		$stheight	= $this->params->get('stheight','');
		$stwidth	= $this->params->get('stwidth','');
		$stcss	= $this->params->get('stcss','');

		$liheight	= $this->params->get('liheight','');
		$liwidth	= $this->params->get('liwidth','');
		$licss	= $this->params->get('licss','');
		$lititle = $title;
		$lisummary = $this->getSummary($article, $context);

		$tmheight	= $this->params->get('tmheight','');
		$tmwidth	= $this->params->get('tmwidth','');
		$tmcss	= $this->params->get('tmcss','');

		$reheight	= $this->params->get('reheight','');
		$rewidth	= $this->params->get('rewidth','');
		$recss	= $this->params->get('recss','');

		$piheight	= $this->params->get('piheight','');
		$piwidth	= $this->params->get('piwidth','');
		$picss	= $this->params->get('picss','');

		$tfheight	= $this->params->get('tfheight','');
		$tfwidth	= $this->params->get('tfwidth','');
		$tfcss	= $this->params->get('tfcss','');

		$bfheight	= $this->params->get('bfheight','');
		$bfwidth	= $this->params->get('bfwidth','');
		$bfcss	= $this->params->get('bfcss','');

		$xiheight	= $this->params->get('xiheight','');
		$xiwidth	= $this->params->get('xiwidth','');
		$xicss	= $this->params->get('xicss','');

		$gsheight	= $this->params->get('gsheight','');
		$gswidth	= $this->params->get('gswidth','');
		$gscss	= $this->params->get('gscss','');

		$emheight	= $this->params->get('emheight','');
		$emwidth	= $this->params->get('emwidth','');
		$emcss	= $this->params->get('emcss','');
		$emurl = $url;
		
		$fbappid		= $this->params->get('fbappid');
		$fbsendbutton	= $this->params->get('fbsendbutton') == 'yes' ? 'true' : 'false';
		$fbfaces 		= $this->params->get('fbfaces') == 'yes' ? 'true' : 'false';
		$fblayout 		= $this->params->get('fblayout');
		$fboutput 		= $this->params->get('fboutput');
		$fbjembed		= $this->params->get('fbjembed');
		$fbverb 		= $this->params->get('fbverb');
		$fbfont 		= $this->params->get('fbfont');
		$fblocale 		= $this->params->get('fblocale');
		$fbcolor 		= $this->params->get('fbcolor');
		$retype 		= $this->params->get('retype');
		$rebgcolor 	= $this->params->get('rebgcolor');
		$rebordercol	= $this->params->get('rebordercol');

		/*$gourl 		= $this->params->get('gourl') != '' ? $this->params->get('gourl') : $surl;
		$gsurl 		= $this->params->get('gsurl') != '' ? $this->params->get('gsurl') : $surl;
		$twurl 		= $this->params->get('twurl') != '' ? $this->params->get('twurl') : $surl;
		$xiurl 		= $this->params->get('xiurl') != '' ? $this->params->get('xiurl') : $surl;
		$fburl 		= $this->params->get('fburl') != '' ? $this->params->get('fburl') : $surl;
		$diurl 		= $this->params->get('diurl') != '' ? $this->params->get('diurl') : $surl;
		$sturl 		= $this->params->get('sturl') != '' ? $this->params->get('sturl') : $surl;
		$liurl 		= $this->params->get('liurl') != '' ? $this->params->get('liurl') : $surl;
		$tmurl 		= $this->params->get('tmurl') != '' ? $this->params->get('tmurl') : $surl;
		$reurl 		= $this->params->get('reurl') != '' ? $this->params->get('reurl') : $surl;*/
		$retitle 	= $this->params->get('retitle') != '' ? $this->params->get('retitle') : $title;
		$dititle 	= $this->params->get('dititle') != '' ? $this->params->get('dititle') : $title;
		$twtitle 	= $this->params->get('twtitle') != '' ? $this->params->get('twtitle') : $title;

		$gojs			= $this->params->get('goembed');
		$gsjs			= $this->params->get('gsembed');
		$twjs			= $this->params->get('twjs');
		$tfjs			= $this->params->get('tfjs');
		$pijs 		= $this->params->get('pijs');
		$dijs 		= $this->params->get('dijs');
		$gslang		= $this->params->get('gslang');
		$gsoutput		= $this->params->get('gsoutput');
		$gooutput		= $this->params->get('gooutput');
		$twlang		= $this->params->get('twlang');
		$golang		= $this->params->get('golang');
		$pitype		= $this->params->get('pitype');
		//$piurl		= $this->params->get('piurl') != '' ? $this->params->get('piurl') : $url;
		//$piurl = bitly_v3_shorten($piurl);

		$pidescription	= $this->params->get('pidescription', '') != '' ? $this->params->get('pidescription') : $title;	

		$tflang			= $this->params->get('tflang');
		$xilang			= $this->params->get('xilang');
		$tfuser			= $this->params->get('tfuser');		
		$tflayout 			= $this->params->get('tflayout');
		$xilayout 			= $this->params->get('xilayout');
		//$bfurl 			= $this->params->get('bfurl') != '' ? $this->params->get('bfurl') : $url;	
		$bftitle 		= $this->params->get('bftitle') != '' ? $this->params->get('bftitle') : $title;	
		$bflayout		= $this->params->get('bflayout');
		$emstyle		= $this->params->get('emstyle');
		//$bfurl = bitly_v3_shorten($bfurl);	

		global $goflag;	
		if((($gojs == 'everytime') || ($gojs == 'once')) && ($goflag != 'yes')) {
			$goflag = 'yes';
			$gojs = '<script type="text/javascript"> (function() {var po = document.createElement("script"); po.type = "text/javascript"; po.async = true; po.src = "https://apis.google.com/js/plusone.js"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s); })(); </script><script type="text/javascript"> window.___gcfg = {lang: "'.$golang.'"}; </script>';
		}else{
			$gojs ='';
		}

		global $gsflag;
		if((($gsjs == 'everytime') || ($gsjs == 'once')) && ($gsflag != 'yes')) {
			$gsflag = 'yes';
			$gsjs = '<script type="text/javascript"> window.___gcfg = {lang: "'.$gslang.'"}; (function() { var po = document.createElement("script"); po.type = "text/javascript"; po.async = true; po.src = "https://apis.google.com/js/plusone.js"; var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(po, s); })(); </script>';
		}else{
			$gsjs ='';
		}

		global $fbflag;
		if((($fbjembed == 'everytime') || ($fbjembed == 'once')) && ($fbflag != 'yes')) {
			$fbflag = 'yes';
			$fbjembed = '<script> (function(d, s, id) {var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) {return}; js = d.createElement(s); js.id = id; js.async = true; js.src = "//connect.facebook.net/'.$fblocale.'/all.js#xfbml=1&appId='.$fbappid.'"; fjs.parentNode.insertBefore(js, fjs); }(document, "script", "facebook-jssdk")); </script>';
		}else{
			$fbjembed = '';
		}

		global $twflag;
		if((($twjs == 'everytime') || ($twjs == 'once')) && ($twflag != 'yes')) {
			$twflag = 'yes';
			$twjs = '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		}else{
			$twjs = '';
		}

		global $tfflag;
		if((($tfjs == 'everytime') || ($tfjs == 'once')) && ($tfflag != 'yes')) {
			$tfflag = 'yes';
			$tfjs = '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
		}else{
			$tfjs = '';
		}

		global $diflag;
		if((($dijs == 'everytime') || ($dijs == 'once')) && ($diflag != 'yes')) {
			$diflag = 'yes';
			$dijs = '<script type="text/javascript">
			(function() { var s = document.createElement("SCRIPT"), s1 = document.getElementsByTagName("SCRIPT")[0]; s.type = "text/javascript"; s.async = true; s.src = "http://widgets.digg.com/buttons.js"; s1.parentNode.insertBefore(s, s1); })(); </script>';
		}else{
			$dijs = '';
		}

		global $piflag;
		if((($pijs == 'everytime') || ($pijs == 'once')) && ($piflag != 'yes')) {
			$piflag = 'yes';
			$pijs = '<script type="text/javascript">
			(function() { window.PinIt = window.PinIt || { loaded:false }; if (window.PinIt.loaded) return; window.PinIt.loaded = true; function async_load(){ var s = document.createElement("script"); s.type = "text/javascript"; s.async = true; if  (window.location.protocol == "https:") s.src = "https://assets.pinterest.com/js/pinit.js"; else s.src = "http://assets.pinterest.com/js/pinit.js"; var x = document.getElementsByTagName("script")[0]; x.parentNode.insertBefore(s, x); } if (window.attachEvent) window.attachEvent("onload", async_load); else window.addEventListener("load", async_load, false); })(); </script>';
		}else{
			$pijs = '';
		}
		

		$twlayout 		= $this->params->get('twlayout');
		$twuser 		= $this->params->get('twuser');
		$twvia 		= $this->params->get('twvia');
		$twhashtag		= $this->params->get('twhashtag');
		$sizegoogle	= $this->params->get("sizegoogle");
		$annotation	= $this->params->get("annotation");
		$gs_annotation	= $this->params->get('gs_annotation');
		$gs_size		= $this->params->get('gs_size');
		$re1			= $this->params->get('re1');
		$tm1			= $this->params->get('tm1');
		$li1			= $this->params->get('li1');
		$st1			= $this->params->get('st1');
		$di1			= $this->params->get('di1');
		$fb1			= $this->params->get('fb1');
		$tw1			= $this->params->get('tw1');
		$go1			= $this->params->get('go1');
		$pi1			= $this->params->get('pi1');
		$tf1			= $this->params->get('tf1');	
		$bf1			= $this->params->get('bf1');	
		$xi1			= $this->params->get('xi1');
		$gs1			= $this->params->get('gs1');
		$em1			= $this->params->get('em1');
		$lilayout 		= $this->params->get('lilayout');
		$tmlayout 		= $this->params->get('tmlayout');
		$dilayout 		= $this->params->get('dilayout');
		$stlayout 		= $this->params->get('stlayout');

		if($align == 'horizontal'){
			if($emcss == ''){
				$emcss = 'float: left;';
			}
			if($gscss == ''){
				$gscss = 'float: left;';
			}
			if($xicss == ''){
				$xicss = 'float: left;';
			}
			if($bfcss == ''){
				$bfcss = 'float: left;';
			}
			if($tfcss == ''){
				$tfcss = 'float: left;';
			}

			if($picss == ''){
				$picss = 'float: left;';
			}
			if($recss == ''){
				$recss = 'float: left;';
			}
			if($tmcss == ''){
				$tmcss = 'float: left;';

			}
			if($licss == ''){
				$licss = 'float: left;';
			}
			if($stcss == ''){
				$stcss = 'float: left;';
			}	
			if($dicss == ''){
				$dicss = 'float: left;';
			}
			if($fbcss == ''){
				$fbcss = 'float: left;';
			}
			if($twcss == ''){
				$twcss = 'float: left;';

			}
			if($gocss == ''){
				$gocss = 'float: left;';
			}

		}else{
			if($emcss == ''){
				$emcss = 'text-align: '.$centering.';';
			}
			if($bfcss == ''){
				$bfcss = 'text-align: '.$centering.';';
			}
			if($xicss == ''){
				$xicss = 'text-align: '.$centering.';';
			}
			if($gscss == ''){
				$gscss = 'text-align: '.$centering.';';
			}
			if($tfcss == ''){
				$tfcss = 'text-align: '.$centering.';';
			}

			if($picss == ''){
				$picss = 'text-align: '.$centering.';';
			}
			if($recss == ''){
				$recss = 'text-align: '.$centering.';';
			}
			if($tmcss == ''){
				$tmcss = 'text-align: '.$centering.';';

			}
			if($licss == ''){
				$licss = 'text-align: '.$centering.';';
			}
			if($stcss == ''){
				$stcss = 'text-align: '.$centering.';';
			}	
			if($dicss == ''){
				$dicss = 'text-align: '.$centering.';';
			}

			if($fbcss == ''){
				$fbcss = 'text-align: '.$centering.';';
			}
			if($twcss == ''){
				$twcss = 'text-align: '.$centering.';';

			}
			if($gocss == ''){
				$gocss = 'text-align: '.$centering.';';
			}


		}

		if($em1 == 'yes'){
			switch($emstyle){
				case 'horizontal':
				$emimage = 'em_hor.png';
				if($emwidth == ''){
					$emwidth = '61';
				}
				if($emheight == ''){
					$emheight = '30';
				}
				break;
				case 'vertical':
				$emimage = 'em_ver.png';
				if($emwidth == ''){
					$emwidth = '61';
				}
				if($emheight == ''){
					$emheight = '75';
				}
				break;
			}
		}



		if($re1 == 'yes'){
			switch($retype){
				case 'button1':
				if($rewidth == ''){
					$rewidth = '35';
				}
				if($reheight == ''){
					$reheight = '30';
				}
				break;
				case 'button2':
				if($rewidth == ''){
					$rewidth = '90';
				}
				if($reheight == ''){
					$reheight = '30';
				}
				break;
				case 'button3':
				if($rewidth == ''){

					$rewidth = '90';
				}
				if($reheight == ''){
					$reheight = '30';
				}
				break;
				case 'button4':
				if($rewidth == ''){
					$rewidth = '130';
				}
				if($reheight == ''){
					$reheight = '30';
				}

				break;
				case 'button5':
				if($rewidth == ''){
					$rewidth = '61';
				}
				if($reheight == ''){
					$reheight = '75';
				}
				break;
				case 'button6':
				if($rewidth == ''){
					$rewidth = '61';
				}
				if($reheight == ''){
					$reheight = '75';
				}
				break;
			}
		}

		if($gs1 == 'yes'){
			switch($gs_annotation){
				case 'none':

				if($gs_size == 'small'){
					if($gswidth == ''){
						$gswidth = '48';
					}
					if($gsheight == ''){
						$gsheight = '15';
					}
				}elseif($gs_size == 'medium'){
					if($gswidth == ''){
						$gswidth = '69';
					}
					if($gsheight == ''){
						$gsheight = '30';
					}
				}else{
					if($gswidth == ''){
						$gswidth = '84';
					}

					if($gsheight == ''){
						$gsheight = '30';
					}
				}
				break;
				case 'bubble':
				if($gs_size == 'small'){
					if($gswidth == ''){
						$gswidth = '88';
					}
					if($gsheight == ''){
						$gsheight = '15';
					}
				}elseif($gs_size == 'medium'){
					if($gswidth == ''){

						$gswidth = '102';
					}

					if($gsheight == ''){
						$gsheight = '30';

					}
				}else{
					if($gswidth == ''){
						$gswidth = '131';
					}
					if($gsheight == ''){
						$gsheight = '30';
					}
				}
				break;
				case 'vertical-bubble':
				if($gswidth == ''){
					$gswidth = '61';
				}
				if($gsheight == ''){
					$gsheight = '75';
				}
				break;
				case 'inline':
				if($gs_size == 'small'){
					if($gswidth == ''){
						$gswidth = '192';
					}
					if($gsheight == ''){
						$gsheight = '15';
					}
				}elseif($gs_size == 'medium'){
					if($gswidth == ''){
						$gswidth = '262';
					}
					if($gsheight == ''){
						$gsheight = '30';
					}
				}else{
					if($gswidth == ''){
						$gswidth = '297';
					}
					if($gsheight == ''){
						$gsheight = '30';
					}
				}
				break;
			}
		}

		if($tm1 == 'yes'){
			if($tmlayout == 'standard'){
				$tmstr='';
				if($tmwidth == ''){
					$tmwidth = '61';
				}
				if($tmheight == ''){
					$tmheight = '75';
				}
			}else{
				$tmstr="compact";
				if($tmwidth == ''){
					$tmwidth = '75';
				}
				if($tmheight == ''){
					$tmheight = '30';
				}
			}
		}

		if($bf1 == 'yes'){
			switch($bflayout){
				case 'horizontal':
				$bfstr="horizontal";
				if($bfwidth == ''){
					$bfwidth = '95';
				}
				if($bfheight == ''){
					$bfheight = '30';
				}
				break;		
				case 'vertical':
				$bfstr="vertical";
				if($bfwidth == ''){
					$bfwidth = '61';
				}
				if($bfheight == ''){
					$bfheight = '75';

				}
				break;		
				case 'no_count':

				$bfstr="none";
				if($bfwidth == ''){
					$bfwidth = '80';
				}
				if($bfheight == ''){
					$bfheight = '30';
				}
				break;		
			}
		}

		if($xi1 == 'yes'){
			switch($xilayout){
				case 'top':
				if($xiwidth == ''){
					$xiwidth = '61';
				}
				if($xiheight == ''){

					$xiheight = '75';
				}
				$xishape = '';
				$xicount = 'top';
				break;		
				case 'right':
				if($xiwidth == ''){
					$xiwidth = '95';
				}
				if($xiheight == ''){
					$xiheight = '30';
				}
				$xishape = '';
				$xicount = 'right';
				break;		
				case 'rectangle':
				if($xiwidth == ''){
					$xiwidth = '80';
				}
				if($xiheight == ''){
					$xiheight = '30';
				}
				$xishape = 'rectangle';
				$xicount = 'no_count';
				break;		
				case 'small_square':
				if($xiwidth == ''){
					$xiwidth = '40';
				}
				if($xiheight == ''){
					$xiheight = '30';
				}
				$xishape = 'smal_square';
				$xicount = 'no_count';
				break;
				case 'square':
				if($xiwidth == ''){
					$xiwidth = '70';
				}
				if($xiheight == ''){
					$xiheight = '60';
				}
				$xishape = 'square';
				$xicount = 'no_count';
				break;
			}
		}

		if($tf1 == 'yes'){
			switch($tflayout){
				case 'standard':
				$tfstr="";
				$tfstr2="false";
				if($tfwidth == ''){
					$tfwidth = '61';
				}
				if($tfheight == ''){
					$tfheight = '30';
				}
				break;		
				case 'show_name':
				$tfstr="";
				$tfstr2="true";
				if($tfwidth == ''){
					$tfwidth = '140';
				}
				if($tfheight == ''){
					$tfheight = '30';
				}
				break;		
				case 'large_standard':
				$tfstr="large";
				$tfstr2="false";
				if($tfwidth == ''){
					$tfwidth = '85';
				}
				if($tfheight == ''){
					$tfheight = '30';
				}
				break;		
				case 'large_show_name':
				$tfstr = "large";
				$tfstr2 = "true";
				if($tfwidth == ''){
					$tfwidth = '150';
				}
				if($tfheight == ''){
					$tfheight = '30';
				}
				break;		
			}
		}
		
		if($pi1 == 'yes'){
			switch($pitype) {
				case 'vertical':
				if($piwidth == ''){
					$piwidth = '61';
				}
				if($piheight == ''){
					$piheight = '75';
				}
				break;
				case 'horizontal':
				if($piwidth == ''){
					$piwidth = '75';
				}
				if($piheight == ''){
					$piheight = '30';
				}
				break;
				case 'none':
				if($piwidth == ''){
					$piwidth = '70';
				}
				if($piheight == ''){
					$piheight = '30';
				}
				break;
			}
		}

		if($li1 == 'yes'){
			switch($lilayout) {
				case 'vertical':
				$listr="top";
				$margin_top = 3;
				if($liwidth == ''){
					$liwidth = '61';
				}
				if($liheight == ''){
					$liheight = '75';
				}
				break;
				case 'horizontal':
				$listr="right";
				if($liwidth == ''){
					$liwidth = '110';
				}
				if($liheight == ''){
					$liheight = '30';
				}
				break;
				case 'no_count':
				$listr="";
				if($liwidth == ''){
					$liwidth = '70';
				}
				if($liheight == ''){
					$liheight = '30';
				}
				break;
				case 'nytimes':
				$listr="nytimes";
				break;
			}
		}

		if($st1 == 'yes'){
			$ststr = $stlayout;
			switch($stlayout) {
				case '1':
				if($stwidth == ''){
					$stwidth = '85';
				}
				if($stheight == ''){
					$stheight = '30';
				}
				break;
				case '2':
				if($stwidth == ''){
					$stwidth = '75';
				}
				if($stheight == ''){
					$stheight = '30';
				}
				break;
				case '3':
				if($stwidth == ''){
					$stwidth = '57';
				}
				if($stheight == ''){
					$stheight = '30';
				}
				break;
				case '5':
				if($stwidth == ''){
					$stwidth = '61';
				}
				if($stheight == ''){
					$stheight = '75';
				}
				break;
			}
		}

		if($di1 == 'yes'){
			switch($dilayout) {
				case 'wide':
				$digstr='DiggWide';
				if($diwidth == ''){
					$diwidth = '120';
				}
				if($diheight == ''){
					$diheight = '50';
				}
				break;
				case 'medium':
				$digstr='DiggMedium';
				if($diwidth == ''){
					$diwidth = '61';
				}
				if($diheight == ''){
					$diheight = '75';
				}
				break;
				case 'compact':
				$digstr='DiggCompact';
				if($diwidth == ''){
					$diwidth = '110';
				}
				if($diheight == ''){
					$diheight = '30';
				}

				break;
			}
		}


		if($fb1 == 'yes'){
			switch($fblayout) {

				case 'standard':
				if($fbwidth == ''){
					$fbwidth = '450';
				}
				if($fbheight == ''){
					if($fbfaces == 'no'){
						$fbheight = '30';
					}else{
						$fbheight = '75';
					}
				}
				break;
				case 'button_count':
				if($fbwidth == ''){
					if($fboutput == 'iframe'){
						$fbwidth = '110';
					}else{
						if($fbsendbutton == 'true'){
							$fbwidth = '150';
						}else{
							$fbwidth = '110';
						}
					}
				}
				if($fbheight == ''){
					$fbheight = '30';
				}
				break;
				case 'box_count':
				if($fbwidth == ''){
					$fbwidth = '61';
				}
				if($fbheight == ''){
					if($fboutput == 'iframe'){
						$fbheight = '75';
					}else{
						if($fbsendbutton == 'true'){
							$fbheight = '100';
						}else{
							$fbheight = '75';
						}
					}
				}
				break;
			}
		}

		if($tw1 == 'yes'){
			switch($twlayout){
				case 'horizontal':
				if($twwidth == ''){
					$twwidth = '120';
				}
				if($twheight == ''){
					$twheight = '30';
				}
				break;		
				case 'vertical':
				if($twwidth == ''){
					$twwidth = '61';
				}
				if($twheight == ''){
					$twheight = '75';
				}
				break;		
				case 'none':
				if($twwidth == ''){
					$twwidth = '65';
				}
				if($twheight == ''){
					$twheight = '30';
				}
				break;		
			}
		}

		if($pi1 == 'yes'){
			switch($pitype) {
				case 'vertical':
				if($piwidth == ''){
					$piwidth = '61';
				}
				if($piheight == ''){
					$piheight = '75';
				}
				break;
				case 'horizontal':
				if($piwidth == ''){
					$piwidth = '70';
				}
				if($piheight == ''){
					$piheight = '30';
				}
				break;
				case 'none':

				if($piwidth == ''){
					$piwidth = '50';
				}
				if($piheight == ''){
					$piheight = '30';
				}
				break;
			}
		}

		if($go1 == 'yes'){
			switch($sizegoogle){
				case 'small':
				if($gowidth == ''){
					if($annotation == 'none'){
						$gowidth = '40';
					}
					if($annotation == 'bubble'){

						$gowidth = '80';
					}
					if($annotation == 'inline'){
						$gowidth = '250';
					}
				}
				if($goheight == ''){
					$goheight = '30';
				}

				break;
				case 'medium':
				if($gowidth == ''){
					if($annotation == 'none'){
						$gowidth = '50';
					}
					if($annotation == 'bubble'){

						$gowidth = '100';
					}
					if($annotation == 'inline'){
						$gowidth = '250';
					}
				}
				if($goheight == ''){
					$goheight = '30';
				}
				break;
				case 'standard':
				if($gowidth == ''){
					if($annotation == 'none'){
						$gowidth = '60';
					}
					if($annotation == 'bubble'){
						$gowidth = '120';

					}
					if($annotation == 'inline'){
						$gowidth = '250';
					}
				}
				if($goheight == ''){
					$goheight = '40';
				}
				break;
				case 'tall':
				if($gowidth == ''){

					if(($annotation == 'none') || ($annotation == 'bubble')){
						$gowidth = '61';
					}else{
						$gowidth = '250';
					}
				}
				if($goheight == ''){
					if(($annotation == 'none') || ($annotation == 'inline')){
						$goheight = '30';

					}else{
						$goheight = '75';
					}
				}
				break;
			}
		}		


			//code plugin

		$htmlCode .= '<div class="NYshareit" style="' .$css. '">';

		for ($i = 1; $i <= 14; $i++) {

			if($go1 == 'yes' && $this->params->get('cbo1') == $i){
				if($gooutput == 'standard'){
					$htmlCode .='<div style="'. $gocss.' z-index:1000; width:'. $gowidth .'px; height:'. $goheight .'px;" id="gogo">'. $gojs .'<g:plusone " size="'.$sizegoogle.'" annotation="'.$annotation.'" href="'.$surl['url'].'"></g:plusone></div>';
				}elseif($gooutput == 'nytimes'){
					$htmlCode .='<div class="gplus" style="'. $gocss.'"><a href="#" onclick="gplus_open_popup()">Google +</a>
				<script type="text/javascript">function gplus_open_popup() {window.open ("https://plus.google.com/share?url=' . $surl['url'] . '%3Fsmid%3Dgo-share&hl=en-US","page","toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=570,left=50,top=50,titlebar=yes"); return false;
	} </script>
					</div>';
					//$htmlCode .='<div style="'. $gocss.' z-index:1000; width:'. $gowidth .'px; height:'. $goheight .'px;" id="gogo">'. $gojs .'<g:plusone " size="'.$sizegoogle.'" annotation="'.$annotation.'" href="'.$gourl.'"></g:plusone></div>';
				}else{
					$htmlCode .='<div style="'. $gocss.' z-index:1000; width:'. $gowidth .'px; height:'. $goheight .'px;" id="gogo">'. $gojs .'<div class="g-plusone" data-size="'.$sizegoogle.'" data-annotation="'.$annotation.'" data-width="'.$gowidth.'" data-href="'.$surl['url'].'"></div></div>';
				}
			}else{
				$htmlCode .='';
			}


			if($fb1 == 'yes' && $this->params->get('cbo2') == $i){
				if($fboutput == 'iframe'){
					$htmlCode .= '<div style="'. $fbcss .' z-index:1000;"><iframe src="http://www.facebook.com/plugins/like.php?locale='.$fblocale.'&amp;href='. rawurlencode($surl['url']) .'&amp;layout='.$fblayout.'&amp;show_faces='.$fbfaces.'&amp;action='.$fbverb.'&amp;colorscheme='.$fbcolor.'&amp;font='.$fbfont.'" scrolling="no" frameborder="0" style="height: '.$fbheight.'px; width: '.$fbwidth.'px; border:none; overflow:hidden;" allowTransparency="true"></iframe></div>';
				}elseif($fboutput == 'html5') {
					$htmlCode .= '<div id="fb-root"></div>' . $fbjembed . '<div style="'. $fbcss .' width: '.$fbwidth.'px; height :'.$fbheight.'px;" class="fb-like" data-layout="'. $fblayout .'" data-send="'. $fbsendbutton .'" data-width="'.$fbwidth.'" data-show-faces="'.$fbfaces.'" data-href="'.$surl['url'].'" data-font="'. $fbfont .'"></div>';
				}elseif($fboutput == 'nytimes'){
					$htmlCode .= '<div class="facebook" style="' . $fbcss . '"><a href="#" onclick="fb_open_popup()">Facebook</a>
					<script type="text/javascript">function fb_open_popup() {window.open ("http://www.facebook.com/sharer/sharer.php?u='.rawurlencode($surl['url']).'%253Fsmid%253Dfb-share","page","toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=570,left=50,top=50,titlebar=yes"); return false;
	} </script>
	</div>';
					//$htmlCode .= '<div style="'. $fbcss .' z-index:1000;"><iframe src="http://www.facebook.com/plugins/like.php?locale='.$fblocale.'&amp;href='. rawurlencode($fburl) .'&amp;layout='.$fblayout.'&amp;show_faces='.$fbfaces.'&amp;action='.$fbverb.'&amp;colorscheme='.$fbcolor.'&amp;font='.$fbfont.'" scrolling="no" frameborder="0" style="height: '.$fbheight.'px; width: '.$fbwidth.'px; border:none; overflow:hidden;" allowTransparency="true"></iframe></div>';
				}else{
					$htmlCode .= '<div id="fb-root"></div>' . $fbjembed . '<div style="'. $fbcss .' width: '.$fbwidth.'px; height :'.$fbheight.'px;"><fb:like href="'.$surl['url'].'" send="'. $fbsendbutton .'" layout="'. $fblayout .'" width="'.$fbwidth.'" show-faces="'.$fbfaces.'" font="'. $fbfont .'"></fb:like></div>';			
				}
			}	else{
				$htmlCode .= '';
			}

			if($li1 == 'yes' && $this->params->get('cbo3') == $i){
				if($listr=='nytimes'){
					$htmlCode .= '<div class="linkedin" style="' . $licss . '"><a href="#" onclick="in_open_popup()">Linkedin</a>
					<script type="text/javascript">function in_open_popup() {
						window.open ("http://www.linkedin.com/shareArticle?url='.$surl['url'].'&title='.str_replace("&#34;", '\"', str_replace('&#039;', "\'", $lititle)).'&summary='.$lisummary.'&smid=li-share&mini=true&source=Insight%20Crime","page","toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=570,left=50,top=50,titlebar=yes"); return false;
	} </script>
	</div>';
				}else{
					$htmlCode .='<div style="'. $licss .' z-index:1000; width:'. $liwidth .'px; height:'. $liheight .'px;">';
					$htmlCode .= '<script type="text/javascript" src="http://platform.linkedin.com/in.js"></script><script type="IN/share" data-url="' . $surl['url'] . '" data-counter="'.$listr.'"></script>'; 
					$htmlCode .= '</div>';
				}
			}else{
				$htmlCode .='';
			}


			if($tm1 == 'yes' && $this->params->get('cbo4') == $i){
				$htmlCode .= '<div style="'. $tmcss .' z-index:1000; width:'. $tmwidth .'px; height:'. $tmheight .'px;">';
				$htmlCode .= '<script type="text/javascript">';
				$htmlCode .= 'tweetmeme_url = "'.$surl['url'].'";';
				$htmlCode .= 'tweetmeme_style = "'.$tmlayout.'";';
				$htmlCode .= '</script><script type="text/javascript" src="http://tweetmeme.com/i/scripts/button.js"></script>'; 
				$htmlCode .= '</div>';
			}else{

				$htmlCode .= '';
			}

			if($tw1 == 'yes' && $this->params->get('cbo5') == $i){
				if($twlayout == 'nytimes'){
					$htmlCode .= '<div class="twitter" style="' . $twcss . '"><a href="#" onclick="tw_open_popup()">Twitter</a>
					<script type="text/javascript">function tw_open_popup() {window.open("https://twitter.com/intent/tweet?'.'url='.urlencode($url).'&text='.str_replace("&#34;", '\"', str_replace('&#039;', "\'", urlencode($twtitle))).'&via='.$twvia.'","page","toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=570,left=50,top=50,titlebar=yes"); return false;
	} </script></div>';
				}else{
					$htmlCode .='<div style="'. $twcss .' z-index:1000; width:'. $twwidth .'px; height:'. $twheight .'px;">';
					$htmlCode .= $twjs . '<a href="https://twitter.com/share" class="twitter-share-button" style="width: '.$twwidth.'px;" data-lang="'.$twlang.'" data-url="'.$surl['url'].'" data-count="'.$twlayout.'" data-text="'.$title.'" data-via="'.$twvia.'" data-related="'.$twuser.'" data-hashtags="'.$twhashtag.'" >Tweet</a>'; 
					$htmlCode .= '</div>';
				}

			}else{
				$htmlCode .='';
			}

			if($tf1 == 'yes' && $this->params->get('cbo6') == $i){
				$htmlCode .='<div style="'. $tfcss .' z-index:1000; width:'. $tfwidth .'px; height:'. $tfheight .'px;">';
				$htmlCode .= $tfjs . '<a href="https://twitter.com/'.$tfuser.'" class="twitter-follow-button" data-show-count="false" data-size="'.$tfstr.'" data-show-screen-name="'.$tfstr2.'">Follow @'.$tfuser.'</a>';
				$htmlCode .= '</div>';		
			}else{
				$htmlCode .='';
			}

			if($di1 == 'yes' && $this->params->get('cbo7') == $i){

				$htmlCode .='<div style="'. $dicss .' z-index:1000; width:'. $diwidth .'px; height:'. $diheight .'px;">';
				$htmlCode .= $dijs . '<a class="DiggThisButton '.$digstr.'" href="http://digg.com/submit?url='.rawurlencode($surl['url']).'&amp;title='.urlencode($dititle).'"></a>';
				$htmlCode .= '</div>';
			}else{
				$htmlCode .='';
			}

			if($st1 == 'yes' && $this->params->get('cbo8') == $i){
				$htmlCode .='<div style="'. $stcss .' z-index:1000; width:'. $stwidth .'px; height:'. $stheight .'px;">';
				$htmlCode .='<script src="http://www.stumbleupon.com/hostedbadge.php?s='.$ststr.'&r='.rawurlencode($surl['url']).'"></script>';
				$htmlCode .= '</div>';
			}else{
				$htmlCode .='';

			}

			if($re1 == 'yes' &&  $this->params->get('cbo9') == $i){
				if(($retype == 'button1') || ($retype == 'button2') || ($retype == 'button3')){
					switch ($retype) {
						case "button1":
						$spreddit = 'spreddit5';
						break;
						case "button2":
						$spreddit = 'spreddit6';
						break;

						case "button3":
						$spreddit = 'spreddit7';
						break;
					}

					$htmlCode .= '<div style="'. $recss .' z-index:1000; width:'. $rewidth .'px; height:'. $reheight .'px;">';
					$htmlCode .= '<a target="_blank" href="http://www.reddit.com/submit?url='. $surl['url'] .'&amp;title='. $retitle .'"> <img src="http://www.reddit.com/static/'. $spreddit .'.gif" alt="submit to reddit" border="0" /> </a></div>';} else {
						switch ($retype) {
							case "button4":
							$spreddit = 'button1';
							break;

							case "button5":
							$spreddit = 'button2';
							break;
							case "button6":
							$spreddit = 'button3';
							break;
						}

						$htmlCode .= '<div style="'. $recss .' z-index:1000; width:'. $rewidth .'px; height:'. $reheight .'px;"><script type="text/javascript">reddit_url = "'. $surl['url'] .'"; reddit_title = "'. $retitle .'"; reddit_bgcolor = "'. $rebgcolor .'"; reddit_bordercolor = "'. $rebordercol .'"; </script>';
						$htmlCode .= '<script type="text/javascript" src="http://www.reddit.com/static/button/'. $spreddit .'.js"></script></div>';}
					}else{
						$htmlCode .='';
					}

					if($pi1 == 'yes' && $this->params->get('cbo10') == $i){
						if($piimage == ''){
							$piimage = $image;
						}
						$htmlCode .= '<div style="'. $picss .' z-index:1000; width:'. $piwidth .'px; height:'. $piheight .'px; id="pi2">';
						$htmlCode .= $pijs . '<a href="http://pinterest.com/pin/create/button/?url=' .rawurlencode($surl['url']). '&amp;media=' .rawurlencode($piimage). '&amp;description='. rawurlencode($pidescription). ' " class="pin-it-button" count-layout="'.$pitype.'"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
						$htmlCode .= '</div>';		
					}else{
						$htmlCode .= '';
					}

					if($bf1 == 'yes' && $this->params->get('cbo11') == $i){
						$htmlCode .='<div style="'. $bfcss .' z-index:1000; width:'. $bfwidth .'px; height:'. $bfheight .'px;">';
						$htmlCode .='<a href="http://bufferapp.com/add" class="buffer-add-button" data-text="'.$bftext.'" data-url="'.$surl['url'].'" data-count="'.$bfstr.'" data-via="'.$bfuser.'">Buffer</a><script type="text/javascript" src="http://static.bufferapp.com/js/button.js"></script></div>';
					}else{
						$htmlCode .= '';
					}

					if($xi1 == 'yes' && $this->params->get('cbo12') == $i){
						$htmlCode .='<div style="'. $xicss .' z-index:1000; width:'. $xiwidth .'px; height:'. $xiheight .'px;">';
						$htmlCode .='<script data-button-shape="'.$xishape.'" data-url="'.$surl['url'].'" data-lang="'.$xilang.'" data-counter="'.$xicount.'" type="XING/Share"></script><script> ;(function(d, s) { var x = d.createElement(s), s = d.getElementsByTagName(s)[0]; x.src ="https://www.xing-share.com/js/external/share.js";
							s.parentNode.insertBefore(x, s); })(document, "script");
</script></div>';
}else{
	$htmlCode .= '';
}

if($gs1 == 'yes' && $this->params->get('cbo13') == $i){
	if($gsoutput == 'standard') {
		$htmlCode .= '<div style="'. $gscss .' z-index:1000; width:'. $gswidth .'px; height:'. $gsheight .'px;">';
		$htmlCode .= $gsjs;
		$htmlCode .= '<g:plus action="share" width="'.$gswidth.'" height="'.$gsheight.'" annotation="'.$gs_annotation.'" href="'.$surl['url'].'"></g:plus></div>';
	}else{
		$htmlCode .= '<div style="'. $gscss .' z-index:1000; width:'. $gswidth .'px; height:'. $gsheight .'px;">';
		$htmlCode .= $gsjs;
		$htmlCode .= '<div class="g-plus" data-action="share" data-annotation="'.$gs_annotation.'" data-href="'.$surl['url'].'"></div></div>';
	}
}else{
	$htmlCode .= '';
}

if($em1 == 'yes' && $this->params->get('cbo14') == $i){
	$urllink2 =  JURI::root() . 'plugins/content/NYshareplus/emailthispage.htm';

	$imagemail =  JURI::root() . 'plugins/content/NYshareplus/NYshareplusassets/' . $emimage;
	if($emstyle == 'nytimes'){
		$htmlCode .= '<div class="email" style="' . $emcss . '"><a href="#" onclick="s5_open_taf_popup()" title="Email This Page">Email</a><script type="text/javascript"> var s5_taf_parent = window.location;
		function s5_open_taf_popup() { window.open ("'. $urllink2 .'","page","toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=570,left=50,top=50,titlebar=yes"); return false;
	} </script></div>';
	}else{
		$htmlCode .= '<div style="'. $emcss .' z-index:1000; width:'. $emwidth .'px; height:'. $emheight .'px;">';
		$htmlCode .= '<input type="image" src="'.$imagemail.'" class="NYshareit" value="Email This Page" onclick="s5_open_taf_popup()" title="Email This Page"/><script type="text/javascript"> var s5_taf_parent = window.location;
		function s5_open_taf_popup() { window.open ("'. $urllink2 .'","page","toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=0,width=500,height=570,left=50,top=50,titlebar=yes")
	} </script></div>';
	}
}else{
	$htmlCode .= '';
}

}

//Print
$htmlCode .= '<div class="print" style="float: left;">
<a class="itemPrintThisPage" rel="nofollow" href="#" onclick="window.open(\''.$emurl.'?print=1&tmpl=component\',\'printWindow\',\'width=900,height=600,location=no,menubar=no,resizable=yes,scrollbars=yes\'); return false;">
			Print
		</a></div>';

$htmlCode .= '</div>';
	//$htmlCode .= '<div style="clear: both;"></div>';

return $htmlCode; 
}


private function isRestricted($article, $context) {

	$result = false;

	switch($this->currentOption) {
		case "com_content":

            	// It's an implementation of "com_myblog"

           	// I don't know why but $option contains "com_content" for a value
            	// I hope it will be fixed in the future versions of "com_myblog"
		if(!strcmp($context, "com_myblog") == 0) {
			if($this->isContentRestricted($article, $context)) {
				$result = true;
			}
			break;
		} 

		case "com_myblog":

		if($this->isMyBlogRestricted($article, $context)) {
			$result = true;
		}

		break;

		case "com_k2":
		if($this->isK2Restricted($article, $context)) {
			$result = true;
		}
		break;

		case "com_virtuemart":
		if($this->isVirtuemartRestricted($article, $context)) {
			$result = true;
		}
		break;

		case "com_jevents":

		if($this->isJEventsRestricted($article, $context)) {
			$result = true;
		}
		break;

		case "com_easyblog":
		if($this->isEasyBlogRestricted($article, $context)) {
			$result = true;
		}
		break;

		default:
		$result = true;
		break;   
	}

	return $result;

}

    /**
     * 
     * Checks allowed articles, exluded categories/articles,... for component COM_CONTENT
     * @param object $article
     * @param string $context
     */
    private function isContentRestricted(&$article, $context) {

        // Check for currect context
    	if(strpos($context, "com_content") === false) {
    		return true;
    	}

    	/** Check for selected views, which will display the buttons. **/   
    	/** If there is a specific set and do not match, return an empty string.**/
    	$showInArticles     = $this->params->get('showInArticles');
    	if(!$showInArticles AND (strcmp("article", $this->currentView) == 0)){
    		return true;
    	}

        // Will be displayed in view "categories"?
    	$showInCategories   = $this->params->get('showInCategories');
    	if(!$showInCategories AND (strcmp("category", $this->currentView) == 0)){
    		return true;
    	}

        // Will be displayed in view "featured"?
    	$showInFeatured   = $this->params->get('showInFeatured');
    	if(!$showInFeatured AND (strcmp("featured", $this->currentView) == 0)){
    		return true;
    	}

    	if(
    		($showInCategories AND ($this->currentView == "category") )
    		OR 
    		($showInFeatured AND ($this->currentView == "featured") )
    		) {
    		$articleData        = $this->getArticle($article);
    	$article->id        = JArrayHelper::getValue($articleData,'id');
    	$article->catid     = JArrayHelper::getValue($articleData,'catid');
    	$article->title     = JArrayHelper::getValue($articleData,'title');
    	$article->images     = JArrayHelper::getValue($articleData,'images');
    	$article->slug      = JArrayHelper::getValue($articleData, 'slug');
    	$article->catslug   = JArrayHelper::getValue($articleData,'catslug');
    }

    if(empty($article->id)) {
    	return true;            
    }

    $excludeArticles = $this->params->get('excludeArticles');
    if(!empty($excludeArticles)){
    	$excludeArticles = explode(',', $excludeArticles);
    }
    settype($excludeArticles, 'array');
    JArrayHelper::toInteger($excludeArticles);

        // Exluded categories
    $excludedCats           = $this->params->get('excludeCats');
    if(!empty($excludedCats)){
    	$excludedCats = explode(',', $excludedCats);
    }
    settype($excludedCats, 'array');
    JArrayHelper::toInteger($excludedCats);

        // Included Articles
    $includedArticles = $this->params->get('includeArticles');
    if(!empty($includedArticles)){
    	$includedArticles = explode(',', $includedArticles);
    }
    settype($includedArticles, 'array');
    JArrayHelper::toInteger($includedArticles);

    if(!in_array($article->id, $includedArticles)) {
            // Check exluded articles
    	if(in_array($article->id, $excludeArticles) OR in_array($article->catid, $excludedCats)){
    		return true;
    	}
    }

    return false;
}

private function isK2Restricted(&$article, $context) {

        // Check for currect context
	if(strpos($context, "com_k2") === false) {
		return true;
	}

	$displayInArticles     = $this->params->get('k2DisplayInArticles', 0);
	if(!$displayInArticles AND (strcmp("item", $this->currentView) == 0)){
		return true;
	}

	$displayInItemlist     = $this->params->get('k2DisplayInItemlist', 0);
	if(!$displayInItemlist AND (strcmp("itemlist", $this->currentView) == 0)){
		return true;
	}

	return false;
}

    /**
     * 
     * Do verifications for JEvent extension
     * @param jIcalEventRepeat $article
     * @param string $context
     */
    private function isJEventsRestricted(&$article, $context) {

        // Display buttons only in the description
    	if (!is_a($article, "jIcalEventRepeat")) { 
    		return true; 
    	};

        // Check for currect context
    	if(strpos($context, "com_jevents") === false) {
    		return true;
    	}

    	$displayInEvents     = $this->params->get('jeDisplayInEvents', 0);
    	if(!$displayInEvents AND (strcmp("icalrepeat.detail", $this->currentTask) == 0)){
    		return true;
    	}

    	return false;
    }
    
    private function isVirtuemartRestricted(&$article, $context) {

        // Check for currect context
    	if(strpos($context, "com_virtuemart") === false) {
    		return true;
    	}

        // Display content only in the view "productdetails"
    	$displayInDetails     = $this->params->get('vmDisplayInDetails', 0);
    	if(!$displayInDetails AND (strcmp("productdetails", $this->currentView) == 0)){
    		return true;
    	}

    	return false;
    }
    
    /**
     * 
    * It's a method that verify restriction for the component "com_easyblog"
     * @param object $article
     * @param string $context
     */
    private function isEasyBlogRestricted(&$article, $context) {
    	$allowedViews = array("categories", "entry", "latest");   
        // Check for currect context
    	if(strpos($context, "easyblog") === false) {
    		return true;
    	}

        // Only put buttons in allowed views
    	if(!in_array($this->currentView, $allowedViews)) {
    		return true;
    	}

   		// Verify the option for displaying in view "categories"
    	$displayInCategories     = $this->params->get('ebDisplayInCategories', 0);
    	if(!$displayInCategories AND (strcmp("categories", $this->currentView) == 0)){
    		return true;
    	}

   		// Verify the option for displaying in view "latest"
    	$displayInLatest     = $this->params->get('ebDisplayInLatest', 0);
    	if(!$displayInLatest AND (strcmp("latest", $this->currentView) == 0)){
    		return true;
    	}

		// Verify the option for displaying in view "entry"
    	$displayInEntry     = $this->params->get('ebDisplayInEntry', 0);
    	if(!$displayInEntry AND (strcmp("entry", $this->currentView) == 0)){
    		return true;
    	}

    	return false;
    }

    /**
     * 
     * It's a method that verify restriction for the component "com_myblog"
     * @param object $article
     * @param string $context
     */
    private function isMyBlogRestricted(&$article, $context) {

        // Check for currect context
    	if(strpos($context, "myblog") === false) {
    		return true;
    	}

    	if(!$this->params->get('mbDisplay', 0)){
    		return true;
    	}

    	return false;
    }


    private function getUrl(&$article, $context) {

    	$url = JURI::getInstance();
    	$uri = "";
    	$domain= $url->getScheme() ."://" . $url->getHost();

    	switch($this->currentOption) {
    		case "com_content":

            	// It's an implementation of "com_myblog"
            	// I don't know why but $option contains "com_content" for a value
            	// I hope it will be fixed in the future versions of "com_myblog"
    		if(!strcmp($context, "com_myblog") == 0) {
    			$uri = JRoute::_(ContentHelperRoute::getArticleRoute($article->slug, $article->catslug), false);
    			break;
    		}

    		case "com_myblog":
    		$uri = $article->permalink;
    		break;    


    		case "com_k2":
    		$uri = $article->link;
    		break;

    		case "com_virtuemart":
    		$uri = $article->link;
    		break;

    		case "com_jevents":
                // Display buttons only in the description
    		if (is_a($article, "jIcalEventRepeat")) { 
    			$uri    = $url->getPath();
    		};

    		break;

    		case "com_easyblog":
    		$uri	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $article->id , false , false );
    		break;


    		default:
    		$uri = "";
    		break;   
    	}

    	return $domain.$uri;

    }

    private function getSummary(&$article, $context) {

    	$summary = "";
    	//print_r($article);
    	switch($this->currentOption) {
    		
    		case "com_content":
    		$summary = $article->introtext;

    		case "com_k2":
    		$summary = $article->introtext;
    		break;

    		default:
    		$summary = "";
    		break;   
    	}

    	return preg_replace("/[^a-zA-Z0-9]+/", " ", strip_tags($summary));


    }
    
    private function getTitle(&$article, $context) {

    	$title = "";

    	switch($this->currentOption) {
    		case "com_content":

           	// It's an implementation of "com_myblog"
            	// I don't know why but $option contains "com_content" for a value
            	// I hope it will be fixed in the future versions of "com_myblog"
    		if(!strcmp($context, "com_myblog") == 0) {
    			$title= $article->title;
    			break;
    		}

    		case "com_myblog":
    		$title= $article->title;
    		break;    

    		case "com_k2":
    		$title= $article->title;
    		break;

    		case "com_virtuemart":
    		$title = (!empty($article->custom_title)) ? $article->custom_title : $article->product_name;
    		break;

    		case "com_jevents":
                // Display buttons only in the description
    		if (is_a($article, "jIcalEventRepeat")) { 

    			$title    = JString::trim($article->title());
    			if(!$title) {
    				$doc     = JFactory::getDocument();
    				/**  @var $doc JDocumentHtml **/
    				$title    =  $doc->getTitle();
    			}
    		};

    		break;   

    		case "com_easyblog":
    		$title= $article->title;
    		break;

    		default:
    		$title = "";
    		break;   
    	}

    	return /*htmlentities(*/$title/*, ENT_QUOTES, "UTF-8")*/;

    }
    
    private function getImage($article, $context) {

    	$result = false;
    	
    	switch($this->currentOption) {
    		case "com_content":

            	// It's an implementation of "com_myblog"
            	// I don't know why but $option contains "com_content" for a value
            	// I hope it will be fixed in the future versions of "com_myblog"
            	//if(!strcmp($context, "com_myblog") == 0) {

            	//	if(!empty($article->images)) {
            	//	    $images = json_decode($article->images);
            	//	    if(isset($images->image_intro)) {
            	//	        $result = JURI::root().$images->image_intro;
           	//	    }
            	//	}

	           //     break;
            	//}
    		$result = '';
    		break; 

    		case "com_k2":
    		if(!empty($article->imageSmall)) {
    			$result = JURI::root();
    			$startpos = strpos($result, '//');
    			$endpos = strpos($result, '/', $startpos + 2);
    			$result = substr($result, 0, $endpos) . $article->imageSmall;
    		}

    		break;

    		case "com_virtuemart":

    	        /*if(!empty($article->file_url)) {
    		        $result = JURI::root().$article->file_url;
    		    }*/
    		    break;

    		    case "com_myblog":
    		    case "com_easyblog":
    		    case "com_jevents":
    		    default:
    		    $result = "";
    		    break;   
    		}

    		return $result;

    	}



    /**
     * 
     * Load an information about article, if missing, on the view 'category' and 'featured'
     * @param object $article
     */
    private function getArticle(&$article) {

    	$db = JFactory::getDbo();

    	$query = "
    	SELECT 
    	`#__content`.`id`,
    	`#__content`.`catid`,
    	`#__content`.`alias`,
    	`#__content`.`title`,
    	`#__content`.`images`,
    	`#__categories`.`alias` as category_alias

    	FROM
    	`#__content`
    	INNER JOIN
    	`#__categories`
    	ON
    	`#__content`.`catid`=`#__categories`.`id`
    	WHERE
    	`#__content`.`introtext` SOUNDS LIKE " . $db->quote($article->text); 

    	$db->setQuery($query);

    	try {
    		$result = $db->loadAssoc();
    	} catch(Exception $e) {
    		JError::raiseError(500, "System error!", $e->getMessage());
    	}

    	if(!empty($result)) {
    		$result['slug']     = $result['alias'] ? $result['id'].':'.$result['alias'] : $result['id'];
    		$result['catslug']  = $result['category_alias'] ? $result['catid'].':'.$result['category_alias'] : $result['catid'];
    	}

    	return $result;
    }

    private function rstrstr($haystack,$needle)
    {
    	return substr($haystack, 0,strpos($haystack, $needle));
    }

    private function setTags($ogtag, &$article, &$doc, $tagvar) {

    	$oglen = strlen($ogtag) + 2;

    	if (strstr($article, '{'.$ogtag.'}')) {
    		$start1 = strpos($article, '{'.$ogtag.'}') + $oglen;
    		$end1 = strpos($article, '{/'.$ogtag.'}');
    		$stend = $end1 - $start1;
    		$tagvar = substr($article, strpos($article, '{'.$ogtag.'}') + $oglen, $stend);
    		$articletemp = $this->rstrstr($article, '{'.$ogtag.'}');
    		$articletemp .= substr(strstr($article, '{/'.$ogtag.'}'), ($oglen +1));
    		$article = $articletemp;
    	}

    	if($tagvar != 'none'){
    		$tagvar != '' and $doc->addCustomTag( '<meta property="og:'.$ogtag.'" content="'.$tagvar.'" />' );
    	}

    }


}
?>