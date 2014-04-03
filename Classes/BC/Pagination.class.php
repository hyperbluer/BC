<?php
/**
 * Pagination.class.php 分页类
 *
 *
 * @author          hyperblue
 * @version         0.1
 * @copyright       (C) 2013- *
 * @license	        http://www.bingceng.com
 * @lastmodify	    2013-06-27
 */

defined('IN_BC') or exit("Access Denied!");

class BC_Pagination
{
    /**
     * 页面Url
     *
     * @var
     */
    public $url;

    /**
     * Url码参数名
     *
     * @var string
     */
    public $paramName = 'page';
    /**
     * 记录总数
     *
     * @var int
     */
    public $totalResult = 0;
    /**
     * 每页显示记录数
     *
     * @var int
     */
    public $limit = 10;
    /**
     * 当前页码
     *
     * @var int
     */
    public $page = 1;

    /**
     * 是否采取ajax请求显示页面
     *
     * @var bool
     */
    public $isAjax = false;
    /**
     * ajax请求的js方法名，默认为pageAjax，当且仅当$isAjax为真触发。
     * 根据前端js，自行赋值修改
     *
     * @var string
     */
    public $ajaxActionName = 'pageAjax';

    /**
     * 首页显示文字
     *
     * @var string
     */
    public $firstPageText = '首页';
    /**
     * 尾页显示文字
     *
     * @var string
     */
    public $lastPageText = '尾页';
    /**
     * 下一页显示文字
     *
     * @var string
     */
    public $prevPageText = '上一页';
    /**
     * 上一页显示文字
     *
     * @var string
     */
    public $nextPageText = '下一页';
    /**
     * 当前页码样式
     *
     * @var string
     */
    public $currentPageStyle = 'class="active"';
    /**
     * 链接模板
     *
     * @var string
     */
    public $linkTpl = '<li><a{href}{attributes}>{text}</a></li>';
    /**
     * 分页显示模板
     *
     * @var string
     */
    public $htmlTpl = '<ul>{first}{prev}{pages}{next}{last}</ul><div class="pageTotalInfo">{page}/{pageCount} Total:{totalResult}</div>';

    /**
     * 构造函数
     */
    public function __construct()
    {
        empty($this->url) && $this->setUrl();
    }

    /**
     * 显示分页
     *
     * @return mixed
     */
    public function show()
    {
        $this->page = max(1, $this->page);
        $pageCount = ceil($this->totalResult/$this->limit);

        if ($pageCount <= 0 || $this->page > $pageCount) return '';
        
        $search = array(
            '{first}',
			'{prev}',
			'{pages}',
			'{next}',
			'{last}',
			'{page}',
			'{totalResult}',
            '{pageCount}'
        );
        $replace = array(
            $this->getFirstPageLink($pageCount),
            $this->getPrevPageLink($pageCount),
            $this->getPages($pageCount),
            $this->getNextPageLink($pageCount),
            $this->getLastPageLink($pageCount),
            $this->page,
            $this->totalResult,
            $pageCount
        );
        
        $html = str_replace($search, $replace, $this->htmlTpl);
        
        return $html;
    }

    /**
     * 获取页面列表，以页面数字为单位显示
     *
     * @param $pageCount
     * @return string
     */
    public function getPages($pageCount)
    {
        $pages = '';
        for ($i = 1; $i <= $pageCount; $i++)
        {
            $style = $i == $this->page ? $this->currentPageStyle : '';
            $url = $this->getUrl($i);
            $pages .= $this->getLink($i, $url, $style);
        }

        return $pages;
    }

    /**
     * 获取首页html
     * 
     * @param $pageCount    页面总数
     * @return mixed
     */
    public function getFirstPageLink($pageCount)
    {
        $firstPageUrl = $pageCount ? $this->getUrl(1) : '';
        
        return $this->getLink($this->firstPageText, $firstPageUrl);
    }

    /**
     * 获取尾页html
     *
     * @param $pageCount    页面总数
     * @return mixed
     */
    public function getLastPageLink($pageCount)
    {
        $lastPageUrl = $pageCount ? $this->getUrl($pageCount) : '';

        return $this->getLink($this->lastPageText, $lastPageUrl);
    }

    /**
     * 获取上一页html
     * 
     * @param $pageCount    页面总数
     * @return mixed
     */
    public function getPrevPageLink($pageCount)
    {
        $prevPageNumber = $this->page - 1;
        $prevPageUrl = $prevPageNumber > 0 && $pageCount ? $this->getUrl($prevPageNumber) : '';

        return $this->getLink($this->prevPageText, $prevPageUrl);
    }

    /**
     * 获取下一页html
     *
     * @param $pageCount    页面总数
     * @return mixed
     */
    public function getNextPageLink($pageCount)
    {
        $nextPageNumber = $this->page + 1;
        $nextPageUrl = $nextPageNumber <= $pageCount && $pageCount ? $this->getUrl($nextPageNumber) : '';

        return $this->getLink($this->nextPageText, $nextPageUrl);
    }

    /**
     * 组装链接html
     *
     * @param $text 显示文字
     * @param string $url   链接Url
     * @param string $style 标签样式
     * @return mixed
     */
    public function getLink($text, $url = '', $style = '')
    {
        if ($url)
        {
            if($this->isAjax)
            {
                $_attrOnClick = $this->ajaxActionName ?  ' onclick="'.$this->ajaxActionName.'(\''.$url.'\')"' : '';
                $href = ' href="#" ';
                $attributes = ' '.$style.$_attrOnClick.' ';
            }
            else
            {
                $href = ' href="'.$url.'" ';
                $attributes = ' '.$style.' ';
            }
        }
        else
        {

            $href = '';
            $attributes = $style;
        }

        $search = array(
            '{text}',
			'{href}',
			'{attributes}',
        );
        $replace = array(
            $text,
            $href,
            $attributes
        );
        $link = str_replace($search, $replace, $this->linkTpl);
        
        return $link;
    }

    /**
     * 获取指定分页页面Url
     *
     * @param $pageNumber 分页id
     * @return string
     */
    public function getUrl($pageNumber)
    {
        $urlObject = new Url($this->url);
        $urlObject->addParam($this->paramName, $pageNumber, true);

        return $urlObject->getUrl();
    }

    /**
     * 设置url，默认为当前访问Url
     *
     * @param string $url
     * @return void
     */
    public function setUrl($url = '')
    {
        $this->url = $url ? $url : Url::getCurrentUrl();
    }
}