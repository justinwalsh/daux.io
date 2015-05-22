<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Todaymade\Daux\Generator;

use InvalidArgumentException;

/**
 * Getopt is a class to parse options for command-line
 * applications.
 *
 * Terminology:
 * Argument: an element of the argv array.  This may be part of an option,
 *   or it may be a non-option command-line argument.
 * Flag: the letter or word set off by a '-' or '--'.  Example: in '--output filename',
 *   '--output' is the flag.
 * Parameter: the additional argument that is associated with the option.
 *   Example: in '--output filename', the 'filename' is the parameter.
 * Option: the combination of a flag and its parameter, if any.
 *   Example: in '--output filename', the whole thing is the option.
 *
 * The following features are supported:
 *
 * - Short flags like '-a'.  Short flags are preceded by a single
 *   dash.  Short flags may be clustered e.g. '-abc', which is the
 *   same as '-a' '-b' '-c'.
 * - Long flags like '--verbose'.  Long flags are preceded by a
 *   double dash.  Long flags may not be clustered.
 * - Options may have a parameter, e.g. '--output filename'.
 * - Parameters for long flags may also be set off with an equals sign,
 *   e.g. '--output=filename'.
 * - Parameters for long flags may be checked as string, word, or integer.
 * - Automatic generation of a helpful usage message.
 * - Signal end of options with '--'; subsequent arguments are treated
 *   as non-option arguments, even if they begin with '-'.
 * - Raise exception Zend\Console\* in several cases
 *   when invalid flags or parameters are given.  Usage message is
 *   returned in the exception object.
 *
 * The format for specifying options uses a PHP associative array.
 * The key is has the format of a list of pipe-separated flag names,
 * followed by an optional '=' to indicate a required parameter or
 * '-' to indicate an optional parameter.  Following that, the type
 * of parameter may be specified as 's' for string, 'w' for word,
 * or 'i' for integer.
 *
 * Examples:
 * - 'user|username|u=s'  this means '--user' or '--username' or '-u'
 *   are synonyms, and the option requires a string parameter.
 * - 'p=i'  this means '-p' requires an integer parameter.  No synonyms.
 * - 'verbose|v-i'  this means '--verbose' or '-v' are synonyms, and
 *   they take an optional integer parameter.
 * - 'help|h'  this means '--help' or '-h' are synonyms, and
 *   they take no parameter.
 *
 * The values in the associative array are strings that are used as
 * brief descriptions of the options when printing a usage message.
 *
 * The simpler format for specifying options used by PHP's getopt()
 * function is also supported.  This is similar to GNU getopt and shell
 * getopt format.
 *
 * Example:  'abc:' means options '-a', '-b', and '-c'
 * are legal, and the latter requires a string parameter.
 */
class Getopt
{
    /**
     * Constant tokens for various symbols used in the mode_zend
     * rule format.
     */
    const PARAM_REQUIRED                    = '=';
    const PARAM_OPTIONAL                    = '-';

    /**
     * Stores the command-line arguments for the calling application.
     *
     * @var array
     */
    protected $argv = array();

    /**
     * Stores the name of the calling application.
     *
     * @var string
     */
    protected $progname = '';

    /**
     * Stores the list of legal options for this application.
     *
     * @var array
     */
    protected $rules = array();

    /**
     * Stores alternate spellings of legal options.
     *
     * @var array
     */
    protected $ruleMap = array();

    /**
     * Stores options given by the user in the current invocation
     * of the application, as well as parameters given in options.
     *
     * @var array
     */
    protected $options = array();

    /**
     * State of the options: parsed or not yet parsed?
     *
     * @var bool
     */
    protected $parsed = false;

    /**
     * The constructor takes one to three parameters.
     *
     * The first parameter is $rules, which may be a string for
     * gnu-style format, or a structured array for Zend-style format.
     *
     * @param  array $rules
     * @throws InvalidArgumentException
     */
    public function __construct($rules, $argv = null)
    {
        $this->argv = $argv?: $_SERVER['argv'];

        $this->progname = $this->argv[0];
        $this->addRules($rules);
    }

    /**
     * Return a list of options that have been seen in the current argv.
     *
     * @return array
     */
    public function getOptions()
    {
        $this->parse();
        return $this->options;
    }

