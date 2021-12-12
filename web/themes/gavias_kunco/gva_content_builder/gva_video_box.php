<?php 

if(!class_exists('element_gva_video_box')):
   class element_gva_video_box{

      public function render_form(){
         $fields = array(
            'type' => 'gsc_video_box',
            'title' => ('Video Box'), 
            'fields' => array(
               array(
                  'id'        => 'content',
                  'type'      => 'text',
                  'title'     => t('Data Url'),
                  'desc'      => t('example: https://www.youtube.com/watch?v=4g7zRxRN1Xk'),
               ),
               array(
                  'id'        => 'image',
                  'type'      => 'upload',
                  'title'     => t('Image Preview'),
               ),
               array(
                  'id'           => 'style',
                  'type'         => 'select',
                  'title'        => 'Style',
                  'options'      => array(
                     'style-1'         => t('Style 1'),
                  )
               ),
              
               array(
                  'id'        => 'animate',
                  'type'      => 'select',
                  'title'     => t('Animation'),
                  'desc'      => t('Entrance animation for element'),
                  'options'   => gavias_content_builder_animate_aos(),
                  'class'     => 'width-1-2'
               ),
               array(
                  'id'        => 'animate_delay',
                  'type'      => 'select',
                  'title'     => t('Animation Delay'),
                  'options'   => gavias_content_builder_delay_aos(),
                  'desc'      => '0 = default',
                  'class'     => 'width-1-2'
               ), 
               array(
                  'id'        => 'el_class',
                  'type'      => 'text',
                  'title'     => t('Extra class name'),
                  'desc'      => t('Style particular content element differently - add a class name and refer to it in custom CSS.'),
               ),
            ),                                       
         );
         return $fields;
      }

      public static function render_content( $attr = array(), $content = '' ){
         global $base_url;
         extract(gavias_merge_atts(array(
            'title'           => '',
            'content'         => '',
            'image'           => '',
            'style'           => 'style-1',
            'sub_title'       => '',
            'link'            => '',
            'text_link'       => '',
            'animate'         => '',
            'animate_delay'   => '',
            'el_class'        => '',
         ), $attr));

         $_id = gavias_content_builder_makeid();
         if($image){
            $image = $base_url .$image; 
         }

         ob_start();
         ?>
      
      <?php if($style == 'style-1'){ ?>
         <div class="widget gsc-video-box <?php print $el_class;?> style-1 clearfix" <?php print gavias_content_builder_print_animate_aos($animate, $animate_delay) ?>>
            <div class="video-inner">
               <div class="image"><img src="<?php print $image ?>" alt=""/></div>
               <div class="video-body">
                  <a class="popup-video gsc-video-link" href="<?php print $content ?>">
                     <i class="fa icon-play space-40"></i>
                  </a>
               </div> 
            </div>    
         </div> 
      <?php } ?>
      
      <?php return ob_get_clean() ?>
       <?php
      }

   }
endif;   




