<?php
namespace core;

class View
{
    const DEFAULT_THEME = 'default';

    protected $data;
    protected $pageTitle;
    protected $currentTheme = self::DEFAULT_THEME;
    protected $pathToTemplates;
    protected $templateFile;
    protected $twig;

    public function setTheme(string $themeName)
    {
        $this->currentTheme = $themeName;
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
    
    public function __get($name)
    {
        return (isset($this->data[$name]) ? $this->data[$name] : '');
    }

    public function setPageTitle($title)
    {
        $this->pageTitle = $title;
    }

    protected function getTwig()
    {
        $this->pathToTemplates = $this->pathToTemplates ?? implode(DIRECTORY_SEPARATOR, [
            $_SERVER['DOCUMENT_ROOT'],
            '..',
            'app',
            'views',
            $this->currentTheme,
        ]);

        try {
            $loader = new \Twig\Loader\FilesystemLoader($this->pathToTemplates);


            /** @var \Twig\Environment $twig */
            $twig = new \Twig\Environment($loader, [
                'debug' => (bool)Settings::DEBUG_MODE,
                [
                    'cache' => $this->pathToTemplates . '_twig_cache',
                    'autoescape' => true
                ]
            ]);

            if (Settings::DEBUG_MODE) {
                $twig->addExtension(new \Twig\Extension\DebugExtension());
            }

            return $twig;
        } catch (\Exception $e) {
            print 'err'.$e->getMessage();
        }
    }

    public function render()
    {

        // Prepare js & css for custom pages
        $stylesScriptsFileName = Context::getInstance()->getRouter()->getStylesScriptsFileName();
        $pageStylesheets = "css/{$this->currentTheme}/{$stylesScriptsFileName}.css";
        $pageStylesheets = file_exists($pageStylesheets) ? "/{$pageStylesheets}" : "";
        $this->data['page_css'] = $pageStylesheets;
        $pageScripts = "js/{$this->currentTheme}/{$stylesScriptsFileName}.js";
        $pageScripts = file_exists($pageScripts) ? "/{$pageScripts}" : "";
        $this->data['page_js'] = $pageScripts;


        $twig = $this->getTwig();

        if (empty($this->templateFile)) {
            $this->templateFile =
                Context::getInstance()->getRouter()->getControllerName().
                DIRECTORY_SEPARATOR.
                Context::getInstance()->getRouter()->getActionToken().
                '.twig';
        }

        ob_start();
        echo $twig->render($this->templateFile, ['pageTitle'=> $this->pageTitle, 'view' => $this->data]);
        return ob_get_clean();
    }
}
