<?php namespace Todaymade\Daux;

use Todaymade\Daux\Tree\Builder;
use Todaymade\Daux\Tree\Directory;

class DauxHelper
{
    /**
     * Set a new base_url for the configuration
     *
     * @param Config $config
     * @param string $base_url
     */
    public static function rebaseConfiguration(Config $config, $base_url)
    {
        // Avoid changing the url if it is already correct
        if ($config['base_url'] == $base_url && !empty($config['theme'])) {
            return;
        }

        // Change base url for all links on the pages
        $config['base_url'] = $config['base_page'] = $base_url;
        $config['theme'] = static::getTheme($config, $base_url);
        $config['image'] = str_replace('<base_url>', $base_url, $config['image']);
    }

    protected static function resolveVariant(Config $params)
    {
        if (array_key_exists('theme-variant', $params['html'])) {
            return;
        }

        if (is_dir(realpath(($params->getThemesPath() . DIRECTORY_SEPARATOR . $params['html']['theme'])))) {
            return;
        }

        $theme = explode('-', $params['html']['theme']);

        // do we have a variant or only a theme ?
        if (isset($theme[1])) {
            $params['html']['theme-variant'] = array_pop($theme);
            $params['html']['theme'] = implode('-', $theme);
        } else {
            $params['html']['theme'] = array_pop($theme);
        }

        if (!is_dir(realpath($params->getThemesPath() . DIRECTORY_SEPARATOR . $params['html']['theme']))) {
            throw new \RuntimeException("Theme '{$params['html']['theme']}' not found");
        }
    }

    /**
     * @param Config $params
     * @param string $current_url
     * @return array
     */
    protected static function getTheme(Config $params, $current_url)
    {
        self::resolveVariant($params);

        $theme_folder = $params->getThemesPath() . DIRECTORY_SEPARATOR . $params['html']['theme'];
        $theme_url = $params['base_url'] . 'themes/' . $params['html']['theme'] . '/';

        $theme = [];
        if (is_file($theme_folder . DIRECTORY_SEPARATOR . 'config.json')) {
            $theme = json_decode(file_get_contents($theme_folder . DIRECTORY_SEPARATOR . 'config.json'), true);
            if (!$theme) {
                $theme = [];
            }
        }

        //Default parameters for theme
        $theme += [
            'name' => $params['html']['theme'],
            'css' => [],
            'js' => [],
            'fonts' => [],
            'favicon' => '<base_url>themes/daux/img/favicon.png',
            'templates' => $theme_folder . DIRECTORY_SEPARATOR . 'templates',
            'variants' => [],
        ];

        if (array_key_exists('theme-variant', $params['html'])) {
            $variant = $params['html']['theme-variant'];
            if (!array_key_exists($variant, $theme['variants'])) {
                throw new Exception("Variant '$variant' not found for theme '$theme[name]'");
            }

            // These will be replaced
            foreach (['templates', 'favicon'] as $element) {
                if (array_key_exists($element, $theme['variants'][$variant])) {
                    $theme[$element] = $theme['variants'][$variant][$element];
                }
            }

            // These will be merged
            foreach (['css', 'js', 'fonts'] as $element) {
                if (array_key_exists($element, $theme['variants'][$variant])) {
                    $theme[$element] = array_merge($theme[$element], $theme['variants'][$variant][$element]);
                }
            }
        }

        $substitutions = [
            '<local_base>' => $params['local_base'],
            '<base_url>' => $current_url,
            '<theme_url>' => $theme_url,
        ];

        // Substitute some placeholders
        $theme['templates'] = strtr($theme['templates'], $substitutions);
        $theme['favicon'] = utf8_encode(strtr($theme['favicon'], $substitutions));

        foreach (['css', 'js', 'fonts'] as $element) {
            foreach ($theme[$element] as $key => $value) {
                $theme[$element][$key] = utf8_encode(strtr($value, $substitutions));
            }
        }

        return $theme;
    }

