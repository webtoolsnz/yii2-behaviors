<?php
namespace webtoolsnz\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Model;
use yii\validators\DateValidator;
use yii\helpers\FormatConverter;

/**
 * DateFormatBehavior automatically converts the specified date fields from one format to another.
 *
 * To use DateFormatBehavior, insert the following code to your ActiveRecord class:
 *
 * ```php
 * use webtoolsnz\behaviors\DateFormatBehavior;
 *
 * public function behaviors()
 * {
 *     return [
 *         [
 *             'class' => DateFormatBehavior::className(),
 *             'attributes' => ['date_of_birth'],
 *         ]
 *     ];
 * }
 * ```
 *
 * By default, DateFormatBehavior will convert the specified fields to `YYYY-MM-dd`, you can set the format
 * using the `saveFormat` option.
 *
 * The conversion will occur before validation by default, if you need it to occur after validation for
 * example you can change the `event` option to `Model::EVENT_AFTER_VALIDATE`, any events your object emits will work.
 *
 * @author Byron Adams <byron@webtools.co.nz>
 * @package webtoolsnz\behaviors
 */
class DateFormatBehavior extends Behavior
{
    /**
     * List of object attributes to convert
     * @var array
     */
    public $attributes = [];

    /**
     * Format to save the date to
     * @var string
     */
    public $saveFormat = 'yyyy-MM-dd';

    /**
     * Format dates are displayed/input as, if null
     * `Yii::$app->formatter->dateFormat` is used.
     * @var null
     */
    public $displayFormat = null;

    /**
     * Event that triggers the attribute conversion.
     * @var string
     */
    public $event = Model::EVENT_BEFORE_VALIDATE;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (!$this->displayFormat) {
            $this->displayFormat = Yii::$app->formatter->dateFormat;
        }

        $this->displayFormat = FormatConverter::convertDateIcuToPhp($this->displayFormat);
        $this->saveFormat = FormatConverter::convertDateIcuToPhp($this->saveFormat);

        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [$this->event => 'convertAttributes'];
    }

    /**
     * Attempts to convert a given date string into the saveFormat
     *
     * @param $value
     * @return bool|string
     */
    public function convertDateFormat($value)
    {
        $date = \DateTime::createFromFormat($this->displayFormat, $value);
        return $date ? $date->format($this->saveFormat) : false;
    }

    /**
     *
     * @param $event
     */
    public function convertAttributes($event)
    {
        foreach($this->owner->attributes as $name => $value) {
            if (in_array($name, $this->attributes) && false !== ($date = $this->convertDateFormat($value))) {
                $this->owner->{$name} = $date;
            }
        }
    }
}

