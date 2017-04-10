<?php

namespace Core\Template;

use Template\Interfaces\View as ViewInterface;
use Template\Interfaces\ViewModel as ViewModelInterface;
use Template\Interfaces\Parser as ParserInterface;

class View implements ViewInterface
{

    protected $templateDir;
    protected $templateExt;

    public function __construct($templateDir = '', $templateExt = '.php')
    {
        $this->templateDir = $templateDir;
        $this->templateExt = $templateExt;
    }

    /**
     * @return string
     */
    public function getTemplateDir()
    {
        return $this->templateDir;
    }

    /**
     * @return string
     */
    public function getTemplateExt()
    {
        return $this->templateExt;
    }

    /**
     * Получить экземпляр парсера
     * @return ParserInterface
     */
    public function makeParser()
    {
        return new Parser($this);
    }

    /**
     * Рендеринг шаблона
     * @param $templateName
     * @param ViewModelInterface $viewModel
     * @return string
     */
    public function render($templateName, ViewModelInterface $viewModel = null)
    {
        return $this->makeParser()->render($templateName, $viewModel);
    }
}