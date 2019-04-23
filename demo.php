<?php

require 'Pagination.php';

$np = isset($_GET['np']) ? intval($_GET['np']) : 1;
$total = 100;
$p = new Pagination($total, 10, 5, '');
$page_str = $p->getPageStr($np);

echo $page_str;

