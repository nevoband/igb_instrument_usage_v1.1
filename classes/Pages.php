<?php
/**
 * Created by PhpStorm.
 * User: nevoband
 * Date: 5/20/14
 * Time: 1:34 PM
 *
 * Object manages website page names to allow for better integration with permissions and dynamic navigation
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

    /**
     * Load all available pages from database to this object
     */
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

    /**Get a page id given a page name
     * @param $pageName
     * @return mixed
     */
    public function GetPageId($pageName)
    {
        return $this->pages[$pageName];
    }

    /**Get a list of available pages
     * @return mixed
     */
    public function GetPagesList()
    {
        return $this->pages;
    }

    /**Set the default page
     * @param $pageName
     */
    public function SetDefaultPage($pageName)
    {
        $this->default = $this->pages[$pageName];
    }

    /**Get all navigation pages
     * @return mixed
     */
    public function GetNavigationPages()
    {
        return $this->navigationPages;
    }

    /**Get the default page
     * @return int
     */
    public function GetDefaultPage()
    {
        return $this->default;
    }
} 