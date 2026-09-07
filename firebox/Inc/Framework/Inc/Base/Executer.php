<?php
/**
 * @package         FirePlugins Framework
 * @version         1.2.5
 * 
 * @author          FirePlugins <info@fireplugins.com>
 * @link            https://www.fireplugins.com
 * @copyright       Copyright © 2026 FirePlugins All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace FPFramework\Base;

use \FPFramework\Libs\Registry;

if (!defined('ABSPATH'))
{
	exit; // Exit if accessed directly.
}

/**
 *  Cleverly evaluate php code using a temporary file and without using the evil eval() PHP method
 */
class Executer
{
    /**
     * The php code is going to be executed
     *
     * @var string
     */
    private $php_code;

    /**
     * The data object passed as argument to function
     *
     * @var mixed
     */
    private $payload;

    /**
     * Executer configuration
     *
     * @var object
     */
    private $options;

    /**
     * Class constructor
     *
     * @param  string  $php_code  The php code that is going to be executed
     * @param  mixed   $payload
     * @param  array   $options
     */
    public function __construct($php_code = null, &$payload = null, $options = array())
    {
        $this->setPhpCode($php_code);
        $this->setPayload($payload);

        // Default options
        $defaults = [
            'forbidden_php_functions' => [
                'fopen', 
                'popen',
                'unlink', 
                'rmdir',
                'dl', 
                'escapeshellarg',
                'escapeshellcmd',
                'exec',
                'passthru',
                'proc_close',
                'proc_open',
                'shell_exec',
                'symlink',
                'system',
                'pcntl_exec',
                'eval',
                'create_function'
            ]
        ];

        $options = array_merge($defaults, is_array($options) ? $options : []);

        // Accept a comma separated string the same way setForbiddenPHPFunctions() does, so
        // allowedToRun() always receives a list. Anything else falls back to the defaults:
        // array_diff() throws a TypeError on a non-array since PHP 8.0, and before that it
        // returned null, which degraded the regex below into one that matched nothing and
        // silently let every forbidden function through.
        if (isset($options['forbidden_php_functions']) && is_string($options['forbidden_php_functions']))
        {
            $options['forbidden_php_functions'] = explode(',', $options['forbidden_php_functions']);
        }

        if (empty($options['forbidden_php_functions']) || !is_array($options['forbidden_php_functions']))
        {
            $options['forbidden_php_functions'] = $defaults['forbidden_php_functions'];
        }

        $this->options = new Registry($options);
    }

    /**
     * Payload contains the variables passed as argumentse into the PHP code
     *
     * @param  mixed $data
     *
     * @return void
     */
    public function setPayload(&$data)
    {
        $this->payload = &$data;
        return $this;
    }

    /**
     * Set forbidden PHP functions. If any found, the whole PHP block won't run.
     *
     * @param  array $functions
     *
     * @return void
     */
    public function setForbiddenPHPFunctions($functions)
    {
        if (empty($functions))
        {
            return $this;
        }

        if (is_string($functions))
        {
            $functions = explode(',', $functions);
        }

        if (!is_array($functions))
        {
            return $this;
        }

        $this->options->set('forbidden_php_functions', $functions);
        return $this;
    }

    /**
     * Helper method to set the php code is about to be executed
     *
     * @param string $php_code
     *
     * @return void
     */
    public function setPhpCode($php_code)
    {
        $this->php_code = $php_code;
        return $this;
    }

    /**
     * Checks if given PHP code is valid and it's allowed to run.
     *
     * @return bool
     */
    private function allowedToRun()
    {
        // Get the forbidden PHP functions, but we want to make sure we whitelist 'curl_exec'
        $forbidden_functions = array_diff((array) $this->options->get('forbidden_php_functions', []), ['curl_exec']);
        
        // Build the regex, making sure 'exec' does not match within 'curl_exec'
        $re = '/\b(' . implode('|', $forbidden_functions) . ')\b(\s*\(|\s+[\'"])/mi';
        preg_match_all($re, $this->php_code ?? '', $matches);
        if (!empty($matches[0]))
        {
            return false;
        }

        // Check for backticks ``
        if ($has_back_ticks = preg_match('/`(.*?)`/s', $this->php_code ?? ''))
        {
            return false;
        }

        return true;
    }

    /**
     * Run function
     *
     * @return function
     */
    public function run()
    {
        if (!$this->allowedToRun())
        {
            return;
        }

        $function_name = $this->getFunctionName();

        // Function doesn't exist. Let's create it.
		if (!function_exists($function_name))
		{
            if (!$this->createFunction())
            {
                return;
            }
        }

		return $function_name($this->payload);
    }

    /**
     * Creates a temporary function in memory
     *
     * @return  bool
     */
    private function createFunction()
    {
        $function_name    = $this->getFunctionName();
        $function_content = $this->getFunctionContent();

        // A predictable name in a shared temp dir invites a pre-planted symlink or a
        // swap between write and include, so use a random name and refuse to follow
        // anything that already exists ('x' fails if the path is taken).
        try
        {
            $suffix = bin2hex(random_bytes(8));
        }
        catch (\Exception $e)
        {
            return false;
        }

        $temp_file = $this->getTempPath() . $function_name . '_' . $suffix . '.php';

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fopen
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
        $open = @fopen($temp_file, 'x');
        if (!$open)
        {
            return false;
        }

        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fputs
        fputs($open, $function_content);
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
        // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
        fclose($open);

        // Include file
        include_once $temp_file;

        // Delete file
        // phpcs:ignore WordPress.WP.AlternativeFunctions.unlink_unlink
        @unlink($temp_file);

        return function_exists($function_name);
    }

    /**
     * Get temporary file content
     *
     * @return string
     */
    private function getFunctionContent()
    {
        $function_name = $this->getFunctionName();
        $variables = $this->getFunctionVariables();

		$contents = [
			'<?php',
			'defined(\'ABSPATH\') or die;',
			'function ' . $function_name . '(&$displayData = null) {
                if ($displayData) {
                    extract($displayData, EXTR_REFS);
                }
            ',
			implode("\n", $variables),
            $this->php_code,
			';return true;}'
		];

		$contents = implode("\n", $contents);

		// Remove Zero Width spaces / (non-)joiners
		$contents = str_replace(
			[
				"\xE2\x80\x8B",
				"\xE2\x80\x8C",
				"\xE2\x80\x8D",
			],
			'',
			$contents
		);

		return $contents;
    }

    /**
     * Make user's life easier by initializing some WordPress helpful variables
     *
     * @return array
     */
    protected function getFunctionVariables()
    {
        return [
			'$user = \FPFramework\Helpers\UserHelper::getUser();'
        ];
    }

    /**
     * Construct a temporary function name
     *
     * @return string
     */
    private function getFunctionName()
    {
		return 'fireplugins_php_' . md5((string) $this->php_code);
    }

    /**
     * Return WordPress temporary path
     *
     * @return  string
     */
    private function getTempPath()
    {
		return get_temp_dir();
    }
}