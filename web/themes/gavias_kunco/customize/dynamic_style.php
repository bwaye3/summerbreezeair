<style class="customize">
<?php
    $customize = (array)json_decode($json, true);
    if($customize):
?>

    <?php //================= Font Body Typography ====================== ?>
    <?php if(isset($customize['font_family_primary'])  && $customize['font_family_primary'] != '---'){ ?>
        body,
        header.header-v4 .main-menu ul.gva_menu_main > li > a, header.header-v4 .header-action a, body.footer-v2 .block .block-title span, body.footer-v2 .copyright a,
        body.footer-v3 #footer .block .block-title span, .progress .progress-bar .percentage, .block.block-blocktabs .ui-widget, .block.block-blocktabs .ui-tabs-nav > li > a, 
        .gva-mega-menu .block-blocktabs .ui-widget, .gsc-carousel-content.style-1 .content-box .content-inner .action a,.gva-mega-menu .block-blocktabs .ui-tabs-nav > li > a,
        .event-full .event-info
        {
            font-family: <?php echo $customize['font_family_primary'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['font_family_second'])  && $customize['font_family_second'] != '---'){ ?>
        h1, h2, h3, h4, h5, h6,
        .h1, .h2, .h3, .h4, .h5, .h6,
        .breadcrumb-content-inner .gva-breadcrumb-content .page-title, .page-notfound .big-title, .page-notfound .action a, .post-block .post-title a, .post-style-list.small .post-title a, .testimonial-node-v1 .quote,
        .testimonial-node-v1 .content-inner .title, .team-block .team-content .team-name, .text-size-big, .nav-tabs > li > a, .owl-carousel.carousel-thumbnail.text.top .owl-dots .owl-dot, .owl-carousel.carousel-thumbnail.text.vertical .owl-dots .owl-dot,
        .btn, .btn-white, .btn-theme, .btn-theme-second, .more-link a, .btn-theme-submit, .btn-line, .user-login-form input[type*='submit'], .user-register-form input[type*='submit'], .user-form input[type*='submit'], .user-pass input[type*='submit'],
        .progress-label, .pricing-table .content-wrap .plan-price .price-value .dollar, .pricing-table .content-wrap .plan-price .price-value .value, .pricing-table .content-wrap .plan-price .interval, .rotate-text .primary-text, #search-block-form input.form-submit,
        .block.block-simplenews input#edit-subscribe, .sidebar .block-menu ul li a, .copyright-links a, .navigation .gva_menu > li > a, .navigation .gva_menu .megamenu > .sub-menu > li > ul.sub-menu li, .navigation .gva_menu .megamenu > .sub-menu > li > a, .navigation .gva_menu .sub-menu > li > a,
        ul[data-drupal-views-infinite-scroll-pager].pager a, .more-link a, .category-list .item-list ul li a, .small-list .item-list ul > li a, .tags-list .item-list > ul > li a , .widget.gsc-heading .sub-title, .widget.gsc-heading .title-desc, .widget.gsc-heading.style-3 .title, .widget.gsc-heading.style-4 .title,
        .widget.gsc-chart .content .title, .widget.gsc-icon-box .highlight_content .title, .widget.milestone-block .milestone-text, .gsc-box-parallax .content-inner .subtitle, .gsc-hover-background .front h2 , .gsc-button, .gsc-image-content.skin-v2 .box-content .read-more a, .gsc-instagram .title, .gsc-carousel-content.style-2 .content-box .content-inner .title a,
         .gva-offcanvas-mobile .gva-navigation .gva_menu > li > a, .gva-offcanvas-mobile .gva-navigation .gva_menu .sub-menu li a, .event-block .event-content .event-info .title, .event-block-list .event-date span.day, .event-block-list .event-title a  
         {
            font-family: <?php echo $customize['font_family_second'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['font_body_size'])  && $customize['font_body_size']){ ?>
        body{
            font-size: <?php echo ($customize['font_body_size'] . 'px'); ?>;
        }
    <?php } ?>    

    <?php if(isset($customize['font_body_weight'])  && $customize['font_body_weight']){ ?>
        body{
            font-weight: <?php echo $customize['font_body_weight'] ?>;
        }
    <?php } ?>    

    <?php //================= Body ================== ?>

    <?php if(isset($customize['body_bg_image'])  && $customize['body_bg_image']){ ?>
        body{
            background-image:url('<?php echo drupal_get_path('theme', 'gavias_kunco') .'/images/patterns/'. $customize['body_bg_image']; ?>');
        }
    <?php } ?> 
    <?php if(isset($customize['body_bg_color'])  && $customize['body_bg_color']){ ?>
        body{
            background-color: <?php echo $customize['body_bg_color'] ?>!important;
        }
    <?php } ?> 
    <?php if(isset($customize['body_bg_position'])  && $customize['body_bg_position']){ ?>
        body{
            background-position:<?php echo $customize['body_bg_position'] ?>;
        }
    <?php } ?> 
    <?php if(isset($customize['body_bg_repeat'])  && $customize['body_bg_repeat']){ ?>
        body{
            background-repeat: <?php echo $customize['body_bg_repeat'] ?>;
        }
    <?php } ?> 

    <?php //================= Body page ===================== ?>
    <?php if(isset($customize['text_color'])  && $customize['text_color']){ ?>
        body .body-page{
            color: <?php echo $customize['text_color'] ?>;
        }
    <?php } ?>

    <?php if(isset($customize['link_color'])  && $customize['link_color']){ ?>
        body .body-page a{
            color: <?php echo $customize['link_color'] ?>!important;
        }
    <?php } ?>

    <?php if(isset($customize['link_hover_color'])  && $customize['link_hover_color']){ ?>
        body .body-page a:hover{
            color: <?php echo $customize['link_hover_color'] ?>!important;
        }
    <?php } ?>

    <?php //===================Header=================== ?>
    <?php if(isset($customize['header_bg'])  && $customize['header_bg']){ ?>
        header .header-main{
            background: <?php echo $customize['header_bg'] ?>!important;
        }
    <?php } ?>

    <?php if(isset($customize['header_color_link'])  && $customize['header_color_link']){ ?>
        header .header-main a{
            color: <?php echo $customize['header_color_link'] ?>!important;
        }
    <?php } ?>

    <?php if(isset($customize['header_color_link_hover'])  && $customize['header_color_link_hover']){ ?>
        header .header-main a:hover{
            color: <?php echo $customize['header_color_link_hover'] ?>!important;
        }
    <?php } ?>

   <?php //===================Menu=================== ?>
    <?php if(isset($customize['menu_bg']) && $customize['menu_bg']){ ?>
        .main-menu, ul.gva_menu{
            background: <?php echo $customize['menu_bg'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['menu_color_link']) && $customize['menu_color_link']){ ?>
        .main-menu ul.gva_menu > li > a{
            color: <?php echo $customize['menu_color_link'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['menu_color_link_hover']) && $customize['menu_color_link_hover']){ ?>
        .main-menu ul.gva_menu > li > a:hover{
            color: <?php echo $customize['menu_color_link_hover'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['submenu_background']) && $customize['submenu_background']){ ?>
        .main-menu .sub-menu{
            background: <?php echo $customize['submenu_background'] ?>!important;
            color: <?php echo $customize['submenu_color'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['submenu_color']) && $customize['submenu_color']){ ?>
        .main-menu .sub-menu{
            color: <?php echo $customize['submenu_color'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['submenu_color_link']) && $customize['submenu_color_link']){ ?>
        .main-menu .sub-menu a{
            color: <?php echo $customize['submenu_color_link'] ?>!important;
        }
    <?php } ?> 

    <?php if(isset($customize['submenu_color_link_hover']) && $customize['submenu_color_link_hover']){ ?>
        .main-menu .sub-menu a:hover{
            color: <?php echo $customize['submenu_color_link_hover'] ?>!important;
        }
    <?php } ?> 

    <?php //===================Footer=================== ?>
    <?php if(isset($customize['footer_bg']) && $customize['footer_bg'] ){ ?>
        #footer .footer-center{
            background: <?php echo $customize['footer_bg'] ?>!important;
        }
    <?php } ?>

     <?php if(isset($customize['footer_color'])  && $customize['footer_color']){ ?>
        #footer .footer-center{
            color: <?php echo $customize['footer_color'] ?> !important;
        }
    <?php } ?>

    <?php if(isset($customize['footer_color_link'])  && $customize['footer_color_link']){ ?>
        #footer .footer-center ul.menu > li a::after, .footer a{
            color: <?php echo $customize['footer_color_link'] ?>!important;
        }
    <?php } ?>    

    <?php if(isset($customize['footer_color_link_hover'])  && $customize['footer_color_link_hover']){ ?>
        #footer .footer-center a:hover{
            color: <?php echo $customize['footer_color_link_hover'] ?> !important;
        }
    <?php } ?>    

    <?php //===================Copyright======================= ?>
    <?php if(isset($customize['copyright_bg'])  && $customize['copyright_bg']){ ?>
        .copyright{
            background: <?php echo $customize['copyright_bg'] ?> !important;
        }
    <?php } ?>

     <?php if(isset($customize['copyright_color'])  && $customize['copyright_color']){ ?>
        .copyright{
            color: <?php echo $customize['copyright_color'] ?> !important;
        }
    <?php } ?>

    <?php if(isset($customize['copyright_color_link'])  && $customize['copyright_color_link']){ ?>
        .copyright a{
            color: $customize['copyright_color_link'] ?>!important;
        }
    <?php } ?>    

    <?php if(isset($customize['copyright_color_link_hover'])  && $customize['copyright_color_link_hover']){ ?>
        .copyright a:hover{
            color: <?php echo $customize['copyright_color_link_hover'] ?> !important;
        }
    <?php } ?>    
<?php endif; ?>    
</style>
