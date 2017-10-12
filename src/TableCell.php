<?php
namespace WebHoanHao\ReadDataHtmlTable;
/**
 * Created by PhpStorm.
 * User: BaoBui
 * Date: 9/15/2017
 * Time: 10:46 AM
 */

/**
 *
 * Class TableCell
 * @property string $id
 * @property string $cellText
 * @property int $posInRowSpan
 * @property int $posInColSpan
 * @method int getColumnSpan()
 */
/**
 * Class TableCell
 * @package WebHoanHao\ReadDataHtmlTable
 */
class TableCell {

    /**
     * @var
     */
    private $id;

    /**
     * @var
     */
    private $spanFrom;

    /**
     * @var
     */
    private $itemType;// ='cell' || ='span' || ='del'

    /**
     * @var
     */
    private $posInRowSpan;

    /**
     * @var
     */
    private $posInColSpan;

    /**
     * @var
     */
    private $cellText;

    /**
     * @var
     */
    private $columnPos;

    /**
     * @var
     */
    private $rowPos;

    /**
     * @var
     */
    private $columnSpan;

    /**
     * @var
     */
    private $rowSpan;

    private $hasValue = true;


    /**
     * @return bool
     */
    public function isHasValue() {
        return $this->hasValue;
    }


    /**
     * @param bool $hasValue
     */
    public function setHasValue($hasValue) {
        $this->hasValue = $hasValue;
    }

    /**
     * @return mixed
     */
    public function getColumnPos() {
        return $this->columnPos;
    }


    /**
     * @param mixed $id
     */
    public function setId($id) {
        $this->id = $id;
    }


    /**
     * @param mixed $posInRowSpan
     */
    public function setPosInRowSpan($posInRowSpan) {
        $this->posInRowSpan = $posInRowSpan;
    }


    /**
     * @param mixed $posInColSpan
     */
    public function setPosInColSpan($posInColSpan) {
        $this->posInColSpan = $posInColSpan;
    }


    /**
     * @param mixed $columnPos
     */
    public function setColumnPos($columnPos) {
        $this->columnPos = $columnPos;
    }


    /**
     * @return mixed
     */
    public function getRowPos() {
        return $this->rowPos;
    }


    /**
     * @param mixed $rowPos
     */
    public function setRowPos($rowPos) {
        $this->rowPos = $rowPos;
    }

    public function toString() {
        print "*** Content: "."<br>".$this->cellText."<br>".
            "<pre>+ position in ColSpan: ".$this->posInColSpan." - position in RowSpan: ".$this->posInRowSpan."</pre>".
            "<pre>+ colPosition: ".$this->columnPos." - rowPosition: ".$this->rowPos."</pre>".
            "<pre>+ rowspan: ".$this->rowSpan." - ".
            "colspan: ".$this->columnSpan."</pre><br>";
    }

    /**
     * @return mixed
     */
    public function getCellText() {
        return $this->cellText;
    }


    /**
     * @param mixed $cellText
     */
    public function setCellText($cellText) {
        $this->cellText = $cellText;
    }


    /**
     * @return mixed
     */
    public function getColumnSpan() {
        return $this->columnSpan;
    }


    /**
     * @param mixed $columnSpan
     */
    public function setColumnSpan($columnSpan) {
        $this->columnSpan = $columnSpan;
    }


    /**
     * @return mixed
     */
    public function getRowSpan() {
        return $this->rowSpan;
    }


    /**
     * @param mixed $rowSpan
     */
    public function setRowSpan($rowSpan) {
        $this->rowSpan = $rowSpan;
    }

}