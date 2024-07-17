add_action('init', 'import_json_data');

function import_json_data()
{
  // Only run the import if the specific URL parameter is set
  if (!isset($_GET['import_json_data'])) {
    return;
  }

  // URL to your JSON file
  $json_file = 'https://resmodtec.com/output.json';

  // Print the file URL for debugging
  // echo 'JSON file URL: ' . $json_file . '<br>';

  // Get the JSON file contents
  $json_data = file_get_contents($json_file);

  // Check if the content was retrieved successfully
  if ($json_data === false) {
    echo 'Failed to retrieve JSON file.';
    return;
  }

  // Decode the JSON data
  $courses = json_decode($json_data, true);

  // Check for JSON decoding errors
  if (json_last_error() !== JSON_ERROR_NONE) {
    echo 'Error decoding JSON: ' . json_last_error_msg();
    return;
  }

  // Loop through each course in the JSON data
  foreach ($courses as $course) {
    // Insert the custom post
    $post_id = wp_insert_post(array(
      'post_title'  => $course['Course Name'],
      'post_type'   => 'wg-course',
      'post_status' => 'publish',
    ));

    if (is_wp_error($post_id)) {
      echo 'Failed to insert post for Course ID ' . $course['id'] . ': ' . $post_id->get_error_message() . '<br>';
      continue;
    }

    // Add discipline as taxonomy term
    $term = wp_set_object_terms($post_id, $course['Discipline'], 'discipline');

    if (is_wp_error($term)) {
      echo 'Failed to set discipline term for Course ID ' . $course['id'] . ': ' . $term->get_error_message() . '<br>';
      continue;
    }

    // Prepare repeater field data
    $locations = array();
    foreach ($course['Location'] as $location) {
      // Convert date format from "Sep 2" to "Y/m/d"
      $date_from = DateTime::createFromFormat('M j', $location['date_from']);
      $date_to = DateTime::createFromFormat('M j', $location['date_to']);

      if (!$date_from || !$date_to) {
        echo 'Failed to parse dates for Course ID ' . $course['id'] . ': Invalid date format<br>';
        continue;
      }

      $date_from = $date_from->format('Y/m/d');
      $date_to = $date_to->format('Y/m/d');

      $term_obj = get_term_by('name', $location['location'], 'location');
      if (!$term_obj) {
        echo 'Failed to get term for location "' . $location['location'] . '" for Course ID ' . $course['id'] . '<br>';
        continue;
      }

      $locations[] = array(
        'course_location' => $term_obj->term_id,
        'date_from'       => $date_from,
        'date_to'         => $date_to,
        'link'            => $location['link'],
      );
    }

    // Update the repeater field
    $update = update_field('field_669118a662b5e', $locations, $post_id);

    if (!$update) {
      echo 'Failed to update field for Course ID ' . $course['id'] . '<br>';
    }
  }

  echo 'Import completed.';
}