    /**
     * Return the state of the option seen on the command line of the
     * current application invocation.
     *
     * This function returns true, or the parameter value to the option, if any.
     * If the option was not given, this function returns false.
     *
     * @param  string $flag
     * @return mixed
     */
    public function getOption($flag)
    {
        $this->parse();

        $flag = strtolower($flag);

        if (isset($this->ruleMap[$flag])) {
            $flag = $this->ruleMap[$flag];
            if (isset($this->options[$flag])) {
                return $this->options[$flag];
            }
        }
        return;
    }

    /**
     * Return a useful option reference, formatted for display in an
     * error message.
     *
     * Note that this usage information is provided in most Exceptions
     * generated by this class.
     *
     * @return string
     */
    public function getUsageMessage()
    {
        $usage = "Usage: {$this->progname} [ options ]\n";
        $maxLen = 20;
        $lines = array();
        foreach ($this->rules as $rule) {
            if (isset($rule['isFreeformFlag'])) {
                continue;
            }
            $flags = array();
            if (is_array($rule['alias'])) {
                foreach ($rule['alias'] as $flag) {
                    $flags[] = (strlen($flag) == 1 ? '-' : '--') . $flag;
                }
            }
            $linepart['name'] = implode('|', $flags);
            if (isset($rule['param']) && $rule['param'] != 'none') {
                $linepart['name'] .= '=""';
                switch ($rule['param']) {
                    case 'optional':
                        $linepart['name'] .= " (optional)";
                        break;
                    case 'required':
                        $linepart['name'] .= " (required)";
                        break;
                }
            }
            if (strlen($linepart['name']) > $maxLen) {
                $maxLen = strlen($linepart['name']);
            }
            $linepart['help'] = '';
            if (isset($rule['help'])) {
                $linepart['help'] .= $rule['help'];
            }
            $lines[] = $linepart;
        }
        foreach ($lines as $linepart) {
            $usage .= sprintf(
                "%s %s\n",
                str_pad($linepart['name'], $maxLen),
                $linepart['help']
            );
        }
        return $usage;
    }

    /**
     * Parse command-line arguments and find both long and short
     * options.
     *
     * Also find option parameters, and remaining arguments after
     * all options have been parsed.
     *
     * @return self
     */
    public function parse()
    {
        if ($this->parsed === true) {
            return $this;
        }

        if (in_array('--help', $this->argv)) {
            echo $this->getUsageMessage();
            exit;
        }

        $this->options = array();

        $long = [];
        $short = '';
        foreach ($this->rules as $rule) {
            foreach ($rule['alias'] as $alias) {
                $prepared = $alias;
                if ($rule['param'] == 'optional') {
                    $prepared .= '::';
                } elseif ($rule['param'] == 'required') {
                    $prepared .= ':';
                }

                if (strlen($alias) == 1) {
                    $short .= $prepared;
                } else {
                    $long[] = $prepared;
                }
            }
        }

        $result = getopt($short, $long);

        foreach ($result as $key => $value) {
            $this->options[$this->ruleMap[$key]] = $value;
        }

        $this->parsed = true;

        return $this;
    }

    /**
     * Define legal options using the Zend-style format.
     *
     * @param  array $rules
     * @throws InvalidArgumentException
     */
    protected function addRules($rules)
    {
        foreach ($rules as $ruleCode => $helpMessage) {
            // this may have to translate the long parm type if there
            // are any complaints that =string will not work (even though that use
            // case is not documented)
            if (in_array(substr($ruleCode, -2, 1), array('-', '='))) {
                $flagList  = substr($ruleCode, 0, -2);
                $delimiter = substr($ruleCode, -2, 1);
            } else {
                $flagList = $ruleCode;
                $delimiter = $paramType = null;
            }

            $flagList = strtolower($flagList);

            $flags = explode('|', $flagList);
            $rule = array();
            $mainFlag = $flags[0];
            foreach ($flags as $flag) {
                if (empty($flag)) {
                    throw new InvalidArgumentException("Blank flag not allowed in rule \"$ruleCode\".");
                }

                if (isset($this->ruleMap[$flag]) || (strlen($flag) != 1 && isset($this->rules[$flag]))) {
                    throw new InvalidArgumentException("Option \"-$flag\" is being defined more than once.");
                }

                $this->ruleMap[$flag] = $mainFlag;
                $rule['alias'][] = $flag;
            }
            $rule['param'] = 'none';
            if (isset($delimiter)) {
                $rule['param'] = $delimiter == self::PARAM_REQUIRED? 'required' : 'optional';
            }

            $rule['help'] = $helpMessage;
            $this->rules[$mainFlag] = $rule;
        }
    }
}
