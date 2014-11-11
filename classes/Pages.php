<?php
/**
 * Created by PhpStorm.
 * User: nevoband
 * Date: 5/20/14
 * Time: 1:34 PM
 */

class Pages {

    private $sqlDataBase;
    private $pages;
    private $default;
    private $navigationPages;
    private $showNavigation;

    public function __construct(PDO $sqlDataBase)
    {
        $this->sqlDataBase = $sqlDataBase;
        $this->LoadPages();
        $this->default = 0;
    }

    public function __destruct()
    {

    }

    private function LoadPages()
    {
        $queryPages = "SELECT * FROM pages";
        $pagesInfo = $this->sqlDataBase->prepare($queryPages);
        $pagesInfo->execute();
        $pagesArr = $pagesInfo->fetchAll(PDO::FETCH_ASSOC);
        $this->pages = array();
        $this->navigationPages = array();
        foreach($pagesArr as $id=>$page)
        {
            $this->pages[$page['page_name']]=$page['id'];
            $this->navigationPages[$page['page_name']]=$page['show_navigation'];

        }
    }

    public function GetPageId($pageName)
    {
        return $this->pages[$pageName];
    }

    public function GetPagesList()
    {
        return $this->pages;
    }

    public function SetDefaultPage($pageName)
    {
        $this->default = $this->pages[$pageName];
    }

    public function GetNavigationPages()
    {
        return $this->navigationPages;
    }
    public function GetDefaultPage()
    {
        return $this->default;
    }
} 