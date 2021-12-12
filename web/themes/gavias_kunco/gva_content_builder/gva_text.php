<?php 
if(!class_exists('element_gva_text')):
   class element_gva_text{
      public function render_form(){
         $fields = array(
            'type' => 'gsc_text',
            'title' => t('Custom Text'),
            'fields' => array(
               array(
                  'id'     => 'title',
                  'type'      => 'text',
                  'title'  => t('Title'),
                   'admin'     => true
               ),
               array(
                  'id'           => 'content',
                  'type'         => 'textarea',
                  'title'        => t('Column content'),
                  'desc'         => t('Shortcodes and HTML tags allowed.'),
                  'shortcodes'   => 'on'
               ),
               array(
                  'id'        => 'style',
                  'type'      => 'select',
                  'title'     => t('Style display'),
                  'options'   => array(
                        ''   => 'Default',
                        'text-size-medium'  => 'Text medium (font-size: 30px, font-weight: 100)',
                  )
               ), 
               array(
                  'id'     => 'animate',
                  'type'      => 'select',
                  'title'  => ('Animation'),
                  'desc'  => t('Entrance animation for element'),
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
         extract(gavias_merge_atts(array(
            'title'           => '',
            'content'         => '',
            'style'           => '',
            'el_class'        => '',
            'animate'         => '',
            'animate_delay'   => '',
         ), $attr));
         $el_class .= ' ' . $style;
  
         $ouput = '';
         $ouput .= '<div class="custom-text '.$el_class.'" '. gavias_content_builder_print_animate_aos($animate, $animate_delay) .'>';
         $ouput .=  $content;
         $ouput .= '</div>';
         return $ouput;
      }

   }
 endif;  



