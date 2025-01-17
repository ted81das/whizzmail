<?php declare(strict_types=1);
/**
 * CUrlValidator class file.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @link http://www.yiiframework.com/
 * @copyright 2008-2013 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

/**
 * CUrlValidator validates that the attribute value is a valid http or https URL.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @package system.validators
 * @since 1.0
 */
class CUrlValidator extends CValidator
{
    /**
     * @var string the regular expression used to validate the attribute value.
     * Since version 1.1.7 the pattern may contain a {schemes} token that will be replaced
     * by a regular expression which represents the {@see validSchemes}.
     */
    public $pattern='/^{schemes}:\/\/(([A-Z0-9][A-Z0-9_-]*)(\.[A-Z0-9][A-Z0-9_-]*)+)/i';
    /**
     * @var array list of URI schemes which should be considered valid. By default, http and https
     * are considered to be valid schemes.
     * @since 1.1.7
     **/
    public $validSchemes=['http', 'https'];
    /**
     * @var string the default URI scheme. If the input doesn't contain the scheme part, the default
     * scheme will be prepended to it (thus changing the input). Defaults to null, meaning a URL must
     * contain the scheme part.
     * @since 1.1.7
     **/
    public $defaultScheme;
    /**
     * @var boolean whether the attribute value can be null or empty. Defaults to true,
     * meaning that if the attribute is empty, it is considered valid.
     */
    public $allowEmpty=true;
    /**
     * @var boolean whether validation process should care about IDN (internationalized domain names). Default
     * value is false which means that validation of URLs containing IDN will always fail.
     * @since 1.1.13
     */
    public $validateIDN=false;

    /**
     * Validates a static value to see if it is a valid URL.
     * Note that this method does not respect {@link allowEmpty} property.
     * This method is provided so that you can call it directly without going through the model validation rule mechanism.
     * @param string $value the value to be validated
     * @return mixed false if the the value is not a valid URL, otherwise the possibly modified value ({@see defaultScheme})
     * @since 1.1.1
     */
    public function validateValue($value)
    {
        if (is_string($value) && strlen($value)<2000) {  // make sure the length is limited to avoid DOS attacks
            if ($this->defaultScheme!==null && strpos($value, '://')===false) {
                $value=$this->defaultScheme . '://' . $value;
            }

            if ($this->validateIDN) {
                $value=$this->encodeIDN($value);
            }

            if (strpos($this->pattern, '{schemes}')!==false) {
                $pattern=(string)str_replace('{schemes}', '(' . implode('|', $this->validSchemes) . ')', $this->pattern);
            } else {
                $pattern=$this->pattern;
            }

            if (preg_match($pattern, $value)) {
                return $this->validateIDN ? $this->decodeIDN($value) : $value;
            }
        }
        return false;
    }

    /**
     * Returns the JavaScript needed for performing client-side validation.
     *
     * @param CModel $object the data object being validated
     * @param string $attribute the name of the attribute to be validated.
     *
     * @return string the client-side validation script.
     * @throws CException
     * @see CActiveForm::enableClientValidation
     * @since 1.1.7
     */
    public function clientValidateAttribute($object, $attribute)
    {
        if ($this->validateIDN) {
            /** @var CWebApplication $app */
            $app = app();
            $app->getClientScript()->registerCoreScript('punycode');
            // punycode.js works only with the domains - so we have to extract it before punycoding
            $validateIDN='
var info = value.match(/^(.+:\/\/|)([^/]+)/);
if (info)
	value = info[1] + punycode.toASCII(info[2]);
';
        } else {
            $validateIDN='';
        }

        $message=$this->message ?? t('yii', '{attribute} is not a valid URL.');
        $message=strtr($message, [
            '{attribute}'=>$object->getAttributeLabel($attribute),
        ]);

        if (strpos($this->pattern, '{schemes}')!==false) {
            $pattern=(string)str_replace('{schemes}', '(' . implode('|', $this->validSchemes) . ')', $this->pattern);
        } else {
            $pattern=$this->pattern;
        }

        $js="
$validateIDN
if(!value.match($pattern)) {
	messages.push(" . json_encode($message) . ');
}
';
        if ($this->defaultScheme!==null) {
            $js='
if(!value.match(/:\\/\\//)) {
	value=' . json_encode($this->defaultScheme) . "+'://'+value;
}
$js
";
        }

        if ($this->allowEmpty) {
            $js="
if(jQuery.trim(value)!='') {
	$js
}
";
        }

        return $js;
    }

    /**
     * Validates the attribute of the object.
     * If there is any error, the error message is added to the object.
     * @param CModel $object the object being validated
     * @param string $attribute the attribute being validated
     *
     * @return void
     */
    protected function validateAttribute($object, $attribute)
    {
        $value=$object->$attribute;
        if ($this->allowEmpty && $this->isEmpty($value)) {
            return;
        }
        if (($value=$this->validateValue($value))!==false) {
            $object->$attribute=$value;
        } else {
            $message=$this->message ?? t('yii', '{attribute} is not a valid URL.');
            $this->addError($object, $attribute, $message);
        }
    }

    /**
     * Converts given IDN to the punycode.
     * @param string $value IDN to be converted.
     * @return string resulting punycode.
     * @since 1.1.13
     */
    private function encodeIDN($value)
    {
        if (preg_match_all('/^(.*):\/\/([^\/]+)(.*)$/', $value, $matches)) {
            $value = $matches[1][0] . '://' . IDNHelper::encode($matches[2][0]) . $matches[3][0];
        }
        return $value;
    }

    /**
     * Converts given punycode to the IDN.
     * @param string $value punycode to be converted.
     * @return string resulting IDN.
     * @since 1.1.13
     */
    private function decodeIDN($value)
    {
        if (preg_match_all('/^(.*):\/\/([^\/]+)(.*)$/', $value, $matches)) {
            $value = $matches[1][0] . '://' . IDNHelper::decode($matches[2][0]) . $matches[3][0];
        }
        return $value;
    }
}
