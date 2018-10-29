<?php

namespace app;

class Task
{
    /**
     * /name task
     * @var string
     */
    public $task;

    /**
     * Params for task
     * @var array
     */
    public $params;

    public function __construct($task, $params)
    {
        $this->task = $task;
        $this->params = $params;
    }

    /**
     * Translates a number into a string based on the language
     * @param $summ float
     * @param $lang string (ua,ru)
     * @return string if all ok or false, where have problem
     */
    public function get_price()
    {
        if (empty($this->params['summ']) || empty($this->params['lang'])) {
            return false;
        }
        if (!is_numeric($this->params['summ'])) return false;
        $this->params['lang'] = strtoupper($this->params['lang']);
        $arr = ['UA', 'RU'];
        $key = array_search( $this->params['lang'], $arr);
        if (!is_numeric($key)) return false;
        $post = [
            'roubles' => $this->params['summ'],
            'lang' => $key,    //0 - UA , 1-RU
            'q' => 'assets/snippets/sumprop/sumprop.php',
        ];        $ch = curl_init("http://e-kao.ru/index-ajax.php");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($ch);
        curl_close($ch);
        preg_match('@<textarea.*?>(.+?)<\/textarea>@is', $response, $matches);
        if (!empty($matches)) {
            return $matches[1];
        }
        return false;
    }

    /**
     * On the input it receives an integer, to the output: the value of the Fibonacci
     * number for the transmitted parameter
     * @param $n integer
     * @return integer
     */
    function fibonacci() {
        if(empty($this->params['number'])) return false;
        if(!is_numeric($this->params['number'])) return false;
        $n = $this->params['number'];
        $sq5 = sqrt(5);
        $a = (1 + $sq5) / 2;
        $b = (1 - $sq5) / 2;
        return (pow($a, $n) - pow($b, $n)) / $sq5;
    }
}