<?PHP

namespace App\Http;

/**
 * Numerous utilities used in our web service
 */

class Utils
{

  /**
   * Regular expressions to filter/test input
   */

  public static $preg_replace = [
    'paragraphe' => "/[^ !\"$%&'()*+,-.:;<=>?@[\]\^_`{|}~[:alnum:]-[:space:]]/u", //"/[^,.?'!%_[:alnum:]-[:space:]]/u",
    'string_array' => "/[^,_[:alnum:] \+]/u",
    'categories_array' => "/[^,[:alnum:]]/u",
    'phone' => "/[^+\-()[0-9]]/"
  ];

  public static function ArrayToFiltredStringOfArray($request)
  {
    if (is_array($request)) {
      $request = preg_replace(Utils::$preg_replace['string_array'], '', implode(',', $request));
    } else {
      $request = preg_replace(Utils::$preg_replace['string_array'], '', $request);
    }
    return $request;
  }

  /**
   * Determines max and min from array or comma seperated string
   */

  public static function SearchRang($searchRang)
  {
    $resultSearchRang = ['min' => null, 'max' => null];

    if (!is_array($searchRang)) {
      $searchRang = explode(',', $searchRang);
    }

    if ($searchRang != null) {
      $searchRang = array_filter(preg_replace('/[^0-9,.]/', '', $searchRang));
      if (count($searchRang) > 0) {
        $resultSearchRang["max"] = floatval(max($searchRang));
        $resultSearchRang["min"] = floatval(min($searchRang));
      }
    }

    return $resultSearchRang;
  }

  public static function MultiSearchStringArray($searchWords)
  {
    $allsearchWords = ".*";
    $result = [];
    if ($searchWords == null) {
      return $allsearchWords;
    } else {
      if (is_array($searchWords)) {
        $searchWords = preg_replace(Utils::$preg_replace['string_array'], '', $searchWords);
      } else {
        $searchWords = preg_replace(Utils::$preg_replace['string_array'], '', explode(',', $searchWords));
      }
      $searchWords = array_filter($searchWords);
      if (count($searchWords) > 0) {
        for ($i = 0; $i < count($searchWords); $i++) {
          array_push($result, "^" . $searchWords[$i] . "$");
          array_push($result, "," . $searchWords[$i] . "$");
          array_push($result, "," . $searchWords[$i] . ",");
          array_push($result, "^" . $searchWords[$i] . ",");
        }
        return implode('|', $result);
      } else {
        return $allsearchWords;
      }
    }
    return $allsearchWords;
  }

  public static function MultiSearchString($searchWords)
  {
    $allsearchWords = ".*";
    if ($searchWords == null) {
      return $allsearchWords;
    } else {
      if (is_array($searchWords)) {
        $searchWords = preg_replace(Utils::$preg_replace['string_array'], '', $searchWords);
      } else {
        $searchWords = preg_replace(Utils::$preg_replace['string_array'], '', explode(',', $searchWords));
      }

      if (count($searchWords) > 0) {
        return str_replace(" ", ".*", implode('|', $searchWords));
      } else {
        return $allsearchWords;
      }
    }
    return $allsearchWords;
  }


  public static function getInvalidIMEI()
  {
    return "invalid_imei";
  }

  /**
   * Validate IMEI using RegExp
   */

  public static function validate_imei($imei)
  {
    $new_imei = preg_replace('/\D/', '', $imei);
    if ($new_imei == $imei && strlen($new_imei) >= 15 && strlen($new_imei) <= 20) {
      return $new_imei;
    } else {
      return Utils::getInvalidIMEI();
    }
  }
}
