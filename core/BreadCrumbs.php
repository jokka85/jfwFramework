<?php
/**
 * core/BreadCrumbs.php
 *
 * Creates the breadcrumbs needed if enabled.
 *
 * @author Joshua Weeks (jokka85@gmail.com)
 * @version 0.1
 * @since 0.1
 */

use Controller\Controller;

class Crumbs extends Controller {

        /**
         * Set the HTML content for the CRUMBS
         *
         * @param string $html
         *
         * @return string
         * Returns the manipulated contents of the crumbs.
         */
        public function setCrumbs($html){

            // First, we fetch the contents of the crumbs page
            $crumb_html = file_get_contents(TEMPLATE_DIRECTORY . "Crumbs"
                . DIRECTORY_SEPARATOR . "index.php");

            // Now we manipulate the crumbs as needed
            $name = $GLOBALS['name'];

            /*
             * Variables to locate:
             *
             *      CRUMB_HOME_ACTIVE               = Is the home link active?
             *      CRUMB_CURRENT_HOME              = Is the current page the home page?
             *      CRUMB_HOME_LINK                 = The Home link
             *      CRUMB_VARS                      = Loop for crumb vars
             *          - CRUMB_PAGE_NAME           = Name of the page
             *          - CRUMB_PAGE_ACTIVE         = Is the page active
             *          - CRUMB_PAGE_CURRENT        = ^^^^
             *          - CRUMB_PAGE_LINK           = Link to page
             *          - CRUMB_PAGE_VISIBLE        = If link needs to be hidden
             */

            $args = [
                "CRUMB_HOME_ACTIVE" => ($name == "Default") ? "active" : "",
                "CRUMB_CURRENT_HOME" => ($name == "Default") ? 'aria-current="page"' : "",
                "CRUMB_HOME_LINK" => ($name == "Default") ? "#" : "/" . BASE_DIR . "/"
            ];

            if($name != "Default"){

                $vars = (isset($GLOBALS['vars'][0]) && !empty($GLOBALS['vars'][0])) ? $GLOBALS['vars'][0] : null;

                $args["CRUMB_VARS"][] = [
                        "CRUMB_PAGE_NAME" => ucfirst($name),
                        "CRUMB_PAGE_VISIBLE" => "visible",
                        "CRUMB_PAGE_ACTIVE" => (is_null($vars)) ? "active" : "",
                        "CRUMB_PAGE_CURRENT" => (is_null($vars)) ? "aria-current=\"page\"" : "",
                        "CRUMB_PAGE_LINK" => (is_null($vars)) ? "#" : "/" . BASE_DIR . "/" . $name . "/"
                    ];

                if(!is_null($vars)){
                    $args["CRUMB_VARS"][] = [
                        "CRUMB_PAGE_NAME" => $vars[0],
                        "CRUMB_PAGE_VISIBLE" => "visible",
                        "CRUMB_PAGE_ACTIVE" => "active",
                        "CRUMB_PAGE_CURRENT" => "aria-current=\"page\"",
                        "CRUMB_PAGE_LINK" => "#"
                    ];
                }

            } else {
                $args["CRUMB_VARS"][] = [
                    "CRUMB_PAGE_NAME" => ucfirst($name),
                    "CRUMB_PAGE_VISIBLE" => "hidden"
                   ];
            }

            $crumb_html = $this->set($args, $crumb_html);

            // Then we put into the HTML and return
            return $this->set(["__BREAD_CRUMBS__" => $crumb_html], $html);

        }
}
