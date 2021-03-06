<?php

class string extends model
{
    protected $stringid, $symbol, $comment;
    public $string;

    public function __construct(&$parent)
    {
        parent::__construct($parent);

        $this->table = 'strings';
        $this->idfield = 'stringid';
        $this->orderby_field = 'stringid';
        //$this->idfield = 's_id';
        //$this->orderby_field = 's_id';
        $this->config = $this->parent->config;

    }

    public function load($stringid)
    {
        $info = $this->db->query("select string from {$this->table} where stringid = '{$stringid}' limit 1")->results();
        $this->string = $info['string'];
    }

    public function toArray()
    {
        $arr = array(
            'stringid' => $this->stringid,
            'value' => $this->string,
            //'id' => $this->s_id,
            'comment' => $this->comment,
            'symbol' => $this->symbol,
        );

        return $arr;
    }

    public function __toString()
    {
        echo $this->string;
    }

    public function db_update()
    {
        $this->delete_all();
        $this->db_update_from_api();

        //     print_r($this->config['stringfiles'] );

        //     foreach($this->config['stringfiles'] as $fname)
        //     {
        //         $f = "{$this->config['stringspath']}\\{$fname}{$this->config['fileext']}";
        //         $this->xmltodb($f);
        //     }
    }

    public function db_update_from_api()
    {
        $response = json_decode(file_get_contents("https://api.projectceleste.com/game/languages/"), true);
        foreach ($response['data'] as $stringtablename => $stringtable) {
            foreach ($stringtable['language']['English'] as $englishStrings) {
                foreach ($englishStrings as $string) {
                    print_r($string);
                    echo '-';
                    echo '<br/>';
                    $insertData = array(
                        'stringid' => $this->db->clean_string($string['_locid']),
                        'string' => $this->db->clean_string($string['text']),
                        'language' => 'English');

                    try {
                        $this->quicksave($insertData);
                    } catch (Exception $err) {
                        echo ($err);
                    }
                }
            }
        }
    }

    private function xmltodb($xmlPath)
    {
        echo $xmlPath . "<br />";
        $dearlord = file_get_contents($xmlPath);

        $XMLReader = new XMLReader();
        $XMLReader->xml($this->CleanUpXML($dearlord));

        while ($XMLReader->read()) {
            if ($XMLReader->name == 'string' && $XMLReader->nodeType != XMLReader::END_ELEMENT) {
                $this->stringid = $this->db->clean_string($XMLReader->getAttribute("_locid"));
                $this->symbol = $this->db->clean_string($XMLReader->getAttribute("symbol"));
                $this->comment = $this->db->clean_string($XMLReader->getAttribute("comment"));
                $this->string = $this->db->clean_string($XMLReader->readString());

                $insertData = array(
                    'stringid' => $this->stringid,
                    'symbol' => $this->symbol,
                    'comment' => $this->comment,
                    'string' => $this->string);
                try {
                    $this->quicksave($insertData);
                } catch (Exception $err) {
                    echo ($err);
                }
            }

        }
    }

    private function CleanUpXML($xmlstring)
    {
        $pattern = "/(<color=)([01]\.[01],[01]\.[01],[01]\.[01])(>)/i";
        $replacement = "$1 \"$2\"$3";
        $xmlstring = preg_replace($pattern, $replacement, $xmlstring);
        $pattern = "/<([a-zA-Z]+)=/";
        $replacement = "<$1 $1=";
        $xmlstring = preg_replace($pattern, $replacement, $xmlstring);
        $pattern = "/<icon icon=([^<>]+)>/";
        $replace = "(icon=$1)";
        $xmlstring = preg_replace($pattern, $replace, $xmlstring);
        $xmlstring = str_replace('\<', '<', $xmlstring);

        return $xmlstring;

    }

}
