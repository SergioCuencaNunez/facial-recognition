<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Login with Facial Recognition.
 *
 * @package auth_facial
 * @author Sergio Cuenca
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/authlib.php');

/**
 * Plugin for facial recognition authentication.
 */
class auth_plugin_facial extends auth_plugin_base {

    /**
     * Constructor.
     */
    public function __construct() {
        $this->authtype = 'facial';
        $this->config = get_config('auth_facial');
    }

    /**
     * Old syntax of class constructor. Deprecated in PHP7.
     *
     * @deprecated since Moodle 3.1
     */
    public function auth_plugin_facial() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }
  
    /**
     * Hook for overriding behaviour of login page.
     * This method is called from login/index.php page for all enabled auth plugins.
     */
    public function loginpage_hook() {
      global $CFG, $OUTPUT, $PAGE;
  
      // Load the custom CSS file
      $PAGE->requires->css('/auth/facial/theme/custom/login.css');
        
      // Render the Mustache template with the custom CSS and no extra data as it is defined in the template
      echo $OUTPUT->render_from_template('auth_facial/login', []);
    }

    /**
     * Extract the cookie of facial recognition server.
     * @return string Response.
     */
    public function get_cookie() {      
        if (isset($_COOKIE['serverResult'])) {
          $encoded_cookie = $_COOKIE['serverResult'];
          $decoded_cookie = base64_decode($encoded_cookie);
          $cookie_value = substr($decoded_cookie, 0, -24); // Remove the last 10 characters (the date)
          return $cookie_value;
        }
    }
      
    /**
     * Returns true if the username and password work and if the cookie is true and false
     * if the user exists and the password is wrong.
     *
     * @param string $username The username
     * @param string $password The password
     * @return bool Authentication success or failure.
     */
    public function user_login($username, $password) {
        global $CFG, $DB;

        if (!$user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
            return false;
        }
        $cookie = $this->get_cookie();

        if (validate_internal_user_password($user, $password) && $cookie == 'true'){
            return true;
        } else{
            redirect(
                new moodle_url('/login/index.php'), 
                'Your identity does not match. Not able to login. Try again.',
                null,
                \core\output\notification::NOTIFY_ERROR
            );
            return false;
        }
    }

    /**
     * Updates the user's password.
     *
     * called when the user password is updated.
     *
     * @param  object  $user        User table object
     * @param  string  $newpassword Plaintext password
     * @return boolean result
     *
     */
    function user_update_password($user, $newpassword) {
        $user = get_complete_user_data('id', $user->id);
        // This will also update the stored hash to the latest algorithm
        // if the existing hash is using an out-of-date algorithm (or the
        // legacy md5 algorithm).
        return update_internal_user_password($user, $newpassword);
    }

    function prevent_local_passwords() {
        return false;
    }

    /**
     * Returns true if this authentication plugin is 'internal'.
     *
     * @return bool
     */
    function is_internal() {
        return true;
    }

    /**
     * Returns true if this authentication plugin can change the user's
     * password.
     *
     * @return bool
     */
    function can_change_password() {
        return true;
    }

    /**
     * Returns the URL for changing the user's pw, or empty if the default can
     * be used.
     *
     * @return moodle_url
     */
    function change_password_url() {
        return null;
    }

    /**
     * Returns true if plugin allows resetting of internal password.
     *
     * @return bool
     */
    function can_reset_password() {
        return true;
    }

    /**
     * Returns true if plugin can be manually set.
     *
     * @return bool
     */
    function can_be_manually_set() {
        return true;
    }   

     /**
     * Hook called when user logs out. Set serverResult cookie to false.
     *
     * @return void
     */
    public function logoutpage_hook() {
        $encoded_cookie_value = base64_encode('false_' . date('Y-m-d H:i:s')); // Add current date with hour, minutes, and seconds to the cookie value and encrypt
        setcookie('serverResult', $encoded_cookie_value, time() + 3600, '/');
        $_COOKIE['serverResult'] = $encoded_cookie_value; // Update the value in the $_COOKIE superglobal
    }
}
