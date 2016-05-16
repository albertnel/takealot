<?php

/**
*   AppController class.
*
*   Handles all the routing and page redirects, as well as
*   generating and displaying the output files.
*
*   @author Albert Nel
*/

/**
*   Constants to define possible result codes.
*/
define('ALERT_SUCCESS', 0);
define('ALERT_ERROR', 1);
define('ALERT_NO_FILE', 2);

class AppController
{
    /**
    *   Handles the index page.
    *
    *   This function receives an upload file to be processed.
    *   After processing it provides links to the processed files
    *   for download.
    *
    *   Finally it renders all of this in the Twig template.
    */
    public function displayIndexPage()
    {
        $parse = [];
        $alert_reason = null;
        $out_file_valid = 'valid_ip_address.txt';
        $out_file_invalid = 'invalid_ip_address.txt';
        $valid_array = [];
        $invalid_array = [];

        // Process uploaded file
        if (!empty($_FILES)) {
            $file_array = $_FILES['ip_address_file'];

            if (empty($file_array['tmp_name'])) {
                // Configure alert
                $parse['alert'] = $this->getAlert(ALERT_NO_FILE);
            } else {
                // Open input file
                $file_handle = fopen($file_array['tmp_name'], "r");
                if ($file_handle) {
                    while (!feof($file_handle)) {
                        $line = trim(fgets($file_handle));
                        if (!empty($line)) {
                            $valid = checkIpAddress($line);

                            if ($valid) {
                                $valid_array[] = $line;
                            } else {
                                $invalid_array[] = $line;
                            }
                        }
                    }
                    fclose($file_handle);

                    // Sort arrays in  natural order
                    natsort($valid_array);
                    natsort($invalid_array);

                    // Open output files
                    $output_path = __DIR__ . '/../public/output/';
                    $file_valid = fopen($output_path . $out_file_valid, "w");
                    $file_invalid = fopen($output_path . $out_file_invalid, "w");

                    // Write to files
                    foreach($valid_array as $valid_ip) {
                        fwrite($file_valid, $valid_ip . "\n");
                    }
                    foreach($invalid_array as $valid_ip) {
                        fwrite($file_invalid, $valid_ip . "\n");
                    }

                    // Close file handlers
                    fclose($file_valid);
                    fclose($file_invalid);

                    // Set output files on template for download
                    $parse['valid_file'] = $out_file_valid;
                    $parse['invalid_file'] = $out_file_invalid;

                    $alert_reason = ALERT_SUCCESS;
                } else {
                    $alert_reason = ALERT_ERROR;
                }

                // Configure alert
                $parse['alert'] = $this->getAlert($alert_reason);
            }
        } else {
            // Hide alert
            $parse['alert']['class'] = 'hidden';
        }

        // Render template
        $twig = loadTwig();
        echo $twig->render('upload_file.html', $parse);
    }

    /**
    *   Get alert message.
    *
    *   Gets the alert message and styling for processing upload files.
    *
    *   @param array $query_array Query array from URI.
    *   @return array $alert Array with all alert details.
    */
    private function getAlert($alert_reason)
    {
        $alert = [];

        switch ($alert_reason) {
            case ALERT_SUCCESS:
                $alert['class'] = 'alert-success';
                $alert['headline'] = 'Success!';
                $alert['message'] = 'File processed successfully.';
                break;
            case ALERT_ERROR:
                $alert['class'] = 'alert-danger';
                $alert['headline'] = 'Error!';
                $alert['message'] = 'Could not process file.';
                break;
            case ALERT_NO_FILE:
                $alert['class'] = 'alert-danger';
                $alert['headline'] = 'Error!';
                $alert['message'] = 'No file supplied.';
                break;
            default:
                $alert['class'] = 'hidden';
                break;
        }

        return $alert;
    }

    /**
    *   Uploads IP address list file.
    *
    *   @param array $file Array of file upload details from $_FILES.
    *   @return bool
    */
    private function uploadProfilePic($file, $id)
    {
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $target_dir = __DIR__ . '/../public/images/profile_pics';
        $target_filename = $id . '.' . $file_extension;
        $target_file_path = $target_dir . '/' . $target_filename;
        $valid = 1;

        $check_image = getimagesize($file["tmp_name"]);
        if ($check_image === false) {
            $valid = 0;
        }

        $valid_extensions = ['jpg', 'jpeg', 'gif', 'png'];
        $found_extension = false;
        foreach ($valid_extensions as $ext) {
            if ($file_extension == $ext) {
                $found_extension = true;
                break;
            }
        }
        if (!$found_extension) {
            $valid = 0;
        }

        if (!$valid) {
            return false;
        } else {
            if (move_uploaded_file($file['tmp_name'], $target_file_path)) {
                $old_files = DB::queryFirstRow("SELECT profile_filename, thumb_filename FROM contacts WHERE id = " . $id);

                // Remove old profile files
                $old_file_path = $target_dir . '/' . $old_files['profile_filename'];
                if ($old_files['profile_filename'] != $target_filename && file_exists($old_file_path)) {
                    unlink($old_file_path);
                }
                $old_thumb_path = $target_dir . '/' . $old_files['thumb_filename'];
                if (file_exists($old_thumb_path)) {
                    unlink($old_thumb_path);
                }

                // Create a thumbnail from the image.
                $manager = new ImageManager(array('driver' => 'gd'));
                $image = $manager->make($target_file_path)->resize(25, 25);
                $thumb_filename = $id . '_thumb.' . $file_extension;
                $thumb_file_path = $target_dir . '/' . $thumb_filename;
                $image->save($thumb_file_path);

                // Update contact profile path
                DB::update('contacts',
                    array(
                        'profile_filename' => $target_filename,
                        'thumb_filename' => $thumb_filename
                    ),
                    'id = ' . $id
                );

                return true;
            } else {
                return false;
            }
        }
    }
}

?>
