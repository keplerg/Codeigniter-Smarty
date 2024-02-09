# CI-Smarty
# Integrate Smarty template engine into CodeIgniter

If you are not familiar with CodeIgniter, it is a lightweight Model-View-Controller (MVC) framework written in PHP. It is an open source project created by EllisLabs. CodeIgniter has a templating system built in but, the last time I looked at it, it does not have many features. It also implements the templating by filtering the page using preg_match() and string replacement. For web sites with low traffic volumes and few variable replacements, this approach is fine. As the traffic volume picks up or with larger pages, this may impact the response time of the web site.

Smarty is another open source project that handles templates and is also written in PHP. It has been around for a while and has many features beyond the simple templating engine built into CodeIgniter. Smarty also "compiles" templates into pure PHP which makes them faster to execute.

Templating is a controversial topic with some devlopers since they feel that using PHP directly in the HTML is easier than learning a new templating language to accomplish the same thing. I personally like having a clean separation between code (PHP) and views (templates using Smarty). I have seen too many developers get sloppy and start adding logic and even database calls in their HTML. This can quickly become a maintenance nightmare. I also like the fact that Smarty uses different delimiters {} from HTML <>. This makes spotting the Smarty tags easy. A simple example that illustrates this point is:

Using Smarty:

```HTML
<title>{$title}</title>
```
Using PHP:

```HTML
<title><?php echo $title;?></title>
```
One minor annoyance is that if you have curly braces in your HTML, like inline javascript or inline stylesheets, you need to escape them so they won't be interpreted as Smarty tags. One way to do this, surround the section you want to turn off Smarty with {literal} and {/literal}. Here is an example:

```HTML
<script type="text/javascript">
  {literal}
  function a(b, c) {
      alert('function a('+b+', '+c+') called!');
  }
  {/literal}
</script>
```
Another approach is to change the delimitters Smarty uses. I have changed mine to use {{ and }} as the opening and closing delimitters respectively. I have the code to change the delimiters in the Smartie class file below. Uncomment the 2 lines to switch to use the double delimiters.

Here's an example using the double delimiters:

```HTML
<script type="text/javascript">
  function d() {
      alert('Smarty variable test contains {{$test}}');
  }
</script>
```
The next question you may have is "How do I use Smarty in CodeIgniter?". Well, I took some time to document how I did it and also give some code and examples to get you started.

 

# Step 1) Install CodeIgniter
That is, if you haven't already installed it. You can download CodeIgniter 3.0 here.

 

# Step 2) Install Smarty
You can find the latest download for Smarty here. You should extract Smarty to the /application/third_party directory. You can then rename the extracted directory to 'smarty' instead of 'smarty-{version}'. If you decide to install Smarty under a different directory name, make sure you update the Smartie.php library module to point to your Smarty installation. The directory you point to should have the Smarty.class.php PHP class file in a folder called libs.

 

