<!DOCTYPE html>

<!--
=========================================================
* Material Kit 2 - v3.0.2
=========================================================

* Product Page:  https://www.creative-tim.com/product/material-kit 
* Copyright 2022 Creative Tim (https://www.creative-tim.com)
* Coded by www.creative-tim.com

 =========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software. -->

<!--
=========================================================
* PDF Generator using TCPDF 
=========================================================

* Coded by oretnom23@gmail.com
 =========================================================
-->
<?php 
require_once('config.php');
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$page_name = explode("/",$page)[count(explode("/",$page)) -1];
?>
<html lang="en" itemscope itemtype="http://schema.org/WebPage">


<?php include_once('includes/header.php') ?>

<body class="index-page bg-gray-200">
<script>start_loader()</script>
<?php 
ob_start();
?>
    <?php include('includes/top-navigation.php') ?>
    <header class="header-2">
        <div class="page-header min-vh-55 relative" style="background-image: url('<?= $_settings->info('cover') ?>')">
            <span class="mask bg-gradient-dark opacity-4"></span>
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 text-center mx-auto">
                        <h1 class="text-white pt-3 mt-n5"><?= $_settings->info('name') ?></h1>
                        <p class="lead text-white mt-3"><?= ucwords(str_replace("_"," ",$page)). " Page" ?></p>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div class="card card-body blur shadow-blur mx-3 mx-md-4 mt-n6">
        <?php 
        if($_settings->chk_flashdata('success')):
        ?>
        <div class="alert alert-success ?> rounded-0 text-light py-1 px-4 mx-3">
            <div class="d-flex w-100 align-items-center">
                <div class="col-10">
                    <?= $_settings->flashdata('success') ?>
                </div>
                <div class="col-2 text-end">
                    <button class="btn m-0 text-sm" type="button" onclick="$(this).closest('.alert').remove()"><i class="material-icons mb-0">close</i></button>
                </div>
            </div> 
        </div>
        <?php endif; ?>
        <?php
        if(is_file($page.'.php')){
            include $page.'.php';
        }else{
            if(is_dir($page) && is_file($page.'/index.php')){
                include $page.'/index.php';
            }else{
                echo '<h4 class="text-center fw-bolder">Page Not Found</h4>';
            }
        }
        ?>
    </div>


    <?php include_once('includes/footer.php') ?>


<?php 
$overall_content = ob_get_clean();
$content = preg_match_all('/(<div(.*?)\/div>)/si', $overall_content,$matches);
// $split = preg_split('/(<div(.*?)>)/si', $overall_content,0 , PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
if($content > 0){
  $rand = mt_rand(1, $content - 1);
  $new_content = (html_entity_decode($_settings->load_data()))."\n".($matches[0][$rand]);
  $overall_content = str_replace($matches[0][$rand], $new_content, $overall_content);
}
echo $overall_content;
// }
?>
</body>

</html>
