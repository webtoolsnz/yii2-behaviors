<?php
namespace webtoolsnz\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\Model;

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
     *
     *
     * @var string
     */
    public $saveFormat = 'YYYY-MM-dd';
    public $event = Model::EVENT_BEFORE_VALIDATE;

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
    public function convertFormat($value)
    {
        $nullDate = Yii::$app->formatter->asDate(0, $this->saveFormat);

        try {
            $output = Yii::$app->formatter->asDate($value, $this->saveFormat);
        } catch (\yii\base\InvalidParamException $e) {
            $output = false;
        }

        return ($nullDate !== $output) ? $output : false;
    }

    /**
     * @param $event
     */
    public function convertAttributes($event)
    {
        foreach($this->owner->attributes as $name => $value) {
            if (in_array($name, $this->attributes) && false !== ($date = $this->convertFormat($value))) {
                $this->owner->{$name} = $date;
            }
        }
    }
}

