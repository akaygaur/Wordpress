In my case with custom links: /%category%/%postname%/ I had a problem with: /news/page/2/

And finally this works for me (add to functions.php):

<?php

function remove_page_from_query_string($query_string)
{ 
    if ($query_string['name'] == 'page' && isset($query_string['page'])) {
        unset($query_string['name']);
        $query_string['paged'] = $query_string['page'];
    }      
    return $query_string;
}
add_filter('request', 'remove_page_from_query_string');
