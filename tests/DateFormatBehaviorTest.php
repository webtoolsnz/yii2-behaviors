<?php

namespace webtoolsnz\behaviors\tests;


use webtoolsnz\behaviors\DateFormatBehavior;
use yii\base\Model;


class DateFormatBehaviorTest extends \PHPUnit_Framework_TestCase
{
    public function testAttributeFormat()
    {
        $model = new DateFormatBehaviorTestModel();
        $model->attachBehavior('dateFormat', [
           'class' => DateFormatBehavior::className(),
            'displayFormat' => 'dd/MM/yyyy',
            'attributes' => ['date_one', 'date_two']
        ]);

        $model->date_one = '15/11/1987';
        $model->date_two = '01/02/2014';
        $model->date_three = '09/05/2015';

        $model->validate();

        $this->assertEquals('1987-11-15', $model->date_one);
        $this->assertEquals('2014-02-01', $model->date_two);
        $this->assertEquals('09/05/2015', $model->date_three);
    }

    public function testInit()
    {
        $model = new DateFormatBehaviorTestModel();

        $model->attachBehavior('dateFormat', [
            'class' => DateFormatBehavior::className(),
            'displayFormat' => 'dd/MM/yyyy',
        ]);

        $this->assertEquals('d/m/Y', $model->displayFormat);
        $this->assertEquals('Y-m-d', $model->saveFormat);
    }


    public function testConvertDateFormat()
    {
        $model = new DateFormatBehaviorTestModel();

        $model->attachBehavior('dateFormat', [
            'class' => DateFormatBehavior::className(),
            'displayFormat' => 'dd/MM/yyyy'
        ]);

        $this->assertEquals('1987-11-15', $model->convertDateFormat('15/11/1987'));
        $this->assertEquals('1970-01-01', $model->convertDateFormat('01/01/1970'));

        $model->saveFormat = 'd-m-Y';

        $this->assertEquals('15-11-1987', $model->convertDateFormat('15/11/1987'));
        $this->assertEquals('01-01-1970', $model->convertDateFormat('01/01/1970'));

    }
}


class DateFormatBehaviorTestModel extends Model
{
    public $date_one;
    public $date_two;
    public $date_three;
}