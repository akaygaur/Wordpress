//Add the following code into your theme’s functions.php file.


function add_copyright_text() {
    if (is_single()) { ?>
 
<script type='text/javascript'>
function addLink() {
    if (
window.getSelection().containsNode(
document.getElementsByClassName('entry-content')[0], true)) {
    var body_element = document.getElementsByTagName('body')[0];
    var selection;
    selection = window.getSelection();
    var oldselection = selection
    var pagelink = "<br /><br /> Read more at WPBeginner: <?php the_title(); ?> <a href='<?php echo wp_get_shortlink(get_the_ID()); ?>'><?php echo wp_get_shortlink(get_the_ID()); ?></a>"; //Change this if you like
    var copy_text = selection + pagelink;
    var new_div = document.createElement('div');
    new_div.style.left='-99999px';
    new_div.style.position='absolute';
 
    body_element.appendChild(new_div );
    new_div.innerHTML = copy_text ;
    selection.selectAllChildren(new_div );
    window.setTimeout(function() {
        body_element.removeChild(new_div );
    },0);
}
}
 
 
document.oncopy = addLink;
</script>
 
<?php
}
}
 
add_action( 'wp_head', 'add_copyright_text');
