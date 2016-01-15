<?php namespace App\Helpers;
  
class Text {
  
  /**
  * Generate a random string, using a cryptographically secure 
  * pseudorandom number generator (random_int)
  * 
  * For PHP 7, random_int is a PHP core function
  * For PHP 5.x, depends on https://github.com/paragonie/random_compat
  * 
  * @param int $length      How many characters do we want?
  * @param string $keyspace A string of all possible characters
  *                         to select from
  * @return string
  */
  public static function randomCryptoString($length = 32, $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
  {
    $str = '';
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
      $str .= $keyspace[random_int(0, $max)];
    }
    return $str;
  }
  
  /**
   * Creates a slug version of text
   */
  static public function slugify($text, $slugChar = '-') {
    // Strip apostrophes
    $text = str_replace(["'","’"], "", $text);
    
    // replace non letter or digits by -
    $text = preg_replace('~[^\\pL\d]+~u', $slugChar, $text);
  
    // clean
    while (strpos($text, "$slugChar$slugChar") !== FALSE)
      $text = str_replace("$slugChar$slugChar", $slugChar, $text);
  
    // trim
    $text = trim($text, $slugChar);
  
    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
  
    // lowercase
    $text = strtolower($text);
  
    // remove unwanted characters
    $text = preg_replace('~[^'.$slugChar.'\w]+~', '', $text);
  
    if (empty($text))
    {
      return 'n'.$slugChar.'a';
    }
  
    return $text;
  }


  /**
   * Convert words in camelCase to human readable words
   */
  public static function camelToWords($str) {
    preg_match_all('/((?:^|[A-Z])[a-z]+)/', $str, $matches);

    array_walk($matches[0], function(&$value, $key) {
      $value = strtolower($value);
    });
    
    return implode(' ', $matches[0]);
  }
  
  /**
   * Convert words in camelCase to human readable words
   */
  public static function camelToDashes($str) {
    preg_match_all('/((?:^|[A-Z])[a-z]+)/', $str, $matches);

    array_walk($matches[0], function(&$value, $key) {
      $value = strtolower($value);
    });
    
    return implode('-', $matches[0]);
  }
  
	/**
	 * Limits a phrase to a given number of words.
	 *
	 * @param   string   phrase to limit words of
	 * @param   integer  number of words to limit to
	 * @param   string   end character or entity
	 * @return  string
	 */
	public static function limitWords($str, $limit = 100, $end_char = NULL)
	{
		$limit = (int) $limit;
		$end_char = ($end_char === NULL) ? '&#8230;' : $end_char;

		if (trim($str) === '')
			return $str;

		if ($limit <= 0)
			return $end_char;

		preg_match('/^\s*+(?:\S++\s*+){1,'.$limit.'}/u', $str, $matches);

		// Only attach the end character if the matched string is shorter
		// than the starting string.
		return rtrim($matches[0]).(strlen($matches[0]) === strlen($str) ? '' : $end_char);
	}