    /**
     * Remove all '/./' and '/../' in a path, without actually checking the path
     *
     * @param string $path
     * @return string
     */
    public static function getCleanPath($path)
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = [];
        foreach ($parts as $part) {
            if ('.' == $part) {
                continue;
            }
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    /**
     * Get the possible output file names for a source file.
     *
     * @param Config $config
     * @param string $part
     * @return string[]
     */
    public static function getFilenames(Config $config, $part)
    {
        $extensions = implode('|', array_map('preg_quote', $config['valid_content_extensions'])) . '|html';

        $raw = preg_replace('/(.*)?\\.(' . $extensions . ')$/', '$1', $part);
        $raw = Builder::removeSortingInformations($raw);

        return ["$raw.html", $raw];
    }

    /**
     * Locate a file in the tree. Returns the file if found or false
     *
     * @param Directory $tree
     * @param string $request
     * @return Tree\Content|Tree\Raw|false
     */
    public static function getFile($tree, $request)
    {
        $request = explode('/', $request);
        foreach ($request as $node) {
            // If the element we're in currently is not a
            // directory, we failed to find the requested file
            if (!$tree instanceof Directory) {
                return false;
            }

            // Some relative paths may start with ./
            if ($node == '.') {
                continue;
            }

            if ($node == '..') {
                $tree = $tree->getParent();
                continue;
            }

            $node = urldecode($node);

            // if the node exists in the current request tree,
            // change the $tree variable to reference the new
            // node and proceed to the next url part
            if (isset($tree->getEntries()[$node])) {
                $tree = $tree->getEntries()[$node];
                continue;
            }

            // if the node doesn't exist, we can try
            // two variants of the requested file:
            // with and w/o the .html extension
            foreach (static::getFilenames($tree->getConfig(), $node) as $filename) {
                if (isset($tree->getEntries()[$filename])) {
                    $tree = $tree->getEntries()[$filename];
                    continue 2;
                }
            }

            // At this stage, we're in a directory, but no
            // sub-item matches, so the current node must
            // be an index page or we failed
            if ($node !== 'index' && $node !== 'index.html') {
                return false;
            }

            return $tree->getIndexPage();
        }

        // If the entry we found is not a directory, we're done
        if (!$tree instanceof Directory) {
            return $tree;
        }

        if ($index = $tree->getIndexPage()) {
            return $index;
        }

        return false;
    }

    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * Taken from Stringy
     *
     * @param  string $title
     * @return string
     */
    public static function slug($title)
    {
        foreach (static::charsArray() as $key => $value) {
            $title = str_replace($value, $key, $title);
        }

        $title = preg_replace('/[^\x20-\x7E]/u', '', $title);

        $separator = '_';
        // Convert all dashes into underscores
        $title = preg_replace('![' . preg_quote('-') . ']+!u', $separator, $title);

        // Remove all characters that are not the separator, letters, numbers, or whitespace.
        $title = preg_replace('![^' . preg_quote($separator) . '\pL\pN\s]+!u', '', $title);

        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace('![' . preg_quote($separator) . '\s]+!u', $separator, $title);

        return trim($title, $separator);
    }

    /**
     * Returns the replacements for the slug() method.
     *
     * Taken from Stringy
     *
     * @return array An array of replacements.
     */
    public static function charsArray()
    {
        static $charsArray;

        if (isset($charsArray)) {
            return $charsArray;
        }

        return $charsArray = [
            'a'    => [
                'à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ',
                'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ä', 'ā', 'ą',
                'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ',
                'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ',
                'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ', ],
            'b'    => ['б', 'β', 'Ъ', 'Ь', 'ب'],
            'c'    => ['ç', 'ć', 'č', 'ĉ', 'ċ'],
            'd'    => ['ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ',
                'д', 'δ', 'د', 'ض', ],
            'e'    => ['é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ',
                'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ',
                'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э',
                'є', 'ə', ],
            'f'    => ['ф', 'φ', 'ف'],
            'g'    => ['ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ج'],
            'h'    => ['ĥ', 'ħ', 'η', 'ή', 'ح', 'ه'],
            'i'    => ['í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į',
                'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ',
                'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ',
                'ῗ', 'і', 'ї', 'и', ],
            'j'    => ['ĵ', 'ј', 'Ј'],
            'k'    => ['ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك'],
            'l'    => ['ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل'],
            'm'    => ['м', 'μ', 'م'],
            'n'    => ['ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن'],
            'o'    => ['ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ',
                'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő',
                'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό',
                'ö', 'о', 'و', 'θ', ],
            'p'    => ['п', 'π'],
            'r'    => ['ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر'],
            's'    => ['ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص'],
            't'    => ['ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط'],
            'u'    => ['ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ',
                'ự', 'ü', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у', ],
            'v'    => ['в'],
            'w'    => ['ŵ', 'ω', 'ώ'],
            'x'    => ['χ'],
            'y'    => ['ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ',
                'ϋ', 'ύ', 'ΰ', 'ي', ],
            'z'    => ['ź', 'ž', 'ż', 'з', 'ζ', 'ز'],
            'aa'   => ['ع'],
            'ae'   => ['æ'],
            'ch'   => ['ч'],
            'dj'   => ['ђ', 'đ'],
            'dz'   => ['џ'],
            'gh'   => ['غ'],
            'kh'   => ['х', 'خ'],
            'lj'   => ['љ'],
            'nj'   => ['њ'],
            'oe'   => ['œ'],
            'ps'   => ['ψ'],
            'sh'   => ['ш'],
            'shch' => ['щ'],
            'ss'   => ['ß'],
            'th'   => ['þ', 'ث', 'ذ', 'ظ'],
            'ts'   => ['ц'],
            'ya'   => ['я'],
            'yu'   => ['ю'],
            'zh'   => ['ж'],
            '(c)'  => ['©'],
            'A'    => ['Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ',
                'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Ä', 'Å', 'Ā',
                'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ',
                'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ',
                'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А', ],
            'B'    => ['Б', 'Β'],
            'C'    => ['Ç', 'Ć', 'Č', 'Ĉ', 'Ċ'],
            'D'    => ['Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'],
            'E'    => ['É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ',
                'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ',
                'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э',
                'Є', 'Ə', ],
            'F'    => ['Ф', 'Φ'],
            'G'    => ['Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'],
            'H'    => ['Η', 'Ή'],
            'I'    => ['Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į',
                'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ',
                'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї', ],
            'K'    => ['К', 'Κ'],
            'L'    => ['Ĺ', 'Ł', 'Л', 'Λ', 'Ļ'],
            'M'    => ['М', 'Μ'],
            'N'    => ['Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'],
            'O'    => ['Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ',
                'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ö', 'Ø', 'Ō',
                'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ',
                'Ὸ', 'Ό', 'О', 'Θ', 'Ө', ],
            'P'    => ['П', 'Π'],
            'R'    => ['Ř', 'Ŕ', 'Р', 'Ρ'],
            'S'    => ['Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'],
            'T'    => ['Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'],
            'U'    => ['Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ',
                'Ự', 'Û', 'Ü', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У', ],
            'V'    => ['В'],
            'W'    => ['Ω', 'Ώ'],
            'X'    => ['Χ'],
            'Y'    => ['Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ',
                'Ы', 'Й', 'Υ', 'Ϋ', ],
            'Z'    => ['Ź', 'Ž', 'Ż', 'З', 'Ζ'],
            'AE'   => ['Æ'],
            'CH'   => ['Ч'],
            'DJ'   => ['Ђ'],
            'DZ'   => ['Џ'],
            'KH'   => ['Х'],
            'LJ'   => ['Љ'],
            'NJ'   => ['Њ'],
            'PS'   => ['Ψ'],
            'SH'   => ['Ш'],
            'SHCH' => ['Щ'],
            'SS'   => ['ẞ'],
            'TH'   => ['Þ'],
            'TS'   => ['Ц'],
            'YA'   => ['Я'],
            'YU'   => ['Ю'],
            'ZH'   => ['Ж'],
            ' '    => ["\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81",
                "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84",
                "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87",
                "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A",
                "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80", ],
        ];
    }

    /**
     * @param string $from
     * @param string $to
     * @return string
     */
    public static function getRelativePath($from, $to)
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to = is_dir($to) ? rtrim($to, '\/') . '/' : $to;
        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);

        $from = explode('/', $from);
        $to = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {
            // find first non-matching dir
            if ($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
            } else {
                // get number of remaining dirs to $from
                $remaining = count($from) - $depth;
                if ($remaining > 1) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');
                    break;
                } else {
                    //$relPath[0] = './' . $relPath[0];
                }
            }
        }

        return implode('/', $relPath);
    }

    public static function isAbsolutePath($path)
    {
        if (!is_string($path)) {
            $mess = sprintf('String expected but was given %s', gettype($path));
            throw new \InvalidArgumentException($mess);
        }

        if (!ctype_print($path)) {
            $mess = 'Path can NOT have non-printable characters or be empty';
            throw new \DomainException($mess);
        }

        // Optional wrapper(s).
        $regExp = '%^(?<wrappers>(?:[[:print:]]{2,}://)*)';

        // Optional root prefix.
        $regExp .= '(?<root>(?:[[:alpha:]]:/|/)?)';

        // Actual path.
        $regExp .= '(?<path>(?:[[:print:]]*))$%';

        $parts = [];
        if (!preg_match($regExp, $path, $parts)) {
            $mess = sprintf('Path is NOT valid, was given %s', $path);
            throw new \DomainException($mess);
        }

        return '' !== $parts['root'];
    }
}
