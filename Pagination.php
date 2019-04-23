<?php

/*
 * Author: roberson young
 * Email: coderzheng@foxmail.com
 * Usage example:
 * $np = isset($_GET['np']) ? intval($_GET['np']) : 1;
 * $total = 100;
 * $p = new Pagination($total, 10, 5, '/test_43.php');
 * $page_str = $p->getPageStr($np);
 */

class Pagination
{
    protected $total; //总记录数
    protected $pageCount; //每页显示记录数
    protected $showCount; // 页面显示页码个数
    protected $totalPage; // 总页数
    protected $url; //文件访问地址(除分页之外的其他部分)
    protected $foo; //处理url时需要用到的一个无意义参数名称
    protected $pageValName; //url上用于指定分页的变量名

    public function __construct($total, $pageCount, $showCount, $url = '', $foo = 'c', $pageValName = 'np')
    {
        $this->total = $total;
        $this->pageCount = $pageCount;
        $this->showCount = $showCount;
        $this->totalPage = ceil($this->total / $this->pageCount);
        $this->foo = $foo;
        $this->pageValName = $pageValName;
        $this->initializeUrl($url);
    }

    protected function initializeUrl($url)
    {
        $query_string = $_SERVER['QUERY_STRING'];
        if (empty($url)) {
            if (empty($query_string)) {
                $this->url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $this->foo . '=1';
                return;
            }
            if (strpos($query_string, $this->pageValName.'=') === false) {
                $this->url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $query_string;
                return;
            } else {
                $arr = explode("&", $query_string);
                foreach ($arr as $k => $v) {
                    $d_arr = explode('=', $v);
                    if ($d_arr[0] == $this->pageValName) {
                        unset($arr[$k]);
                    }
                }
                $query_string = implode("&", $arr);
                $this->url = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . '?' . $query_string;
                return;
            }
        }

        if (strpos($url, $this->pageValName.'=') === false) {
            $this->url = (strpos($url, '?') === false ? $url . '?' . $this->foo . '=1' : $url);
        } else {
            exit('指定的querystring字符串中包含和自定义的分页变量重名,请重新调整分页变量名');
        }
        return;
    }

    public function getPageStr($currentPage)
    {
        $page = $currentPage ? $currentPage : 1; // 当前页码, $currentPage需要自行计算
        $page = ($page > $this->totalPage) ? 1 : $page;
        $str_head = "共" . $this->total . "条记录" . "&nbsp;" . $page . "/" . $this->totalPage . "页&nbsp;";
        $str_first = ''; //首页
        $str_pre = ''; // 上页
        $str_a = ''; //页码部分
        $str_next = ''; //下页
        $str_last = ''; //尾页
        $str_form = ''; //表单
        $mod = $page % $this->showCount;
        if ($mod != 0) {
            $n = floor($page / $this->showCount);
        } else {
            $n = floor($page / $this->showCount) - 1;
        }
        for ($i = $this->showCount * $n + 1; $i <= ($this->showCount * $n + $this->showCount); $i++) {
            if ($i <= $this->totalPage) {
                if ($page != $i) {
                    $str_a .= '<a href="' . $this->url . '&' . $this->pageValName . '=' . $i . '">' . $i . '</a>&nbsp;';
                } else {
                    $str_a .= '<a class="bold">' . $i . '</a>&nbsp;';
                }
            } else {
                break;
            }
        }
        if ($page > 1) {
            $str_first = '<a href="' . $this->url . '&' . $this->pageValName . '=1">' . '首页</a>&nbsp;';
            $str_pre = '<a href="' . $this->url . '&' . $this->pageValName . '=' . ($page - 1) . '">上页</a>&nbsp;';
        } else {
            $str_first = "首页&nbsp;";
            $str_pre = '<a href="javascript:;">上页</a>&nbsp;';
        }

        if ($page < $this->totalPage) {
            $str_last = '<a href="' . $this->url . '&' . $this->pageValName . '=' . $this->totalPage . '">尾页</a>&nbsp;';
            $str_next = '<a href="' . $this->url . '&' . $this->pageValName . '=' . ($page + 1) . '">下页</a>&nbsp;';
        } else {
            $str_last = "尾页&nbsp;";
            $str_next = '<a href="javascript:;">下页</a>&nbsp;';
        }

        //$str_form = "<input type=\"button\" value=\"Go\" id=\"go\" class=\"gobtn\"><input type=\"text\" style=\"width:30px;\" name=\"np\" id=\"np\"  class=\"np\">";
        $str = $str_head . $str_first . $str_pre . $str_a . $str_next . $str_last . $str_form;
        return $str;
    }
}