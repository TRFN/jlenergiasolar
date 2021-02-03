<?php
    trait Caminhos {
        /* PODE EDITAR OS VALORES AQUI */

        public $minificar = 1; /* MINIFICAR O CODIGO */

        public $remover_extensao_html_dos_links_das_paginas = 1; /* IRA REMOVER TODAS AS EXTENSOES [.html] DOS LINKS AUTOMATICAMENTE */

        public $corrigir_caminho = 1; /* INCLUI UMA BASE NO ROOT DO SITE PARA CORRIGIR CARREGAMENTOS INTERNOS */

        public $urls = array(
            /* URL PERSONALIZADA */    /* CAMINHO DO ARQUIVO */

            "/home"                 => "/index.html",                    /* PADRAO */
            "/formatacao/franquias" => "/formatacao-de-franquias.html",  /* EXEMPLO SUB-DIRETORIO */
            "/Formatacao/AdvoGados" => "/advocacia.html"                 /* CASE-SENSITIVE */

            /* CASO VOCE APAGUE, ELE TENTARA ACHAR DINAMICAMENTE. */
            /* SE O ARQUIVO NAO EXISTIR, ELE VOLTARA PARA PAGINA INICIAL */
        );

        /* NAO EDITAR A PARTIR DAQUI */

        function url(int $index=-1, String $url = "empty"){
            if($url == "empty"){
                $url = $_SERVER["REQUEST_URI"];
            }
            $url = explode("/", $url);
            array_shift($url);
            return $index == -1 ? array_filter($url):$url[$index];
        }
    }

    new class {
        use Caminhos;

        function __construct(){
            if(($file = $this->searchIndex())===false){
                header("Location: /");
            } else {
                header("Content-Type: text/html;Charset=UTF-8");
                $this->getCode($file);
            }
        }

        function getCode(String $content){
            $content = file_get_contents($content);

            if((bool)$this->corrigir_caminho===true){
                $content = "<base href='/' />" . $content;
            }

            if((bool)$this->remover_extensao_html_dos_links_das_paginas===true){
                $content = preg_replace('/\.html\"/','"',$content);
            }

            if((bool)$this->minificar===true){
                $regex = array('/\>[^\S ]+/s','/[^\S ]+\</s','/(\s)+/s');
                $subst = array('>','<','\\1');
                $content = preg_replace($regex,$subst,$content);
            }

            exit($content);
        }

        function base(){
            return __DIR__;
        }

        function path(){
            return implode("/",$this->url());
        }

        function searchIndex(){
            if(isset($this->urls["/".$this->path()]) && file_exists($fl=$this->base() . $this->urls["/".$this->path()])){
                return $fl;
            } elseif(file_exists($fl=$this->base() . "/" . $this->path() . ".html")){
                return $fl;
            } elseif(file_exists($fl=$this->base() . "/" . $this->path() . ".htm")){
                return $fl;
            } elseif(file_exists($fl=$this->base() . "/" . $this->path() . "/index.html")){
                return $fl;
            } elseif(file_exists($fl=$this->base() . "/" . $this->path() . "/index.htm")){
                return $fl;
            }

            return false;
        }
    };
?>
