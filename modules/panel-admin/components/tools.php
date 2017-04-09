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

	/**
	 * Get all pages under themes folder
	 * @return array
	 */
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

	/**
	 * Geting the detail page information
	 * @param $slug
	 * @return array|bool
	 */
	public function getPage($slug)
	{
		$path = $this->basePath.'/themes/'.$this->themeName.'/views/'.$slug.'.phtml';
		if (!file_exists($path))
			return false;
		return [ 'page' => $slug, 'path' => $path, 'content' => file_get_contents($path) ];
	}

	/**
	 * Create new page, new phtml file inside themes directory
	 * @param $data
	 * @return bool
	 */
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

	/**
	 * Delete Page
	 * @param $slug
	 * @return bool
	 */
	public function deletePage($slug)
	{
		$pages = self::getPage($slug);
		if (!is_array($pages))
			return false;

		// delete the file
		unlink($pages['path']);

		return true;
	}

	/**
	 * List of themes
	 * @return array
	 */
	public function getThemes()
	{
		$items = array();
		foreach (scandir($this->basePath.'/themes') as $dir) {
			if ( !in_array($dir, ['.', '..']) && is_dir($this->basePath.'/themes/'.$dir) ){
				if (file_exists($this->basePath.'/themes/'.$dir.'/manifest.json')){
					$manifest = file_get_contents($this->basePath.'/themes/'.$dir.'/manifest.json');
					$item = json_decode($manifest, true);

					if (!is_array($item)){
						$item = ['id'=>$dir, 'name'=>ucfirst($dir), 'preview'=>'screenshot.png'];
					}

					$item ['path'] = $this->basePath.'/themes/'.$dir;
					$item ['img_path'] = 'themes/'.$dir.'/'.$item['preview'];
					$items[$dir] = $item;
				}
			}
		}

		return $items;
	}

	public function updateTheme($id)
	{
		if (!file_exists($this->basePath.'/themes/'.$id.'/manifest.json'))
			return false;

		$manifest = file_get_contents($this->basePath.'/themes/'.$id.'/manifest.json');
		$item = json_decode($manifest, true);

		if (!is_array($item)){
			$item = ['id'=>$id, 'name'=>ucfirst($id)];
		} else {
			$item = ['id'=>$item['id'], 'name'=>$item['name']];
		}

		$update = file_put_contents($this->basePath.'/data/theme.json', json_encode($item));

		return ($update)? true : false;
	}
}
