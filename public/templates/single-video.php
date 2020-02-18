<?php 
get_header();
the_post();
// echo 'Hello This is custom single Template for Video Post Type';



$get_repeat_meta = get_post_meta(get_the_id(), 'mi_tm_personal_section', true);

$tp = json_decode($get_repeat_meta['mi_skill_info']);

$tp_data = $tp->mi_tm_personal_section_mi_skill_info_parent;



foreach($tp_data as $_tp_data){
    echo $_tp_data->mi_skill_title.'<br/>';
}

// echo '<pre>';
// print_r();
// echo '</pre>';

echo $get_repeat_meta['designation'];



get_footer();
?>