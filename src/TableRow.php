<?php
namespace WebHoanHao\ReadDataHtmlTable;
/**
 * Created by PhpStorm.
 * User: BaoBui
 * Date: 9/16/2017
 * Time: 4:10 PM
 */
class TableRow {
    private $cells;//array

    public function __construct() {
        $this->cells = array();
    }

    public function countCells () {
        return count($this->cells);
    }

    public function hasCells(){
        return (count($this->cells) >= 1);
    }

    public function addCell($cellContent, $columnPosition, $rowPosition, $columnSpanNum, $rowSpanNum){
        $newCell = new TableCell($cellContent,$columnPosition, $rowPosition,$columnSpanNum,$rowSpanNum);
        $this->cells[] = $newCell;
        //array_unshift($this->cells, $new_cell);
    }
    public function removeCell($pos) {
        if ($pos>=0&&$pos<count($this->cells)) {
            unset($this->cells[$pos]);
        }
    }

    public function toString() {
        if ($this->hasCells()) {
            foreach ($this->cells as $cell) {
                print $cell->toString();
            }
            print "<hr />";
        } else {
            print "Row no cell<hr />";
        }
    }
}