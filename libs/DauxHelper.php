<?php namespace Todaymade\Daux;

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

    public static function resolveVariant(Config $params)
    {
        if (array_key_exists('theme-variant', $params['html'])) {
            return;
        }

        if (is_dir($params['themes_path'] . DIRECTORY_SEPARATOR . $params['html']['theme'])) {
            return;
        }

        $theme = explode('-', $params['html']['theme']);

        $params['html']['theme-variant'] = array_pop($theme);
        $params['html']['theme'] = implode('-', $theme);

        if (!is_dir($params['themes_path'] . DIRECTORY_SEPARATOR . $params['html']['theme'])) {
            throw new \RuntimeException("Theme '{$params['html']['theme']}' not found");
        }
    }

    /**
     * @param Config $params
     * @param string $current_url
     * @return array
     */
    public static function getTheme(Config $params, $current_url)
    {
        self::resolveVariant($params);

        $theme_folder = $params['themes_path'] . DIRECTORY_SEPARATOR . $params['html']['theme'];
        $theme_url = $params['base_url'] . $params['themes_directory'] . '/' . $params['html']['theme'] . '/';

        $theme = array();
        if (is_file($theme_folder . DIRECTORY_SEPARATOR . "config.json")) {
            $theme = json_decode(file_get_contents($theme_folder . DIRECTORY_SEPARATOR . "config.json"), true);
            if (!$theme) {
                $theme = array();
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
            '<theme_url>' => $theme_url
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
     * @param string $path
     * @return string
     */
    public static function getCleanPath($path)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
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

            $node = urldecode($node);

            // if the node exists in the current request tree,
            // change the $tree variable to reference the new
            // node and proceed to the next url part
            if (isset($tree->getEntries()[$node])) {
                $tree = $tree->getEntries()[$node];
                continue;
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
     * @param  string  $title
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
        $title = preg_replace('![' . preg_quote("-") . ']+!u', $separator, $title);

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

        return $charsArray = array(
            'a'    => array(
                'à', 'á', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ',
                'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'ä', 'ā', 'ą',
                'å', 'α', 'ά', 'ἀ', 'ἁ', 'ἂ', 'ἃ', 'ἄ', 'ἅ', 'ἆ',
                'ἇ', 'ᾀ', 'ᾁ', 'ᾂ', 'ᾃ', 'ᾄ', 'ᾅ', 'ᾆ', 'ᾇ', 'ὰ',
                'ά', 'ᾰ', 'ᾱ', 'ᾲ', 'ᾳ', 'ᾴ', 'ᾶ', 'ᾷ', 'а', 'أ'),
            'b'    => array('б', 'β', 'Ъ', 'Ь', 'ب'),
            'c'    => array('ç', 'ć', 'č', 'ĉ', 'ċ'),
            'd'    => array('ď', 'ð', 'đ', 'ƌ', 'ȡ', 'ɖ', 'ɗ', 'ᵭ', 'ᶁ', 'ᶑ',
                'д', 'δ', 'د', 'ض'),
            'e'    => array('é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ',
                'ệ', 'ë', 'ē', 'ę', 'ě', 'ĕ', 'ė', 'ε', 'έ', 'ἐ',
                'ἑ', 'ἒ', 'ἓ', 'ἔ', 'ἕ', 'ὲ', 'έ', 'е', 'ё', 'э',
                'є', 'ə'),
            'f'    => array('ф', 'φ', 'ف'),
            'g'    => array('ĝ', 'ğ', 'ġ', 'ģ', 'г', 'ґ', 'γ', 'ج'),
            'h'    => array('ĥ', 'ħ', 'η', 'ή', 'ح', 'ه'),
            'i'    => array('í', 'ì', 'ỉ', 'ĩ', 'ị', 'î', 'ï', 'ī', 'ĭ', 'į',
                'ı', 'ι', 'ί', 'ϊ', 'ΐ', 'ἰ', 'ἱ', 'ἲ', 'ἳ', 'ἴ',
                'ἵ', 'ἶ', 'ἷ', 'ὶ', 'ί', 'ῐ', 'ῑ', 'ῒ', 'ΐ', 'ῖ',
                'ῗ', 'і', 'ї', 'и'),
            'j'    => array('ĵ', 'ј', 'Ј'),
            'k'    => array('ķ', 'ĸ', 'к', 'κ', 'Ķ', 'ق', 'ك'),
            'l'    => array('ł', 'ľ', 'ĺ', 'ļ', 'ŀ', 'л', 'λ', 'ل'),
            'm'    => array('м', 'μ', 'م'),
            'n'    => array('ñ', 'ń', 'ň', 'ņ', 'ŉ', 'ŋ', 'ν', 'н', 'ن'),
            'o'    => array('ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ',
                'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ø', 'ō', 'ő',
                'ŏ', 'ο', 'ὀ', 'ὁ', 'ὂ', 'ὃ', 'ὄ', 'ὅ', 'ὸ', 'ό',
                'ö', 'о', 'و', 'θ'),
            'p'    => array('п', 'π'),
            'r'    => array('ŕ', 'ř', 'ŗ', 'р', 'ρ', 'ر'),
            's'    => array('ś', 'š', 'ş', 'с', 'σ', 'ș', 'ς', 'س', 'ص'),
            't'    => array('ť', 'ţ', 'т', 'τ', 'ț', 'ت', 'ط'),
            'u'    => array('ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ',
                'ự', 'ü', 'û', 'ū', 'ů', 'ű', 'ŭ', 'ų', 'µ', 'у'),
            'v'    => array('в'),
            'w'    => array('ŵ', 'ω', 'ώ'),
            'x'    => array('χ'),
            'y'    => array('ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'ÿ', 'ŷ', 'й', 'ы', 'υ',
                'ϋ', 'ύ', 'ΰ', 'ي'),
            'z'    => array('ź', 'ž', 'ż', 'з', 'ζ', 'ز'),
            'aa'   => array('ع'),
            'ae'   => array('æ'),
            'ch'   => array('ч'),
            'dj'   => array('ђ', 'đ'),
            'dz'   => array('џ'),
            'gh'   => array('غ'),
            'kh'   => array('х', 'خ'),
            'lj'   => array('љ'),
            'nj'   => array('њ'),
            'oe'   => array('œ'),
            'ps'   => array('ψ'),
            'sh'   => array('ш'),
            'shch' => array('щ'),
            'ss'   => array('ß'),
            'th'   => array('þ', 'ث', 'ذ', 'ظ'),
            'ts'   => array('ц'),
            'ya'   => array('я'),
            'yu'   => array('ю'),
            'zh'   => array('ж'),
            '(c)'  => array('©'),
            'A'    => array('Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ',
                'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'Ä', 'Å', 'Ā',
                'Ą', 'Α', 'Ά', 'Ἀ', 'Ἁ', 'Ἂ', 'Ἃ', 'Ἄ', 'Ἅ', 'Ἆ',
                'Ἇ', 'ᾈ', 'ᾉ', 'ᾊ', 'ᾋ', 'ᾌ', 'ᾍ', 'ᾎ', 'ᾏ', 'Ᾰ',
                'Ᾱ', 'Ὰ', 'Ά', 'ᾼ', 'А'),
            'B'    => array('Б', 'Β'),
            'C'    => array('Ç', 'Ć', 'Č', 'Ĉ', 'Ċ'),
            'D'    => array('Ď', 'Ð', 'Đ', 'Ɖ', 'Ɗ', 'Ƌ', 'ᴅ', 'ᴆ', 'Д', 'Δ'),
            'E'    => array('É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ',
                'Ệ', 'Ë', 'Ē', 'Ę', 'Ě', 'Ĕ', 'Ė', 'Ε', 'Έ', 'Ἐ',
                'Ἑ', 'Ἒ', 'Ἓ', 'Ἔ', 'Ἕ', 'Έ', 'Ὲ', 'Е', 'Ё', 'Э',
                'Є', 'Ə'),
            'F'    => array('Ф', 'Φ'),
            'G'    => array('Ğ', 'Ġ', 'Ģ', 'Г', 'Ґ', 'Γ'),
            'H'    => array('Η', 'Ή'),
            'I'    => array('Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Î', 'Ï', 'Ī', 'Ĭ', 'Į',
                'İ', 'Ι', 'Ί', 'Ϊ', 'Ἰ', 'Ἱ', 'Ἳ', 'Ἴ', 'Ἵ', 'Ἶ',
                'Ἷ', 'Ῐ', 'Ῑ', 'Ὶ', 'Ί', 'И', 'І', 'Ї'),
            'K'    => array('К', 'Κ'),
            'L'    => array('Ĺ', 'Ł', 'Л', 'Λ', 'Ļ'),
            'M'    => array('М', 'Μ'),
            'N'    => array('Ń', 'Ñ', 'Ň', 'Ņ', 'Ŋ', 'Н', 'Ν'),
            'O'    => array('Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ',
                'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ö', 'Ø', 'Ō',
                'Ő', 'Ŏ', 'Ο', 'Ό', 'Ὀ', 'Ὁ', 'Ὂ', 'Ὃ', 'Ὄ', 'Ὅ',
                'Ὸ', 'Ό', 'О', 'Θ', 'Ө'),
            'P'    => array('П', 'Π'),
            'R'    => array('Ř', 'Ŕ', 'Р', 'Ρ'),
            'S'    => array('Ş', 'Ŝ', 'Ș', 'Š', 'Ś', 'С', 'Σ'),
            'T'    => array('Ť', 'Ţ', 'Ŧ', 'Ț', 'Т', 'Τ'),
            'U'    => array('Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ',
                'Ự', 'Û', 'Ü', 'Ū', 'Ů', 'Ű', 'Ŭ', 'Ų', 'У'),
            'V'    => array('В'),
            'W'    => array('Ω', 'Ώ'),
            'X'    => array('Χ'),
            'Y'    => array('Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Ÿ', 'Ῠ', 'Ῡ', 'Ὺ', 'Ύ',
                'Ы', 'Й', 'Υ', 'Ϋ'),
            'Z'    => array('Ź', 'Ž', 'Ż', 'З', 'Ζ'),
            'AE'   => array('Æ'),
            'CH'   => array('Ч'),
            'DJ'   => array('Ђ'),
            'DZ'   => array('Џ'),
            'KH'   => array('Х'),
            'LJ'   => array('Љ'),
            'NJ'   => array('Њ'),
            'PS'   => array('Ψ'),
            'SH'   => array('Ш'),
            'SHCH' => array('Щ'),
            'SS'   => array('ẞ'),
            'TH'   => array('Þ'),
            'TS'   => array('Ц'),
            'YA'   => array('Я'),
            'YU'   => array('Ю'),
            'ZH'   => array('Ж'),
            ' '    => array("\xC2\xA0", "\xE2\x80\x80", "\xE2\x80\x81",
                "\xE2\x80\x82", "\xE2\x80\x83", "\xE2\x80\x84",
                "\xE2\x80\x85", "\xE2\x80\x86", "\xE2\x80\x87",
                "\xE2\x80\x88", "\xE2\x80\x89", "\xE2\x80\x8A",
                "\xE2\x80\xAF", "\xE2\x81\x9F", "\xE3\x80\x80"),
        );
    }
}