# Step 3) Create template directories
I created the templates directory (Smarty's root template directory) and templates_c directory (where the compiled versions of the Smarty templates reside) in /application/views. Again, if you decide to keep your templates elsewhere, be sure to update Smartie.php to point to your directories. Make sure the templates_c directory has write access for your application. 

 

# Step 4) Install the code from this site
The code from this site includes the Smartie library class that bridges between Smarty and CodeIgniter, A few Smarty plugins to help bridge access to CodeIgniter features, and a small demo called "example".

The main class is called Smartie and should be added to the autoload config array as 'smartie'=>'smarty'. This allows you to access the library as "smarty" without having a class name collision. By extending the main Smarty class as a CodeIgniter library, you have access to all of the Smarty API calls. I added an extra function called smarty->view() as a convenience function which behaves as the parser->parse() functon from CodeIgniter's templating system. Further down I list Smarty plugins that bridge the gap between Smarty and the CodeIgniter libraries for those times you need access in the template. The ones I have included do not break the MVC paradigm, but rather make it easier to access language file data, validation errors, etc. instead of trying to stuff everything into the $data array before calling your template.

Here is the main Smartie library class:

```PHP
<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Smartie Class
 *
 * @package        CodeIgniter
 * @subpackage     Libraries
 * @category       Smarty
 * @author         Kepler Gelotte
 * @link           https://www.coolphptools.com/codeigniter-smarty
 */
require_once( APPPATH.'/third_party/smarty/libs/Smarty.class.php' );

class Smartie extends Smarty {

    var $debug = false;

    function __construct()
    {
        parent::__construct();

        $this->template_dir = APPPATH . "views/templates";
        $this->compile_dir = APPPATH . "views/templates_c";
        if ( ! is_writable( $this->compile_dir ) )
        {
            // make sure the compile directory can be written to
            @chmod( $this->compile_dir, 0777 );
        } 

        // Uncomment these 2 lines to change Smarty's delimiters
        // $this->left_delimiter = '{{';
        // $this->right_delimiter = '}}';

        $this->assign( 'FCPATH', FCPATH );     // path to website
        $this->assign( 'APPPATH', APPPATH );   // path to application directory
        $this->assign( 'BASEPATH', BASEPATH ); // path to system directory

        log_message('debug', "Smarty Class Initialized");
    }

    function setDebug( $debug=true )
    {
        $this->debug = $debug;
    }

    /**
     *  Parse a template using the Smarty engine
     *
     * This is a convenience method that combines assign() and
     * display() into one step. 
     *
     * Values to assign are passed in an associative array of
     * name => value pairs.
     *
     * If the output is to be returned as a string to the caller
     * instead of being output, pass true as the third parameter.
     *
     * @access    public
     * @param    string
     * @param    array
     * @param    bool
     * @return    string
     */
    function view($template, $data = array(), $return = FALSE)
    {
        if ( ! $this->debug )
        {
            $this->error_reporting = false;
        }
        $this->error_unassigned = false;

        foreach ($data as $key => $val)
        {
            $this->assign($key, $val);
        }
        
        if ($return == FALSE)
        {
            $CI =& get_instance();
            if (method_exists( $CI->output, 'set_output' ))
            {
                $CI->output->set_output( $this->fetch($template) );
            }
            else
            {
                $CI->output->final_output = $this->fetch($template);
            }
            return;
        }
        else
        {
            return $this->fetch($template);
        }
    }
}
// END Smartie Class
```
 

Here are all the files included in the download:

The CodeIgniter library class used to bridge to Smarty:

/application/libraries/Smartie.php
Smarty plugin functions to bridge to CodeIgniter classes:

/application/third_party/smarty/libs/plugins/function.ci_config.php
/application/third_party/smarty/libs/plugins/function.ci_db_session.php
/application/third_party/smarty/libs/plugins/function.ci_form_validation.php
/application/third_party/smarty/libs/plugins/function.ci_language.php
/application/third_party/smarty/libs/plugins/function.ci_validation.php
Example code:

/application/controllers/example.php
/application/language/english/label_lang.php
/application/views/templates/example.tpl
/application/views/templates/header.tpl
/application/views/templates/footer.tpl
 

Step 5) Update CodeIgniter's config
In the /application/config directory, there is a file called autoload.php. Edit that file and add 'smartie'=>'smarty' to the array of library classes to be autloaded:

$autoload['libraries'] = array(…,'smartie'=>'smarty');
 

Step 6) Run the example
If you installed everything correctly, when you go to http://yourdomain/example you should see a screen like this:

```HTML
Title: Welcome To The Smarty Website
The configuration value of base_url is http://www.coolphptools.com/

The current date and time is 2024-02-09 15:45:05

The value of global assigned variable $SCRIPT_NAME is /index.php

The value of server environment variable SERVER_NAME is www.coolphptools.com

The value of your IP address is: 162.158.154.235

The value of {{$Name}} is Fred Irving Johnathan Bradley Peppergill

The value of {{$Name|upper}} is FRED IRVING JOHNATHAN BRADLEY PEPPERGILL

An example of a section loop:
1 * John Doe
2 * Mary Smith
3 . James Johnson
4 . Henry Case
An example of section looped key values:
phone: 555-1234
fax: 555-2345
cell: 999-9999
phone: 555-4444
fax: 555-3333
cell: 888-8888
An example testing strip tags:
This is a test
```
