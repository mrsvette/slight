<?php
namespace PanelAdmin\Components;

class AdminTools
{
    protected $basePath;
    protected $themeName;

    public function __construct($settings)
    {
        $this->basePath = (is_object($settings))? $settings['basePath'] : $settings['settings']['basePath'];
        $this->themeName = (is_object($settings))? $settings['theme']['name'] : $settings['settings']['theme']['name'];
    }

    public function getPages()
    {
        $pages = array();
        foreach (glob($this->basePath.'/themes/'.$this->themeName.'/views/*.phtml') as $filename) {
            $page = basename($filename, '.phtml');
            if ( $page == 'index' ){
                $name = 'Home';
            } else {
                $name = ucwords( implode(" ", explode("-", $page)) );
            }
            $pages[] = [ 'name' => $name, 'slug' => $page, 'path' => $filename, 'info' => pathinfo($filename) ];
        }

        return $pages;
    }

	public function getPage($slug)
	{
		$path = $this->basePath.'/themes/'.$this->themeName.'/views/'.$slug.'.phtml';
		if (!file_exists($path))
			return false;
		return [ 'page' => $slug, 'path' => $path, 'content' => file_get_contents($path) ];
	}

	public function createPage($data)
	{
		if (is_array(self::getPage($data['name'])))
			return false;

		// create the file
		$slug = str_replace(" ", "-", strtolower($data['name']));
		$fp = fopen($this->basePath.'/themes/'.$this->themeName.'/views/'.$slug.'.phtml', "wb");
		fwrite($fp, $data['content']);
		fclose($fp);

		return true;
	}

	public function deletePage($slug)
	{
		$pages = self::getPage($slug);
		if (!is_array($pages))
			return false;

		// delete the file
		unlink($pages['path']);

		return true;
	}
}
