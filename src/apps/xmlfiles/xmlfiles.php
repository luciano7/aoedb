<?php

class xmlfiles extends app
{
    public function __construct(&$parent)
    {
        parent::__construct($parent);

        $this->load->app('aoeo');
    }

    public function c_index($folder, $file)
    {
        $path = '/xmlfiles/' . $folder . '/' . $file . '.xml';
        $this->aoeo->header();
        start_tooltip();

        echo highlight_string(file_get_contents($path));

        end_tooltip();
        $this->aoeo->footer();

    }

    public function c_update()
    {
        $this->load->model('defaultxml');
        $this->m_defaultxml->export_all();
    }
}