	/**
	 * Limits a phrase to a given number of characters.
	 *
	 * @param   string   phrase to limit characters of
	 * @param   integer  number of characters to limit to
	 * @param   string   end character or entity
	 * @param   boolean  enable or disable the preservation of words while limiting
	 * @return  string
	 */
	public static function limitChars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE)
	{
		$end_char = ($end_char === NULL) ? '&#8230;' : $end_char;

		$limit = (int) $limit;

		if (trim($str) === '' OR utf8::strlen($str) <= $limit)
			return $str;

		if ($limit <= 0)
			return $end_char;

		if ($preserve_words == FALSE)
		{
			return rtrim(utf8::substr($str, 0, $limit)).$end_char;
		}

		preg_match('/^.{'.($limit - 1).'}\S*/us', $str, $matches);

		return rtrim($matches[0]).(strlen($matches[0]) == strlen($str) ? '' : $end_char);
	}

  /**
   * Converts line breaks to paragraphs
   */
  public static function autoP($str) {
		// Trim whitespace
		if (($str = trim($str)) === '')
			return '';

		// Standardize newlines
		$str = str_replace(array("\r\n", "\r"), "\n", $str);

		// Trim whitespace on each line
		$str = preg_replace('~^[ \t]+~m', '', $str);
		$str = preg_replace('~[ \t]+$~m', '', $str);

		// The following regexes only need to be executed if the string contains html
		if ($html_found = (strpos($str, '<') !== FALSE))
		{
			// Elements that should not be surrounded by p tags
			$no_p = '(?:p|div|h[1-6r]|ul|ol|li|blockquote|d[dlt]|pre|t[dhr]|t(?:able|body|foot|head)|c(?:aption|olgroup)|form|s(?:elect|tyle)|a(?:ddress|rea)|ma(?:p|th))';

			// Put at least two linebreaks before and after $no_p elements
			$str = preg_replace('~^<'.$no_p.'[^>]*+>~im', "\n$0", $str);
			$str = preg_replace('~</'.$no_p.'\s*+>$~im', "$0\n", $str);
		}

		// Do the <p> magic!
		$str = '<p>'.trim($str).'</p>';
		$str = preg_replace('~\n{2,}~', "</p>\n\n<p>", $str);

		// The following regexes only need to be executed if the string contains html
		if ($html_found !== FALSE)
		{
			// Remove p tags around $no_p elements
			$str = preg_replace('~<p>(?=</?'.$no_p.'[^>]*+>)~i', '', $str);
			$str = preg_replace('~(</?'.$no_p.'[^>]*+>)</p>~i', '$1', $str);
		}

		// Convert single linebreaks to <br />
		$str = preg_replace('~(?<!\n)\n(?!\n)~', "<br />\n", $str);

		return $str;
  }
  
  /**
   * Returns the $singular or $plural form of a word based on $count
   */
  public static function singularOrPlural($count, $singular, $plural) {
    return ($count != 1) ? $plural : $singular;
  }
  
	/**
	 * Alternates between two or more strings.
	 *
	 * @param   string  strings to alternate between
	 * @return  string
	 */
	public static function alternate()
	{
		static $i;

		if (func_num_args() === 0)
		{
			$i = 0;
			return '';
		}

		$args = func_get_args();
		return $args[($i++ % count($args))];
	}

  public static function creditCardType($cardNumber) {
    $type = "Unknown";
    
    // Strip out all non-numeric characters
    $cardNumber=preg_replace('/\D/', '', $cardNumber);
    
    // If we're not dealing with a number, there's no point in continuing
    if ( ! is_numeric($cardNumber)) return FALSE;
    
    // Check for two-digit matches
    switch (substr($cardNumber, 0, 2)) {
      case 34:
      case 37:
        $type = "American Express";
        break;
      case 36:
        $type = "Diners Club";
        break;
      case 38:
        $type = "Carte Blanche";
        break;
      case 51:
      case 52:
      case 53:
      case 54:
      case 55:
        $type = "MasterCard";
        break;
      default:
        // Check for four-digit matches
        switch (substr($cardNumber, 0, 4)) {
          case 2014:
          case 2149:
            $type = "EnRoute";
            break;
          case 2131:
          case 1800:
            $type = "JCB";
            break;
          case 6011:
            $type = "Discover";
            break;
          default:
            // Check for three-digit matches
            switch (substr($cardNumber, 0, 3)) {
              case 300:
              case 301:
              case 302:
              case 303:
              case 304:
              case 305:
                $type = "American Diners Club";
                break;
              default:
                // Check for one-digit matches
                switch (substr($cardNumber, 0, 1)) {
                  case 3:
                    $type = "JCB";
                    break;
                  case 4:
                    $type = "Visa";
                    break;
                }
                break;
            }
            break;
        }
        break;
    }
    
    return $type;
  }
  
  public static function validCC($number) {
  
    // Strip any non-digits (useful for credit card numbers with spaces and hyphens)
    $number=preg_replace('/\D/', '', $number);
      
    // Set the string length and parity
    $number_length=strlen($number);
    $parity=$number_length % 2;
  
    // Loop through each digit and do the maths
    $total=0;
    for ($i=0; $i<$number_length; $i++) {
      $digit=$number[$i];
      // Multiply alternate digits by two
      if ($i % 2 == $parity) {
        $digit*=2;
        // If the sum is two digits, add them together (in effect)
        if ($digit > 9) {
          $digit-=9;
        }
      }
      // Total up the digits
      $total+=$digit;
    }
  
    // If the total mod 10 equals 0, the number is valid
    return ($total % 10 == 0) ? TRUE : FALSE;
  
  }
  
  /**
   * Returns a formatted number string, takes an amount in cents
   */
  public static function moneyFormat($amount, $showCents = TRUE) {
    return '$'.number_format($amount / 100, $showCents ? 2 : 0);
  }
  
  /**
   * Return a list of valid timezones for a HTML select dropdown
   */
  public static function timezoneList() {
    $tzlist = \DateTimeZone::listIdentifiers(\DateTimeZone::ALL);
    $selectList = [];
    foreach ($tzlist as $tz) {
      $selectList[$tz] = str_replace(['/', '_'], [' - ', ' '], $tz);
    }
    
    return $selectList;
  }

  
}