<?php

namespace Core\Template;

use Template\Interfaces\View as ViewInterface;
use Template\Interfaces\ViewModel as ViewModelInterface;
use Template\Interfaces\Parser as ParserInterface;

class Parser implements ParserInterface
{

    /**
     * @var View
     */
    protected $template;

    /**
     * Модель представления
     * @var ViewModelInterface
     */
    protected $model = null;

    /**
     * Имя макета шаблона
     * @var string
     */
    protected $layoutTemplateName = '';

    /**
     * Модель макета шаблона
     * @var ViewModelInterface
     */
    protected $layoutModel = null;

    /**
     * Секции
     * @var array
     */
    protected $section = [];

    public function __construct(ViewInterface $template)
    {
        $this->template = $template;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return (!empty($this->model) and isset($this->model->$name))
            ? $this->model->$name
            : null;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return (!empty($this->model) and isset($this->model->$name));
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        if (!empty($this->model) and method_exists($this->model, $name)) {
              return $this->model->$name(...$arguments);
        }
        throw new Exception('Метод недоступен');
    }

    /**
     * Старт секции
     * @param string $name
     * @throws Exception
     */
    public function start($name)
    {
        if ($name === 'content') {
            throw new Exception('Секция с именем "content" зарезервированна.');
        }
        $this->section[$name] = null;
        ob_start();
    }

    /**
     * Стоп секции
     * @throws Exception
     */
    public function stop()
    {
        if (empty($this->section)) {
            throw new Exception('Сперва нужно стартовать секцию методом start()');

        }
        end($this->section);
        $this->section[key($this->section)] = ob_get_contents();
        ob_end_clean();
    }

    /**
     * Вывод секции
     * @param string $name
     * @return string|null;
     */
    public function section($name)
    {
        return isset($this->section[$name]) ? $this->section[$name] : null;
    }

    /**
     * Объявление макета шаблона
     * @param $layoutTemplateName
     * @param ViewModelInterface $layoutModel
     */
    public function layout($layoutTemplateName, ViewModelInterface $layoutModel = null)
    {
        $this->layoutTemplateName = $layoutTemplateName;
        $this->layoutModel = $layoutModel ?: $this->model;
    }

    /**
     * Вставка представления в текущий шаблон
     * @param $templateName
     * @param ViewModelInterface $viewModel
     * @return string
     */
    public function insert($templateName, ViewModelInterface $viewModel = null)
    {
        return $this->template->makeParser()
            ->render($templateName, $viewModel ?: $this->model);
    }

    /**
     * Загрузка шаблона
     * @param $templateName
     * @throws Exception
     */
    private function loadTemplateFile($templateName)
    {
        $fileName = $this->template->getTemplateDir() .
            $templateName .
            $this->template->getTemplateExt();
        if (!file_exists($fileName)) {
            throw new Exception('Файл шаблона "' . $fileName . '" не может быть загружен');
        }
        include $fileName;
    }

    /**
     * @inheritdoc
     */
    public function render($templateName, ViewModelInterface $viewModel = null)
    {
        try {
            ob_start();
            $this->model = $viewModel;
            $this->loadTemplateFile($templateName);
            $content = ob_get_contents();
            ob_end_clean();
            if (!empty($this->layoutTemplateName)) {
                $layout = $this->template->makeParser();
                $layout->section = array_merge($this->section, ['content' => $content]);
                $content = $layout->render($this->layoutTemplateName, $this->layoutModel);
            }
            return $content;
        } catch (Exception $e) {
            if (ob_get_length() > 0) {
                ob_end_clean();
            }
            throw $e;
        }
    }

}