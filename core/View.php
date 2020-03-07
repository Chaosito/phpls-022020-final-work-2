<?php
namespace core;

class View
{
    protected $data;
    protected $pathToWrapper = '';
    protected $pathToTemplate;
    protected $pageTitle;

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
    
    public function __get($name)
    {
        return (isset($this->data[$name]) ? $this->data[$name] : '');
    }
    
    public function setTemplatePath($path)
    {
        $this->pathToTemplate = $path;
    }

    public function setWrapperPath($path)
    {
        $this->pathToWrapper = $path;
    }

    public function setPageTitle($title)
    {
        $this->pageTitle = $title;
    }
    
    public function render()
    {
        ob_start();
        if (file_exists($this->pathToWrapper)) {
            include $this->pathToWrapper;
        } else {
            throw new \Exception("Template `{$this->pathToWrapper}` not found!!!");
        }
        return ob_get_clean();
    }
}
