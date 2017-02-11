<?php
class Twig_Extensions_Slight extends \Twig_Extension
{
	/**
	 * Twig_Extensions_Slight constructor.
	 */
	public function __construct($c)
	{
		$this->config = $c['config'];
	}

	public function getFunctions()
    {
        return array(
			new Twig_SimpleFunction('asset_url', function($asset) {
				return 'http://localhost/slightsite/source/assets/' . ltrim($asset, '/');
			}),
			new Twig_SimpleFunction('currency', 'twig_currency', array('needs_environment' => true)),
			new Twig_SimpleFunction('link', 'twig_link', array('needs_environment' => true)),
			new Twig_SimpleFunction('text_more', 'twig_text_more', array('needs_environment' => true)),
			new Twig_SimpleFunction('str_highlight', 'twig_str_highlight', array('needs_environment' => true)),
			new Twig_SimpleFunction('dumps', 'twig_dumps', array('is_safe' => array('html'), 'needs_context' => true, 'needs_environment' => true)),
			new Twig_SimpleFunction('this_url', 'twig_this_url', array('needs_environment' => true)),
			new Twig_SimpleFunction('config', function($var){
				return isset($this->config[$var]) ? $this->config[$var] : '' ;
			}),
        );
    }

	public function getFilters()
	{
		return array(
			new Twig_SimpleFilter('money', function($price) {
				return $this->config['currency_symbol'] . ' ' .$price;
			}),
		);
	}

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'idh';
    }
}

//@todo: use money filter
function twig_currency(Twig_Environment $env)
{
	return ID_CURRENCY_SYMBOL;
}

function twig_link(Twig_Environment $env, $link)
{
	//return $link;
	return "http://localhost/idhostinger/idhostinger.com/web/index.php".$link;
}

//@todo: use truncate filter from default twig extensions pack
function twig_text_more(Twig_Environment $env, $txt)
{
	$num_words = 16;
	$words = array();
	$words = explode(" ", $txt, $num_words);

	if(count($words) == 16){
	   $words[15] = "";
	}
	
	$string = implode(" ", $words);
	return $string;
}

function twig_str_highlight(Twig_Environment $env, $txt, $param) {
	$term_escaped = preg_quote(strtolower($param));
	$res = preg_replace("~$term_escaped~i", '<span class="highlight">\0</span>', strtolower($txt));
	return ucfirst($res);
}

function twig_dumps(Twig_Environment $env, $txt) {
	if (is_array($txt)) {
		unset($txt['flash']);
	}
	$m = var_dump($txt, true);
	
    return $m;
}

function twig_this_url(Twig_Environment $env) {
	//$actual_link = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	$actual_link = "http://localhost/slightsite/public";
	return $actual_link;
}
