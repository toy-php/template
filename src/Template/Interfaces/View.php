<?php

namespace Template\Interfaces;

interface View
{
    /**
     * Получить директорию с шаблонами
     * @return string
     */
    public function getTemplateDir();

    /**
     * Получить расширение файлов
     * @return string
     */
    public function getTemplateExt();

    /**
     * Получить экземпляр парсера
     * @return Parser
     */
    public function makeParser();

    /**
     * Рендеринг шаблона
     * @param $templateName
     * @param ViewModel $viewModel
     * @return string
     */
    public function render($templateName, ViewModel $viewModel = null);
}