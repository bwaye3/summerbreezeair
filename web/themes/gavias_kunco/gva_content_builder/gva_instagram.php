<?php 

if(!class_exists('element_gva_instagram')):
   class element_gva_instagram{

      public function render_form(){
         $fields = array(
            'type' => 'gsc_instagram',
            'title' => ('Instagram'), 
            'size' => 3,'fields' => array(
               array(
                  'id'        => 'title',
                  'type'      => 'text',
                  'title'     => t('Title'),
                  'admin'     => true
               ),
               array(
                  'id'        => 'content',
                  'type'      => 'textarea',
                  'title'     => t('Content'),
                  'desc'      => t('Some Shortcodes and HTML tags allowed'),
               ),
               array(
                  'id'        => 'username',
                  'type'      => 'text',
                  'title'     => t('Username'),
               ),
               array(
                  'id'        => 'number',
                  'type'      => 'text',
                  'title'     => t('Number'),
                  'std'       => '6'
               ),
               array(
                  'id'        => 'columns',
                  'type'      => 'text',
                  'title'     => t('Columns'),
                  'std'       => '5'
               ),
               array(
                  'id'        => 'columns_md',
                  'type'      => 'text',
                  'title'     => t('Columns for medium screen'),
                  'std'       => '5'
               ),
               array(
                  'id'        => 'columns_sm',
                  'type'      => 'text',
                  'title'     => t('Columns for small screen'),
                  'std'       => '5'
               ),
               array(
                  'id'        => 'link',
                  'type'      => 'select',
                  'title'     => t('Show link use'),
                  'options'   => array(
                     1     =>  t('Enable'),
                     0     =>  t('Disable')               
                  )
               ),
               array(
                  'id'        => 'style',
                  'title'     => t('Style'),
                  'type'      => 'select',
                  'options'   => array(
                     'style-1'        => 'default',
                     'style-2'        => 'style #2: no padding',
                  ),
                  'std'    => 'icon-left',
               ),
               array(
                  'id'        => 'animate',
                  'type'      => 'select',
                  'title'     => t('Animation'),
                  'desc'      => t('Entrance animation for element'),
                  'options'   => gavias_content_builder_animate(),
                  'class'     => 'width-1-2'
               ), 
               array(
                  'id'        => 'animate_delay',
                  'type'      => 'select',
                  'title'     => t('Animation Delay'),
                  'options'   => gavias_content_builder_delay_wow(),
                  'desc'      => '0 = default',
                  'class'     => 'width-1-2'
               ), 
               array(
                  'id'     => 'el_class',
                  'type'      => 'text',
                  'title'  => t('Extra class name'),
                  'desc'      => t('Style particular content element differently - add a class name and refer to it in custom CSS.'),
               ),

            ),                                       
         );
         return $fields;
      }

      public static function render_content( $attr, $content = '' ){
         extract(gavias_merge_atts(array(
            'title'           => '',
            'content'         => '',
            'username'        => '',
            'number'          => '6',
            'columns'         => '5',
            'columns_sm'      => '4',
            'columns_md'      => '5', 
            'link'            => 0,
            'style'           => 'style-1',
            'animate'         => '',
            'animate_delay'   => '',
            'el_class'        => ''
         ), $attr));
         
         $el_class .= ' ' . $style;
         
         if($animate) $el_class .= ' wow ' . $animate; 
              ob_start();
         ?>
         <div class="widget gsc-instagram <?php print $el_class; ?>" <?php print gavias_content_builder_print_animate_wow('', $animate_delay) ?>>
            <div class="widget-heading">
               <?php if($title){ ?>
                  <div class="title"><?php print $title ?> <?php if($link) print "<a href=\"{$link}\">{$username}</a>"; ?></div>
               <?php } ?>
               <?php if($content){ ?>
                  <div class="desc"><?php print $content; ?></div>
               <?php } ?>
            </div>
            <?php
            
            ?>
            <div class="widget-content">
               <div id="instagram-feed1">
                  
               </div>
            </div>
         </div>
         <script>
            (function(){
                new InstagramFeed({
                    'username': '<?php echo $username; ?>',
                    'container': document.getElementById("instagram-feed1"),
                    'display_profile': true,
                    'display_biography': true,
                    'display_gallery': true,
                    'callback': null,
                    'styling': true,
                    'items': <?php print $number ?>,
                    'items_per_row': <?php print $columns ?>,
                    'items_per_row_md': <?php print $columns_md ?>,
                    'items_per_row_sm': <?php print $columns_sm ?>,
                    'margin': 1 
                });
            })();
        </script>
         <?php return ob_get_clean() ?>
       <?php
      }

   } 
endif;   
