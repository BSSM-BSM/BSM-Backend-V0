<?php
// http://htmlpurifier.org/
// Standards-Compliant HTML Filtering
// Safe  : HTML Purifier defeats XSS with an audited whitelist
// Clean : HTML Purifier ensures standards-compliant output
// Open  : HTML Purifier is open-source and highly customizable
function html_purifier($html){
  require_once('/lib/htmlpurifier/HTMLPurifier.auto.php');
  
  $config = HTMLPurifier_Config::createDefault();
  $config->set('Core.Encoding', 'UTF-8');
  $config->set('Attr.EnableID', false);
  $config->set('Attr.DefaultImageAlt', '');

  $config->set('Core.EscapeNonASCIICharacters', true);
  $config->set('AutoFormat.Linkify', true);
  $config->set('HTML.MaxImgLength', null);
  $config->set('CSS.MaxImgLength', null);
  
  $config->set('HTML.SafeEmbed', true);
  $config->set('HTML.SafeIframe', true);
  $config->set('URI.SafeIframeRegexp', '#^(?:https?:)?//(?:'.implode('|', array(
      'www\\.youtube(?:-nocookie)?\\.com/',
      'maps\\.google\\.com/',
      'player\\.vimeo\\.com/video/',
      'www\\.microsoft\\.com/showcase/video\\.aspx',
      '(?:serviceapi\\.nmv|player\\.music)\\.naver\\.com/',
      '(?:api\\.v|flvs|tvpot|videofarm)\\.daum\\.net/',
      'v\\.nate\\.com/',
      'play\\.mgoon\\.com/',
      'channel\\.pandora\\.tv/',
      'www\\.tagstory\\.com/',
      'play\\.pullbbang\\.com/',
      'tv\\.seoul\\.go\\.kr/',
      'ucc\\.tlatlago\\.com/',
      'vodmall\\.imbc\\.com/',
      'www\\.musicshake\\.com/',
      'www\\.afreeca\\.com/player/Player\\.swf',
      'static\\.plaync\\.co\\.kr/',
      'video\\.interest\\.me/',
      'player\\.mnet\\.com/',
      'sbsplayer\\.sbs\\.co\\.kr/',
      'img\\.lifestyler\\.co\\.kr/',
      'c\\.brightcove\\.com/',
      'www\\.slideshare\\.net/',
  )).')#');
  $purifier = new HTMLPurifier($config);
  return $purifier->purify($html);
}  
?>
