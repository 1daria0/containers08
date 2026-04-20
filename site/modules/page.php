<?php
class Page {
    private $templatePath;

    public function __construct($template) {
        $this->templatePath = $template;
    }

    public function Render($data) {
        if (!file_exists($this->templatePath)) {
            return "Template not found";
        }
        extract($data);
        ob_start();
        include $this->templatePath;
        return ob_get_clean();
    }
}