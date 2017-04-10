<?php

namespace Template\Interfaces;

interface Parser
{

    /**
     * Рендеринг шаблона
     * @param $templateName
     * @param $viewModel
     * @return string
     */
    public function render($templateName, ViewModel $viewModel = null);
}